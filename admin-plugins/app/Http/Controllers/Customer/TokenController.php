<?php

namespace App\Http\Controllers\Customer;

use App\Models\Token;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TokenController extends Controller
{
    public function __construct()
    {
        $this->middleware('is_customer');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {         
        $token = auth()->user()->token;
        return view('customer.tokens.index', compact('token'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $token = auth()->user()->token;
        if(!$token || Carbon::now()->diffInDays($token->created_at) >= 7){
            if($token) $token->delete();

            $token = new Token;
            $token->user_id = auth()->user()->id;
            $token->token = $this->generateCode();        
            $token->save();
            session()->flash('message', ['success', 'Token creado con exito']);
        }else{
            $days = 7 - Carbon::now()->diffInDays($token->created_at);
            session()->flash('message', ['danger', 'Debes esperar ' . $days .' dias para poder generar un nuevo token. Si no desea esperar contactese con el administrador.']);
        }
        return true;
    }

    /**
     * Update the blocked field.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function lock(Token $access_token)
    {
        $access_token->blocked = 1;
        $access_token->save();
        session()->flash('message', ['success', 'Token bloqueado con exito']);
        return true;
    }

    /**
     * Update the blocked field.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function unlock(Token $access_token)
    {
        $access_token->blocked = 0;
        $access_token->save();
        session()->flash('message', ['success', 'Token habilitado con exito']);
        return true;
    }

    protected function generateCode() {
        $key = '';
        $pattern = '1234567890abcdefghijklmnopqrstuvwxyz';
        $max = strlen($pattern)-1;
        for ($i=0; $i < 20; $i++) { 
            $key .= $pattern[mt_rand(0,$max)];
        }
        $token = Token::where('token','token_'.$key)->first();
        if($token) $this->generateCode();
        return 'token_' . $key;
    }
}
