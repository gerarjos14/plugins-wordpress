<?php
namespace App\Traits;

use App\Models\State;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Admin\StoreCompanyRequest;
use Image;

use Illuminate\Support\Facades\File;

trait ManageCompanies {

    public function index()
    {
        $user = Auth::user();
        $user->load("company");
        $company = $user->company;
        $states = State::where('country_id', $user->country_id)->get();
        if($user->country_id == '1'){
            return view("customer.companies.cl.create",  compact("company", "states"));

        }else{
            return view("customer.companies.co.create",  compact("company", "states"));
        }
        
    }

    public function store(StoreCompanyRequest $request)
    {
        try{



            $user = Auth::user();
            $user->load("company");
            if(empty($user->company)) {

                $company = Company::create([
                    'user_id'          => Auth::id(),
                    'gr'               => $request->gr,
                    'rut'              => $request->rut,
                    'name'             => $request->name,
                    'phone'            => $request->phone, 
                    'is_wordpress'     => $request->is_wordpress,
                    'ecomerce_url'     => $request->ecomerce_url,
                    'email'            => $request->email,
                    'address'          => $request->address,
                    'state_id'         => $request->state_id,
                    'type_document'    => $request->type_document,
                    'resolution_nro'   => $request->resolution_nro,
                    'resolution_date'  => $request->resolution_date,
                    'economy_activity' => $request->economy_activity,

                ]);
                session()->flash('message', ['success', 'Compañia ingresada con exito']);
            }else{
                $user->company()->update([
                    'gr'               => $request->gr,
                    'rut'              => $request->rut,
                    'name'             => $request->name,
                    'phone'            => $request->phone,
                    'is_wordpress'     => $request->is_wordpress,
                    'ecomerce_url'     => $request->ecomerce_url,
                    'email'            => $request->email,
                    'address'          => $request->address,
                    'state_id'         => $request->state_id,
                    'type_document'    => $request->type_document,
                    'resolution_nro'   => $request->resolution_nro,
                    'resolution_date'  => $request->resolution_date,
                    'economy_activity' => $request->economy_activity,
                ]);
                session()->flash('message', ['success', 'Compañia actualizada con exito']);
            }

            if($request->hasFile('logo')){
                //-- Convertimos el logo a PNG y lo guardamos. --//
                $file=$request->file('logo');
                $filename = time().$file->getClientOriginalName();
                $image = Image::make($file->getRealPath())->encode('png');
                $image->stream();
                $savedName = time().'.png';
                Storage::disk('local')->put('files/logos/'.$savedName, $image, 'public');
                $user->company()->update(['logo' => $savedName]);
            }

            if($user->country_id == '2'){
                return redirect()->route('customer.col-companies.index');

            }else{
                return redirect()->route('customer.companies.index');

            }

        }
        catch (\Exception $e)
        {
            session()->flash('message', ['danger', $e->getMessage()]);
            $user = Auth::user();
            if($user->country_id == '2'){
                return redirect()->route('customer.col-companies.index');
            }else{
                return redirect()->route('customer.companies.index');
            }
        }
    }
}
