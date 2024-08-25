<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Override;

class UniqueInArray implements DataAwareRule, ValidationRule
{
    protected array $data;

    public function __construct(protected string $targetArrayPath) {}

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $targetArray = data_get($this->data, $attribute) ?? [];

        $unique = collect($targetArray)->unique(fn ($item) => data_get($item, $this->targetArrayPath));

        if ($unique->count() !== count($targetArray)) {
            $fail('The :attribute must be unique within the specified array.');
        }
    }

    #[Override]
    public function setData(array $data): void
    {
        $this->data = $data;
    }
}
