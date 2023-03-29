<?php

namespace App\Http\Controllers\Customer;

use App\Models\Plan;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderLine;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Laravel\Cashier\Exceptions\PaymentFailure;
use Laravel\Cashier\Exceptions\PaymentActionRequired;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller{

    const ENDPOINT_DATA_STORE_BY_EMAIL = 'shop_data_email/';
    const ENDPOINT_SUSCRIPTION         = 'updateVendorSubscription';

    public function __construct(){
        $this->middleware('is_customer');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){     

        $user             = auth()->user();
        $lifetime_plan    = $user->purchased_plans()->first();
        $priceCurrentPlan = null;
        $currentPlan      = null;
        $plans            = null;

        if (!$lifetime_plan) {
            $admin = User::where('role', User::ADMIN)->first();
            if ($user->parent_id == $admin->id) {
                $plans = Plan::where('user_id', $admin->id)
                    ->where('active', true)
                    //->where('interval', Plan::LIFETIME)
                    ->get();
            } else {
                $plans = Plan::where('user_id', $user->parent_id)
                    ->where('active', true)
                    ->when($user->allow_lifetime, function ($query) {
                        $query->orWhere('interval', \App\Models\Plan::LIFETIME);
                    })
                    ->get();
            }

            $currentPlan = $user->subscription('main');
            if ($currentPlan) {
                if ($currentPlan->active()) {
                    $plan = \App\Models\Plan::where('plan_id', $currentPlan->stripe_plan)->first();
                    $priceCurrentPlan = $plan->amount;
                }
            }
        }
        return view('customer.subscriptions.index', compact('plans', 'priceCurrentPlan', 'lifetime_plan'));
    }



    public function buy(){
        /**
         * Si el usuario se acaba de registrar desde la web de billconnector,
         * obtenemos el plan que seleccione y arrastro desde alli, y generamos la suscripcion de forma automatica.
         */


        if (session()->has('package')) {
            //OBTENEMOS EL PLAN EN BASE AL NOMBRE QUE SE SELECCIONO
            $plan_selected = \App\Models\Plan::where('name', session('package'))->first();
            session()->forget('package');
            //OBTENEMOS EL ID DEL PLAN
            if ($plan_selected) {
                $planId = $plan_selected->id;
                //ELIMINAMOS LA VARIABLE DE SESSION DONDE SE ALMACENABA EL PLAN
                session()->forget('package');
            } else {
                session()->flash('message', ['success', 'Error']);
                return $this->index();
            }
        } else {
            
            /**
             * ESTE ES EL FLUJO NORMAL SI EL CLIENTE VIENE DESDE LA SELECCION DE SUSCRIPCION POR DEFECTO.
             */

            $lifetime_plan = auth()->user()->purchased_plans()->first();
            if ($lifetime_plan) {
                return abort(401);
            }

            /**
             * * Se verifica si el usuario es de BeOn24
             * * Se ejecuta después de que se selecciona un plan y es la 1° vez
             * @author Matías
             */
            if (session()->has('url_redirect') && !(session()->has('id_package_beon24'))) {
                Log::debug('Test');
                if (!auth()->user()->hasPaymentMethod()) {
                    $this->validate(request(), [
                        'plan' => 'required'
                    ]);
                    $planId = (int) request("plan");
                    $plan   = \App\Models\Plan::find($planId);

                    // Se carga la información del paquete
                    session([
                        'id_package_beon24' => $plan->id
                    ]);

                    return redirect()->route('customer.billing.credit_card_form');
                }

            }


            if (!auth()->user()->hasPaymentMethod()) {
                session()->flash('message', ['danger', 'No sabemos cómo has llegado hasta aquí, ¡añade una tarjeta para contratar un plan!']);
                return back();
            }

            if (session()->has('id_package_beon24')) {
                $plan_selected = \App\Models\Plan::find(session('id_package_beon24'));
                //OBTENEMOS EL ID DEL PLAN
                if ($plan_selected) {
                    $planId = $plan_selected->id;
                } else {
                    session()->flash('message', ['success', 'Error']);
                    return $this->index();
                }
            } else{

                $this->validate(request(), [
                    'plan' => 'required'
                ]);

                $planId = (int) request("plan");
                //obtenemos el plan que se está intentando contratar
            }          
        }        

        $user = User::with('company')->where('id', auth()->user()->id)->first();
        $plan = session()->has('id_package_beon24') ? \App\Models\Plan::find(session('id_package_beon24')) :  \App\Models\Plan::find($planId);

        $user->qty_of_plan_documents = $plan->qty_documents;

        try {
            if (empty($plan->plan_id) && $plan->interval == \App\Models\Plan::LIFETIME) {

                $order               = new Order;
                $order->user_id      = auth()->id();
                $order->total_amount = $plan->amount;
                $order->save();
                
                OrderLine::insert([
                    'plan_id'  => $plan->id,
                    'order_id' => $order->id,
                    'price'    => $plan->amount,
                ]);

                auth()->user()->invoiceFor('Plan vitalicio para ' . $plan->platform, $plan->amount * 100);
                session()->flash('message', [
                    'success', 'Has comprado el plan vitalicio para ' . $plan->platform  . ' correctamente, recuerda revisar tu correo electrónico por si es necesario confirmar el pago'
                ]);

                return redirect(route('customer.billing.invoice'));

            } else {

                if ($planId === $plan->id && $plan->active) {

                    $currentPlan = auth()->user()->subscription('main');
                    // si no ha finalizado subimos el plan
                    if ($currentPlan && !$currentPlan->ended()) {                        
                        $currentPlanForCompare = \App\Models\Plan::where('plan_id', $currentPlan->stripe_plan)->first();
                        //comparamos los precios para saber que el próximo plan tiene un precio superior
                        if ($currentPlanForCompare) {
                            auth()->user()->subscription('main')->swapAndInvoice($plan->plan_id);
                            session()->flash('message', ['info', 'Has cambiado al plan ' . $plan->name . ' correctamente, recuerda revisar tu correo electrónico por si es necesario confirmar el pago']);
                            $user->save();
                            return redirect(route('customer.billing.invoice'));
                            // ! REVISAR
                            // if ($currentPlanForCompare->amount <= $plan->amount) {
                            //     //subimos el plan y generamos la factura al momento!
                            //     auth()->user()->subscription('main')->swapAndInvoice($plan->plan_id);
                            //     session()->flash('message', ['info', 'Has cambiado al plan ' . $plan->name . ' correctamente, recuerda revisar tu correo electrónico por si es necesario confirmar el pago']);
                            //     $user->save();
                            //     return redirect(route('customer.billing.invoice'));
                            // }
                        }
                    } else {
                        // si nunca ha contratado una suscripción

                        auth()->user()->newSubscription('main', $plan->plan_id)->create();
                        session()->flash('message', ['success', 'Te has suscrito al plan ' . $plan->name . ' correctamente, recuerda revisar tu correo electrónico por si es necesario confirmar el pago']);
                        $user->save();

                        // * Si el usuario proviene desde el plugin de BEON, se pasa a la url indicada en session
                        if (session()->has('url_redirect')) {

                            $url_ecommerce = session('url_store');
                            // Se manda información de actualización de suscripción a tienda
                            // se evalua si hubo algún incoveniente o no
                            if (!($this->updateStatusSubscription($url_ecommerce, $user))) {
                                session()->flash('message', ['info', 'Error interno al actualizar información de suscripción en ecommerce']);
                                return back();
                            }

                            return true;
                        }
                        return redirect(route('customer.billing.invoice'));
                    }
                } else {
                    session()->flash('message', ['info', 'El plan seleccionado parece no estar disponible']);
                    return back();
                }
            }
        } catch (PaymentActionRequired $exception) {
            session()->flash('message', ['success', 'Te has suscrito al plan ' . $plan->name . ' correctamente, ya puedes disfrutar de todas las ventajas']);
            return redirect()->route(
                'cashier.payment',
                [$exception->payment->id, 'redirect' => back()]
            );
        } catch (PaymentFailure $exception) {
            session()->flash('message', ['danger', $exception->getMessage()]);
            return back();
        } catch (\Exception $exception) {
            dd($exception->getMessage());
        }
        return abort(401);
    }

    /**
     * Función updateStatusSubscription
     *
     * @param string $url
     * @param User $user
     * @author Matías | BEON
     */
    public function updateStatusSubscription(string $url, User $user): bool{

        // Se manda información a sistema BEON
        $this->updateDataStatusInBeon($user);
        // Se actualiza en el worpdress del cliente
        $endpoint = $url . 'wp-json/plugin-beon/v1/update_subscription';
        $data     = [
            'url_store'           => $user->company->ecomerce_url,
            'name_store'          => $user->name,
            'email_store'         => $user->email,
            'status_subscription' => true
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $result = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($result);

        if ($response->status == 200) {
            return true;
        }
        return false;
    }

    /**
     * Función updateDataStatusInBeon
     * 
     * Función encargada de mandar a beon actualización de 
     * status de billconnector de la tienda
     * @param User $user
     * @author Matías
     * @return bool
     */
    public function updateDataStatusInBeon(User $user): bool{
        // Primero se obtiene la data de la tienda por medio del email
        $vendor_id = $this->getDataStoreBeon($user->email);

        if ($vendor_id) {
            // Se envia data para actualizar suscripción
            $url      = env('APP_ENV') == 'production' ? env('BEON_URI') : env('BEON_URI_TEST');

            $endpoint = $url . self::ENDPOINT_SUSCRIPTION;

            $data = [
                "vendor_id"                     => strval($vendor_id),
                "bill_conection_payment_check"  => "true",
                "bill_conection_payment_expire" => "NULL",
            ];

            $data = json_encode($data);

            //Log::debug($data);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $endpoint);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);


            $result = curl_exec($ch);
            curl_close($ch);
            //Log::debug($result);

            $response = json_decode($result);
            //Log::debug('Desde actualización suscripción');
            //Log::debug($response->success);
            return $response ? ($response->success ? true : false) : false;
        }
        return false;
    }

    public function getDataStoreBeon(string $email){
        $url =  env('APP_ENV') == 'production' ? env('BEON_URI') : env('BEON_URI_TEST');
        $endpoint = $url . self::ENDPOINT_DATA_STORE_BY_EMAIL . $email;
        //Log::debug($endpoint);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        $result = curl_exec($ch);

        curl_close($ch);
        $response = json_decode($result);

        if($response){
            if($response->success = 'success'){
                User::where('id', auth()->user()->id)->update([
                    'name'       => $response->data[0]->shopName,
                    'updated_at' => Carbon::now(),
                ]);
            }
        }

        return $response ? ($response->success = 'success' ? $response->data[0]->shopId : null) : null;
    }

    public function cancel(Request $request){
        auth()->user()->subscription(request('plan'))->cancel();
        session()->flash('message', ['success', 'La suscripción se ha cancelado con exito']);
        return back();
    }


    public function resume(){
        $subscription = request()->user()->subscription(request('plan'));

        if ($subscription->cancelled()) {
            request()->user()->subscription(request('plan'))->resume();
            session()->flash('message', ['success', 'Has reanudado tu suscripción con exito']);
            return back();
        }
        session()->flash('message', ['danger', 'La suscripción no se puede reanudar, consulta con el administrador']);
        return back();
    }

    public function invoice(){
        $plan = null;
        $currentPlan = null;
        if (auth()->user()->hasPaymentMethod()) {
            $currentPlan = auth()->user()->subscription('main');
            if ($currentPlan) {
                if ($currentPlan->active()) {
                    $plan = \App\Models\Plan::where('plan_id', $currentPlan->stripe_plan)->first();
                }
            }
        }
        return view('customer.subscriptions.invoices', compact('plan', 'currentPlan'));
    }

    public function downloadInvoice($invoiceId){
        return auth()->user()->downloadInvoice($invoiceId, [
            'vendor'    => 'Mi empresa',
            'product'   => 'Suscripción',
        ]);
    }
}
