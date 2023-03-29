<?php


namespace App\Dtes\Sii;

class EnvioDte extends \App\Dtes\Sii\Base\Envio
{

    private $dtes = []; ///< Objetos con los DTE que se enviarán
    private $config = [ // 0: DTE, 1: boleta
        'SubTotDTE_max' => [20, 2], ///< máxima cantidad de tipos de documentos en el envío
        'DTE_max' => [2000, 1000], ///< máxima cantidad de DTE en un envío
        'tipos' => ['EnvioDTE', 'EnvioBOLETA'], ///< Tag para el envío, según si son Boletas o no
        'schemas' => ['EnvioDTE_v10', 'EnvioBOLETA_v11'], ///< Schema (XSD) que se deberá usar para validar según si son boletas o no
    ]; ///< Configuración/reglas para el documento XML
    private $tipo = null; ///< =0 DTE, =1 boleta

    public function agregar(Dte $DTE)
    {
        // determinar el tipo del envío (DTE o boleta)
        if ($this->tipo === null) {
            $this->tipo = (int)$DTE->esBoleta();
        }
        // validar que el tipo de documento sea del tipo que se espera
        else if ((int)$DTE->esBoleta() != $this->tipo) {
            return false;
        }
        //
        if (isset($this->dtes[$this->config['DTE_max'][$this->tipo]-1])) {
            \App\Dtes\Log::write(
                \App\Dtes\Estado::ENVIODTE_DTE_MAX,
                \App\Dtes\Estado::get(\App\Dtes\Estado::ENVIODTE_DTE_MAX, $this->config['DTE_max'][$this->tipo])
            );
            return false;
        }
        $this->dtes[] = $DTE;
        return true;
    }

    public function setCaratula(array $caratula)
    {
        // si no hay DTEs para generar entregar falso
        if (!isset($this->dtes[0])) {
            \App\Dtes\Log::write(
                \App\Dtes\Estado::ENVIODTE_FALTA_DTE,
                \App\Dtes\Estado::get(\App\Dtes\Estado::ENVIODTE_FALTA_DTE)
            );
            return false;
        }
        // si se agregaron demasiados DTE error
        $SubTotDTE = $this->getSubTotDTE();
        if (isset($SubTotDTE[$this->config['SubTotDTE_max'][$this->tipo]])) {
            \App\Dtes\Log::write(
                \App\Dtes\Estado::ENVIODTE_TIPO_DTE_MAX,
                \App\Dtes\Estado::get(\App\Dtes\Estado::ENVIODTE_TIPO_DTE_MAX, $this->config['SubTotDTE_max'][$this->tipo])
            );
            return false;
        }
        // generar caratula
        $this->caratula = array_merge([
            '@attributes' => [
                'version' => '1.0'
            ],
            'RutEmisor' => $this->dtes[0]->getEmisor(),
            'RutEnvia' => isset($this->Firma) ? $this->Firma->getID() : false,
            'RutReceptor' => $this->dtes[0]->getReceptor(),
            'FchResol' => '',
            'NroResol' => '',
            'TmstFirmaEnv' => date('Y-m-d\TH:i:s'),
            'SubTotDTE' => $SubTotDTE,
        ], $caratula);
        return true;
    }

    public function enviar($retry = null, $gzip = false)
    {
        // si es boleta no se envía al SII
        if ($this->tipo) {
            return false;
        }
        // enviar al SII
        return parent::enviar($retry, $gzip);
    }

    public function generar()
    {
        // si ya se había generado se entrega directamente
        if ($this->xml_data)
            return $this->xml_data;
        // si no hay DTEs para generar entregar falso
        if (!isset($this->dtes[0])) {
            \App\Dtes\Log::write(
                \App\Dtes\Estado::ENVIODTE_FALTA_DTE,
                \App\Dtes\Estado::get(\App\Dtes\Estado::ENVIODTE_FALTA_DTE)
            );
            return false;
        }
        // genear XML del envío
        $xmlEnvio = (new \App\Dtes\XML())->generate([
            $this->config['tipos'][$this->tipo] => [
                '@attributes' => [
                    'xmlns' => 'http://www.sii.cl/SiiDte',
                    'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
                    'xsi:schemaLocation' => 'http://www.sii.cl/SiiDte '.$this->config['schemas'][$this->tipo].'.xsd',
                    'version' => '1.0'
                ],
                'SetDTE' => [
                    '@attributes' => [
                        'ID' => 'DTES_SetDoc'
                    ],
                    'Caratula' => $this->caratula,
                    'DTE' => null,
                ]
            ]
        ])->saveXML();
        // generar XML de los DTE que se deberán incorporar
        $DTEs = [];
        foreach ($this->dtes as &$DTE) {
            $DTEs[] = trim(str_replace(['<?xml version="1.0" encoding="ISO-8859-1"?>', '<?xml version="1.0"?>'], '', $DTE->saveXML()));
        }
        // firmar XML del envío y entregar
        $xml = str_replace('<DTE/>', implode("\n", $DTEs), $xmlEnvio);
        $this->xml_data = $this->Firma ? $this->Firma->signXML($xml, '#DTES_SetDoc', 'SetDTE', true) : $xml;
        return $this->xml_data;
    }

