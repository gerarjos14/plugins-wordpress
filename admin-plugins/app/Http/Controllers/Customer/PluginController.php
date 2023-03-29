<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PluginController extends Controller
{
    public function __construct()
    {
        $this->middleware('is_customer');
    }

    public function index()
    {
        return view('customer.plugin');
    }

    public function download()
    {
        // $agency = auth()->user()->agency;
        // if(!$agency->plugin){
        //     return back();
        // }
        // $filename = $agency->plugin->filename;
        return response()->download(storage_path('billconnector.zip'));
    }
}
