<?php

namespace App\Http\Controllers\Api;

use App\Models\Plan;
use App\Models\Token;
use App\Models\User;
use Validator;
use Carbon\Carbon;
use App\Models\PlatformInvoice;
use App\Models\PlatformProduct;
use App\Models\PlatformCustomer;
use Illuminate\Http\Request;
use App\Services\AlegraService;
use Automattic\WooCommerce\Client;
use App\Http\Controllers\Controller;
use Laravel\Cashier\Subscription;

use Illuminate\Support\Facades\Log;

define('TOP_SERVICES', 5); // modificar cada vez que se agregue un nuevo servicio @author


class PluginAPIController extends Controller
{

    public function __construct()
    {
        $this->plans = Plan::PLATFORMS;
    }
    /**
     * @author Matias
     * @return bool  ||
     * @return array
     */
    public function checkToken(Request $request){
        
        $exist_token = Token::where('token', $request['token'])->first();

        // compruebo si existe el token o no
        if($exist_token){
            $user_data = User::with('country')->where('id', $exist_token->user_id)->first();

            // verifico si tiene planes pagos y cuáles son
            $plans_paids = $this->getDataPlansUser($user_data->id);

            \Log::debug($plans_paids[0]['platform']);
            
            $plugin_send  = [
                'token'        => $exist_token->token,
                'user_id'      => $user_data->id,
                'name'         => $user_data->name,
                'email'        => $user_data->email,
                'rol'          => $user_data->role,
                'id_country'   => $user_data->country->id,
                'country'      => $user_data->country->name,
                'plans_paids'  => $plans_paids,
                'plans_sistem' => $this->plans,
            ];

            return response()->json([
                'status' => 'OK',
                'data'   => $plugin_send
            ], 200);

        }else{
            return response()->json([
                'status' => 'ERROR', 
                'message' => 'El token no se encuentra en nuestro registros'
            ], 400);
        }
    }

    public function getDataPlansUser($id_user){
        // primero reviso si tiene subscripciones 
        $stripe_plans =  Subscription::where('user_id', $id_user)->get();

        // recorro array 
        $services_paids = [];
        foreach($stripe_plans as $plans){
            // consulto en plans la plataforma del mismo en base al id del plan
            $platform_plans = Plan::where('plan_id', $plans->stripe_plan)->first();
            $date = date_create($plans->ends_at);
            $plans->ends_at = date_format($date,"Y-m-d");
            
            // armo array en funcion del valor del plan

            $services_paids[] = [
                'platform' => $platform_plans->platform,
                'date_end' => date_format($date,"Y-m-d")
            ];            
        }
        

        return $services_paids;

        
    }
}