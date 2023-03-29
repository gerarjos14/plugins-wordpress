<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImagenController extends Controller
{
    public function descargar($logo){


        /**
        *
        *OBTENEMOS LA DIRECCION PUBLICA
        */

        $public_path = storage_path('app');
        /**
        *
        *GENERAMOS LA URL
        */
        $url = $public_path.'/files/logos/'.$logo;// depende de root en el archivo filesystems.php.
        //verificamos si el archivo existe y lo retornamos

        //return response()->download($url);
        if (\Storage::exists('/files/logos/'.$logo))
        {
            return response()->download($url);
        }
        //si no se encuentra lanzamos un error 404.
        abort(404);
    }
}
