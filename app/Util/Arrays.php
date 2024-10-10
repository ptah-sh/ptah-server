<?php

namespace App\Util;

use Illuminate\Support\Arr;
use InvalidArgumentException;

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

    public static function niceMergeByKey(array $array1, array $array2, string $keyBy): array
    {
        if (! Arr::isList($array1) || ! Arr::isList($array2)) {
            throw new InvalidArgumentException('Arrays must be lists');
        }

        $array1Keys = array_flip(array_column($array1, $keyBy));
        $array2Keys = array_flip(array_column($array2, $keyBy));

        foreach ($array2Keys as $key => $value) {
            if (isset($array1Keys[$key])) {
                $array1[$array1Keys[$key]] = [
                    ...$array1[$array1Keys[$key]],
                    ...$array2[$value],
                ];
            } else {
                $array1[] = $array2[$value];
            }
        }

        return array_values($array1);
    }
}
