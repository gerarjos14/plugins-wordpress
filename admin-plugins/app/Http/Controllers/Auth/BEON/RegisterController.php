<?php

namespace App\Http\Controllers\Auth\BEON;

use App\Http\Controllers\Controller;

use App\Models\User;
use App\Models\Company;

use App\Providers\RouteServiceProvider;
use App\Events\LarsChileUserRegisteredEvent;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Mail;
use App\Mail\success_buy_planbeon24;
use App\Mail\welcome_register;

class RegisterController extends Controller{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){
        $this->middleware('guest');
    }

    public function index(Request $request){

        $url_store    = $request['url_store'];
        $url_redirect = $request['url_redirect'];
        $is_wordpress = $request['is_wordpress'];
        $is_shopify   = $request['is_shopify'];

        $url_redirect = session([
            'redirect'     => true,
            'url_store'    => $url_store,
            'url_redirect' => $url_redirect,
            'is_wordpress' => $is_wordpress,
            'is_shopify'   => $is_shopify
        ]);

        return redirect($this->redirectPathRegister());
        
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm(Request $request, $redirect = null){
        if (isset($redirect)) {
            session([
                'redirect'  => true,
                'package'   => $redirect,
            ]);
        }

        return view('auth.beon24.register');
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function register(Request $request){
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));
        // Proceso para enviar email a nueva cuenta
        if( !(Mail::to($request['email'])->send(new welcome_register($request->all()))) ){

        }

        $this->guard()->login($user);

        if ($response = $this->registered($request, $user)) {
            return $response;
        }

        return $request->wantsJson()
            ? new JsonResponse([], 201)
            : redirect($this->redirectPath());
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data){
        return Validator::make($data, [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data){


        $user = User::create([
            'name'       => $data['name'],
            'email'      => $data['email'],
            'password'   => Hash::make($data['password']),
            'parent_id'  => 3, // * LARS BEON 24
            'country_id' => 2,
        ]);

        $company = Company::create([
            'user_id'          => $user->id,
            'gr'               => '-',
            'rut'              => '-',
            'name'             => $data['name'],
            'phone'            => '-',
            'is_wordpress'     => session('is_wordpress'),
            'is_shopify'       => session('is_shopify'),
            'ecomerce_url'     => session('url_store'),
            'email'            => $data['email'],
            'address'          => '-',
            'state_id'         => 1,
            'type_document'    => 'invoice',

        ]);

        return $user;
    }

    protected function registered(Request $request, $user){
        if (session()->has('redirect')) {
            session()->forget('redirect');

            return redirect()->route('customer.subscriptions.index', [
                'redirect' => session('package')
            ]);
        }
    }

    public function redirectPath(){
        return '/subscriptions';
    }

    public function redirectPathRegister(){
        return '/register-beon';
    }
}
