<?php


namespace App\Dtes;

class Chile
{

    private static $ciudades = [
        'HUECHURABA' => 'Santiago',
        'LA CISTERNA' => 'Santiago',
        'LAS CONDES' => 'Santiago',
        'LO ESPEJO' => 'Santiago',
        'PEÑALOLÉN' => 'Santiago',
        'PUDAHUEL' => 'Santiago',
        'RECOLETA' => 'Santiago',
        'SAN MIGUEL' => 'Santiago',
        'VITACURA' => 'Santiago',
    ]; /// Ciudades de Chile según la comuna

    public static function getCiudad($comuna)
    {
        if (!$comuna)
            return false;
        $comuna = mb_strtoupper($comuna, 'UTF-8');
        return isset(self::$ciudades[$comuna]) ? self::$ciudades[$comuna] : false;
    }

}
