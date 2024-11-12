<?php

namespace App\Models;

use App\Models\DeploymentData\Process;
use App\Rules\UniqueInArray;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

    public function fork(string $ref, array $processData, array $workerData): DeploymentData
    {
        if (! isset($processData['name']) || ! isset($workerData['name'])) {
            throw new ValidationException('Process and worker names are required');
        }

        $process = $this->findProcessByName($processData['name']);
        if (! $process) {
            throw new NotFoundHttpException('Process not found');
        }

        $worker = $process->findWorkerByName($workerData['name']);
        if (! $worker) {
            throw new NotFoundHttpException('Worker not found');
        }

        $result = $this->toArray();

        $result['internalDomain'] = Str::slug($ref).'.'.$this->internalDomain;

        $result['processes'] = [
            $process->copyWith($processData)->toArray(),
        ];

        $result['processes'][0]['workers'] = [
            $worker->copyWith($workerData)->toArray(),
        ];

        $result = self::clearDockerNames($result);

        $result['processes'][0]['dockerName'] = dockerize_name($this->processes[0]->dockerName.'-'.$ref);

        $result['processes'][0]['ports'] = isset($processData['ports']) ? $processData['ports'] : [];
        $result['processes'][0]['caddy'] = isset($processData['caddy']) ? $processData['caddy'] : [];

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

    private static function clearDockerNames(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = self::clearDockerNames($value);
            } elseif ($key === 'dockerName') {
                $data[$key] = null;
            }
        }

        return $data;
    }
}
