<?php

if (!function_exists('dockerize_labels')) {
    function dockerize_labels(array $labels): array
    {
        $result = [
            'sh.ptah.managed' => "1",
        ];

        foreach ($labels as $label => $value) {
            $result["sh.ptah.{$label}"] = (string) $value;
        }

        return $result;
    }
}
