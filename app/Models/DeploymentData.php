<?php

namespace App\Models;

use App\Models\DeploymentData\Process;
use App\Rules\UniqueInArray;
use Illuminate\Validation\ValidationException;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;

class DeploymentData extends Data
{
    public function __construct(
        public string $networkName,
        public string $internalDomain,
        #[DataCollectionOf(Process::class)]
        #[Rule(new UniqueInArray('name'))]
        /* @var Process[] */
        public array $processes
    ) {}

    public static function make(array $attributes): static
    {
        $processDefaults = Process::make([]);

        $defaults = [
            'networkName' => '',
            'internalDomain' => '',
            'processes' => [$processDefaults],
        ];

        return self::from([
            ...$defaults,
            ...$attributes,
        ]);
    }

    public function copyWith(array $attributes): DeploymentData
    {
        $result = $this->toArray();
        $errors = [];

        // TODO: there is a bug. If the $result['processes'] array has multiple items and only second (by name) item is in $attributes, it will copy the first item instead of the second.
        if (isset($attributes['processes'])) {
            foreach ($attributes['processes'] as $idx => $process) {
                if (! isset($process['name'])) {
                    $errors["processes.{$idx}.name"] = 'Process name is required';

                    continue;
                }

                $existingProcess = $this->findProcessByName($process['name']);
                if (! $existingProcess) {
                    $errors["processes.{$idx}.name"] = "Process {$process['name']} does not exist";

                    continue;
                }

                $result['processes'][$idx] = $existingProcess->copyWith($process)->toArray();
            }
        }

        if (! empty($errors)) {
            throw ValidationException::withMessages($errors);
        }

        return self::validateAndCreate($result);
    }

    public function findProcess(string $dockerName): ?Process
    {
        return collect($this->processes)->first(fn (Process $process) => $process->dockerName === $dockerName);
    }

    public function findProcessByName(string $name): ?Process
    {
        return collect($this->processes)->first(fn (Process $process) => $process->name === $name);
    }
}
