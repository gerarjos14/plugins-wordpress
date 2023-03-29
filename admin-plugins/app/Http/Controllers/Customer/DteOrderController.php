<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\DteOrder;
use App\Models\Dte;
use Illuminate\Support\Facades\Auth;

class DteOrderController extends Controller
{


    public function index(){

        $user = Auth::user();
        $orders = DteOrder::where('user_id',$user->id)->get();

        return view('customer.orders.orders',compact('orders'));
    }

    public function show($id){

        $order = DteOrder::where('order_id', $id)->with('details')->first();


        $dte = $order->dtes_id? Dte::where('order_id',$id)->with('envio_dte')->first():null;



        return view('customer.orders.order_details',compact('order','dte'));

    }

    public function cancel(DteOrder $DteOrder)
    {
        // $user = Auth::user();

        // $response = $this->cancel_dte($user->token()->first(), $DteOrder->order_id);

        // return $response;


    }
}
