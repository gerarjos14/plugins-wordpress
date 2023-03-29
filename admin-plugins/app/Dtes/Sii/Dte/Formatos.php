<?php


namespace App\Dtes\Sii\Dte;

class Formatos
{

    private static $namespaces = [
        '\App\Dtes',
        '\App\Dtes\Extra'
    ]; ///< Posible ubicaciones para los formatos que DTES soporta
    private static $formatos = []; ///< Formatos oficialmente soportados (para los que existe un parser)

    public static function toArray($formato, $datos)
    {
        $founded = false;
        foreach (self::$namespaces as $namespace) {
            $formato = str_replace('.', '\\', $formato);
            $combinations = [
                $namespace.'\Sii\Dte\Formatos\\'.$formato,
                $namespace.'\Sii\Dte\Formatos\\'.strtoupper($formato),
                $namespace.'\Sii\Dte\Formatos\\'.strtolower($formato),
            ];
            foreach ($combinations as $class) {
                if (class_exists($class)) {
                    $founded = $class;
                    break;
                }
            }
            if ($founded) {
                break;
            }
        }
        if (!$founded) {
            throw new \Exception('Formato '.$formato.' no es válido como entrada para datos del DTE');
        }
        return $founded::toArray($datos);
    }

    public static function toJSON($formato, $datos)
    {
        return json_encode(self::toArray($formato, $datos), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public static function getFormatos()
    {
        if (!self::$formatos) {
            $dir = dirname(__FILE__).'/Formatos';
            $formatos = scandir($dir);
            foreach($formatos as &$formato) {
                if ($formato[0]=='.')
                    continue;
                if (is_dir($dir.'/'.$formato)) {
                    $subformatos = scandir($dir.'/'.$formato);
                    foreach($subformatos as &$subformato) {
                        if ($subformato[0]=='.')
                            continue;
                        self::$formatos[] = $formato.'.'.substr($subformato, 0, -4);
                    }
                } else {
                    self::$formatos[] = substr($formato, 0, -4);
                }
            }
        }
        return self::$formatos;
    }

}