    private function getSubTotDTE()
    {
        $SubTotDTE = [];
        $subtotales = [];
        foreach ($this->dtes as &$DTE) {
            if (!isset($subtotales[$DTE->getTipo()]))
                $subtotales[$DTE->getTipo()] = 0;
            $subtotales[$DTE->getTipo()]++;
        }
        foreach ($subtotales as $tipo => $subtotal) {
            $SubTotDTE[] = [
                'TpoDTE' => $tipo,
                'NroDTE' => $subtotal,
            ];
        }
        return $SubTotDTE;
    }

    public function loadXML($xml_data)
    {
        if (!parent::loadXML($xml_data)) {
            return false;
        }
        $tagName = $this->xml->documentElement->tagName;
        if ($tagName=='DTE' or $tagName=='SetDTE') {
            // obtener documentos
            if ($tagName=='DTE') {
                $dtes = [$xml_data];
            } else {
                $dtes = [];
                $aux = $this->xml->documentElement->getElementsByTagName('DTE');
                foreach ($aux as $a) {
                    $dtes[] = $a;
                }
                unset($aux);
            }
            unset($xml_data);
            // reiniciar datos leídos
            $this->xml = null;
            $this->xml_data = null;
            $this->arreglo = null;
            // agregar documentos
            foreach ($dtes as $dte) {
                $Dte = new Dte(is_string($dte) ? $dte : $dte->C14N(), false);
                $this->agregar($Dte);
            }
            // crear carátula falta
            $this->setCaratula([
                'RutEnvia' => $Dte->getEmisor(),
                'RutReceptor' => $Dte->getReceptor(),
                'FchResol' => date('Y-m-d'),
                'NroResol' => ($Dte->getCertificacion()?'0':'').'9999',
            ]);
            // cargar nuevo XML con datos completos
            if (!parent::loadXML($this->generar())) {
                return false;
            }
            $tagName = $this->xml->documentElement->tagName;
        }
        if ($tagName=='EnvioDTE') {
            $this->tipo = 0;
            return $this->xml;
        }
        if ($tagName=='EnvioBOLETA') {
            $this->tipo = 1;
            return $this->xml;
        }
        return false;
    }

    public function getCaratula()
    {
        return isset($this->arreglo[$this->config['tipos'][$this->tipo]]['SetDTE']['Caratula']) ? $this->arreglo[$this->config['tipos'][$this->tipo]]['SetDTE']['Caratula'] : false;
    }

    public function getID()
    {
        return isset($this->arreglo[$this->config['tipos'][$this->tipo]]['SetDTE']['@attributes']['ID']) ? $this->arreglo[$this->config['tipos'][$this->tipo]]['SetDTE']['@attributes']['ID'] : false;
    }

    public function getDigest()
    {
        return isset($this->arreglo[$this->config['tipos'][$this->tipo]]['Signature']['SignedInfo']['Reference']['DigestValue']) ? $this->arreglo[$this->config['tipos'][$this->tipo]]['Signature']['SignedInfo']['Reference']['DigestValue'] : false;
    }

    public function getEmisor()
    {
        $Caratula = $this->getCaratula();
        return $Caratula ? $Caratula['RutEmisor'] : false;
    }

    public function getReceptor()
    {
        $Caratula = $this->getCaratula();
        return $Caratula ? $Caratula['RutReceptor'] : false;
    }

    public function getFechaEmisionInicial()
    {
        $fecha = '9999-12-31';
        foreach ($this->getDocumentos() as $Dte) {
            if ($Dte->getFechaEmision() < $fecha)
                $fecha = $Dte->getFechaEmision();
        }
        return $fecha;
    }

