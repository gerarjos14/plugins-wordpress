<?php

namespace App\Http\Controllers\Logs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\Log;
use App\Models\User;
use App\Models\Company;
use App\Models\Token;

class LogController extends Controller
{
  public function index(){
      return Log::all();
  }

  public function store(Request $request){
     try{
          if(isset($request))
          {
            $rules = [
                'token'         => 'required',
                'web_url'       => 'required',
                'big_data'      => 'required',
            ];

            $customMessages = [
                'required'  => 'The :attribute field can not be blank.',
            ];


            $validator = Validator::make($request->all(), $rules, $customMessages);
            if ($validator->fails()) //Verificar si el Validator fallo
            {  return response()->json(['status' => 'error', 'color' => 'error', 'message' => $validator->errors()->first(), 'failedMessages' => $validator->getMessageBag(), 'failedRules' => $validator->failed(), 'validator->messages' => $validator->errors()], 400);  }

            // Compruebo si el token existe y no esta bloqueado
            $token = Token::where('token', $request->token)->first();
            if(!$token || $token->blocked)
            {  return response()->json(['status' => 'ERROR', 'message' => 'Token invalido!'], 200); }

            // Obtengo el usuario a partir del token
            $user = $token->user;
            $company = Company::where('user_id',$user->id)->first();

            try {
                  if(isset($company)){
                      $log = Log::create([
                          'user_id'           =>  $user->id,
                          'company_id'        =>  $company->id,
                          'visitor_id'        =>  isset( $request->visitor_id)    ?  $request->visitor_id : null ,
                          'big_data'          =>  $request->big_data,
                          'web_url'           =>  $request->web_url,

                      ]);
                  }
                return response( )->json(['status' => 'OK', 'color' => 'success', 'message' => 'Log creado exitosamente.'], 200);
            } catch (\Throwable $throwable) {
                return response()->json(['status' => 'error', 'color' => 'error', 'message' => 'Ha ocurrido un error durante el proceso de guardado.', 'message-error' => $throwable->getMessage(), 'line-error' => $throwable->getLine(), 'error-data' => $throwable], 500);
            }
          }
      }
      catch (\Throwable $throwable) {
          //DB::rollback();
          return response()->json(['status' => 'error', 'message' => $throwable->getMessage(), 'line' => $throwable->getLine(), 'error' => $throwable]);
      }
  }


}
