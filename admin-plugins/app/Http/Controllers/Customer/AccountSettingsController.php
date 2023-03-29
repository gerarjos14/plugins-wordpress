<?php

namespace App\Http\Controllers\Customer;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\UpdateUserKeyRequest;

class AccountSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('is_customer');
    }

    public function configureKeys()
    {
        $key = auth()->user()->key;
        return view('customer.settings.configure_keys', compact('key'));
    }
    
    public function updateKeys(UpdateUserKeyRequest $request)
    {
        auth()->user()->key()->update(
            [
                'alegra_user' => $request->alegra_user,
                'alegra_token' => $request->alegra_token,
                'wc_consumer_key' => $request->wc_consumer_key,
                'wc_consumer_secret' => $request->wc_consumer_secret,
            ]
        );
        session()->flash("message", ["success", 'Claves actualizadas creado con exito']);
        return redirect()->route('customer.configure-keys');
    }

}
