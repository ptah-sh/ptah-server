<?php

use App\Models\DeploymentData;
use App\Models\DeploymentData\EnvVar;
use App\Models\DeploymentData\Process;
use App\Models\DeploymentData\Worker;
use Illuminate\Validation\ValidationException;

describe(DeploymentData::class, function () {
    describe('::copyWith', function () {
        it('doesn\'t change the original data for empty arrays', function () {
            $original = DeploymentData::make([
                'networkName' => 'test',
                'internalDomain' => 'test',
                'processes' => [],
            ]);

            $copy = $original->copyWith([]);

            expect($copy)->toMatchSnapshot();
            expect($original)->not()->toBe($copy);
        });

        it('validates process names', function () {
            $this->expectExceptionObject(ValidationException::withMessages([
                'processes.0.name' => 'Process svc1 does not exist',
            ]));

            DeploymentData::make([
                'networkName' => 'test',
                'internalDomain' => 'test',
                'processes' => [
                    Process::make([
                        'name' => 'svc',
                    ]),
                ],
            ])->copyWith([
                'processes' => [
                    [
                        'name' => 'svc1',
                    ],
                ],
            ]);
        });

        it('merges env vars', function () {
            $original = DeploymentData::make([
                'networkName' => 'test',
                'internalDomain' => 'test',
                'processes' => [
                    Process::make([
                        'name' => 'service',
                        'workers' => [
                            Worker::make([
                                'name' => 'worker',
                                'source' => [
                                    'type' => 'docker_image',
                                    'docker' => [
                                        'image' => 'testimage',
                                    ],
                                ],
                            ]),
                        ],
                        'envVars' => [
                            EnvVar::validateAndCreate([
                                'name' => 'FOO',
                                'value' => 'BAR',
                            ]),
                            EnvVar::validateAndCreate([
                                'name' => 'BAZ',
                                'value' => 'QUX',
                            ]),
                        ],
                    ]),
                ],
            ]);

            $copy = $original->copyWith([
                'processes' => [
                    [
                        'name' => 'service',
                        'envVars' => [
                            [
                                'name' => 'BAZ',
                                'value' => 'XUQ',
                            ],
                        ],
                    ],
                ],
            ]);

            expect($copy->processes[0]->envVars)->toHaveLength(2);
            expect($copy->processes[0]->envVars)->toEqual([
                EnvVar::validateAndCreate([
                    'name' => 'FOO',
                    'value' => 'BAR',
                ]),
                EnvVar::validateAndCreate([
                    'name' => 'BAZ',
                    'value' => 'XUQ',
                ]),
            ]);
        });
    });
});
