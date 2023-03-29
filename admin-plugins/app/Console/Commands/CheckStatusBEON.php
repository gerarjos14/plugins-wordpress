<?php

namespace App\Console\Commands;

use App\Helpers\Beon24;
use App\Helpers\EndpointBeon24;
use App\Models\Plan;
use App\Models\User;

use Illuminate\Console\Command;
use Laravel\Cashier\Subscription;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class CheckStatusBEON extends Command{

    const LIFE    = 'lifetime';
    const ANNUAL  = 'year';
    const MONTHLY = 'month';

    

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check_status:beon24';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Se revisa el estado de suscripción en el sistema de las tiendas que estén registradas en el plan de BEON24';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * 
     */
    public function handle()
    {
        // se busca la información de los planes de BEON
        $data_beon = Plan::where('platform', \App\Models\Plan::BEON)->where('plan_id', '!=', null)->get();
        $active_subscriptions = [];
        // se recorre la data de als suscripciones de beon24
        foreach ($data_beon as $data) {
            // Se filtra a aquellos que tengan cargado la data del id de suscripción en stripe
            if (!(is_null($data->plan_id))) {
                $subscriptions = $this->getDataSubscription($data->plan_id);                
                
                foreach($subscriptions as $plan_beon){
                    // se obtiene el status de suscripción
                    $status_plan = $this->getDataUnexpiredPlans($plan_beon, $data->interval);
                    $data_store  = $this->getDataStores($plan_beon->user_id);
                    if(($data_store)){
                        $vendor_id = $this->getDataVendor($data_store->email);
                        if(!($vendor_id)){
                            break;
                        }
                        $data = [
                            "vendor_id"                     => $vendor_id,//id de vendedor
                            "bill_conection_payment_check"  => $status_plan, //respuesta del pago o status
                            "bill_conection_payment_expire" => $data_store->ends_at,
                            "vendor_ecommerce_url"          => $data_store->ecomerce_url
                        ];

                        $data = json_encode($data);
                        
                        $response = Beon24::query_post(EndpointBeon24::UPDATE_VENDOR_SUBSCRIPTION, $data, false);
                          
                        if($response->success != 'success'){
                            break;
                        }
                    }                    
                   
                }                
            }
        }
    }

    public function getDataVendor(string $email){
        $endpoint = EndpointBeon24::GET_DATA_VENDOR_BY_EMAIL . $email;
        $response = Beon24::query_get($endpoint, false);
        
        if($response){
            return $response->data[0]->shopId;
        }else{
            return false;
        }
    }

    /**
     * Función getDataSubscription
     *
     * @param string $plan_id
     */
    public function getDataSubscription(string $plan_id)
    {
        return Subscription::with('user')
            ->where('stripe_plan', $plan_id)
            ->get();
    }

    /**
     * Función getDataUnexpiredPlan
     *
     * Se devuelve un valor true para aquellos planes que están activos
     * y su suscripción no venció
     * @param  mixed $data
     * @return bool
     * @author Matías
     */
    public function getDataUnexpiredPlans($data){
        if ($data->stripe_status == 'active') {
            // se analiza el intervalo de tiempo
            $date = Carbon::now();
            // Se analiza si el plan tiene o no renovación automática
            // En caso de tenerla, el campo ends_at será null
            if (is_null($data->ends_at)) {
                return true;
            } else {
                // como no tiene renovación automática, se analiza si esta la supera o no
                return ($date->diffInDays($data->ends_at) <= 0) ? false : true;
            }
        }else{
            return false;
        }
    }

    /**
     * Función getDataStores
     *
     * @param integer $user_id
     * @author Matías
     */
    public function getDataStores(int $user_id){
        return User::with('company', 'key')->where('id', $user_id)->first();    
    }
}
