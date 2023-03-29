<?php

namespace App\Http\Controllers\Agency;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Agency\StoreBanckAccountRequest;

class BankAccountController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('is_agency');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $bankAccount = auth()->user()->bank_account;
        return view('agency.bank_account.index', compact('bankAccount'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreBanckAccountRequest $request)
    {
        auth()->user()->bank_account()->update(
            [
                'name' => $request->name,
                'last_name' => $request->last_name,
                'account_number' => $request->account_number,
                'account_type' => $request->account_type,
                'bank_name' => $request->bank_name,
                'identity_card' => $request->identity_card,
            ]
        );
        session()->flash("message", ["success", 'Cuenta actualizada creado con exito']);
        return redirect()->route('agency.bank-account.index');
    }

}
