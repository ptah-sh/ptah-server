<?php

namespace App\Util;

use Illuminate\Support\Str;
use RuntimeException;

class ResourceId
{
    protected static $useIdsPool = false;

    protected static $idsPool = [];

    public static function useIdsPool(array $ids): void
    {
        self::$idsPool = $ids;
        self::$useIdsPool = true;
    }

    public static function make(string $resourceType): string
    {
        if (self::$useIdsPool) {
            if (empty(self::$idsPool)) {
                throw new RuntimeException('IDs pool is empty');
            }

            return $resourceType.'-'.array_shift(self::$idsPool);
        }

        return $resourceType.'-'.Str::random(11);
    }
}
