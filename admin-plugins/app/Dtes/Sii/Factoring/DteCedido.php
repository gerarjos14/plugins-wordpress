<?php


namespace App\Dtes\Sii\Factoring;

class DteCedido
{

    private $dte; ///< Objeto con el DTE que se está cediendo
    private $xml; ///< String con el XML del DTE cedido

    public function __construct(\App\Dtes\Sii\Dte $DTE)
    {
        $this->dte = $DTE;
        $xml = (new \App\Dtes\XML())->generate([
            'DTECedido' => [
                '@attributes' => [
                    'xmlns' => 'http://www.sii.cl/SiiDte',
                    'version' => '1.0'
                ],
                'DocumentoDTECedido' => [
                    '@attributes' => [
                        'ID' => 'DTES_DTECedido'
                    ],
                    'DTE' => null,
                    'ImagenDTE' => false,
                    'Recibo' => false,
                    'TmstFirma' => date('Y-m-d\TH:i:s'),
                ]
            ]
        ])->saveXML();
        $xml_dte = $this->dte->saveXML();
        $xml_dte = substr($xml_dte, strpos($xml_dte, '<DTE'));
        $this->xml = str_replace('<DTE/>', $xml_dte, $xml);
    }

    public function firmar(\App\Dtes\FirmaElectronica $Firma)
    {
        $xml = $Firma->signXML($this->xml, '#DTES_DTECedido', 'DocumentoDTECedido');
        if (!$xml) {
            \App\Dtes\Log::write(
                \App\Dtes\Estado::DTE_ERROR_FIRMA,
                \App\Dtes\Estado::get(\App\Dtes\Estado::DTE_ERROR_FIRMA, '#DTES_DTECedido')
            );
            return false;
        }
        $this->xml = $xml;
        return true;
    }

    public function saveXML()
    {
        return $this->xml;
    }

    public function schemaValidate()
    {
        return true;
    }

    public function getDTE()
    {
        return $this->dte;
    }

}
