<?php

namespace App\Http\Controllers\Customer;

use Illuminate\Http\Request;
use App\Services\AlegraService;
use App\Http\Controllers\Controller;

class PlatformBankAccountController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('is_customer');
    }
    
    public function index()
    {
        $user = auth()->user();
        $key = $user->key;
        $emptyKeys = true;
        $bankAccounts = [];

        $bankAccount = $user->platform_bank_account;
        
        if(!empty($key->alegra_user) || !empty($key->alegra_token)){
            $emptyKeys = false;
            try {
                $alegra = new AlegraService($key->alegra_user, $key->alegra_token);
                $bankAccounts = $alegra->listaCuentasBancancarias();
            } catch (\Exception $exception) {    
                session()->flash('message', ['danger', 'Ha ocurrido un error inesperado contáctese con el administrador.']);
                if(method_exists($exception, 'getResponse')){
                    $response = $exception->getResponse();
                    if(method_exists($response, 'getStatusCode')){
                        if($response->getStatusCode() == 401){
                            session()->flash('message', ['danger', 'Errores en las credenciales de alegra.']);
                        }
                    }
                } 
            }
        }
        return view('customer.bank_account.index', compact('emptyKeys', 'bankAccounts', 'bankAccount'));
    }

    public function store(Request $request)
    {
        $request->validate(['platform_bank_account' => 'required|numeric']);
        $user = auth()->user();  
        if($user->platform_bank_account){
            $user->platform_bank_account()->update([
                'account_id' => $request->platform_bank_account,
            ]);
        }else{
            $user->platform_bank_account()->create([
                'account_id' => $request->platform_bank_account,
            ]);
        }
        return back();
    }
}
