<?php


namespace App\Dtes\Sii;

class ConsumoFolio extends \App\Dtes\Sii\Base\Libro
{

    private $documentos = []; ///< Documentos que se deben reportar en el consumo

    public function setDocumentos(array $documentos)
    {
        $this->documentos = $documentos;
    }

    public function agregar(array $detalle)
    {
        $this->detalles[] = $detalle;
    }

    public function setCaratula(array $caratula)
    {
        $this->caratula = array_merge([
            '@attributes' => [
                'version' => '1.0',
            ],
            'RutEmisor' => false,
            'RutEnvia' => isset($this->Firma) ? $this->Firma->getID() : false,
            'FchResol' => false,
            'NroResol' => false,
            'FchInicio' => $this->getFechaEmisionInicial(),
            'FchFinal' => $this->getFechaEmisionFinal(),
            'Correlativo' => false,
            'SecEnvio' => 1,
            'TmstFirmaEnv' => date('Y-m-d\TH:i:s'),
        ], $caratula);
        $this->id = 'DTES_CONSUMO_FOLIO_'.str_replace('-', '', $this->caratula['RutEmisor']).'_'.str_replace('-', '', $this->caratula['FchInicio']).'_'.date('U');
    }

    public function generar()
    {
        // si ya se había generado se entrega directamente
        if ($this->xml_data) {
            return $this->xml_data;
        }
        // generar XML del envío
        $xmlEnvio = (new \App\Dtes\XML())->generate([
            'ConsumoFolios' => [
                '@attributes' => [
                    'xmlns' => 'http://www.sii.cl/SiiDte',
                    'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
                    'xsi:schemaLocation' => 'http://www.sii.cl/SiiDte ConsumoFolio_v10.xsd',
                    'version' => '1.0',
                ],
                'DocumentoConsumoFolios' => [
                    '@attributes' => [
                        'ID' => $this->id,
                    ],
                    'Caratula' => $this->caratula,
                    'Resumen' => $this->getResumen(),
                ],
            ]
        ])->saveXML();
        // firmar XML del envío y entregar
        $this->xml_data = $this->Firma ? $this->Firma->signXML($xmlEnvio, '#'.$this->id, 'DocumentoConsumoFolios', true) : $xmlEnvio;
        return $this->xml_data;
    }

    public function getFechaEmisionInicial()
    {
        $fecha = '9999-12-31';
        foreach ($this->detalles as &$d) {
            if ($d['FchDoc'] < $fecha) {
                $fecha = $d['FchDoc'];
            }
        }
        return $fecha;
    }

    public function getFechaEmisionFinal()
    {
        $fecha = '0000-01-01';
        foreach ($this->detalles as &$d) {
            if ($d['FchDoc'] > $fecha) {
                $fecha = $d['FchDoc'];
            }
        }
        return $fecha;
    }

    private function getResumen()
    {
        // si hay detalles generar resumen
        $Resumen = [];
        $RangoUtilizados = [];
        //$RangoAnulados = [];
        foreach ($this->detalles as &$d) {
            // si no existe el tipo de documento se utiliza
            if (!isset($Resumen[$d['TpoDoc']])) {
                $key = array_search($d['TpoDoc'], $this->documentos);
                if ($key!==false) {
                    unset($this->documentos[$key]);
                }
                $Resumen[$d['TpoDoc']] = [
                    'TipoDocumento' => $d['TpoDoc'],
                    'MntNeto' => false,
                    'MntIva' => false,
                    'TasaIVA' => $d['TasaImp'] ? $d['TasaImp'] : false,
                    'MntExento' => false,
                    'MntTotal' => 0,
                    'FoliosEmitidos' => 0,
                    'FoliosAnulados' => 0,
                    'FoliosUtilizados' => false,
                    'RangoUtilizados' => false,
                    //'RangoAnulados' => false,
                ];
                $RangoUtilizados[$d['TpoDoc']] = [];
                //$RangoAnulados[$d['TpoDoc']] = [];
            }
            // ir agregando al resumen cada detalle
            if ($d['MntNeto']) {
                $Resumen[$d['TpoDoc']]['MntNeto'] += $d['MntNeto'];
                $Resumen[$d['TpoDoc']]['MntIva'] += $d['MntIVA'];
            }
            if ($d['MntExe']) {
                $Resumen[$d['TpoDoc']]['MntExento'] += $d['MntExe'];
            }
            $Resumen[$d['TpoDoc']]['MntTotal'] += (int)$d['MntTotal'];
            $Resumen[$d['TpoDoc']]['FoliosEmitidos']++;
            // ir guardando folios emitidos para luego crear rangos
            $RangoUtilizados[$d['TpoDoc']][] = $d['NroDoc'];
        }
        // ajustes post agregar detalles
        foreach ($Resumen as &$r) {
            // obtener folios utilizados = emitidos + anulados
            $r['FoliosUtilizados'] = $r['FoliosEmitidos'] + $r['FoliosAnulados'];
            $r['RangoUtilizados'] = $this->getRangos($RangoUtilizados[$r['TipoDocumento']]);
        }
        // completar con los resumenes que no se colocaron
        foreach ($this->documentos as $tipo) {
            $Resumen[$tipo] = [
                'TipoDocumento' => $tipo,
                'MntTotal' => 0,
                'FoliosEmitidos' => 0,
                'FoliosAnulados' => 0,
                'FoliosUtilizados' => 0,
            ];
        }
        // entregar resumen
        return $Resumen;
    }

    private function getRangos($folios)
    {
        // crear auxiliar con los folios separados por rangos
        sort($folios);
        $aux = [];
        $inicial = $folios[0];
        $i = $inicial;
        foreach($folios as $f) {
            if ($i!=$f) {
                $inicial = $f;
                $i = $inicial;
            }
            $aux[$inicial][] = $f;
            $i++;
        }
        // crear rangos
        $rangos = [];
        foreach ($aux as $folios) {
            $rangos[] = [
                'Inicial' => $folios[0],
                'Final' => $folios[count($folios)-1],
            ];
        }
        return $rangos;
    }

    public function getSecuencia()
    {
        return $this->caratula['SecEnvio'];
    }

}
