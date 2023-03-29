<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Admin\StoreCustomerRequest;
use App\Http\Requests\Admin\UpdateCustomerRequest;

class CustomerController extends Controller
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
        return view('admin.customers.index');
    }

    /**
     * Data for the list
     *
     * @return Datatable
     */

    public function datatable()
    {
        $customers = User::select('id', 'name', 'email', 'allow_lifetime')
                        ->where('role', User::CUSTOMER)
                        ->with('company')
                        ->get();

        return datatables()->of($customers)
                ->editColumn('allow_lifetime', function ($row) {
                    if($row->allow_lifetime){
                        return '<i class="fa fa-check text-success" style="font-size:18px;"></i>';
                    }
                    return '<i class="fa fa-times text-danger" style="font-size:18px;"></i>';
                })
                ->editColumn('company.ecomerce_url', function ($row) {
                    if(isset($row->company)){
                        return $row->company->ecomerce_url;
                    }
                    return 'Sin Datos';
                })
                ->addColumn('action', function ($row) {
                    $html = '<a href="'. route('admin.customers.edit', $row->id) .'" class="btn btn-sm btn-outline-secondary mr-1"><i class="fas fa-pencil-alt mr-1"></i>Editar</a>';
                    $html .= '<a href="#" data-route="'.route('admin.customers.destroy', $row->id).'" class="btn btn-sm btn-outline-danger delete-record"><i class="far fa-trash-alt mr-1"></i>Borrar</a>';
                    return $html;
                })
                ->rawColumns(['allow_lifetime', 'action'])
                ->toJson();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $agencies = User::select('id', 'name')->where('role', User::AGENCY)->get();
        return view('admin.customers.create', compact('agencies'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCustomerRequest $request)
    {
        $user = User::findOrFail($request->agency);
        $customer = User::create([
                        'name'              => $request->name,
                        'email'             => $request->email,
                        'password'          => Hash::make($request->password),
                        'role'              => User::CUSTOMER,
                        'allow_lifetime'    => isset($request->allow_lifetime) ? true : false,
                        'parent_id'         => $request->agency,
                        'country_id'        => isset($user->country_id) ? $user->country_id : 2,
                    ]);
        $customer->key()->create();

        session()->flash('message', ['success','Cliente creado con exito']);
        return redirect()->route('admin.customers.index');
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
        $agencies = User::select('id', 'name')->where('role', User::AGENCY)->get();
        return view('admin.customers.edit', compact('customer', 'agencies'));
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


        $customer->name           = $request->name;
        $customer->email          = $request->email;
        $customer->allow_lifetime = isset($request->allow_lifetime) ? true : false;
        $customer->parent_id      = $request->id;
        
        if(isset($request->password)){
            $customer->password = Hash::make($request->password);
        }

        $customer->save();

        session()->flash('message', ['success', 'Cliente actualizado con exito']);
        return redirect()->route('admin.customers.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $customer)
    {
        $customer->delete();
        return back()->with('info','Eliminado correctamente');
    }
}
