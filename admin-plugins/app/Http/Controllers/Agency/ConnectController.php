<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ConnectController extends Controller
{
    public function index()
    {
        if(auth()->user()->connected_account){
            $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
            $redirect = $stripe->accounts->createLoginLink(
                auth()->user()->account_id,
                []
            );
            return redirect()->away($redirect->url);
        }
        return view('agency.stripe_connect.index');
    }

    public function create()
    {
        $user = auth()->user();
        if(empty($user->account_id)){
            $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
            $account = $stripe->accounts->create([
                'type' => 'express',
            ]);
            $user->account_id = $account->id;
            $user->save();
        };
        return redirect()->route('agency.stripe-connect.index');
    }

    public function createAccountLink()
    {
        $user = auth()->user();
        if($user->account_id && !$user->connected_account){
            $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
            $redirect = $stripe->accountLinks->create([
                'account' => $user->account_id,
                'refresh_url' => route('agency.dashboard'),
                'return_url' => route('agency.dashboard'),
                'type' => 'account_onboarding',
            ]);
            return redirect()->away($redirect->url);
        };
        return redirect()->route('agency.stripe-connect.index');
    }

    public function enableAccount()
    {

        try {
            $user = auth()->user();
            if($user->account_id){
                $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));      
                $account = $stripe->accounts->retrieve(
                    $user->account_id,
                    []
                );
                $enabled = $account->details_submitted;
                if($enabled){
                    $charges_enabled = $account->charges_enabled;
                    if($charges_enabled){
                        $user->connected_account = $enabled;
                        $user->save();
                    }
                    session()->flash('message', ['danger', 'Has completado tus datos correctamente, pero aún no podemos activar tu cuenta. Contáctese con el administrador']);
                }else{
                    session()->flash('message', ['danger', 'Aun debes completar datos en stripe.']);
                }
            }
        } catch (\Exception $exception) {
            session()->flash('message', ['danger', 'Ha ocurrido un error inesperado contáctese con el administrador.']);
        }
        return true;
    }
}
