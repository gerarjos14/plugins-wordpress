<?php
namespace App\Traits;


use App\ChileRut;
use App\Dtes\Arreglo;
use App\Dtes\Log;

use App\Models\Dte;

use App\Models\User;
use App\Jobs\DteSend;
use App\Models\Token;
use App\Models\Company;
use App\Models\DteOrder;
use App\Models\DteOrderDetail;


use App\Dtes\Sii\Folios;
use App\Dtes\Sii\EnvioDte;
use Illuminate\Support\Str;

use Illuminate\Http\Request;
use App\Dtes\FirmaElectronica;
use App\Dtes\Sii;
use App\Dtes\Sii\Dte as SiiDte;
use App\Traits\ManageCompanies;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Mail\Mail\SendError;
use App\Rules\Rut;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Laravel\Cashier\Exceptions\PaymentFailure;
use Illuminate\Support\Facades\Validator;


trait DteActions {


    public function create_dte($data, $token)
    {


        $token = $this->isValidtoken($token);
        $company = $this->getCompanyByToken($token);


        $chile_rut = new ChileRut;

        $data['type'] = Company::TYPES[$data['type'] ?? $company->type_document];
        $data['rut'] = $chile_rut->clean($data['rut'] ?? '66666666-6');
        $data['name'] = Str::upper($data['name'] ?? 'CLIENTE GENERICO');
        $data['address'] = Str::upper($data['address'] ?? 'Nueva providencia');
        $data['state'] = Str::upper($data['state'] ?? 'Providencia');
        $data['classification'] = Str::upper($data['classification'] ?? 'Consultoria en informatica de la gestión o publicidad');

        $data['discount_perc'] = (isset($data['discount']) && isset($data['discount_perc'])) ? $data['discount_perc'] : true;

        $user = User::where('id', $token->user_id)->lockForUpdate()->first();

        $order = $this->createDteOrder($data, $user);



        //return $request;
        DB::beginTransaction();
        try {
            // Funcion de esta clase que obtiene el token o lanza una excepcion

            $this->canUseTheService($user); // Si tiene un metodo de pago o si tiene copias para usar. */


            $query = $company->cafs()->where('type', $data['type'])->where('available', '>', 0)->where('certification', $company->certification);

            if (!$query->exists()) {
                throw new \Exception('No existen folios validos, para generar, boletas electrónicas');
            }

            $caf = $query->orderBy('created_at', 'DESC')->lockForUpdate()->first();


            $folios = new Folios(Crypt::decryptString($caf->xml));

            $signature = $company->signature;

            if (!$signature) {
                throw new \Exception('No existe una firma asociada a esta compañia');
            }

            $signature_object =  new FirmaElectronica([
                'data' => base64_decode($company->signature->file),
                'pass' => Crypt::decryptString($company->signature->password)
            ]);

            $Receptor = [
                'RUTRecep' => $data['rut'],
                'RznSocRecep' => $data['name'],
            ];

            if ($data['type'] === 33 || $data['type'] === 34) {
                $Receptor['GiroRecep'] = $data['classification'];
                $Receptor['DirRecep'] = $data['address'];
                $Receptor['CmnaRecep'] = $data['state'];

                if (isset($data['email'])) {
                    $Receptor['CorreoRecep'] = $data['email'];
                }
            }

            $content = [
                'Encabezado' => [
                    'IdDoc' => [
                        'TipoDTE' => $data['type'],
                        'Folio' => $caf->next,
                    ],
                    'Emisor' => [
                        'RUTEmisor' => $company->rut,
                        'RznSoc' => $company->name, // tag verdadero es RznSocEmisor, pero se permite usar el de DTE
                        'GiroEmis' => $company->gr, // tag verdadero es GiroEmisor, pero se permite usar el de DTEta,true);
                        'Acteco' => $company->economy_activity,
                        'DirOrigen' => $company->address,
                        'CmnaOrigen' => $company->state->name,
                    ],
                    'Receptor' => $Receptor,
                ],
                'Detalle' => [],
            ];

            if (isset($data['discount']) && $data['discount'] != 0) {
                if ($data['discount_perc']) {
                    $data['discount'] = 100 * $data['discount'];
                }
                $content['DscRcgGlobal'] = [
                    'TpoMov' => $data['discount'] > 0 ? 'D' : 'R',
                    'TpoValor' => $data['discount_perc'] ? '%' : '$', // % or $
                    'ValorDR' => abs($data['discount']),
                ];
            }

            foreach ($data['items'] as $i) {
                $item = [
                    'NmbItem' => $i['description'],
                    'QtyItem' => $i['quantity'],
                    'PrcItem' => $i['unit_price'],
                ];

                if (isset($i['unit'])) {
                    $item['UnmdItem'] = $i['unit'];
                }

                if (isset($i['discount']) && $data['discount'] != 0) {
                    $discount_perc = isset($i['discount_perc']) ? $i['discount_perc'] : true;
                    $discount = $i['discount'];

                    if ($discount > 0) {
                        /* $item['TipoDscto'] = $discount_perc ? '%' : '$'; */
                        if ($discount_perc) {
                            $item['DescuentoPct'] = 100 * $discount;
                        } else {
                            $item['DescuentoMonto'] = $discount;
                        }
                    } elseif ($discount < 0) {
                        /* $item['TipoRecargo'] = $discount_perc ? '%' : '$'; */
                        if ($discount_perc) {
                            $item['RecargoPct'] = abs(100 * $discount);
                        } else {
                            $item['RecargoMonto'] = abs($discount);
                        }
                    }
                }

                if (isset($i['exempt']) && $i['exempt'] === true) {
                    $item['IndExe'] = 1;
                }

                $content['Detalle'][] = $item;
            }

            $dte = new SiiDte($content);
            $dte->timbrar($folios);
            $dte->firmar($signature_object);

            if (!$dte->schemaValidate()) {
                throw new \Exception('Schema validation failed in DTE');
            }

            $envio = new EnvioDte();
            $envio->agregar($dte);

            $envio->setCaratula(
                [
                    'RutEnvia' => $company->signature->run,
                    'RutReceptor' => '60803000-K',
                    'FchResol' => $company->resolution_date,
                    'NroResol' => $company->resolution_nro,
                ]
            );

            $envio->setFirma($signature_object);
            $envio->generar();

            if (!$envio->schemaValidate()) {

                throw new \Exception('Schema validation failed in EnvioDTE');
            }

            /**
             * Puede faltar realizar el cargo a la tarjeta si no esta suscripto
             * Podria ponerse en otra parte del flujo.
             * Puede lanzar excepciones de falta de saldo, de tarjeta declinada, etc.
             */

            if ($user->subscribed('main')) {
                $user->qty_of_plan_documents -= 1;
                $user->save();
            } else { // Metodo de cargo unico

                // Obtiene el metodo de pago por default del cliente.
                $paymentMethod = $user->defaultPaymentMethod();
                // Busca establecer el precio por copia
                // Si pasa los 500 dte este mes es otro precio
                $qtyDte = Dte::where('user_id', $user->id)
                    ->where('company_id', $company->id)
                    ->whereMonth('created_at', '=', date('m'))
                    ->whereYear('created_at', '=', date('Y'))
                    ->count();
                $price = ($qtyDte >= 500) ? 1100 : 1431;
                $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
                $payment = $stripe->paymentIntents->create([
                    'amount'    => $price,
                    'currency'  => 'clp',
                    'confirm'   => true,
                    'customer'  => $user->stripe_id,
                    'payment_method' => $paymentMethod->id
                ]);
            }

            $DTE = Dte::create([
                'company_id' => $company->id,
                'xml' => base64_encode($dte->saveXML()),
                'folio' => $caf->next,
                'uuid' => (string) Str::uuid(),
                'type' => $data['type'],
                'certification' => $company->certification,
                'user_id' => $user->id,
                'order_id' => $data['order_id']
            ]);

            $caf->next += 1;
            $caf->available -= 1;
            $caf->save();
            DB::commit();

            DteSend::dispatch($DTE);

            $order->dtes_id = $DTE->id;
            $order->save();

            return response()->json([
                'id' => $data['order_id'],
                'type' => $data['type'] ? $data['type'] : $company->type_document,
                'link' => url('/dte/' . $DTE->uuid),
            ], 200);
        } catch (PaymentFailure $exception) {
            DB::rollBack();
            // Esto indica que un pago falló por varias otras razones, como no tener fondos disponibles.
            $order->log = 'Error al realizar el cargo a la tarjeta';
            $order->save();
            Mail::to($company->email)->send(new SendError($company->name, url('order/'.$data['order_id']), 'Error al realizar el cargo a la tarjeta','DTE'));
            return response()->json(['message' => 'Error al realizar el cargo a la tarjeta'], 400);
        } catch (\Exception $e) {
            DB::rollBack();

            $error = $e->getMessage();
            foreach (Log::readAll() as $err) {
                $error = $error . '<br>' . $err;
            }
            $order->log = $error;
            $order->save();
            Mail::to($company->email)->send(new SendError($company->name, url('order/'.$data['order_id']), $error, 'DTE'));
            return response()->json(['message' => $error], 400);
        }
    }

