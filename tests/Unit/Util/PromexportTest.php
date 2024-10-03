<?php

use App\Util\Promexport;

describe('Promexport', function () {
    test('parses metric line', function () {
        $line = 'metric{label="value"} 1 1714200000';
        $result = Promexport::parseLine($line);

        expect($result)->toEqual([
            [
                'metric' => 'metric',
                'timestamp' => '1714200000',
                'labels' => ['label' => 'value'],
                'value' => '1',
            ],
        ]);
    });

    test('parses metric line with multiple labels', function () {
        $line = 'metric{label1="value1",label2="value2"} 1 1714200000';
        $result = Promexport::parseLine($line);

        expect($result)->toEqual([
            [
                'metric' => 'metric',
                'labels' => ['label1' => 'value1', 'label2' => 'value2'],
                'value' => '1',
                'timestamp' => '1714200000',
            ],
        ]);
    });

    test('parses metric line with no labels', function () {
        $line = 'metric 1 1714200000';
        $result = Promexport::parseLine($line);

        expect($result)->toEqual([
            [
                'metric' => 'metric',
                'labels' => [],
                'value' => '1',
                'timestamp' => '1714200000',
            ],
        ]);
    });

    test('parses multiple metrics', function () {
        $doc = "metric1{label1=\"value1\"} 1 1714200000\nmetric2{label2=\"value2\"} 2 1714200001";
        $result = Promexport::parse($doc);

        expect($result)->toEqual([
            [
                'metric' => 'metric1',
                'labels' => ['label1' => 'value1'],
                'value' => '1',
                'timestamp' => '1714200000',
            ],
            [
                'metric' => 'metric2',
                'labels' => ['label2' => 'value2'],
                'value' => '2',
                'timestamp' => '1714200001',
            ],
        ]);
    });
});
