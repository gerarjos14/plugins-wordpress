<?php

namespace App\Http\Controllers\Agency;

use Carbon\Carbon;
use App\Models\TransferRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TransferRequestController extends Controller
{ 
    public function __construct()
    {
        $this->middleware('is_agency');
    }

    public function create()
    {

        return view('agency.transfer_request.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1'
        ]);
        $user = auth()->user();
        $amount = $request->amount;
        if($user->balance < $amount){
            return back()
                    ->withErrors(['amount' => 'El monto de la transferencia no debe superar los $'.$user->balance])
                    ->withInput($request->all());
        }

        $transfer = new TransferRequest;
        $transfer->amount = $amount;
        $transfer->created_at = Carbon::now();
        $transfer->user_id = $user->id;
        $transfer->save();
        return redirect()->route('agency.dashboard');
    }

}
