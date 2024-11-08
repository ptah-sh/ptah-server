<?php

use App\Models\DeploymentData\EnvVar;
use App\Models\DeploymentData\Process;
use App\Models\DeploymentData\Worker;
use Illuminate\Validation\ValidationException;

describe(Process::class, function () {
    describe('::copyWith', function () {
        it('merges env vars', function () {
            $process = Process::make([
                'name' => 'service',
                'envVars' => [
                    ['name' => 'FOO', 'value' => 'BAR'],
                ],
                'workers' => [
                    Worker::make([
                        'name' => 'worker',
                        'source' => [
                            'type' => 'docker_image',
                            'docker' => [
                                'image' => 'php:8.1-cli',
                            ],
                        ],
                    ]),
                ],
            ]);

            $copy = $process->copyWith([
                'envVars' => [
                    ['name' => 'BAZ', 'value' => 'XUQ'],
                ],
            ]);

            expect($copy->envVars)->toHaveLength(2);
            expect($copy->envVars)->toEqual([
                EnvVar::from(['name' => 'FOO', 'value' => 'BAR']),
                EnvVar::from(['name' => 'BAZ', 'value' => 'XUQ']),
            ]);
        });

        it('validates workers', function () {
            $this->expectExceptionObject(ValidationException::withMessages([
                'workers.0.name' => 'Worker new-worker does not exist',
            ]));

            $process = Process::make([
                'name' => 'service',
                'workers' => [
                    Worker::make([
                        'name' => 'worker',
                        'source' => [
                            'type' => 'docker_image',
                            'docker' => [
                                'image' => 'php:8.1-cli',
                            ],
                        ],
                    ]),
                ],
            ]);

            $process->copyWith([
                'workers' => [
                    ['name' => 'new-worker', 'source' => ['type' => 'docker_image', 'docker' => ['image' => 'php:8.1-cli']]],
                ],
            ]);
        });

        it('merges workers', function () {
            $process = Process::make([
                'name' => 'service',
                'workers' => [
                    Worker::make([
                        'name' => 'worker', 'source' => ['type' => 'docker_image', 'docker' => ['image' => 'php:8.1-cli']],
                    ]),
                ],
            ]);

            $copy = $process->copyWith([
                'workers' => [
                    ['name' => 'worker', 'source' => ['type' => 'docker_image', 'docker' => ['image' => 'php:9-cli']]],
                ],
            ]);

            expect($copy->workers)->toHaveLength(1);
            expect($copy->workers)->toEqual([
                Worker::make([
                    'name' => 'worker', 'source' => ['type' => 'docker_image', 'docker' => ['image' => 'php:9-cli']],
                ]),
            ]);
        });
    });
});
