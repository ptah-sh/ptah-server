<?php

namespace App\Util;

/**
 * Promexport is a utility class for parsing Prometheus metrics from a string.
 *
 * It doesn't support (yet) all possible Prometheus metric types, but it's
 * designed to be super-fast, so it can be used in high-performance scenarios
 * like metrics ingestion from untrusted sources.
 */
class Promexport
{
    public static function parse(string $doc): array
    {
        // This "explode" can be optimized with a loop as well
        $lines = explode("\n", $doc);
        $metrics = [];

        foreach ($lines as $line) {
            if (empty($line) || strpos($line, '#') === 0) {
                continue;
            }

            $metric = self::parseLine($line);
            if ($metric) {
                $metrics[] = $metric;
            }
        }

        return $metrics;
    }

    // Please don't judge me for this code. I'm a PHP developer.
    public static function parseLine(string $line): array|false
    {
        $cursor = 0;
        $lastCursor = 0;

        $metric = '';
        $labels = [];
        $value = '';
        $timestamp = '';

        $hasLabels = false;

        $length = strlen($line);

        while ($cursor < $length) {
            if ($line[$cursor] === '{') {
                $hasLabels = true;
            }

            if ($hasLabels || $line[$cursor] === ' ') {
                $metric = substr($line, 0, $cursor);

                break;
            }

            $cursor++;
        }

        $cursor++;
        $lastCursor = $cursor;

        if ($hasLabels) {
            while ($cursor < $length) {
                if ($line[$cursor] === '}') {
                    break;
                }

                parseLabel:

                while ($cursor < $length) {
                    if ($line[$cursor] === '=') {
                        break;
                    }

                    $cursor++;
                }

                $label = substr($line, $lastCursor, $cursor - $lastCursor);

                $cursor++;
                $lastCursor = $cursor;

                while ($cursor < $length) {
                    if ($line[$cursor] === '"') {
                        break;
                    }

                    $cursor++;
                }

                $cursor++;
                $lastCursor = $cursor;

                $labelValue = '';
                while ($cursor < $length) {
                    if ($line[$cursor] === '"') {
                        $labelValue = substr($line, $lastCursor, $cursor - $lastCursor);

                        break;
                    }

                    $cursor++;
                }

                $labels[$label] = $labelValue;

                $cursor++;
                $lastCursor = $cursor;

                if ($line[$cursor] === ',') {
                    $cursor++;
                    $lastCursor = $cursor;

                    goto parseLabel;
                }
            }

            $cursor++;
            $lastCursor = $cursor;

            // skip ' '
            $cursor++;
            $lastCursor = $cursor;
        }

        while ($cursor < $length) {
            if ($line[$cursor] === ' ') {
                $value = substr($line, $lastCursor, $cursor - $lastCursor);

                break;
            }

            $cursor++;
        }

        $cursor++;
        $lastCursor = $cursor;

        $timestamp = substr($line, $lastCursor);

        if ($metric === '' || $value === '' || $timestamp === '') {
            return false;
        }

        return [
            'metric' => $metric,
            'labels' => $labels,
            'value' => $value,
            'timestamp' => $timestamp,
        ];
    }
}
