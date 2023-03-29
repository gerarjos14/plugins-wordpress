<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Token;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTokenRequest;

class TokenController extends Controller
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
        return view('admin.tokens.index');
    }

    /**
     * Data for the list
     * 
     * @return Datatable
     */
    public function datatable()
    {
        $tokens = Token::with('user')->get();

        return datatables()->of($tokens)
                ->editColumn('user_id', function ($row) {
                    return $row->user->name;
                })
                ->editColumn('blocked', function ($row) {
                    if($row->blocked){
                        return '<i class="fa fa-check text-success" style="font-size:18px;"></i>';
                    }
                    return '<i class="fa fa-times text-danger" style="font-size:18px;"></i>';
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('Y-m-d');
                })
                ->addColumn('action', function ($row) {
                    $html = '<a href="#" data-route="'.route('admin.access-token.destroy', $row->id).'" class="btn btn-sm btn-outline-danger delete-record"><i class="far fa-trash-alt mr-1"></i>Borrar</a>';
                    return $html;
                })
                ->rawColumns(['blocked', 'action'])
                ->toJson();   
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $customers = User::where('role', User::CUSTOMER)->get();
        return view('admin.tokens.create', compact('customers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTokenRequest $request)
    {
        $token = new Token;
        $token->user_id = $request->customer;
        $token->token = $this->generateCode();        
        $token->save();
        
        session()->flash('message', ['success', 'Token creado con exito']);
        return redirect()->route('admin.access-token.index');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Token $access_token)
    {
        $access_token->delete();
        session()->flash('message', ['success', 'Token eliminado con exito']);
        return back();
    }

    protected function generateCode() {
        $key = '';
        $pattern = '1234567890abcdefghijklmnopqrstuvwxyz';
        $max = strlen($pattern)-1;
        for ($i=0; $i < 20; $i++) { 
            $key .= $pattern[mt_rand(0,$max)];
        }
        $token = Token::where('token','token_'.$key)->first();
        if($token) $this->generateCode();
        return 'token_' . $key;
    }
}
