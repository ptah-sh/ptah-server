<?php

namespace App\Models\DeploymentData;

use Illuminate\Support\Str;
use RuntimeException;

class WorkerCookie
{
    protected static $useIdsPool = false;

    protected static $idsPool = [];

    public static function useIdsPool(array $ids): void
    {
        self::$idsPool = $ids;
        self::$useIdsPool = true;
    }

    public static function make(): string
    {
        if (self::$useIdsPool) {
            if (empty(self::$idsPool)) {
                throw new RuntimeException('IDs pool is empty');
            }

            return array_shift(self::$idsPool);
        }

        return Str::random(32);
    }
}
