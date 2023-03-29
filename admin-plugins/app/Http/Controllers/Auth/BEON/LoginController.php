<?php

namespace App\Http\Controllers\Auth\BEON;

use App\Helpers\Beon24;
use App\Helpers\EndpointBeon24;
use App\Http\Controllers\Controller;

use App\Models\User;
use App\Models\Company;

use App\Providers\RouteServiceProvider;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Mail;

/**
 * Clase encargada de proceso de login desde los plugins al sistema.
 * @author Matías
 * 
 */
class LoginController extends Controller{

    use RegistersUsers;

    const MESSAGE_NULL        = 'Verifica que todos los datos de tu tienda se encuentren cargados en el formulario de configuración del plugin';
    const MESSAGE_ERROR_TOKEN = 'Los datos ingresados no coinciden con los registros de BeOn24. Revisalos y vuelve a intentar más tarde.';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){
        $this->middleware('guest');
    }

    /**
     * Login
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     * @author Matías
     */
    public function login(Request $request){
        $data_store       = $this->getDataRequest($request['data_store']);        
        # se convierte el objeto tienda a array
        $array_data_store = (json_decode(json_encode($data_store), true));

        # validación de data
        if(in_array(null, $array_data_store, true)){
            // Como hay error de elemento nulo en la información proveniente de worpdress o shoppify
            // se envia a vista correspondiente
            return view('customer.beon24.error')
                    ->with('type_error', 'null')
                    ->with('message', self::MESSAGE_NULL);
        }else{
            // Análisis de token de tienda según su valor de Wordpress o Shopify
            if( $this->getDataTokenStore($array_data_store) ){
                // Proceso para buscar usuario en base al email
                $user = User::where('email', $array_data_store['email_store'])->first();
                $this->guard()->login($user);                
                // Redirección
                redirect($this->redirectPath());
            }else{
                return view('customer.beon24.error')
                        ->with('type_error', 'null')
                        ->with('message', self::MESSAGE_ERROR_TOKEN);
            }       
        }                                                                                               
    }

    /**
     * getDataRequest
     *
     * @param mixed $data_store
     * @author Matías
     */
    protected function getDataRequest($data_store){
        return json_decode(base64_decode(urldecode($data_store))); 
    }

    /**
     * getDataTokenStore
     *  
     * Se obtiene la información del token de la tienda en función de los
     * datos enviados desde el plugin
     * @author Matías
     * @param array $store
     * @return boolean
     */
    protected function getDataTokenStore(array $store) : bool{
        $type_store = $store['is_wordpress'] ? 'wordpress' : 'shopify';
        $endpoint   = EndpointBeon24::CHECK_TOKEN_STORE . $type_store . '/' . $store['email_store'] . '/' . $store['token'];
        $result     = Beon24::query_get($endpoint, true);
        
        return $result ? ( $result->success ? true : false) : false;
    }
    
    public function redirectPath(){
        return '/subscriptions';
    }
}
