<?php

namespace App\Util;

use Illuminate\Support\Arr;

class Arrays
{
    public static function niceMerge(array $array1, array $array2): array
    {
        if (Arr::isList($array1) && Arr::isList($array2)) {
            return [...$array1, ...$array2];
        }

        $allKeys = array_unique(array_merge(array_keys($array1), array_keys($array2)));

        $result = [];

        foreach ($allKeys as $key) {
            $array1KeyMissing = ! array_key_exists($key, $array1);
            $array2KeyMissing = ! array_key_exists($key, $array2);
            $array2KeyExists = array_key_exists($key, $array2);
            $array2KeyNull = $array2KeyExists && $array2[$key] === null;
            $array2KeyScalar = $array2KeyExists && is_scalar($array2[$key]);

            if ($array2KeyMissing) {
                $result[$key] = $array1[$key];

                continue;
            }

            if ($array1KeyMissing || $array2KeyNull || $array2KeyScalar) {
                $result[$key] = $array2[$key];

                continue;
            }

            $result[$key] = self::niceMerge($array1[$key], $array2[$key]);
        }

        return $result;
    }
}
