<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use App\Http\Controllers\Customer\SubscriptionController;
use App\Mail\success_buy_planbeon24;
use App\Mail\success_store_card;
use App\Models\Plan;
use App\Models\User;
use App\Models\UserCards;
use App\Models\UserCardsActive;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class BillingController extends Controller{

    const MESSAGE_SUCCESS_UPDATE        = 'Tarjeta actualizada correctamente.';
    const MESSAGE_BEON_NEXT_PASS        = 'Vayamos al siguiente paso. ';
    const MESSAGE_LINK_SUSCRIPTION_BEON = 'Suscríbite a un plan BEON24';
    const URL_ADMIN_BEON24_WORDPRESS    = 'wp-admin/admin.php?page=beon24';

    protected $suscriptions;

    public function __construct(){
        $this->middleware('is_customer');
        $this->suscriptions = new SubscriptionController();
    }

    public function creditCardForm($redirect = null){
        if (isset($redirect)) {
            session([
                'redirect' => true,
                'package' => $redirect
            ]);
        }
        return view("customer.credit_card_form");
    }

    public function processCreditCardForm(){

        $this->validate(request(), [
            'card_number'    => 'required',
            'card_exp_year'  => 'required',
            'card_exp_month' => 'required',
            'cvc'            => 'required'
        ]);

        try {
            DB::beginTransaction();
            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

            if (!auth()->user()->hasPaymentMethod()) {
                auth()->user()->createAsStripeCustomer();
            }

            // se analiza si el usuario posee tarjetas actualmente
            if($this->checkUserCards()){
                $this->updateDataCards();               
            }else{
                $this->createDataCards();
            }                    

            // Se comprueba si el usuario ya seleccionó un plan de BEON
            if (session()->has('id_package_beon24')) {
                // Envio de email a usuario
                Mail::to(auth()->user()->email)->send(new success_store_card(auth()->user()));

                if ($this->suscriptions->buy()) {
                    // se redirecciona a la vista ok plan
                    return redirect(route('customer.subscription_beon24.success'));
                }
            } else {
                session()->flash('message', ['success', self::MESSAGE_SUCCESS_UPDATE]);
                return back();
            }

            if (session()->has('redirect')) {
                session()->forget('redirect');
                /**
                 * Redirigimos al usuario directamente a generar una suscripcion
                 * segun lo seleccionado desde la pagina
                 */
                $plan_selected = \App\Models\Plan::where('name', session('package'))->first();

                //OBTENEMOS EL ID DEL PLAN
                if (!$plan_selected) {
                    session()->forget('package');
                }
                return redirect()->route('customer.subscriptions.index');
            }
            session()->flash('message', ['success', self::MESSAGE_SUCCESS_UPDATE]);
            return back();
        } catch (\Exception $exception) {
            DB::rollBack();
            session()->flash('message', ['danger', $exception->getMessage()]);
            return back();
        }
    }

    /**
     * checkUserCards
     *
     * @return boolean
     * @author Matías
     */
    public function checkUserCards() : bool{
        $info_cards_users = UserCards::where('user_id', auth()->user()->id)->first();
        

        return $info_cards_users ? true : false;
    }

    public function createDataCards(){

        // Carga en stripe
        $paymentMethod = \Stripe\PaymentMethod::create([
            'type' => 'card',
            'card' => [
                'number'    => request('card_number'),
                'exp_month' => request('card_exp_month'),
                'exp_year'  => request('card_exp_year'),
                'cvc'       => request('cvc'),
            ]
        ]);
        // Carga de información en tablas
        $data_card_user = UserCards::create([
            'user_id'           => auth()->user()->id,
            'number_card'       => base64_encode(request('card_number')),
            'month'             => base64_encode(request('card_exp_month')),
            'year'              => base64_encode(request('card_exp_year')),
            'cvc'               => base64_encode(request('cvc')),
            'card_last_four'    => $this->getLastNumberCards(request('card_number')),
            'card_brand'        => $this->getBrandCard(request('card_number')),
            'payment_method_id' => $paymentMethod->id,
            'created_at'        => Carbon::now(),
            'updated_at'        => Carbon::now(),
        ]);

        // Carga en tabla pivote
        UserCardsActive::create([
            'user_id'    => auth()->user()->id,
            'card_id'    => $data_card_user->id,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        
        auth()->user()->updateDefaultPaymentMethod($paymentMethod->id);
        auth()->user()->save();
        DB::commit();
    }

    /**
     * getLastNumberCards
     *
     * @param integer $number_card
     * @return string
     * @author Matías
     */
    protected function getLastNumberCards(int $number_card) : string{
        $number_card = strval($number_card);
        return substr($number_card, -4);
    }

    /**
     * getBrandCard
     * 
     * Función para obtener la empresa de la tarjeta
     *
     * @param integer $number_card
     * @return string 
     * @author Matías
     */
    protected function getBrandCard(int $number_card){

        $number_card  = strval($number_card);
        $first_number = substr($number_card, 0, 1);

        switch($first_number){
            case '4':
                return 'VISA';
            break;

            case '5':
            case '2':            
                return 'Mastercard';
            break;
            
            case '3':
                $number_card   = strval($number_card);
                $first_numbers = substr($number_card, 0, 2);

                return $first_numbers == '37' ? 'American Express' : 'Diners Club';        
            break;

            case '6':
                return 'Discover';
        }
    }


    public function updateDataCards(){
        // Carga de información en tablas
        $data_card_user = UserCards::create([
            'user_id'        => auth()->user()->id,
            'number_card'    => base64_encode(request('card_number')),
            'month'          => base64_encode(request('card_exp_month')),
            'year'           => base64_encode(request('card_exp_year')),
            'cvc'            => base64_encode(request('cvc')),
            'card_last_four' => $this->getLastNumberCards(request('card_number')),
            'card_brand'     => $this->getBrandCard(request('card_number')),
            'created_at'     => Carbon::now(),
            'updated_at'     => Carbon::now(),
        ]);

        // Carga en tabla pivote
        UserCardsActive::where('user_id', auth()->user()->id)->update([
            'card_id'    => $data_card_user->id,
            'updated_at' => Carbon::now()
        ]);

        // Carga en stripe
        $paymentMethod = \Stripe\PaymentMethod::update([
            'type' => 'card',
            'card' => [
                'number'    => request('card_number'),
                'exp_month' => request('card_exp_month'),
                'exp_year'  => request('card_exp_year'),
                'cvc'       => request('cvc'),
            ]
        ]);
        auth()->user()->updateDefaultPaymentMethod($paymentMethod->id);
        auth()->user()->save();
        DB::commit();
    }

    public function showSuccessView(){
        // Se eliminan los valores de session
        $this->forget_sessions('url_redirect');
        $this->forget_sessions('url_store');
        $this->forget_sessions('id_package_beon24');

        // En función del id, se busca la información del usuario
        $user        = User::with('company')->where('id', auth()->user()->id)->first();
        $currentPlan = $user->subscription('main');
        // información del plan elegido
        if ($currentPlan) {
            if ($currentPlan->active()) {
                $plan = \App\Models\Plan::where('plan_id', $currentPlan->stripe_plan)->first();
            }
        }

        // Envío de email informando ok compra de plan
        Mail::to($user->email)->send(new success_buy_planbeon24($user, $plan));

        $data_show = [
            'url_redirect' => ($user->company->is_wordpress == 1) ?  $user->company->ecomerce_url . self::URL_ADMIN_BEON24_WORDPRESS  : $user->company->ecomerce_url,
            'is_wordpress' => $user->company->is_wordpress,
            'plan'         => $plan,
            'date_buy'     => $currentPlan->created_at
        ];

        auth()->logout();

        return view('customer.beon24.success')->with('data', $data_show);
    }

    public function forget_sessions(string $value){
        if (session()->has($value)) {
            session()->forget($value);
        }
    }
}
