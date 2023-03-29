<?php


namespace App\Dtes\Sii;

class RegistroCompraVenta
{

    private static $config = [
        'wsdl' => [
            'https://ws1.sii.cl/WSREGISTRORECLAMODTE/registroreclamodteservice?wsdl',
            'https://ws2.sii.cl/WSREGISTRORECLAMODTECERT/registroreclamodteservice?wsdl',
        ],
        'servidor' => ['ws1', 'ws2'], ///< servidores 0: producción, 1: certificación
    ];

    public static $dtes = [
        33 => 'Factura electrónica',
        34 => 'Factura no afecta o exenta electrónica',
        43 => 'Liquidación factura electrónica',
    ]; ///< Documentos que tienen acuse de recibo

    public static $acciones = [
        'ERM' => 'Otorga recibo de mercaderías o servicios',
        'ACD' => 'Acepta contenido del documento',
        'RCD' => 'Reclamo al contenido del documento',
        'RFP' => 'Reclamo por falta parcial de mercaderías',
        'RFT' => 'Reclamo por falta total de mercaderías',
    ]; ///< Posibles acciones a que tiene asociadas un DTE

    public static $eventos = [
        'A' => 'No reclamado en plazo (recepción automática)',
        'C' => 'Recibo otorgado por el receptor',
        'P' => 'Forma de pago al contado',
        'R' => 'Reclamado',
    ];

    public static $tipo_transacciones = [
        1 => 'Compras del giro',
        2 => 'Compras en supermercados o comercios similares',
        3 => 'Adquisición de bienes raíces',
        4 => 'Compra de activo fijo',
        5 => 'Compras con IVA uso común',
        6 => 'Compras sin derecho a crédito',
    ]; ///< Tipos de transacciones o caracterizaciones/clasificaciones de las compras

    public static $estados_ok = [
        7,  // Evento registrado previamente
        8,  // Pasados 8 días después de la recepción no es posible registrar reclamos o eventos
        27, // No se puede registrar un evento (acuse de recibo, reclamo o aceptación de contenido) de un DTE pagado al contado o gratuito
    ]; ///< Código de estado de respuesta de la asignación de estado que son considerados como OK

    private $token; ///< Token que se usará en la sesión de consultas al RCV

    public function __construct(\App\Dtes\FirmaElectronica $Firma)
    {
        // Se usa siempre ambiente de producción para obtener el token
        // https://github.com/DTES/DTES-lib/issues/72
        $ambienteAntiguo = \App\Dtes\Sii::getAmbiente();
        \App\Dtes\Sii::setAmbiente(\App\Dtes\Sii::PRODUCCION);
        $this->token = \App\Dtes\Sii\Autenticacion::getToken($Firma);
        \App\Dtes\Sii::setAmbiente($ambienteAntiguo);
        if (!$this->token) {
            throw new \Exception('No fue posible obtener el token para la sesión del RCV');
        }
    }

    public function ingresarAceptacionReclamoDoc($rut, $dv, $dte, $folio, $accion)
    {
        // ingresar acción al DTE
        $r = $this->request('ingresarAceptacionReclamoDoc', [
            'rutEmisor' => $rut,
            'dvEmisor' => $dv,
            'tipoDoc' => $dte,
            'folio' => $folio,
            'accionDoc' => $accion,
        ]);
        // si no se pudo recuperar error
        if ($r===false) {
            return false;
        }
        // entregar resultado del ingreso
        return [
            'codigo' => $r->codResp,
            'glosa' => $r->descResp,
        ];
    }

    public function listarEventosHistDoc($rut, $dv, $dte, $folio)
    {
        // consultar eventos del DTE
        $r = $this->request('listarEventosHistDoc', [
            'rutEmisor' => $rut,
            'dvEmisor' => $dv,
            'tipoDoc' => $dte,
            'folio' => $folio,
        ]);
        // si no se pudo recuperar error
        if ($r===false) {
            return false;
        }
        // si hubo error informar
        if (!in_array($r->codResp, [8, 15, 16])) {
            throw new \Exception($r->descResp);
        }
        // entregar eventos del DTE
        $eventos = [];
        if (!empty($r->listaEventosDoc)) {
            if (!is_array($r->listaEventosDoc)) {
                $r->listaEventosDoc = [$r->listaEventosDoc];
            }
            foreach ($r->listaEventosDoc as $evento) {
                $eventos[] = [
                    'codigo' => $evento->codEvento,
                    'glosa' => $evento->descEvento,
                    'responsable' => $evento->rutResponsable.'-'.$evento->dvResponsable,
                    'fecha' => $evento->fechaEvento,
                ];
            }
        }
        return $eventos;
    }

