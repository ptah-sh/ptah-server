<?php

namespace App\Models;

use App\Models\DeploymentData\Healthcheck;
use App\Models\DeploymentData\LaunchMode;
use App\Models\DeploymentData\Process;
use App\Models\DeploymentData\ReleaseCommand;
use App\Rules\UniqueInArray;
use App\Util\Arrays;
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
        $processDefaults = [
            'name' => 'svc',
            'placementNodeId' => null,
            'dockerRegistryId' => null,
            'dockerImage' => '',
            'releaseCommand' => ReleaseCommand::from([
                'command' => null,
            ]),
            'command' => '',
            'healthcheck' => Healthcheck::from([
                'command' => null,
            ]),
            'backups' => [],
            'workers' => [],
            'launchMode' => LaunchMode::Daemon->value,
            'envVars' => [],
            'secretVars' => [],
            'configFiles' => [],
            'secretFiles' => [],
            'volumes' => [],
            'ports' => [],
            'replicas' => 1,
            'caddy' => [],
            'fastCgi' => null,
            'redirectRules' => [],
            'rewriteRules' => [],
        ];

        $defaults = [
            'networkName' => '',
            'internalDomain' => '',
            'processes' => empty($attributes['processes']) ? [$processDefaults] : $attributes['processes'],
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

                $processExists = false;

                foreach ($result['processes'] as $existingIdx => $existingProcess) {
                    if ($existingProcess['name'] === $process['name']) {
                        if (isset($process['envVars'])) {
                            $updatedVars = collect($process['envVars'])->pluck('name')->toArray();

                            $existingProcess['envVars'] = collect($result['processes'][$existingIdx]['envVars'])
                                ->reject(fn ($var) => in_array($var['name'], $updatedVars))
                                ->values()
                                ->toArray();
                        }

                        $result['processes'][$existingIdx] = Arrays::niceMerge($existingProcess, $process);

                        $processExists = true;
                    }
                }

                if (! $processExists) {
                    $errors["processes.{$idx}.name"] = "Process {$process['name']} does not exist";
                }
            }
        }

        if (! empty($errors)) {
            throw ValidationException::withMessages($errors);
        }

        return DeploymentData::validateAndCreate($result);
    }

    public function findProcess(string $dockerName): ?Process
    {
        return collect($this->processes)->first(fn (Process $process) => $process->dockerName === $dockerName);
    }
}
