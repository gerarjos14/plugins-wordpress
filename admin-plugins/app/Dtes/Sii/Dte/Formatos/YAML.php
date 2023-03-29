<?php


namespace App\Dtes\Sii\Dte\Formatos;

class YAML
{

    public static function toArray($data)
    {
        if (!function_exists('\yaml_parse')) {
            throw new \Exception('No hay soporte para YAML en PHP');
        }
        if (empty($data)) {
            throw new \Exception('No hay datos que procesar en formato YAML');
        }
        return \yaml_parse($data);
    }

}
