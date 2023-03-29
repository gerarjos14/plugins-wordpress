<?php

namespace App\Http\Controllers\Admin;

use App\Models\Country;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCountryRequest;

class CountryController extends Controller
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
        return view('admin.countries.index');
    }

    /**
     * Data for the list
     * 
     * @return Datatable
     */
    public function datatable()
    {
        $countries = Country::get();

        return datatables()->of($countries)    
                ->addColumn('action', function ($row) {
                    return "";
                })            
                ->toJson();   
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.countries.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCountryRequest $request)
    {
        $country = Country::create([
            'name' => $request->name,
            'code' => strtoupper($request->code)
        ]);
        session()->flash('message', ['success', 'Pais creado con exito']);
        return redirect(route('admin.countries.index'));
    }
}