    public function cancel_dte($token, $order_id)
    {
     //return $token;
      // Funcion de esta clase que obtiene el token o lanza una excepcion
      $token = $this->isValidtoken($token);
      $company = $this->getCompanyByToken($token);


      DB::beginTransaction();
      try {

        $order_id = $order_id;
        if (empty($order_id)) {
          throw new \Exception('El campo order id es obligatorio.');
        }

        $user = User::where('id', $token->user_id)->lockForUpdate()->first();
        $this->canUseTheService($user);

        $dte_to_cancel = Dte::where('user_id', $user->id)
          ->where('company_id', $company->id)
          ->where('order_id', $order_id)
          ->first();

        //Validar si es que ya esta enviado
        if (empty($dte_to_cancel)) {
          throw new \Exception('No encontramos ningún documento a cancelar.');
        }


        $dte_credit_note = $dte_to_cancel->references()->first();

        if (!empty($dte_credit_note)) {
            throw new \Exception('Ya existe note de credito de esta Orden.');
        }

        $query = $company->cafs()->where('type', Company::TYPES[Company::CREDIT_NOTE])->where('available', '>', 0)->where('certification', $company->certification);

        if (!$query->exists()) {
          throw new \Exception('No existen folios validos, para generar notas creditos');
        }

        $caf = $query->orderBy('created_at', 'DESC')->lockForUpdate()->first();


        $folios = new Folios(Crypt::decryptString($caf->xml));

        $signature = $company->signature;

        if (!$signature) {
          throw new \Exception('No existe una firma asociada a esta compañia');
        }

        $signature_object =  new FirmaElectronica([
          'data' => base64_decode($company->signature->file),
          'pass' => Crypt::decryptString($company->signature->password)
        ]);

        // if (!$dte_to_cancel->envio_dte) {
        //   throw new \Exception('Aun no se ha emitido un dte para esta orden, debe reintentar luego del envio al sii.');
        // }

        // if ($dte_to_cancel->envio_dte->estado !== 'EPR') {
        //   throw new \Exception('Aun no hemos confirmado el estado de emision del DTE, o el estado es invalido');
        // }

        $dte_ = new SiiDte(base64_decode($dte_to_cancel->xml));
        $data = $dte_->getDatos();


        /**
         * Puede faltar realizar el cargo a la tarjeta si no esta suscripto
         * Podria ponerse en otra parte del flujo.
         * Puede lanzar excepciones de falta de saldo, de tarjeta declinada, etc.
         */
        if ($user->subscribed('main')) {
            $user->qty_of_plan_documents -= 1;
            $user->save();
          } else { // Metodo de cargo unico

            // // Obtiene el metodo de pago por default del cliente.
            // $paymentMethod = $user->defaultPaymentMethod();
            // // Busca establecer el precio por copia
            // // Si pasa los 500 dte este mes es otro precio
            // $qtyDte = Dte::where('user_id', $user->id)
            //   ->where('company_id')
            //   ->whereMonth('created_at', '=', date('m'))
            //   ->whereYear('created_at', '=', date('Y'))
            //   ->count();
            // $price = ($qtyDte >= 500) ? 1100 : 1431;
            // $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
            // $payment = $stripe->paymentIntents->create([
            //   'amount'    => $price,
            //   'currency'  => 'clp',
            //   'confirm'   => true,
            //   'customer'  => $user->stripe_id,
            //   'payment_method' => $paymentMethod->id
            // ]);
          }

        if ($data['Encabezado']['IdDoc']['TipoDTE'] == Company::TYPES[Company::INVOICE]) {
          $RazonRef = 'ANULA FACTURA ELECTRONICA';
        } elseif ($data['Encabezado']['IdDoc']['TipoDTE'] == Company::TYPES[Company::EXEMPT_INVOICE]) {
          $RazonRef = 'ANULA FACTURA EXENTA';
        } elseif ($data['Encabezado']['IdDoc']['TipoDTE'] == Company::TYPES[Company::BALLOT]) {
          $RazonRef = 'ANULA BOLETA ELECTRONICA';
        }

        $content = [
          'Encabezado' => [
            'IdDoc' => [
              'TipoDTE' => Company::TYPES[Company::CREDIT_NOTE],
              'Folio' => $caf->next,
              'MntBruto' => 1,
            ],
            'Emisor' => $data['Encabezado']['Emisor'],
            'Receptor' => $data['Encabezado']['Receptor'],
            'Totales' => [
              'MntNeto' => 0,
              'TasaIVA' => Sii::getIVA(),
              'IVA' => 0,
              'MntTotal' => 0,
            ],
          ],
          'Detalle' => $data['Detalle'],
          'Referencia' => [
            'TpoDocRef' => $data['Encabezado']['IdDoc']['TipoDTE'],
            'FolioRef' => $data['Encabezado']['IdDoc']['Folio'],
            'CodRef' => 1,
            'RazonRef' => $RazonRef,
          ],
        ];

        if (isset($data['DscRcgGlobal'])) {
          $content['DscRcgGlobal'] = $data['DscRcgGlobal'];
        }

        $dte = new SiiDte($content);
        $dte->timbrar($folios);
        $dte->firmar($signature_object);

        if (!$dte->schemaValidate()) {
          throw new \Exception('Schema validation failed in DTE');
        }

        $envio = new EnvioDte();
        $envio->agregar($dte);

        $envio->setCaratula(
          [
            'RutEnvia' => $company->signature->run,
            'RutReceptor' => '60803000-K',
            'FchResol' => $company->resolution_date,
            'NroResol' => $company->resolution_nro,
          ]
        );

        $caf->next += 1;
        $caf->available -= 1;
        $caf->save();

        $envio->setFirma($signature_object);
        $envio->generar();

        if (!$envio->schemaValidate()) {
          throw new \Exception('Schema validation failed in EnvioDTE');
        }

        $DTE = Dte::create([
          'company_id' => $company->id,
          'xml' => base64_encode($dte->saveXML()),
          'folio' => $caf->next,
          'uuid' => (string) Str::uuid(),
          'type' => Company::TYPES[Company::CREDIT_NOTE],
          'certification' => $company->certification,
          'user_id' => $user->id,
        ]);

        $caf->next += 1;
        $caf->available -= 1;
        $caf->save();


        $dte_to_cancel->references()->attach($DTE->id, ['type' => 'ANULATION']);
        DB::commit();

        DteSend::dispatch($DTE);

        return response()->json([
          'type' => Company::CREDIT_NOTE,
          'link' => url('/dte/' . $DTE->uuid),
        ], 200);
      } catch (PaymentFailure $exception) {
        DB::rollBack();
        // Esto indica que un pago falló por varias otras razones, como no tener fondos disponibles.
        Mail::to($company->email)->send(new SendError($company->name, url('order/'.$order_id), 'Error al realizar el cargo a la tarjeta','Nota Credito'));
        return response()->json(['message' => 'Error al realizar el cargo a la tarjeta'], 400);
      } catch (\Exception $e) {
        DB::rollBack();
        $error = $e->getMessage();
        foreach (Log::readAll() as $err) {
          $error = $error . '<br>' . $err;
        }
        //return $error;
        Mail::to($company->email)->send(new SendError($company->name, url('order/'.$order_id), $error,'Nota Credito'));
        return response()->json(['message' => $error], 400);
      }
    }

