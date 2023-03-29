<?php

namespace App\Http\Controllers\Agency;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Agency\StoreCustomerRequest;
use App\Http\Requests\Agency\UpdateCustomerRequest;

class CustomerController extends Controller
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
        return view('agency.customers.index');
    }

    /**
     * Data for the list
     * 
     * @return Datatable
     */

    public function datatable()
    {        
        $customers = User::select('id', 'name', 'email')
                        ->where('role', User::CUSTOMER)
                        ->where('parent_id', auth()->user()->id)
                        ->get();
        
        return datatables()->of($customers)
                ->addColumn('action', function ($row) {
                    $html = '<a href="'. route('agency.customers.edit', $row->id) .'" class="btn btn-sm btn-outline-secondary mr-1"><i class="fas fa-pencil-alt mr-1"></i>Editar</a>';
                    $html .= '<a href="#" data-route="'.route('agency.customers.destroy', $row->id).'" class="btn btn-sm btn-outline-danger delete-record"><i class="far fa-trash-alt mr-1"></i>Borrar</a>';
                    return $html;
                })->toJson();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('agency.customers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCustomerRequest $request)
    {
        $customer = User::create([
                        'name'       => $request->name,
                        'email'      => $request->email,
                        'password'   => Hash::make($request->password),
                        'role'       => User::CUSTOMER,
                        'parent_id'  => auth()->user()->id,
                        'country_id' => auth()->user()->country_id
        ]);
        $customer->key()->create();
        session()->flash("message", ["success", __("Cliente creado con exito")]);
        return redirect()->route('agency.customers.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $customer)
    {
        return view('agency.customers.edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCustomerRequest $request, User $customer)
    {

        $this->authorize('update', $customer);

        $customer->name = $request->name;
        $customer->email = $request->email;
        
        if(isset($request->password)){
            $customer->password = Hash::make($request->password);
        }
        
        $customer->save();
        
        session()->flash("message", ["success", __("Cliente actualizado con exito")]);
        return redirect()->route('agency.customers.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $customer)
    {
        $this->authorize('delete', $customer);

        $customer->delete();
        return back()->with('info','Eliminado correctamente');
    }
}
