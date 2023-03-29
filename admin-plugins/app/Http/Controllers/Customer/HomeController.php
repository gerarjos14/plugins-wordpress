<?php

namespace App\Http\Controllers\Customer;

use App\Models\Caf;
use App\Dtes\Sii\Folios;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\Auth;
use App\Models\Company;
use Illuminate\Support\Facades\Crypt;

class HomeController extends Controller
{
    public function index(){


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
        return view("customer.home.index", compact(
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


        //return $company_id;

    }
}
