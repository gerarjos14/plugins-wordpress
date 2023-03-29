<?php


namespace App\Dtes\Sii\Factoring;

class Cesion
{

    private $Encabezado; ///< Encabezado del DTE que se está cediendo
    private $datos; ///< Datos del XML de cesión
    private $declaracion = 'Yo, {usuario_nombre}, RUN {usuario_run}, representando a {emisor_razon_social}, RUT {emisor_rut}, declaro que he puesto a disposición del cesionario {cesionario_razon_social}, RUT {cesionario_rut}, el documento donde constan los recibos de la recepción de mercaderías entregadas o servicios prestados, entregados por parte del deudor de la factura {receptor_razon_social}, RUT {receptor_rut}, de acuerdo a lo establecido en la Ley N° 19.983'; ///< Declaración estándar en caso que no sea indicada al momento de crear al cedente
    private $secuencia = 1;

    public function __construct(DteCedido $DTECedido = null, $Seq = 1)
    {
        if (!empty($DTECedido)) {
            $this->secuencia = $Seq;
            $this->Encabezado = $DTECedido->getDTE()->getDatos()['Encabezado'];
            $this->datos = [
                'Cesion' => [
                    '@attributes' => [
                        'xmlns' => 'http://www.sii.cl/SiiDte',
                        'version' => '1.0'
                    ],
                    'DocumentoCesion' => [
                        '@attributes' => [
                            'ID' => $this->getID(),
                        ],
                        'SeqCesion' => $Seq,
                        'IdDTE' => [
                            'TipoDTE' => $this->Encabezado['IdDoc']['TipoDTE'],
                            'RUTEmisor' => $this->Encabezado['Emisor']['RUTEmisor'],
                            'RUTReceptor' => $this->Encabezado['Receptor']['RUTRecep'],
                            'Folio' => $this->Encabezado['IdDoc']['Folio'],
                            'FchEmis' => $this->Encabezado['IdDoc']['FchEmis'],
                            'MntTotal' => $this->Encabezado['Totales']['MntTotal'],
                        ],
                        'Cedente' => false,
                        'Cesionario' => false,
                        'MontoCesion' => $this->Encabezado['Totales']['MntTotal'],
                        'UltimoVencimiento' =>
                            isset($this->Encabezado['IdDoc']['MntPagos']['FchPago'])
                            ? $this->Encabezado['IdDoc']['MntPagos']['FchPago']
                            : $this->Encabezado['IdDoc']['FchEmis'],
                        'OtrasCondiciones' => false,
                        'eMailDeudor' => false,
                        'TmstCesion' => date('Y-m-d\TH:i:s')
                    ]
                ]
            ];
        }
    }

    protected function getID()
    {
        return 'DTES_Cesion_'.$this->secuencia;
    }

    public function setDeclaracion($declaracion)
    {
        $this->declaracion = $declaracion;
    }

    public function setCedente(array $cedente = [])
    {
        $this->datos['Cesion']['DocumentoCesion']['Cedente'] = \App\Dtes\Arreglo::mergeRecursiveDistinct([
            'RUT' => $this->Encabezado['Emisor']['RUTEmisor'],
            'RazonSocial' => $this->Encabezado['Emisor']['RznSoc'],
            'Direccion' => $this->Encabezado['Emisor']['DirOrigen'].', '.$this->Encabezado['Emisor']['CmnaOrigen'],
            'eMail' => !empty($this->Encabezado['Emisor']['CorreoEmisor']) ? $this->Encabezado['Emisor']['CorreoEmisor'] : false,
            'RUTAutorizado' => [
                'RUT' => false,
                'Nombre' => false,
            ],
            'DeclaracionJurada' => false,
        ], $cedente);
        if (!$this->datos['Cesion']['DocumentoCesion']['Cedente']['DeclaracionJurada']) {
            $this->datos['Cesion']['DocumentoCesion']['Cedente']['DeclaracionJurada'] = mb_substr(str_replace(
                [
                    '{usuario_nombre}',
                    '{usuario_run}',
                    '{emisor_razon_social}',
                    '{emisor_rut}',
                    '{cesionario_razon_social}',
                    '{cesionario_rut}',
                    '{receptor_razon_social}',
                    '{receptor_rut}',
                ],
                [
                    $this->datos['Cesion']['DocumentoCesion']['Cedente']['RUTAutorizado']['Nombre'],
                    $this->datos['Cesion']['DocumentoCesion']['Cedente']['RUTAutorizado']['RUT'],
                    $this->datos['Cesion']['DocumentoCesion']['Cedente']['RazonSocial'],
                    $this->datos['Cesion']['DocumentoCesion']['Cedente']['RUT'],
                    $this->datos['Cesion']['DocumentoCesion']['Cesionario']['RazonSocial'],
                    $this->datos['Cesion']['DocumentoCesion']['Cesionario']['RUT'],
                    $this->Encabezado['Receptor']['RznSocRecep'],
                    $this->Encabezado['Receptor']['RUTRecep'],
                ],
                $this->declaracion
            ), 0, 512);
        }
    }

    public function setCesionario(array $cesionario)
    {
        $this->datos['Cesion']['DocumentoCesion']['Cesionario'] = $cesionario;
    }

    public function setDatos(array $datos)
    {
        if (!empty($datos['MontoCesion'])) {
            $this->datos['Cesion']['DocumentoCesion']['MontoCesion'] = $datos['MontoCesion'];
        }
        if (!empty($datos['UltimoVencimiento'])) {
            $this->datos['Cesion']['DocumentoCesion']['UltimoVencimiento'] = $datos['UltimoVencimiento'];
        }
        if (!empty($datos['OtrasCondiciones'])) {
            $this->datos['Cesion']['DocumentoCesion']['OtrasCondiciones'] = $datos['OtrasCondiciones'];
        }
        if (!empty($datos['eMailDeudor'])) {
            $this->datos['Cesion']['DocumentoCesion']['eMailDeudor'] = $datos['eMailDeudor'];
        }
    }

    public function firmar(\App\Dtes\FirmaElectronica $Firma)
    {
        $xml_unsigned = (new \App\Dtes\XML())->generate($this->datos)->saveXML();
        $xml = $Firma->signXML($xml_unsigned, '#'.$this->getID(), 'DocumentoCesion');
        if (!$xml) {
            \App\Dtes\Log::write(
                \App\Dtes\Estado::DTE_ERROR_FIRMA,
                \App\Dtes\Estado::get(\App\Dtes\Estado::DTE_ERROR_FIRMA, '#'.$this->getID())
            );
            return false;
        }
        $this->xml = new \App\Dtes\XML();
        if (!$this->xml->loadXML($xml) or !$this->schemaValidate())
            return false;
        return true;
    }

    public function saveXML()
    {
        return $this->xml->saveXML();
    }

    public function schemaValidate()
    {
        return true;
    }

    public function getCedente()
    {
        return $this->datos['Cesion']['DocumentoCesion']['Cedente'];
    }

    public function getCesionario()
    {
        return $this->datos['Cesion']['DocumentoCesion']['Cesionario'];
    }

    public function loadXML($xml_data)
    {
        $this->xml = new \App\Dtes\XML();
        if (!$this->xml->loadXML($xml_data)) {
            return false;
        }
        return $this->xml;
    }

}
