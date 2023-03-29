<?php


namespace App\Dtes;

class Arreglo
{

    public static function mergeRecursiveDistinct(array $array1, array $array2)
    {
        $merged = $array1;
        foreach ( $array2 as $key => &$value ) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged [$key] = self::mergeRecursiveDistinct(
                    $merged [$key],
                    $value
                );
            } else {
                $merged [$key] = $value;
            }
        }
        return $merged;
    }

}
