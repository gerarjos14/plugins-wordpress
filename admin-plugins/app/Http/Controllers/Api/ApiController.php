<?php

namespace App\Http\Controllers\Api;

use App\Models\Plan;
use App\Models\Token;
use Validator;
use Carbon\Carbon;
use App\Models\PlatformInvoice;
use App\Models\PlatformProduct;
use App\Models\PlatformCustomer;
use Illuminate\Http\Request;
use App\Services\AlegraService;
use Automattic\WooCommerce\Client;
use App\Http\Controllers\Controller;

class ApiController extends Controller
{

    public function createOrder(Request $request){


        // Valido la llegada de los parametros
        $rules = [
            'order_id' => 'required|numeric',
            'token'    => 'required|string',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
        {  return response()->json(['status' => 'ERROR', 'message' => $validator->errors()->first()], 200);  }
        
        // Compruebo si el token existe y no esta bloqueado
        $token = Token::where('token', $request->token)->first();
        if(!$token || $token->blocked)
        {  return response()->json(['status' => 'ERROR', 'message' => 'Token invalido!'], 200); }
        
        // Obtengo el usuario a partir del token
        $user = $token->user;

        // Obtengo plan vitalicio (Si tiene)
        $lifetime_plan = $user->purchased_plans()->first();
        if($lifetime_plan){
            $plan = $lifetime_plan;
        }else{
            // Compruebo si esta suscripto a un plan de stripe
            if(!$user->subscribed('main'))
            {  return response()->json(['status' => 'ERROR', 'message' => 'No tienes un plan!'], 200); }
            // Obtengo el plan al cual esta suscripto el cliente
            $subscription = $user->subscription('main');        
            $plan = Plan::where('plan_id', $subscription->stripe_plan)->first();
        }
        
        // Busco si ya hay una orden registrada y si existe devuelvo error
        $invoice = PlatformInvoice::where('woocommerce_order', $request->order_id)
                            ->where('user_id', $user->id)
                            ->first();
        if($invoice)
        {  return response()->json(['status' => 'ERROR', 'message' => 'La factura de esta orden ya fue generada!'], 200); }
        
        // Obtengo las claves del usuario
        $key = $user->key;       
        
        // Valido que el usuario tenga las credenciales configuradas (Woocommerce)        
        if(empty($key->wc_consumer_key) || empty($key->wc_consumer_secret) || empty($key->website))
        {  return response()->json(['status' => 'ERROR', 'message' => 'Debes configurar tus claves!'], 200); }
        
        try{
            // Instancio el cliente de woocommerce
            $woocommerce = new Client(
                $key->website,
                $key->wc_consumer_key,
                $key->wc_consumer_secret,
                [
                    'version' => 'wc/v3',
                ]
            );
            
            // Obtengo la orden de woocommerce
            $order_id = $request->order_id;
            $order = $woocommerce->get("orders/{$order_id}");

        } catch (\Exception $exception) {
            if($exception->getCode() == 401){
                return response()->json(['status' => 'ERROR', 'message' => 'Errores en las credenciales de woocommerce.']);                
            } 
            if($exception->getCode() == 404){
                return response()->json(['status' => 'ERROR', 'message' => 'Pagina web no encontrada. La URL o el número de orden son incorrectos.']);                
            } 
            return response()->json(['status' => 'ERROR', 'message' => $exception->getMessage()]);
        }
        

        if(isset($order->customer_id)){
            if($plan->platform === Plan::ALEGRA){
                if(empty($key->alegra_user) || empty($key->alegra_token))
                {  return response()->json(['status' => 'ERROR', 'message' => 'Debes configurar tus claves!'], 200); }
                
                $alegra = new AlegraService($key->alegra_user, $key->alegra_token);

                $customer = PlatformCustomer::where('woocommerce_customer', $order->customer_id)
                                ->where('user_id',$user->id)
                                ->first();
                try {
                    if($customer){
                        // Si existe el cliente se edita para la nueva facturación
                        $contact = $alegra->editarContacto($customer->customer, $order->billing); 

                    }else{
                        // Si no existe el cliente se crea
                        $contact = $alegra->crearContacto($order->billing);
                        // return response()->json($alegra->crearContacto($order->billing));

                        // Y se registra 
                        $customer = new PlatformCustomer;
                        $customer->woocommerce_customer = $order->customer_id;
                        $customer->customer             = $contact->id;
                        $customer->user_id              = $user->id;
                        $customer->save();

                    }// if($customer)

                } catch (\Exception $exception) {                     
                    if(method_exists($exception, 'getResponse')){
                        $response = $exception->getResponse();
                        if($response->getStatusCode() == 401){
                            return response()->json(['status' => 'ERROR', 'message' => 'Errores en las credenciales de alegra.']);                
                        } 
                    }
                    return response()->json(['status' => 'ERROR', 'message' => $exception->getMessage()]);               
                }

                try {
                    $line_items = array();  
                    foreach ($order->line_items as $index => $item) {
                        //Verificar que el producto no exista en la BD
                        $product = PlatformProduct::where('user_id', $user->id)
                                        ->where('woocommerce_product', $item->product_id)
                                        ->first();
                        if(!$product){
                            //Crea producto en alegra
                            $alegraProduct = $alegra->crearProducto($item);    
    
                            $product = new PlatformProduct;
    
                            $product->user_id             = $user->id;
                            $product->product             = $alegraProduct->id;
                            $product->woocommerce_product = $item->product_id;
    
                            $product->save();
                        }
    
                        $line_items[$index] = [
                            'id'        => $product->product,
                            'quantity'  => $item->quantity,
                            'price'     => $item->price,
                        ];
    
                    }
    
                    $factura = $alegra->crearFactura($customer->customer, $line_items);  
                    
                    $invoice = new PlatformInvoice;
    
                    $invoice->invoice = $factura->id;
                    $invoice->woocommerce_order = $order->id;
                    $invoice->user_id = $user->id;
                    $invoice->save();

                    $bankAccount = $user->platform_bank_account;
                    if($bankAccount){
                        $pago = $alegra->crearPago($factura, $bankAccount->account_id);
                    }
    
                } catch (\Exception $exception) {
                    $response = $exception->getResponse();
                    if($response->getStatusCode() == 401){
                        return response()->json(['status' => 'ERROR', 'message' => 'Errores en las credenciales de alegra.']);                
                    }            
                    return response()->json(['status' => 'ERROR', 'message' => $exception->getMessage()]);
                }
    
                return response()->json([
                    'status'    => 'OK',
                    'message'   => 'Factura generada con éxito.',
                    'factura'     => $factura,
                ]);    

            }// if($plan->platform)

        }// if($order)
        
        return response()->json([
            'status'    => 'OK',
            'message'   => "Hola",
        ]);
        
    }
}