    public function getFechaEmisionFinal()
    {
        $fecha = '0000-01-01';
        foreach ($this->getDocumentos() as $Dte) {
            if ($Dte->getFechaEmision() > $fecha)
                $fecha = $Dte->getFechaEmision();
        }
        return $fecha;
    }

    public function getDocumentos($c14n = true)
    {
        // si no hay documentos se deben crear
        if (!$this->dtes) {
            // si no hay XML no se pueden crear los documentos
            if (!$this->xml) {
                \App\Dtes\Log::write(
                    \App\Dtes\Estado::ENVIODTE_GETDOCUMENTOS_FALTA_XML,
                    \App\Dtes\Estado::get(\App\Dtes\Estado::ENVIODTE_GETDOCUMENTOS_FALTA_XML)
                );
                return false;
            }
            // crear documentos a partir del XML
            $DTEs = $this->xml->getElementsByTagName('DTE');
            foreach ($DTEs as $nodo_dte) {
                $xml = $c14n ? $nodo_dte->C14N() : $this->xml->saveXML($nodo_dte);
                $this->dtes[] = new Dte($xml, false); // cargar DTE sin normalizar
            }
        }
        return $this->dtes;
    }

    public function getDocumento($emisor, $dte, $folio)
    {
        $emisor = str_replace('.', '', $emisor);
        // si no hay XML no se pueden crear los documentos
        if (!$this->xml) {
            \App\Dtes\Log::write(
                \App\Dtes\Estado::ENVIODTE_GETDOCUMENTOS_FALTA_XML,
                \App\Dtes\Estado::get(\App\Dtes\Estado::ENVIODTE_GETDOCUMENTOS_FALTA_XML)
            );
            return false;
        }
        // buscar documento
        $DTEs = $this->xml->getElementsByTagName('DTE');
        foreach ($DTEs as $nodo_dte) {
            $e = $nodo_dte->getElementsByTagName('RUTEmisor')->item(0)->nodeValue;
            if (is_numeric($emisor))
                $e = substr($e, 0, -2);
            $d = (int)$nodo_dte->getElementsByTagName('TipoDTE')->item(0)->nodeValue;
            $f = (int)$nodo_dte->getElementsByTagName('Folio')->item(0)->nodeValue;
            if ($folio == $f and $dte == $d and $emisor == $e) {
                return new Dte($nodo_dte->C14N(), false); // cargar DTE sin normalizar
            }
        }
        return false;
    }

    public function esBoleta()
    {
        return $this->tipo!==null ? (bool)$this->tipo : null;
    }

    public function getEstadoValidacion(array $datos = null)
    {
        if (!$this->schemaValidate()) {
            return 1;
        }
        if (!$this->checkFirma()) {
            return 2;
        }
        if ($datos and $this->getReceptor()!=$datos['RutReceptor']) {
            return 3;
        }
        return 0;
    }

    public function checkFirma()
    {
        if (!$this->xml) {
            return null;
        }
        // listado de firmas del XML
        $Signatures = $this->xml->documentElement->getElementsByTagName('Signature');
        // verificar firma de SetDTE
        $SetDTE = $this->xml->documentElement->getElementsByTagName('SetDTE')->item(0)->C14N();
        $SignedInfo = $Signatures->item($Signatures->length-1)->getElementsByTagName('SignedInfo')->item(0);
        $DigestValue = $Signatures->item($Signatures->length-1)->getElementsByTagName('DigestValue')->item(0)->nodeValue;
        $SignatureValue = trim(str_replace(["\n", ' ', "\t"], '', $Signatures->item($Signatures->length-1)->getElementsByTagName('SignatureValue')->item(0)->nodeValue));
        $X509Certificate = trim(str_replace(["\n", ' ', "\t"], '', $Signatures->item($Signatures->length-1)->getElementsByTagName('X509Certificate')->item(0)->nodeValue));
        $X509Certificate = '-----BEGIN CERTIFICATE-----'."\n".wordwrap($X509Certificate, 64, "\n", true)."\n".'-----END CERTIFICATE-----';
        $valid = openssl_verify($SignedInfo->C14N(), base64_decode($SignatureValue), $X509Certificate) === 1 ? true : false;
        return $valid and $DigestValue===base64_encode(sha1($SetDTE, true));
    }

}
