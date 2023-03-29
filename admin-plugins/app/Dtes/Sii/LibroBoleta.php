<?php


namespace App\Dtes\Sii;

class LibroBoleta extends \App\Dtes\Sii\Base\Libro
{

    public function agregar(array $detalle)
    {
        $this->detalles[] = array_merge([
            'TpoDoc' => false,
            'FolioDoc' => false,
            'Anulado' => false,
            'TpoServ' => 3,
            'FchEmiDoc' => false,
            'FchVencDoc' => false,
            'PeriodoDesde' => false,
            'PeriodoHasta' => false,
            'CdgSIISucur' => false,
            'RUTCliente' => false,
            'CodIntCli' => false,
            'MntExe' => false,
            'MntTotal' => false,
            'MntNoFact' => false,
            'MntPeriodo' => false,
            'SaldoAnt' => false,
            'VlrPagar' => false,
            'TotTicketBoleta' => false,
        ], $detalle);
    }

    public function setCaratula(array $caratula)
    {
        $this->caratula = array_merge([
            'RutEmisorLibro' => false,
            'RutEnvia' => isset($this->Firma) ? $this->Firma->getID() : false,
            'PeriodoTributario' => date('Y-m'),
            'FchResol' => false,
            'NroResol' => false,
            'TipoLibro' => 'ESPECIAL',
            'TipoEnvio' => 'TOTAL',
            'FolioNotificacion' => null,
        ], $caratula);
        $this->id = 'DTES_LIBRO_BOLETA_'.str_replace('-', '', $this->caratula['RutEmisorLibro']).'_'.str_replace('-', '', $this->caratula['PeriodoTributario']).'_'.date('U');
    }

    public function generar()
    {
        // si ya se había generado se entrega directamente
        if ($this->xml_data)
            return $this->xml_data;
        // generar XML del envío
        $xmlEnvio = (new \App\Dtes\XML())->generate([
            'LibroBoleta' => [
                '@attributes' => [
                    'xmlns' => 'http://www.sii.cl/SiiDte',
                    'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
                    'xsi:schemaLocation' => 'http://www.sii.cl/SiiDte LibroBOLETA_v10.xsd',
                    'version' => '1.0',
                ],
                'EnvioLibro' => [
                    '@attributes' => [
                        'ID' => $this->id,
                    ],
                    'Caratula' => $this->caratula,
                    'ResumenPeriodo' => $this->getResumenPeriodo(),
                    'Detalle' => $this->detalles,
                    'TmstFirma' => date('Y-m-d\TH:i:s'),
                ],
            ]
        ])->saveXML();
        // firmar XML del envío y entregar
        $this->xml_data = $this->Firma ? $this->Firma->signXML($xmlEnvio, '#'.$this->id, 'EnvioLibro', true) : $xmlEnvio;
        // PARCHE! SII usa su propio namespace para la firma en las boletas ¬¬ ¡MAL!
        $this->xml_data = str_replace('xmlns="http://www.w3.org/2000/09/xmldsig#"', 'xmlns="http://www.sii.cl/SiiDte"', $this->xml_data);
        // entregar dato del XML
        return $this->xml_data;
    }

    private function getResumenPeriodo()
    {
        $resumen = [];
        foreach ($this->detalles as &$d) {
            // si el tipo de boleta no está en el resumen se crea
            if (!isset($resumen[$d['TpoDoc']])) {
                $resumen[$d['TpoDoc']] = [
                    'TpoDoc' => $d['TpoDoc'],
                    'TotAnulado' => false,
                    'TotalesServicio' => [],
                ];
            }
            // si no existe el tipo de servicio se agrega
            if (!isset($resumen[$d['TpoDoc']]['TotalesServicio'][$d['TpoServ']])) {
                $resumen[$d['TpoDoc']]['TotalesServicio'][$d['TpoServ']] = [
                    'TpoServ' => $d['TpoServ'],
                    'PeriodoDevengado' => false,
                    'TotDoc' => false,
                    'TotMntExe' => false,
                    'TotMntNeto' => 0,
                    'TasaIVA' => 0,
                    'TotMntIVA' => 0,
                    'TotMntTotal' => false,
                    'TotMntNoFact' => false,
                    'TotMntPeriodo' => false,
                    'TotSaldoAnt' => false,
                    'TotVlrPagar' => false,
                    'TotTicket' => false,
                ];
            }
            // agregar detalle al resumen
            if (empty($d['Anulado'])) {
                // contabilizar documento
                $resumen[$d['TpoDoc']]['TotalesServicio'][$d['TpoServ']]['TotDoc'] += 1;
                // ir sumando valores
                $vals = ['MntExe'=>'TotMntExe', 'MntTotal'=>'TotMntTotal', 'MntNoFact'=>'TotMntNoFact', 'MntPeriodo'=>'TotMntPeriodo', 'SaldoAnt'=>'TotSaldoAnt', 'VlrPagar'=>'TotVlrPagar', 'TotTicketBoleta'=>'TotTicket'];
                foreach ($vals as $ori => $des) {
                    if ($d[$ori]) {
                        $resumen[$d['TpoDoc']]['TotalesServicio'][$d['TpoServ']][$des] += $d[$ori];
                    }
                }
                // determinar neto e iva
                $tasa = \App\Dtes\Sii::getIVA();
                $neto = round(($d['MntTotal'] - $d['MntExe']) / (1 + $tasa/100));
                if ($neto) {
                    $resumen[$d['TpoDoc']]['TotalesServicio'][$d['TpoServ']]['TotMntNeto'] += $neto;
                    $resumen[$d['TpoDoc']]['TotalesServicio'][$d['TpoServ']]['TasaIVA'] = $tasa;
                    // WARNING: problema por aproximaciones al calcular el NETO e IVA a partir del BRUTO
                    //$resumen[$d['TpoDoc']]['TotalesServicio'][$d['TpoServ']]['TotMntIVA'] = round($resumen[$d['TpoDoc']]['TotalesServicio'][$d['TpoServ']]['TotMntNeto'] * ($tasa/100));
                    $resumen[$d['TpoDoc']]['TotalesServicio'][$d['TpoServ']]['TotMntIVA'] = $resumen[$d['TpoDoc']]['TotalesServicio'][$d['TpoServ']]['TotMntTotal'] - $resumen[$d['TpoDoc']]['TotalesServicio'][$d['TpoServ']]['TotMntExe'] - $resumen[$d['TpoDoc']]['TotalesServicio'][$d['TpoServ']]['TotMntNeto'];
                }
            }
            // documento anulado
            else if ($d['Anulado']=='A') {
                $resumen[$d['TpoDoc']]['TotAnulado'] += 1;
            }
        }
        // armar resumen verdadero
        $ResumenPeriodo = ['TotalesPeriodo'=>[]];
        foreach ($resumen as $r) {
            $ResumenPeriodo['TotalesPeriodo'][] = $r;
        }
        // entregar resumen
        return $ResumenPeriodo;
    }

}
