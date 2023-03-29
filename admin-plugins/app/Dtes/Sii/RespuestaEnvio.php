<?php


namespace App\Dtes\Sii;

class RespuestaEnvio extends \App\Dtes\Sii\Base\Documento
{

    private $respuesta_envios = [];
    private $respuesta_documentos = [];
    private $config = [
        'respuesta_envios_max' => 1000,
        'respuesta_documentos_max' => 1000,
    ]; ///< Configuración/reglas para el documento XML

    // posibles estados para la respuesta del envío
    public static $estados = [
        'envio' => [
            0 => 'Envío Recibido Conforme',
            1 => 'Envío Rechazado - Error de Schema',
            2 => 'Envío Rechazado - Error de Firma',
            3 => 'Envío Rechazado - RUT Receptor No Corresponde',
            90 => 'Envío Rechazado - Archivo Repetido',
            91 => 'Envío Rechazado - Archivo Ilegible',
            99 => 'Envío Rechazado - Otros',
        ],
        'documento' => [
            0 => 'DTE Recibido OK',
            1 => 'DTE No Recibido - Error de Firma',
            2 => 'DTE No Recibido - Error en RUT Emisor',
            3 => 'DTE No Recibido - Error en RUT Receptor',
            4 => 'DTE No Recibido - DTE Repetido',
            99 => 'DTE No Recibido - Otros',
        ],
        'respuesta_documento' => [
            0 => 'ACEPTADO OK',
            1 => 'ACEPTADO CON DISCREPANCIAS',
            2 => 'RECHAZADO',
        ],
    ];

    public function agregarRespuestaEnvio(array $datos)
    {
        if (isset($this->respuesta_documentos[0]))
            return false;
        if (isset($this->respuesta_envios[$this->config['respuesta_envios_max']-1]))
            return false;
        $this->respuesta_envios[] = array_merge([
            'NmbEnvio' => false,
            'FchRecep' => date('Y-m-d\TH:i:s'),
            'CodEnvio' => 0,
            'EnvioDTEID' => false,
            'Digest' => false,
            'RutEmisor' => false,
            'RutReceptor' => false,
            'EstadoRecepEnv' => false,
            'RecepEnvGlosa' => false,
            'NroDTE' => false,
            'RecepcionDTE' => false,
        ], $datos);
        return true;
    }

    public function agregarRespuestaDocumento(array $datos)
    {
        if (isset($this->respuesta_envios[0]))
            return false;
        if (isset($this->respuesta_documentos[$this->config['respuesta_documentos_max']-1]))
            return false;
        $this->respuesta_documentos[] = array_merge([
            'TipoDTE' => false,
            'Folio' => false,
            'FchEmis' => false,
            'RUTEmisor' => false,
            'RUTRecep' => false,
            'MntTotal' => false,
            'CodEnvio' => false,
            'EstadoDTE' => false,
            'EstadoDTEGlosa' => false,
            'CodRchDsc' => false,
        ], $datos);
        return true;
    }

    public function setCaratula(array $caratula)
    {
        $this->caratula = array_merge([
            '@attributes' => [
                'version' => '1.0'
            ],
            'RutResponde' => false,
            'RutRecibe' => false,
            'IdRespuesta' => 0,
            'NroDetalles' => isset($this->respuesta_envios[0]) ? count($this->respuesta_envios) : count($this->respuesta_documentos),
            'NmbContacto' => false,
            'FonoContacto' => false,
            'MailContacto' => false,
            'TmstFirmaResp' => date('Y-m-d\TH:i:s'),
        ], $caratula);
        if ($this->caratula['NmbContacto']) {
            $this->caratula['NmbContacto'] = mb_substr($this->caratula['NmbContacto'], 0, 40);
        }
        $this->id = 'ResultadoEnvio';
    }

