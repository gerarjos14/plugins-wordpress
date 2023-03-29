<?php

namespace App\Http\Controllers\Admin;

use App\Models\Payment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PaymentController extends Controller
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
        return view('admin.payments.index');
    }

    /**
     * Data for the list
     * 
     * @return Datatable
     */

    public function datatable()
    {     
        $payments = Payment::with('user')->get();
        return datatables()->of($payments)
                ->editColumn('user', function ($row) {
                    return $row->user->name;
                }) 
                ->toJson();
    }
}
