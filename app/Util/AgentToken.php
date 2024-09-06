<?php

namespace App\Util;

use Illuminate\Support\Str;
use RuntimeException;

class AgentToken
{
    protected static $useFakeToken = false;

    protected static $fakeToken = null;

    public static function useFakeToken(string $token): void
    {
        self::$fakeToken = $token;
        self::$useFakeToken = true;
    }

    public static function make(): string
    {
        if (self::$useFakeToken) {
            if (self::$fakeToken === null) {
                throw new RuntimeException('Fake token is not set');
            }

            return self::$fakeToken;
        }

        return Str::random(42);
    }
}
