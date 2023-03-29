<?php


namespace App\Dtes\Sii;

class Autenticacion
{

    private static function getSeed()
    {
        $xml = \App\Dtes\Sii::request('CrSeed', 'getSeed');
        if ($xml===false or (string)$xml->xpath('/SII:RESPUESTA/SII:RESP_HDR/ESTADO')[0]!=='00') {
            \App\Dtes\Log::write(
                \App\Dtes\Estado::AUTH_ERROR_SEMILLA,
                \App\Dtes\Estado::get(\App\Dtes\Estado::AUTH_ERROR_SEMILLA)
            );
            return false;
        }
        return (string)$xml->xpath('/SII:RESPUESTA/SII:RESP_BODY/SEMILLA')[0];
    }

    private static function getTokenRequest($seed, $Firma = [])
    {
        if (is_array($Firma))
            $Firma = new \App\Dtes\FirmaElectronica($Firma);
        $seedSigned = $Firma->signXML(
            (new \App\Dtes\XML())->generate([
                'getToken' => [
                    'item' => [
                        'Semilla' => $seed
                    ]
                ]
            ])->saveXML()
        );
        if (!$seedSigned) {
            \App\Dtes\Log::write(
                \App\Dtes\Estado::AUTH_ERROR_FIRMA_SOLICITUD_TOKEN,
                \App\Dtes\Estado::get(\App\Dtes\Estado::AUTH_ERROR_FIRMA_SOLICITUD_TOKEN)
            );
            return false;
        }
        return $seedSigned;
    }

    public static function getToken($Firma = [])
    {
        if (!$Firma) return false;
        $semilla = self::getSeed();
        if (!$semilla) return false;
        $requestFirmado = self::getTokenRequest($semilla, $Firma);
        if (!$requestFirmado) return false;
        $xml = \App\Dtes\Sii::request('GetTokenFromSeed', 'getToken', $requestFirmado);
        if ($xml===false or (string)$xml->xpath('/SII:RESPUESTA/SII:RESP_HDR/ESTADO')[0]!=='00') {
            \App\Dtes\Log::write(
                \App\Dtes\Estado::AUTH_ERROR_TOKEN,
                \App\Dtes\Estado::get(\App\Dtes\Estado::AUTH_ERROR_TOKEN)
            );
            return false;
        }
        return (string)$xml->xpath('/SII:RESPUESTA/SII:RESP_BODY/TOKEN')[0];
    }

}