    public function consultarDocDteCedible($rut, $dv, $dte, $folio)
    {
        // consultar eventos del DTE
        $r = $this->request('consultarDocDteCedible', [
            'rutEmisor' => $rut,
            'dvEmisor' => $dv,
            'tipoDoc' => $dte,
            'folio' => $folio,
        ]);
        // si no se pudo recuperar error
        if ($r===false) {
            return false;
        }
        // entregar información de cesión para el DTE
        return [
            'codigo' => $r->codResp,
            'glosa' => $r->descResp,
        ];
    }

    public function consultarFechaRecepcionSii($rut, $dv, $dte, $folio)
    {
        // consultar eventos del DTE
        $r = $this->request('consultarFechaRecepcionSii', [
            'rutEmisor' => $rut,
            'dvEmisor' => $dv,
            'tipoDoc' => $dte,
            'folio' => $folio,
        ]);
        // si no se pudo recuperar error
        if (!$r) {
            return false;
        }
        // armar y entregar fecha
        list($dia, $hora) = explode(' ', $r);
        list($d, $m, $Y) = explode('-', $dia);
        return $Y.'-'.$m.'-'.$d.' '.$hora;
    }

    private function request($request, $args, $retry = 10)
    {
        $options = ['keep_alive' => false];
        if (!\App\Dtes\Sii::getVerificarSSL()) {
            if (\App\Dtes\Sii::getAmbiente()==\App\Dtes\Sii::PRODUCCION) {
                $msg = \App\Dtes\Estado::get(\App\Dtes\Estado::ENVIO_SSL_SIN_VERIFICAR);
                \App\Dtes\Log::write(\App\Dtes\Estado::ENVIO_SSL_SIN_VERIFICAR, $msg, LOG_WARNING);
            }
            $options['stream_context'] = stream_context_create([
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ]);
        }
        // buscar WSDL
        $ambiente = \App\Dtes\Sii::getAmbiente();
        $wsdl = resource_path() . '/wsdl/' . self::$config['servidor'][$ambiente].'/registroreclamodteservice.xml';
        if (!file_exists($wsdl)) {
            $wsdl = self::$config['wsdl'][$ambiente];
        }
        // crear el cliente SOAP
        try {
            $soap = new \SoapClient($wsdl, $options);
            $soap->__setCookie('TOKEN', $this->token);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            if (isset($e->getTrace()[0]['args'][1]) and is_string($e->getTrace()[0]['args'][1])) {
                $msg .= ': '.$e->getTrace()[0]['args'][1];
            }
            \App\Dtes\Log::write(\App\Dtes\Estado::REQUEST_ERROR_SOAP, \App\Dtes\Estado::get(\App\Dtes\Estado::REQUEST_ERROR_SOAP, $msg));
            return false;
        }
        // hacer consultas al SII
        for ($i=0; $i<$retry; $i++) {
            try {
                $body = call_user_func_array([$soap, $request], $args);
                break;
            } catch (\Exception $e) {
                $msg = $e->getMessage();
                if (isset($e->getTrace()[0]['args'][1]) and is_string($e->getTrace()[0]['args'][1])) {
                    $msg .= ': '.$e->getTrace()[0]['args'][1];
                }
                \App\Dtes\Log::write(\App\Dtes\Estado::REQUEST_ERROR_SOAP, \App\Dtes\Estado::get(\App\Dtes\Estado::REQUEST_ERROR_SOAP, $msg));
                $body = null;
            }
        }
        if ($body===null) {
            \App\Dtes\Log::write(\App\Dtes\Estado::REQUEST_ERROR_BODY, \App\Dtes\Estado::get(\App\Dtes\Estado::REQUEST_ERROR_BODY, $wsdl, $retry));
            return false;
        }
        return $body;
    }

}
