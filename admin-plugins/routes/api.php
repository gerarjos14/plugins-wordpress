<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('create-order', 'Api\ApiController@createOrder');

Route::post('create-dte', 'Customer\CompanyController@create_dte');

Route::post('cancel-dte', 'Customer\CompanyController@cancel_dte');

Route::post('/save-log', 'Logs\LogController@store');

Route::get('/get-companies-urls', 'Customer\CompanyController@getCompaniesUrls');

Route::group(['prefix'=> 'plugin'], function(){
    Route::get('check-token/{token}', 'Api\PluginAPIController@checkToken');
    Route::get('check-services/{id_user}', 'Api\PluginAPIController@getDataPlansUser');
});