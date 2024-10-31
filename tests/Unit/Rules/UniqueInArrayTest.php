<?php

namespace Tests\Unit\Rules;

use App\Rules\UniqueInArray;
use Illuminate\Support\Facades\Validator;

describe(UniqueInArray::class, function () {
    it('passes when the array is unique', function () {
        $rule = new UniqueInArray('name');

        $validator = Validator::make(['names' => [['name' => 'John'], ['name' => 'Jane']]], ['names' => [$rule]]);

        expect($validator->passes())->toBeTrue();
    });

    it('fails when the array is not unique', function () {
        $rule = new UniqueInArray('name');

        $validator = Validator::make(['names' => [['name' => 'John'], ['name' => 'John']]], ['names' => [$rule]]);

        expect($validator->passes())->toBeFalse();
    });

    it('passes when the array is empty', function () {
        $rule = new UniqueInArray('name');

        $validator = Validator::make(['names' => []], ['names' => [$rule]]);

        expect($validator->passes())->toBeTrue();
    });
});
