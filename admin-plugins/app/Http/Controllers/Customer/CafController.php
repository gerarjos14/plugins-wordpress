<?php

namespace App\Http\Controllers\Customer;

use App\Models\Caf;
use App\Dtes\Sii\Folios;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class CafController extends Controller
{
    public function create()
    {

        $user = Auth::user();
        $user->load("company");
        $company_id=null;

        if(!empty($user->company)) {
            $company_id = $user->load("company")->company->id;
        }

        $folioInvoice=null;
        $folioBallot=null;
        $folioExent=null;
        $folioNotaDebito=null;
        $folioNotaCredito=null;


        $cafBallot = Caf::where('type',39)->where('company_id', $company_id)->orderBy('id','DESC')->first();
        if(isset($cafBallot))
        $folioBallot = new Folios(Crypt::decryptString($cafBallot->xml));

        $cafInvoice = Caf::where('type',33)->where('company_id', $company_id)->orderBy('id','DESC')->first();
        if(isset($cafInvoice))
        $folioInvoice = new Folios(Crypt::decryptString($cafInvoice->xml));

        $cafExent = Caf::where('type',34)->where('company_id', $company_id)->orderBy('id','DESC')->first();
        if(isset($cafExent))
        $folioExent = new Folios(Crypt::decryptString($cafExent->xml));

        $cafNotaDebito = Caf::where('type',56)->where('company_id', $company_id)->orderBy('id','DESC')->first();
        if(isset($cafNotaDebito))
        $folioNotaDebito = new Folios(Crypt::decryptString($cafNotaDebito->xml));

        $cafNotaCredito = Caf::where('type',61)->where('company_id', $company_id)->orderBy('id','DESC')->first();
        if(isset($cafNotaCredito))
        $folioNotaCredito = new Folios(Crypt::decryptString($cafNotaCredito->xml));

        //return $cafs->getDesde();
        return view("customer.caf.create", compact(
            'cafInvoice',
            'folioInvoice',
            'cafBallot',
            'folioBallot',
            'cafExent',
            'folioExent',
            'cafNotaDebito',
            'folioNotaDebito',
            'cafNotaCredito',
            'folioNotaCredito',
        ));
    }
    /**
     * undocumented function
     *
     * @return void
     */
    public function store(Request $request)
    {

        $validator = $request->validate([
            'folio' => 'required|mimes:xml'
        ]);

        // Validar que el usuario tenga una compañia
        $user = Auth::user();
        $user->load("company");
        if(empty($user->company)) {
            session()->flash('message', ['danger', 'No sabemos como has llegado hasta aquí']);
            return redirect()->route('customer.companies.index');
        }
        $company = $user->company;

        try{
            //return simplexml_load_file("note.xml")->get();

            $xml = $request->file('folio')->get();
            //return 'aca';
            $folios = new Folios($xml);

            $type = $folios->getTipo();
            $issuer = $folios->getEmisor();
            $from = $folios->getDesde();
            $to = $folios->getHasta();
            $certification = $folios->getCertificacion();
            $date = $folios->getFechaAutorizacion();

            // TODO: Validation
            if (!$folios->check()) {
              throw new Exception("Los folios, que acaba de subir no tienen un formato válidos");
            }

            if ($company->rut !== $issuer) {
              throw new Exception("Los folios, no pertenecen a esta compañia verifique y vuelva a intentar");
            }

            Caf::create([
            'xml' => Crypt::encryptString($xml),
            'type' => $type,
            'from' => $from,
            'to' => $to,
            'next' => $from,
            'available' => $to - $from + 1,
            'authorized_at' => $date,
            'certification' => $certification,
            'company_id' => $company->id,
            ]);

            session()->flash('message', ['success', 'Caf ingresado con exito']);
            return redirect()->route('customer.companies.index');

        } catch (\Exception $th) {
            session()->flash('error', ['danger', $th->getMessage()]);
            return redirect()->route('customer.cafs.create');
        }
    }
}
