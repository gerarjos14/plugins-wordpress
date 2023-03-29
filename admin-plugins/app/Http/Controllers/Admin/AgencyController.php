<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Plugin;
use App\Models\Country;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Admin\StoreAgencyRequest;
use App\Http\Requests\Admin\UpdateAgencyRequest;

class AgencyController extends Controller
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
        return view('admin.agencies.index');
    }

    /**
     * Data for the list
     * 
     * @return Datatable
     */
    public function datatable()
    {
        $agencies = User::with('plugin')->where('role', User::AGENCY)->get();
        return datatables()->of($agencies)
                ->addColumn('action', function ($row) {
                    $html = '<a href="'. route('admin.agencies.edit', $row->id) .'" class="btn btn-sm btn-outline-secondary mr-1"><i class="fas fa-pencil-alt mr-1"></i>Editar</a>';
                    $html .= '<a href="#" data-route="'.route('admin.agencies.destroy', $row->id).'" class="mr-1 btn btn-sm btn-outline-danger delete-record"><i class="far fa-trash-alt mr-1"></i>Borrar</a>';
                    if($row->plugin){
                        $html .= '<a href="#" data-route="'.route('admin.agencies.delete-plugin', $row->id)
                        .'" data-toggle="tooltip" title="Eliminar Plugin" class="mr-1 btn btn-sm btn-outline-danger delete-record" ><i class="fas fa-file-archive"></i></a>';
                    }else{
                        $html .= '<a href="'. route('admin.agencies.upload-plugin', $row->id) 
                        .'"  data-toggle="tooltip" title="Subir plugin" class="btn btn-sm btn-outline-primary" ><i class="fas fa-file-archive"></i></a>';
                    }                    
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
        $countries = Country::all();
        return view('admin.agencies.create', compact("countries"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAgencyRequest $request)
    {
        $agency = User::create([
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'role'       => User::AGENCY,
            'country_id' => $request->country
        ]);
        $agency->bank_account()->create();
        session()->flash("message", ["success", __("Agencia creada con exito")]);
        return redirect()->route('admin.agencies.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $agency)
    {
        return view('admin.agencies.edit', compact('agency'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAgencyRequest $request, User $agency)
    {
        $agency->name = $request->name;
        $agency->email = $request->email;
        
        if(isset($request->password)){
            $agency->password = Hash::make($request->password);
        }
        
        $agency->save();
        
        session()->flash("message", ["success", __("Agencia actualizada con exito")]);
        return redirect()->route('admin.agencies.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $agency)
    {
        $agency->delete();
        session()->flash("message", ["success", __("Agencia eliminada con exito")]);
        return back();
    }

    public function uploadPlugin(User $agency)
    {
        return view('admin.agencies.plugin', compact('agency'));
    }

    public function storePlugin(Request $request, User $agency)
    {   
        $plugin = $agency->plugin;
        if(!$plugin){
            $request->validate([
                'plugin' => 'required|max:20000|mimes:zip,rar'
            ]); 
            
            $file = $request->file('plugin');
            $filename = $file->getClientOriginalName();
            $filename = time(). '.' . $filename;
            Storage::put($filename, File::get($file));
            $agency->plugin()->create([
                'filename' => $filename,
            ]);
            session()->flash('message', ['success','Plugin subido con exito']);
        }
      
        return redirect()->route('admin.agencies.index');

    }

    public function deletePlugin(User $agency)
    {
        $plugin = $agency->plugin;
        if($plugin){
            Storage::exists($plugin->filename) ? Storage::delete($plugin->filename) : '';
            $plugin->delete();
        }
        session()->flash('message', ['success','Plugin eliminado con exito']);
        return true;
    }


}
