<?php


namespace App\Dtes\Sii\Base;

abstract class Libro extends Envio
{

    protected $detalles = []; ///< Arreglos con los detalles del documento
    protected $resumen = []; ///< resumenes del libro

    abstract public function agregar(array $detalle);

    public function cantidad()
    {
        return count($this->detalles);
    }

    public function getDetalle()
    {
        return $this->detalles;
    }

}
