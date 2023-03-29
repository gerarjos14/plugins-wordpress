<?php

namespace App\Http\Controllers\Customer;

use App\Models\Signature;
use Illuminate\Http\Request;
use App\Dtes\FirmaElectronica;
use App\Dtes\Sii\Autenticacion;
use App\Http\Controllers\Controller;
use Error;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class SignatureController extends Controller
{
    public function create()
    {
        return view("customer.signature.create");
    }
    /**
     * undocumented function
     *
     * @return void
     */
    public function store(Request $request)
    {
        // Validar que el usuario tenga una compañia
        $user = Auth::user();
        $user->load("company");
        if(empty($user->company)) {
            session()->flash('message', ['danger', 'No sabemos como has llegado hasta aquí']);
            return redirect()->route('customer.companies.index');
        }
        $company = $user->company;

        try {
            // si el usuario tiene una firma asociada se borra antes de agregar la nueva
            // esto es necesario porque la PK de la firma es el RUN de la misma y no el ID
            // del usuario, además un usuario puede tener sólo una firma. Entonces si un
            // usuario ya tiene firma y trata de subir una nueva con un RUN diferente el
            // guardado de la firma falla. Para evitar este problema, se borra si existe una
            $data = $request->file('signature')->get();

            $signature_object = new FirmaElectronica([
                'data' => $data,
                'pass' => $request->password,
            ]);

            $token = Autenticacion::getToken([
              'data' => $data,
              'pass' => $request->password,
            ]);

            if (!$token) {
              throw new Exception('Al parecer no pudimos obtener acceso a www.sii.com usando su firma, intente de nuevo, y si persiste pruebe con otra firma');
            }

            $run = $signature_object->getID(); // RUT;
            $from = $signature_object->getFrom();
            $to = $signature_object->getTo();
            $name = $signature_object->getName();
            $email = $signature_object->getEmail();
            $issuer = $signature_object->getIssuer();

            // Borrar firmas anteriores este comportamiento puede cambiar si pasamos a utilizar multiples firmas
            Signature::where('company_id', $company->id)->delete();

            Signature::create([
                'run' => $run,
                'name' => $name,
                'issuer' => $issuer,
                'from' => $from,
                'to' => $to,
                'email' => $email,
                'file' => base64_encode($data),
                'password' => Crypt::encryptString($request->password),
                'company_id' => $company->id,
            ]);


            session()->flash('message', ['success', 'Firma ingresada con exito']);
            return redirect()->route('customer.companies.index');
        } catch (\Throwable $th) {
            session()->flash('error', ['danger', $th->getMessage()]);
            return redirect()->route('customer.signatures.create');
        } catch (Exception $e) {
            session()->flash('error', ['danger', $e->getMessage()]);
            return redirect()->route('customer.signatures.create');
        }
    }
}
