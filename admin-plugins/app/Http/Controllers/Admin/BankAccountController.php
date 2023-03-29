<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\BankAccount;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateBankAccountRequest;

class BankAccountController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('is_admin');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.bank_account.index');
    }

    /**
     * Data for the list
     * 
     * @return Datatable
     */

    public function datatable()
    {
        $agencies = User::where('role', User::AGENCY)
                        ->pluck('id');

        $bankAccounts = BankAccount::with('user')->whereIn('user_id', $agencies)->get();
        
        return datatables()->of($bankAccounts)
            ->editColumn('user', function ($row) {
                return $row->user->name;
            })
            ->editColumn('name', function ($row) {
                return ucfirst($row->name) . ' ' . ucfirst($row->last_name);
            })
            ->editColumn('account_type', function ($row) {
                return $row->type;
            })
            ->addColumn('action', function ($row) {
                $html = '<a href="'. route('admin.bank-account.edit', $row->id) .'" class="btn btn-sm btn-outline-secondary mr-1"><i class="fas fa-pencil-alt mr-1"></i>Editar</a>';
                return $html;
            })
            ->toJson();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(BankAccount $bank_account)
    {
        return view('admin.bank_account.edit', compact('bank_account'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBankAccountRequest $request, BankAccount $bank_account)
    {
        $bank_account->name = $request->name;
        $bank_account->last_name = $request->last_name;
        $bank_account->account_number = $request->account_number;
        $bank_account->account_type = $request->account_type;
        $bank_account->bank_name = $request->bank_name;
        $bank_account->identity_card = $request->identity_card;
        $bank_account->save();
        session()->flash("message", ["success", 'Cuenta actualizada con exito']);
        return redirect()->route('admin.bank-account.index');
    }

}
