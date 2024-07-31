<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Override;

class RequiredIfArrayHas implements DataAwareRule, ValidationRule
{
    protected array $data;

    public function __construct(protected string $targetArrayPath, protected string $targetValue) {}

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $data = data_get($this->data, $this->targetArrayPath) ?? [];

        if (in_array($this->targetValue, $data) && empty($value)) {
            $fail('The :attribute is required.');
        }
    }

    #[Override]
    public function setData(array $data): void
    {
        $this->data = $data;
    }
}
