<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use Laravel\Cashier\Cashier;
use Illuminate\Http\Response;
use Laravel\Cashier\Subscription;
use Illuminate\Support\Facades\Log;
use Illuminate\Notifications\Notifiable;
use Stripe\PaymentIntent as StripePaymentIntent;
use Laravel\Cashier\Http\Controllers\WebhookController;

class StripeWebHookController extends WebhookController {
    /**
     *
     * WEBHOOK que se encarga de eliminar la suscripción del usuario en la plataforma
     * customer.subscription.deleted
     *
     * @param array $payload
     * @return Response|\Symfony\Component\HttpFoundation\Response
     */
    public function handleCustomerSubscriptionDeleted ( array $payload ) {
        $user = $this->getUserByStripeId($payload['data']['object']['customer']);
        if ($user) {
            $user->subscriptions->filter(function ($subscription) use ($payload) {
                return $subscription->stripe_id === $payload['data']['object']['id'];
            })->each(function ($subscription) {
                $subscription->markAsCancelled();
            });
        }
        return new Response('Webhook Handled', 200);
    }

    /**
     *
     * WEBHOOK que se encarga de insertar la información de cada pago en 
     * la tabla de Payments al generarse una factura satisfactoriamente 
     * en Stripe
     *
     * @param array $payload
     * @return Response|\Symfony\Component\HttpFoundation\Response
     */
    public function handleInvoicePaymentSucceeded($payload)
    {
        try {
            $data     = $payload['data'];
            $object   = $data['object'];
            $customer = $object['customer'];
            
            $user = $this->getUserByStripeId($customer);
            if ($user) {
                $subscription = Subscription::whereStripeId($object['subscription'])->first();
                if ($subscription) {
                    $subscription->stripe_status = "active";
                    $subscription->save();
                    $plan   = \App\Models\Plan::where('plan_id', $subscription->stripe_plan)->first();        
                    if($user->country_id == 1){
                        $user->qty_of_plan_documents = $plan->qty_documents;
                        $user->save();
                    }
                        
                    $admin  = User::where('role', User::ADMIN)->first();
                    
                    $base_plan = \App\Models\Plan::where([
                        'user_id'   => $admin->id,
                        'interval'  => $plan->interval,
                        'platform'  => $plan->platform,
                    ])->first();

                    $user->payments()->create([
                        'charge_id'     => $object['charge'],
                        'plan_id'       => $plan->id,
                        'base_price'    => $base_plan->amount,
                        'sale_price'    => $object['total'] / 100,
                    ]);

                }else if($user->country_id != 1) {
                    $order = $user->orders()
                                  ->where("status", Order::PENDING)
                                  ->latest()
                                  ->first();

                    if($order){
                        $order->update([
                            'status' => Order::SUCCESS
                        ]);
                        $planId = $order->order_lines()->pluck("plan_id");
                        $user->purchased_plans()->attach($planId);
                    }
                }
                return new Response('Webhook Handled: {handleInvoicePaymentSucceeded}', 200);
            }            
            return new Response('Webhook Handled but user not found: {handleInvoicePaymentSucceeded}', 200);
        } catch (\Exception $exception) {
            Log::debug($exception->getMessage());
            return new Response('Webhook Unhandled: {handleInvoicePaymentSucceeded}', $exception->getCode());
        }
    }

    /**
     *
     * WEBHOOK que se encarga de obtener un evento al 
     * hacer la devolución de una suscripción desde Stripe
     * charge.refunded
     *
     * @param array $payload
     * @return Response|\Symfony\Component\HttpFoundation\Response
     */
    public function handleChargeRefunded ($payload)
    {
        try {
            $user = $this->getUserByStripeId($payload['data']['object']['customer']);
            if ($user) {
                if ($user->subscription('main') && $user->subscription('main')->active()) {
                    $user->subscription('main')->cancelNow();
                }
                return new Response('Webhook Handled: {handleChargeRefunded}', 200);
            }
        } catch (\Exception $exception) {
            Log::debug("Excepción Webhook {handleChargeRefunded}: " . $exception->getMessage() . ", Line: " . $exception->getLine() . ', File: ' . $exception->getFile());
            return new Response('Webhook Handled with error: {handleChargeRefunded}', 400);
        }
    }

    /**
     * WEBHOOK que se encarga de manejar el SCA notificando al usuario por correo electrónico
     *
     * @param  array  $payload
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handleInvoicePaymentActionRequired(array $payload)
    {
        try {
            $subscription = Subscription::whereStripeId($payload['data']['object']['subscription'])->first();
            if ($subscription) {
                $subscription->stripe_status = "incomplete";
                $subscription->save();

                if (is_null($notification = config('cashier.payment_notification'))) {
                    return $this->successMethod();
                }

                if ($user = $this->getUserByStripeId($payload['data']['object']['customer'])) {
                    if (in_array(Notifiable::class, class_uses_recursive($user))) {
                        $payment = new \Laravel\Cashier\Payment(StripePaymentIntent::retrieve(
                            $payload['data']['object']['payment_intent'],
                            Cashier::stripeOptions()
                        ));
                        $user->notify(new $notification($payment));
                    }
                }
            }
            return $this->successMethod();
        } catch (\Exception $exception) {
            Log::debug("Excepción Webhook {handleChargeRefunded}: " . $exception->getMessage() . ", Line: " . $exception->getLine() . ', File: ' . $exception->getFile());
            return new Response('Webhook Handled with error: {handleChargeRefunded}', 400);
        }

        
    }

    // public function handleAccountUpdated(array $payload)
    // {
    //     try {
    //         Log::info('entro en account.update');
    //         $data = $payload['data'];
    //         $object = $data['object'];
    //         $id = $object['id'];
    //         $user = User::where('account_id', $id)->first();
    //         if($user){                
    //             $enabled = $object['charges_enabled'];
    //             $user->connected_account = $enabled;
    //             $user->save();
    //             return new Response('Webhook Handled: {handleAccountUpdate}', 200);
    //         }
    //         return new Response('Webhook Handled but user not found: {handleAccountUpdate}', 200);
    //     } catch (\Exception $exception) {
    //         return new Response('Webhook Handled with error: {handleAccountUpdate}', 400);
    //     }
    // }
}
