<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

use Illuminate\Support\Facades\DB;

use App\Models\User;
use App\Models\UserCards;
use App\Models\UserCardsActive;


class CardsController extends Controller
{

    const MESSAGE_CARD_REGISTERED   = 'La tarjeta ingresada ya ha sido registrado. Ingresa una nueva tarjeta';
    const MESSAGE_CARD_NO_EXIST     = 'La tarjeta seleccionada no existe.';
    const MESSAGE_SUCCESS_ACTIVE    = 'La tarjeta seleccionada ha sido activada correctamente';
    const MESSAGE_SUCCESS_DESACTIVE = 'La tarjeta seleccionada ha sido desactivada correctamente';

    public function __construct(){
        $this->middleware('is_customer');
    }

    public function index(){
        // Se busca la información de las tarjetas del usuario en cuestión
        $data_cards = UserCards::where('user_id', auth()->user()->id)->paginate(10);

        return view('customer.cards.index', [
            'user_cards' => $data_cards
        ]);
    }

    public function showFormNewCard(){
        return view("customer.beon24.credit_card_form");

    }

    /**
     * registerCard
     *
     * @param Request $request
     * @return void
     */
    public function registerCard(Request $request){
        // Se verifica primero que la tarjeta no se haya registeado antes
        if(! ( $this->verifyCardRegistration($request->all()) ) ){
            return view('customer.beon24.error_card_exist')->with('message', self::MESSAGE_CARD_REGISTERED);
        }

        // Proceso para carga de información de la tarjeta
        $data = $this->processCreateCard($request->all());
        return view('customer.beon24.success_cards')->with('data', $data);

    }

    /**
     * verifyCardRegistration
     *
     * Se verifica que la tarjeta no exista en los registros 
     * @param array $data
     * @return boolean
     */
    protected function verifyCardRegistration(array $data) : bool{

        $user_cards = UserCards::where('user_id', auth()->user()->id)->get();

        if(isset($user_cards[0])){
            foreach($user_cards as $key){
                if(base64_decode($key->number_card) == $data['card_number']){
                    return false;
                    break;
                }
            }
            return true;
        }
        return true;

    }

    protected function processCreateCard($data){
        // Revisión de información tarjeta de usuario
        $update = UserCards::where('user_id', auth()->user()->id)->first();


        // Se agrega a stripe
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        $paymentMethod = \Stripe\PaymentMethod::create([
            'type' => 'card',
            'card' => [
                'number'    => $data['card_number'],
                'exp_month' => $data['card_exp_month'],
                'exp_year'  => $data['card_exp_year'],
                'cvc'       => $data['cvc'],
            ]
        ]);


        // Carga de información en tablas
        $data_card_user = UserCards::create([
            'user_id'           => auth()->user()->id,
            'number_card'       => base64_encode($data['card_number']),
            'month'             => base64_encode($data['card_exp_month']),
            'year'              => base64_encode($data['card_exp_year']),
            'cvc'               => base64_encode($data['cvc']),
            'card_last_four'    => $this->getLastNumberCards($data['card_number']),
            'card_brand'        => $this->getBrandCard($data['card_number']),
            'payment_method_id' => $paymentMethod->id,
            'created_at'        => Carbon::now(),
            'updated_at'        => Carbon::now(),
        ]);

       

        if( isset($data['is_active']) && ($data['is_active'] == 'on') ){

            auth()->user()->updateDefaultPaymentMethod($paymentMethod->id);
            auth()->user()->save();

            if($update){
                UserCardsActive::where('user_id', auth()->user()->id)->update([
                    'card_id'    => $data_card_user->id,
                    'updated_at' => Carbon::now()
                ]);
            }else{
                UserCardsActive::create([
                    'user_id'    => auth()->user()->id,
                    'card_id'    => $data_card_user->id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }

        return $data_card_user;
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


    public function activeCard($data_card){

        $card = UserCards::where('number_card', $data_card)
                ->where('user_id', auth()->user()->id)
                ->first();

        if(!$card){
            return view('customer.beon24.error_card_no_exist')->with('message', self::MESSAGE_CARD_NO_EXIST);
        }

        $card_active = UserCardsActive::where('user_id', auth()->user()->id)->first();
        
        DB::beginTransaction();
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        auth()->user()->updateDefaultPaymentMethod($card->payment_method_id);
        auth()->user()->save();
        DB::commit();

        if($card_active){
            UserCardsActive::where('user_id', auth()->user()->id)->update([
                'card_id'    => $card->id,
                'updated_at' => Carbon::now()
            ]);
        }else{
            UserCardsActive::create([
                'user_id'    => auth()->user()->id,
                'card_id'    => $card->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
        

        return view('customer.beon24.success_active_card')
                ->with('message', self::MESSAGE_SUCCESS_ACTIVE)
                ->with('card', $card);
    }    
}
