<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Beon24 extends Model{

    // Definición de endpoints de Beon24
    const CHECK_TOKEN_STORE = 'checkVendor/';

    /**
     * Función query_get
     *
     * @param string $endpoint
     * @author Matías
     */
    public static function query_get(string $endpoint, bool $is_vendor){
        $ch = curl_init();
        if($is_vendor)
            curl_setopt($ch, CURLOPT_URL,  env('APP_ENV') == 'production' ? env('BEON_URI_VENDOR') . $endpoint : env('BEON_URI_VENDOR_TEST') . $endpoint);
        else
            curl_setopt($ch, CURLOPT_URL,  env('APP_ENV') == 'production' ? env('BEON_URI') . $endpoint : env('BEON_URI_TEST') . $endpoint);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);         
        curl_setopt($ch, CURLOPT_HEADER, 0);        

        $result = curl_exec($ch);
        // Log::debug($result);
        curl_close($ch); 
        
        return json_decode($result);  
    }
    
    /**
     * Función query_post
     * 
     * Función encargada de consultas POST
     *
     * @param string $endpoint
     * @param mixed $postdata Data a enviar al endpoint -> puede ser un array o un json
     * @param bool $order - indica si es para generar o no una nueva orden
     * @author Matías
     */
    public static function query_post(string $endpoint, $postdata, bool $is_vendor){      
      
        $ch = curl_init();
        if($is_vendor)
            curl_setopt($ch, CURLOPT_URL,  env('APP_ENV') == 'production' ? env('BEON_URI_VENDOR') . $endpoint : env('BEON_URI_VENDOR_TEST') . $endpoint);
        else
            curl_setopt($ch, CURLOPT_URL,  env('APP_ENV') == 'production' ? env('BEON_URI') . $endpoint : env('BEON_URI_TEST') . $endpoint);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);        

        $result = curl_exec($ch);
        curl_close($ch); 

        return json_decode($result);  
    }
}
