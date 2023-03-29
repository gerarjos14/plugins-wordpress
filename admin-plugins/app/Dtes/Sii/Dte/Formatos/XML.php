<?php


namespace App\Dtes\Sii\Dte\Formatos;

class XML
{

    public static function toArray($data)
    {
        $XML = new \App\Dtes\XML();
        if (!$XML->loadXML($data)) {
            throw new \Exception('Ocurrió un problema al cargar el XML');
        }
        $datos = $XML->toArray();
        if (!isset($datos['DTE'])) {
            throw new \Exception('El nodo raíz del string XML debe ser el tag DTE');
        }
        if (isset($datos['DTE']['Documento']))
            $dte = $datos['DTE']['Documento'];
        else if (isset($datos['DTE']['Exportaciones']))
            $dte = $datos['DTE']['Exportaciones'];
        else if (isset($datos['DTE']['Liquidacion']))
            $dte = $datos['DTE']['Liquidacion'];
        unset($dte['@attributes']);
        return $dte;
    }

}
