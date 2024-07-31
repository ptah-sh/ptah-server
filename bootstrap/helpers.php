<?php

use Illuminate\Support\Str;

if (! function_exists('dockerize_labels')) {
    function dockerize_labels(array $labels): array
    {
        $result = [
            'sh.ptah.managed' => '1',
        ];

        foreach ($labels as $label => $value) {
            if (Str::startsWith($label, 'sh.ptah.')) {
                $result[$label] = (string) $value;
            } else {
                $result["sh.ptah.{$label}"] = (string) $value;
            }
        }

        return $result;
    }
}

if (! function_exists('dockerize_name')) {
    function dockerize_name(string $name): string
    {
        $name = Str::snake($name);
        $name = Str::replaceMatches('/\W/', '_', $name);
        $name = Str::replaceMatches('/_+/', '_', $name);

        if (Str::length($name) > 63) {
            $name = Str::substr($name, 0, 57).'_'.Str::random(5);
        }

        return $name;
    }
}
