<?php


namespace App\Dtes\Sii;

class LibroGuia extends \App\Dtes\Sii\Base\Libro
{

    public function agregar(array $detalle, $normalizar = true)
    {
        if ($normalizar)
            $this->normalizarDetalle($detalle);
        $this->detalles[] = $detalle;
        return true;
    }

    private function normalizarDetalle(array &$detalle)
    {
        // agregar nodos (esto para mantener orden del XML)
        $detalle = array_merge([
            'Folio' => false,
            'Anulado' => false,
            'Operacion' => false,
            'TpoOper' => false,
            'FchDoc' => date('Y-m-d'),
            'RUTDoc' => false,
            'RznSoc' => false,
            'MntNeto' => false,
            'TasaImp' => 0,
            'IVA' => 0,
            'MntTotal' => false,
            'MntModificado' => false,
            'TpoDocRef' => false,
            'FolioDocRef' => false,
            'FchDocRef' => false,
        ], $detalle);
        // acortar razon social
        if ($detalle['RznSoc']) {
            $detalle['RznSoc'] = mb_substr($detalle['RznSoc'], 0, 50);
        }
        // calcular valores que no se hayan entregado
        if (!$detalle['IVA'] and $detalle['TasaImp'] and $detalle['MntNeto']) {
            $detalle['IVA'] = round($detalle['MntNeto'] * ($detalle['TasaImp']/100));
        }
        // calcular monto total si no se especificó
        if ($detalle['MntTotal']===false) {
            $detalle['MntTotal'] = $detalle['MntNeto'] + $detalle['IVA'];
        }
    }

    public function agregarCSV($archivo, $separador = ';')
    {
        $data = \App\Dtes\CSV::read($archivo);
        $n_data = count($data);
        $detalles = [];
        for ($i=1; $i<$n_data; $i++) {
            // detalle genérico
            $detalle = [
                'Folio' => $data[$i][0],
                'Anulado' => !empty($data[$i][1]) ? $data[$i][1] : false,
                'Operacion' => !empty($data[$i][2]) ? $data[$i][2] : false,
                'TpoOper' => !empty($data[$i][3]) ? $data[$i][3] : false,
                'FchDoc' => !empty($data[$i][4]) ? $data[$i][4] : date('Y-m-d'),
                'RUTDoc' => !empty($data[$i][5]) ? $data[$i][5] : false,
                'RznSoc' => !empty($data[$i][6]) ? mb_substr($data[$i][6], 0, 50) : false,
                'MntNeto' => !empty($data[$i][7]) ? $data[$i][7] : false,
                'TasaImp' => !empty($data[$i][8]) ? $data[$i][8] : 0,
                'IVA' => !empty($data[$i][9]) ? $data[$i][9] : 0,
                'MntTotal' => !empty($data[$i][10]) ? $data[$i][10] : false,
                'MntModificado' => !empty($data[$i][11]) ? $data[$i][11] : false,
                'TpoDocRef' => !empty($data[$i][12]) ? $data[$i][12] : false,
                'FolioDocRef' => !empty($data[$i][13]) ? $data[$i][13] : false,
                'FchDocRef' => !empty($data[$i][14]) ? $data[$i][14] : false,
            ];
            // agregar a los detalles
            $this->agregar($detalle);
        }
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
        if ($this->caratula['TipoEnvio']=='ESPECIAL')
            $this->caratula['FolioNotificacion'] = null;
        $this->id = 'DTES_LIBRO_GUIA_'.str_replace('-', '', $this->caratula['RutEmisorLibro']).'_'.str_replace('-', '', $this->caratula['PeriodoTributario']).'_'.date('U');
    }

    public function generar($incluirDetalle = true)
    {
        // si ya se había generado se entrega directamente
        if ($this->xml_data)
            return $this->xml_data;
        // generar XML del envío
        $xmlEnvio = (new \App\Dtes\XML())->generate([
            'LibroGuia' => [
                '@attributes' => [
                    'xmlns' => 'http://www.sii.cl/SiiDte',
                    'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
                    'xsi:schemaLocation' => 'http://www.sii.cl/SiiDte LibroGuia_v10.xsd',
                    'version' => '1.0',
                ],
                'EnvioLibro' => [
                    '@attributes' => [
                        'ID' => $this->id,
                    ],
                    'Caratula' => $this->caratula,
                    'ResumenPeriodo' => $this->getResumenPeriodo(),
                    'Detalle' => $incluirDetalle ? $this->detalles : false,
                    'TmstFirma' => date('Y-m-d\TH:i:s'),
                ],
            ]
        ])->saveXML();
        // firmar XML del envío y entregar
        $this->xml_data = $this->Firma ? $this->Firma->signXML($xmlEnvio, '#'.$this->id, 'EnvioLibro', true) : $xmlEnvio;
        return $this->xml_data;
    }

    private function getResumenPeriodo()
    {
        $ResumenPeriodo = [
            'TotFolAnulado' => false,
            'TotGuiaAnulada' => false,
            'TotGuiaVenta' => 0,
            'TotMntGuiaVta' => 0,
            'TotTraslado' => false,
        ];
        foreach ($this->detalles as &$d) {
            // se contabiliza si la guía está anulada
            if ($d['Anulado']==1 or $d['Anulado']==2) {
                if ($d['Anulado']==1) {
                    $ResumenPeriodo['TotFolAnulado'] = (int)$ResumenPeriodo['TotFolAnulado'] + 1;
                } else {
                    $ResumenPeriodo['TotGuiaAnulada'] = (int)$ResumenPeriodo['TotGuiaAnulada'] + 1;
                }
            }
            // si no está anulado
            else {
                // si es de venta
                if ($d['TpoOper']==1) {
                    $ResumenPeriodo['TotGuiaVenta'] = (int)$ResumenPeriodo['TotGuiaVenta'] + 1;
                    $ResumenPeriodo['TotMntGuiaVta'] = (int)$ResumenPeriodo['TotMntGuiaVta'] + $d['MntTotal'];
                }
                // si no es de venta
                else {
                    if ($ResumenPeriodo['TotTraslado']===false) {
                        $ResumenPeriodo['TotTraslado'] = [];
                    }
                    if (!isset($ResumenPeriodo['TotTraslado'][$d['TpoOper']])) {
                        $ResumenPeriodo['TotTraslado'][$d['TpoOper']] = [
                            'TpoTraslado' => $d['TpoOper'],
                            'CantGuia' => 0,
                            'MntGuia' => 0,
                        ];
                    }
                    $ResumenPeriodo['TotTraslado'][$d['TpoOper']]['CantGuia']++;
                    $ResumenPeriodo['TotTraslado'][$d['TpoOper']]['MntGuia'] += $d['MntTotal'];
                }
            }
        }
        return $ResumenPeriodo;
    }

    public function getFolioNotificacion()
    {
        return $this->toArray()['LibroGuia']['EnvioLibro']['Caratula']['FolioNotificacion'];
    }

}