    public function generar()
    {
        // si ya se había generado se entrega directamente
        if ($this->xml_data)
            return $this->xml_data;
        // si no hay respuestas para generar entregar falso
        if (!isset($this->respuesta_envios[0]) and !isset($this->respuesta_documentos[0])) {
            \App\Dtes\Log::write(
                \App\Dtes\Estado::RESPUESTAENVIO_FALTA_RESPUESTA,
                \App\Dtes\Estado::get(\App\Dtes\Estado::RESPUESTAENVIO_FALTA_RESPUESTA)
            );
            return false;
        }
        // si no hay carátula error
        if (!$this->caratula) {
            \App\Dtes\Log::write(
                \App\Dtes\Estado::RESPUESTAENVIO_FALTA_CARATULA,
                \App\Dtes\Estado::get(\App\Dtes\Estado::RESPUESTAENVIO_FALTA_CARATULA)
            );
            return false;
        }
        // crear arreglo de lo que se enviará
        $arreglo = [
            'RespuestaDTE' => [
                '@attributes' => [
                    'xmlns' => 'http://www.sii.cl/SiiDte',
                    'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
                    'xsi:schemaLocation' => 'http://www.sii.cl/SiiDte RespuestaEnvioDTE_v10.xsd',
                    'version' => '1.0',
                ],
                'Resultado' => [
                    '@attributes' => [
                        'ID' => 'DTES_ResultadoEnvio'
                    ],
                    'Caratula' => $this->caratula,
                ]
            ]
        ];
        if (isset($this->respuesta_envios[0])) {
            $arreglo['RespuestaDTE']['Resultado']['RecepcionEnvio'] = $this->respuesta_envios;
        } else {
            $arreglo['RespuestaDTE']['Resultado']['ResultadoDTE'] = $this->respuesta_documentos;
        }
        // generar XML del envío
        $xmlEnvio = (new \App\Dtes\XML())->generate($arreglo)->saveXML();
        // firmar XML del envío y entregar
        $this->xml_data = $this->Firma ? $this->Firma->signXML($xmlEnvio, '#DTES_ResultadoEnvio', 'Resultado', true) : $xmlEnvio;
        return $this->xml_data;
    }

    public function esRecepcionEnvio()
    {
        return isset($this->arreglo['RespuestaDTE']['Resultado']['RecepcionEnvio']);
    }

    public function esResultadoDTE()
    {
        return isset($this->arreglo['RespuestaDTE']['Resultado']['ResultadoDTE']);
    }

    public function getRecepciones()
    {
        if (!$this->esRecepcionEnvio())
            return false;
        // si no hay respustas se deben crear
        if (!$this->respuesta_envios) {
            // si no está creado el arrelgo con los datos error
            if (!$this->arreglo) {
                return false;
            }
            // procesa rsólo si hay recepciones
            if (isset($this->arreglo['RespuestaDTE']['Resultado']['RecepcionEnvio']['RecepcionDTE'])) {
                // crear repuestas a partir del arreglo
                $Recepciones = $this->arreglo['RespuestaDTE']['Resultado']['RecepcionEnvio']['RecepcionDTE'];
                if (!isset($Recepciones[0]))
                    $Recepciones = [$Recepciones];
                foreach ($Recepciones as $Recepcion) {
                    $this->respuesta_envios[] = $Recepcion;
                }
            }
        }
        // entregar recibos
        return $this->respuesta_envios;
    }

    public function getResultados()
    {
        if (!$this->esResultadoDTE())
            return false;
        // si no hay respustas se deben crear
        if (!$this->respuesta_documentos) {
            // si no está creado el arrelgo con los datos error
            if (!$this->arreglo) {
                return false;
            }
            // crear repuestas a partir del arreglo
            $Resultados = $this->arreglo['RespuestaDTE']['Resultado']['ResultadoDTE'];
            if (!isset($Resultados[0]))
                $Resultados = [$Resultados];
            foreach ($Resultados as $Resultado) {
                $this->respuesta_documentos[] = $Resultado;
            }
        }
        // entregar recibos
        return $this->respuesta_documentos;
    }

}
