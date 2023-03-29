<?php


namespace App\Dtes\Sii\Base;

abstract class Documento
{

    protected $xml; ///< Objeto XML que representa el EnvioDTE
    protected $xml_data; ///< String con el documento XML
    protected $caratula; ///< arreglo con la caratula del envío
    protected $Firma; ///< objeto de la firma electrónica
    protected $id; ///< ID del documento (se usa como referencia en la firma del XML)
    protected $arreglo; ///< Arreglo con los datos del XML
    private $schemas = [
        'EnvioDTE' => 'EnvioDTE_v10.xsd',
    ]; ///< Tablas de esquemas por defecto (por si no vienen en el XML)

    abstract public function setCaratula(array $caratula);

    abstract public function generar();

    public function getID()
    {
        return $this->id;
    }

    public function setFirma(\App\Dtes\FirmaElectronica $Firma)
    {
        $this->Firma = $Firma;
    }

    public function schemaValidate()
    {
        if (!$this->xml_data) {
            \App\Dtes\Log::write(
                \App\Dtes\Estado::DOCUMENTO_FALTA_XML,
                \App\Dtes\Estado::get(
                    \App\Dtes\Estado::DOCUMENTO_FALTA_XML,
                    substr(get_class($this), strrpos(get_class($this), '\\')+1)
                )
            );
            return null;
        }
        $this->xml = new \App\Dtes\XML();
        $this->xml->loadXML($this->xml_data);
        $schema = $this->xml->getSchema();
        if (!$schema) {
            $tag = array_keys($this->toArray())[0];
            if (isset($this->schemas[$tag])) {
                $schema = $this->schemas[$tag];
            }
        }
        if ($schema) {
            $xsd = resource_path() .'/schemas/'.$schema;
        }
        if (!$schema or !is_readable($xsd)) {
            \App\Dtes\Log::write(
                \App\Dtes\Estado::DOCUMENTO_FALTA_SCHEMA,
                \App\Dtes\Estado::get(
                    \App\Dtes\Estado::DOCUMENTO_FALTA_SCHEMA
                )
            );
            return null;
        }
        $result = $this->xml->schemaValidate($xsd);
        if (!$result) {
            \App\Dtes\Log::write(
                \App\Dtes\Estado::DOCUMENTO_ERROR_SCHEMA,
                \App\Dtes\Estado::get(
                    \App\Dtes\Estado::DOCUMENTO_ERROR_SCHEMA,
                    substr(get_class($this), strrpos(get_class($this), '\\')+1),
                    implode("\n", $this->xml->getErrors())
                )
            );
        }
        return $result;
    }

    public function saveXML()
    {
        return $this->xml_data ? $this->xml_data : false;
    }

    public function loadXML($xml_data)
    {
        $this->xml_data = $xml_data;
        $this->xml = new \App\Dtes\XML();
        if (!$this->xml->loadXML($this->xml_data)) {
            return false;
        }
        $this->toArray();
        return $this->xml;
    }

    public function toArray()
    {
        if (!$this->xml and $this->xml_data) {
            $this->loadXML($this->xml_data);
        }
        if (!$this->xml) {
            return false;
        }
        if (!$this->arreglo) {
            $this->arreglo = $this->xml->toArray();
        }
        return $this->arreglo;
    }

}
