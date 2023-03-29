<?php


namespace App\Dtes\Sii\Base;

abstract class Envio extends Documento
{

    public function enviar($retry = null, $gzip = false)
    {
        // generar XML que se enviará
        if (!$this->xml_data) {
            $this->xml_data = $this->generar();
        }
        if (!$this->xml_data) {
            \App\Dtes\Log::write(
                \App\Dtes\Estado::DOCUMENTO_ERROR_GENERAR_XML,
                \App\Dtes\Estado::get(
                    \App\Dtes\Estado::DOCUMENTO_ERROR_GENERAR_XML,
                    substr(get_class($this), strrpos(get_class($this), '\\')+1)
                )
            );
            return false;
        }
        // validar schema del documento antes de enviar
        if (!$this->schemaValidate()) {
            return false;
        }
        // si no se debe enviar no continuar
        if ($retry === 0) {
            return false;
        }
        // solicitar token
        $token = \App\Dtes\Sii\Autenticacion::getToken($this->Firma);
        if (!$token) {
            return false;
        }
        // enviar DTE
        $envia = $this->caratula['RutEnvia'];
        $emisor = !empty($this->caratula['RutEmisor']) ? $this->caratula['RutEmisor'] : $this->caratula['RutEmisorLibro'];
        $result = \App\Dtes\Sii::enviar($envia, $emisor, $this->xml_data, $token, $gzip, $retry);
        if ($result===false) {
            return false;
        }
        // retornar track id del SII
        if (!is_numeric((string)$result->TRACKID)) {
            return false;
        }
        return (int)(string)$result->TRACKID;
    }

}
