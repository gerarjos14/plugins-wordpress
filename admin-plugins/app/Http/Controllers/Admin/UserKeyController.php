<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\UserKey;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateUserKeyRequest;

class UserKeyController extends Controller
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
        return view('admin.user_key.index');
    }

    /**
     * Data for the list
     * 
     * @return Datatable
     */

    public function datatable()
    {
        $customers = User::where('role', User::CUSTOMER)
                        ->pluck('id');

        $keys = UserKey::with('user')->whereIn('user_id', $customers)->get();
        
        return datatables()->of($keys)
            ->addColumn('user', function ($row) {
                return $row->user->name;
            })
            ->addColumn('action', function ($row) {
                $html = '<a href="'. route('admin.user-key.edit', $row->id) .'" class="btn btn-sm btn-outline-secondary mr-1"><i class="fas fa-pencil-alt mr-1"></i>Editar</a>';
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
    public function edit(UserKey $user_key)
    {
        return view('admin.user_key.edit', compact('user_key'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserKeyRequest $request, UserKey $user_key)
    {
        $user_key->website = $request->website;
        $user_key->alegra_user = $request->alegra_user ? $request->alegra_user : $user_key->alegra_user ;
        $user_key->alegra_token = $request->alegra_token ? $request->alegra_token : $user_key->alegra_token ;
        $user_key->wc_consumer_key = $request->wc_consumer_key ? $request->wc_consumer_key : $user_key->wc_consumer_key ;
        $user_key->wc_consumer_secret = $request->wc_consumer_secret ? $request->wc_consumer_secret : $user_key->wc_consumer_secret ;
        $user_key->save();
        session()->flash("message", ["success", 'Claves actualizadas con exito']);
        return redirect()->route('admin.user-key.index');
    }
}
