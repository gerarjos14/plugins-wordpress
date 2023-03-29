<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::post(
    'stripe/webhook',
    'StripeWebHookController@handleWebhook'
);

Route::get('/', function () {
    return view('welcome');
});



Auth::routes(["register" => false]);

Route::get('voucher', 'Front\VoucherController@index');
Route::post('voucher/getDte', 'Front\VoucherController@searchDte')->name('search_dte');

Route::get('dte/{uuid}', 'Customer\DteController@download')->name('dte.download');

Route::get('register-chile/{redirect?}', 'Auth\LarsChile\RegisterController@showRegistrationForm')->name('register.lars-chile');
Route::post('register-chile', 'Auth\LarsChile\RegisterController@register');

Route::get('register-beon1/', 'Auth\BEON\RegisterController@index')->name('register_1.lars_beon24');
Route::get('register-beon/{redirect?}', 'Auth\BEON\RegisterController@showRegistrationForm')->name('register.lars_beon24');
Route::post('register-beon', 'Auth\BEON\RegisterController@register');

Route::get('login-beon24/', 'Auth\BEON\LoginController@login')->name('login_plugin.lars_beon24');


Route::group(
    ['prefix' => 'admin', 'namespace' => 'Admin', 'as' => 'admin.', 'middleware' => ['is_admin']],
    function () {
        // Dashboard
        Route::get('dashboard', 'DashboardController@index')->name('dashboard');

        // Plans
        Route::get('plans', 'PlanController@index')->name('plans.index');
        Route::get('plans/{plan}/edit', 'PlanController@edit')->name('plans.edit');
        Route::put('plans/{plan}', 'PlanController@update')->name('plans.update');
        Route::get('plans/datatable', 'PlanController@datatable')->name('plans.datatable');

        //Countries
        Route::get('countries', 'CountryController@index')->name('countries.index');
        Route::get('countries/create', 'CountryController@create')->name('countries.create');
        Route::post('countries', 'CountryController@store')->name('countries.store');
        Route::get('countries/datatable', 'CountryController@datatable')->name('countries.datatable');

        // Agencias
        Route::resource('agencies', 'AgencyController')->except([
            'show'
        ]);
        Route::get('agencies/datatable', 'AgencyController@datatable')->name('agencies.datatable');
        Route::get('agencies/{agency}/upload-plugin', 'AgencyController@uploadPlugin')->name('agencies.upload-plugin');
        Route::post('agencies/{agency}/store-plugin', 'AgencyController@storePlugin')->name('agencies.store-plugin');
        Route::delete('agencies/{agency}/delete-plugin', 'AgencyController@deletePlugin')->name('agencies.delete-plugin');

        Route::get('bank-account', 'BankAccountController@index')->name('bank-account.index');
        Route::get('bank-account/{bank_account}/edit', 'BankAccountController@edit')->name('bank-account.edit');
        Route::put('bank-account/{bank_account}', 'BankAccountController@update')->name('bank-account.update');
        Route::get('bank-account/datatable', 'BankAccountController@datatable')->name('bank-account.datatable');

        Route::get('transfer-request', 'TransferRequestController@index')->name('transfer-request.index');
        Route::get('transfer-request/datatable', 'TransferRequestController@datatable')->name('transfer-request.datatable');
        Route::get('transfer-request/view-agency/{agency}', 'TransferRequestController@viewAgency')->name('transfer-request.view-agency');
        Route::post('transfer-request/{transfer_request}/pending', 'TransferRequestController@pending')->name('transfer-request.pending');
        Route::post('transfer-request/{transfer_request}/confirmed', 'TransferRequestController@confirmed')->name('transfer-request.confirmed');

        // Clientes
        Route::resource('customers', 'CustomerController')->except([
            'show'
        ]);
        Route::get('customers/datatable', 'CustomerController@datatable')->name('customers.datatable');

        Route::get('payments', 'PaymentController@index')->name('payments.index');
        Route::get('payments/datatable', 'PaymentController@datatable')->name('payments.datatable');

        Route::resource('access-token', 'TokenController')->except([
            'show', 'edit', 'update'
        ]);
        Route::get('access-token/datatable', 'TokenController@datatable')->name('access-token.datatable');

        Route::get('user-key', 'UserKeyController@index')->name('user-key.index');
        Route::get('user-key/{user_key}/edit', 'UserKeyController@edit')->name('user-key.edit');
        Route::put('user-key/{user_key}', 'UserKeyController@update')->name('user-key.update');
        Route::get('user-key/datatable', 'UserKeyController@datatable')->name('user-key.datatable');
    }
);
Route::group(
    ['prefix' => 'agency', 'namespace' => 'Agency', 'as' => 'agency.', 'middleware' => ['is_agency']],
    function () {
        Route::get('dashboard', 'DashboardController@index')->name('dashboard');
        Route::post('store-logo', 'DashboardController@storeLogo')->name('store-logo');
        Route::get('withdraw-funds', 'TransferRequestController@create')->name('transfer-request.create');
        Route::post('withdraw-funds', 'TransferRequestController@store')->name('transfer-request.store');

        // TransferRequest
        Route::resource('customers', 'CustomerController')->except([
            'show'
        ]);
        Route::get('customers/datatable', 'CustomerController@datatable')->name('customers.datatable');

        // Planes
        Route::resource('plans', 'PlanController')->except([
            'show', 'destroy'
        ]);
        Route::get('plans/datatable', 'PlanController@datatable')->name('plans.datatable');
        Route::post('plans/activate/{plan}', 'PlanController@activate')->name('plans.activate');
        Route::post('plans/deactivate/{plan}', 'PlanController@deactivate')->name('plans.deactivate');


        Route::get('bank-account', 'BankAccountController@index')->name('bank-account.index');
        Route::post('bank-account/store', 'BankAccountController@store')->name('bank-account.store');

        /**********************************
         *   Funcionamiento en "stop"
         **********************************/
        // Stripe Connect Account
        // Route::get('stripe-connect','ConnectController@index')->name('stripe-connect.index');
        // Route::get('stripe-connect/create','ConnectController@create')->name('stripe-connect.create');
        // Route::get('stripe-connect/create-account-link','ConnectController@createAccountLink')->name('stripe-connect.create-account-link');
        // Route::post('stripe-connect/enable-account','ConnectController@enableAccount')->name('stripe-connect.enable-account');

    }
);
Route::group(
    ['namespace' => 'Customer', 'as' => 'customer.', 'middleware' => ['is_customer']],
    function () {

        /**
         * * USO COMÚN
         */
        Route::get('home', 'HomeController@index')->name('home.index');

        Route::get('billing', 'SubscriptionController@invoice')->name('billing.invoice');
        Route::get('billing/download-invoice/{invoice}', 'SubscriptionController@downloadInvoice')->name('billing.downloadInvoice');

        Route::get("credit-card/{redirect?}", 'BillingController@creditCardForm')->name("billing.credit_card_form");
        Route::post("credit-card", 'BillingController@processCreditCardForm')->name("billing.process_credit_card");
        Route::get('cancel-dte/{DteOrder}', 'DteOrderController@cancel')->name('cancel-dte');
        Route::post('cancel-dte', 'CompanyController@cancel_dte');

        // Para BeOn24

        // COLOMBIA
        Route::middleware('can:is-customer-colombia')->group(function () {
            Route::get("orders", 'DteOrderController@index');
            Route::get("order/{id}", 'DteOrderController@show')->name('order.show');


            Route::get('configure-keys', 'AccountSettingsController@configureKeys')->name('configure-keys');
            Route::post('configure-keys', 'AccountSettingsController@updateKeys')->name('configure-keys');
            // Bank account - api alegra
            Route::get('bank-account', 'PlatformBankAccountController@index')->name('bank-account.index');
            Route::post('bank-account/store', 'PlatformBankAccountController@store')->name('bank-account.store');
            Route::get('col-companies', 'CompanyController@index')->name('col-companies.index');
            Route::post('col-companies', 'CompanyController@store')->name('col-companies.store');

            Route::get('subscriptions', 'SubscriptionController@index')->name('subscriptions.index');
            Route::post('subscriptions/buy', 'SubscriptionController@buy')->name('subscriptions.buy');
            Route::post('subscriptions/cancel', 'SubscriptionController@cancel')->name('subscriptions.cancel');
            Route::post('subscriptions/resume', 'SubscriptionController@resume')->name('subscriptions.resume');


            Route::get('plugin', 'PluginController@index')->name('plugin.index');
            Route::get('plugin/download', 'PluginController@download')->name('plugin.download');

            Route::get('access-token', 'TokenController@index')->name('access-token.index');
            Route::post('access-token', 'TokenController@store')->name('access-token.store');
            Route::post('access-token/lock/{access_token}', 'TokenController@lock')->name('access-token.lock');
            Route::post('access-token/unlock/{access_token}', 'TokenController@unlock')->name('access-token.unlock');
        });

        // CHILE
        Route::middleware('can:is-customer-chile')->group(function () {

            Route::get("orders", 'DteOrderController@index');
            Route::get("order/{id}", 'DteOrderController@show')->name('order.show');


            Route::get('companies', 'CompanyController@index')->name('companies.index');
            Route::post('companies', 'CompanyController@store')->name('companies.store');

            Route::get("companies/logo/{image}", 'ImagenController@descargar');

            Route::get('/datatable-example', function () {
                return view('datatable-example');
            });

            Route::get('signatures/create', 'SignatureController@create')->name('signatures.create');
            Route::post('signatures', 'SignatureController@store')->name('signatures.store');

            Route::get('cafs/create', 'CafController@create')->name('cafs.create');
            Route::post('cafs', 'CafController@store')->name('cafs.store');


            Route::get('dte/{uuid}/xml', 'DteController@xml')->name('dte.download.xml');

            Route::get('subscriptions', 'SubscriptionController@index')->name('subscriptions.index');
            Route::post('subscriptions/buy', 'SubscriptionController@buy')->name('subscriptions.buy');
            Route::post('subscriptions/cancel', 'SubscriptionController@cancel')->name('subscriptions.cancel');
            Route::post('subscriptions/resume', 'SubscriptionController@resume')->name('subscriptions.resume');

            Route::get('plugin', 'PluginController@index')->name('plugin.index');
            Route::get('plugin/download', 'PluginController@download')->name('plugin.download');

            Route::get('access-token', 'TokenController@index')->name('access-token.index');
            Route::post('access-token', 'TokenController@store')->name('access-token.store');
            Route::post('access-token/lock/{access_token}', 'TokenController@lock')->name('access-token.lock');
            Route::post('access-token/unlock/{access_token}', 'TokenController@unlock')->name('access-token.unlock');
        });


        Route::middleware('can:is-customer-beon24')->group(function () {
            // * Suscripciones planes

            Route::get('subscriptions', 'SubscriptionController@index')->name('subscriptions.index');
            Route::post('subscriptions_beon24/buy', 'SubscriptionController@buy')->name('subscriptions_beon24.buy');
            Route::post('subscriptions_beon24/buy', 'SubscriptionController@buy')->name('subscriptions_beon24.buy');
            Route::post('subscriptions/buy', 'SubscriptionController@buy')->name('subscriptions.buy');
            Route::post('subscriptions/cancel', 'SubscriptionController@cancel')->name('subscriptions.cancel');
            Route::post('subscriptions/resume', 'SubscriptionController@resume')->name('subscriptions.resume');
            Route::get('subscriptions_beon24/success', 'BillingController@showSuccessView')->name('subscription_beon24.success');

            Route::get('col-companies', 'CompanyController@index')->name('col-companies.index');
            Route::post('col-companies', 'CompanyController@store')->name('col-companies.store');

            Route::get('cards', 'CardsController@index')->name('cards.index');
            Route::get('register-card', 'CardsController@showFormNewCard')->name('cards.show_new_card');
            Route::post('process-register-card', 'CardsController@registerCard')->name('cards.process_card');
            Route::get('active-card/{card_number}', 'CardsController@activeCard')->name('cards.active_card');
        });
    }
);