    private function isValidtoken($token)
    {
        \Log::debug($token);
        // Compruebo si el token existe y no esta bloqueado
        $token = Token::where('token', request('token', null))->first();
        if (empty($token) || $token->blocked) {
            throw new \Exception('Invalid token');
        }
        return $token;
    }

    private function getCompanyByToken($token)
    {
        $user = $token->user;
        $company = $user->company;
        if (is_null($company)) {
            throw new \Exception('Completa los datos de tu compañia');
        }
        return $company;
    }

    private function createDteOrder($data, $user)
    {

        $dteOrder = DteOrder::updateOrCreate([
            'order_id'      =>      $data['order_id'],
        ], [
            'type'          =>      $data['type'],
            'user_id'       =>      $user->id,
            'rut'           =>      $data['rut'],
            'classification'=>      $data['classification'],
            'name'          =>      $data['name'],
            'email'         =>      isset($data['email']) ? $data['email'] : '',
            'phone'         =>      isset($data['phone']) ? $data['phone'] : '',
            'address'       =>      $data['address'],
            'state'         =>      $data['state'],
            'city'          =>      isset($data['city']) ? $data['city'] : '',
            'discount'      =>      isset($data['discount']) ? $data['discount'] : '0',
            'discount_prec' =>      $data['discount_perc']
        ]);

        if(empty($dteOrder->details()->get())){
            foreach ($data['items'] as $i) {
                DteOrderDetail::updateOrCreate([
                    'dte_order'     =>      $dteOrder->id,
                    'description'   =>      $i['description'],
                    'exempt'        =>      isset($i['exempt']) ? $i['exempt'] : 0,
                    'discount'      =>      isset($i['discount']) ? $i['discount'] : 0,
                    'discount_prec' =>      isset($i['discount_prec']) ? $i['discount_prec'] : 0,
                    'quantity'      =>      $i['quantity'],
                    'unit_price'    =>      $i['unit_price']
                ]);
            }
        }
        return $dteOrder;

    }

    private function canUseTheService($user)
    {
        if (!$user->hasPaymentMethod()) {
            throw new \Exception('No tienes un metodo de pago configurado.');
        }
        if ($user->subscribed('main')) {
            if ($user->qty_of_plan_documents < 1) {
                throw new \Exception('Se acabaron los documentos de tu plan');
            }
        }
    }



}
