<?php

use App\Util\Arrays;

describe(Arrays::class, function () {
    describe('::niceMerge', function () {
        test('merges numeric arrays', function () {
            $result = \App\Util\Arrays::niceMerge([1, 2, 3], [4, 5, 6]);

            expect($result)->toEqual([1, 2, 3, 4, 5, 6]);
        });

        test('merges associative arrays', function () {
            $result = \App\Util\Arrays::niceMerge([
                'hello' => 'world',
                'non-matching key array 1' => 'from array 1',
            ], [
                'hello' => 'universe',
                'from array 2' => 'non-matching value array 2',
            ]);

            expect($result)->toEqual([
                'hello' => 'universe',
                'non-matching key array 1' => 'from array 1',
                'from array 2' => 'non-matching value array 2',
            ]);
        });

        test('merges nested structure', function () {
            $result = \App\Util\Arrays::niceMerge([
                'hello' => 'world',
                'non-matching key array 1' => 'from array 1',
                'nested' => [
                    'hello' => 'world',
                    'non-matching nested key array 1' => 'from array 1',
                    'list' => [1, 2, 3],

                ],
            ], [
                'hello' => 'universe',
                'key from array 2' => 'non-matching value from array 2',
                'nested' => [
                    'hello' => 'universe',
                    'nested key from array 2' => 'non-matching value from array 2',
                    'list' => [4, 5, 6],
                ],
            ]);

            expect($result)->toEqual([
                'hello' => 'universe',
                'non-matching key array 1' => 'from array 1',
                'key from array 2' => 'non-matching value from array 2',
                'nested' => [
                    'hello' => 'universe',
                    'non-matching nested key array 1' => 'from array 1',
                    'nested key from array 2' => 'non-matching value from array 2',
                    'list' => [1, 2, 3, 4, 5, 6],
                ],
            ]);
        });
    });

    describe('::niceMergeByKey', function () {
        test('merges arrays by key', function () {
            $result = Arrays::niceMergeByKey([
                ['name' => 'FOO', 'value' => 'BAR'],
                ['name' => 'BAZ', 'value' => 'QUX'],
            ], [
                ['name' => 'BAZ', 'value' => 'XUQ'],
                ['name' => 'NEW', 'value' => 'VALUE', 'other' => 'THING'],
            ], 'name');

            expect($result)->toEqual([
                ['name' => 'FOO', 'value' => 'BAR'],
                ['name' => 'BAZ', 'value' => 'XUQ'],
                ['name' => 'NEW', 'value' => 'VALUE', 'other' => 'THING'],
            ]);
        });
    });
});
