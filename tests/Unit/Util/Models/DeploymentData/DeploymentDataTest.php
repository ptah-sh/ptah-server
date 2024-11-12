<?php

use App\Models\DeploymentData;
use App\Models\DeploymentData\AppSource;
use App\Models\DeploymentData\Caddy;
use App\Models\DeploymentData\ConfigFile;
use App\Models\DeploymentData\EnvVar;
use App\Models\DeploymentData\NodePort;
use App\Models\DeploymentData\Process;
use App\Models\DeploymentData\SecretFile;
use App\Models\DeploymentData\Volume;
use App\Models\DeploymentData\Worker;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Mockery\MockInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use function Pest\Laravel\mock;

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

        // The test should fix this todo
        // TODO: there is a bug. If the $result['processes'] array has multiple items and only second (by name) item is in $attributes, it will copy the first item instead of the second.
        it('copies correct data', function () {});
    });

    describe('::fork', function () {
        it('validates process names', function () {
            $this->expectExceptionObject(new NotFoundHttpException('Process not found'));

            DeploymentData::make([
                'networkName' => 'test',
                'internalDomain' => 'test',
                'processes' => [
                    Process::make(['name' => 'svc']),
                ],
            ])->fork('ref', ['name' => 'svc1'], ['name' => 'worker1'], []);
        });

        it('validates worker names', function () {
            $this->expectExceptionObject(new NotFoundHttpException('Worker not found'));

            DeploymentData::make([
                'networkName' => 'test',
                'internalDomain' => 'test',
                'processes' => [
                    Process::make(['name' => 'svc']),
                ],
            ])->fork('ref', ['name' => 'svc'], ['name' => 'worker1'], []);
        });

        it('forks specific processes', function () {
            $deploymentData = DeploymentData::make([
                'networkName' => 'test',
                'internalDomain' => 'test',
                'processes' => [
                    Process::make(['name' => 'svc']),
                    Process::make([
                        'name' => 'other',
                        'workers' => [
                            Worker::make([
                                'name' => 'not the one I want to see',
                                'source' => [
                                    'type' => 'docker_image',
                                    'docker' => [
                                        'image' => 'testimage',
                                    ],
                                ],
                            ]),
                            Worker::make([
                                'name' => 'other-worker',
                                'source' => [
                                    'type' => 'docker_image',
                                    'docker' => [
                                        'image' => 'testimage',
                                    ],
                                ],
                            ]),
                        ],
                    ]),
                ],
            ]);

            $forked = $deploymentData->fork(
                'ref',
                ['name' => 'other'],
                ['name' => 'other-worker', 'source' => [
                    'type' => 'docker_image',
                    'docker' => [
                        'image' => 'other-image',
                    ],
                ],
                ],
            );

            expect($forked->processes)->toHaveLength(1);
            expect($forked->processes[0]->workers)->toHaveLength(1);
            expect($forked->processes[0]->workers[0]->name)->toBe('other-worker');
            expect($forked->processes[0]->workers[0]->source->docker->image)->toBe('other-image');
        });

        it('clears dockerNames', function () {
            Validator::shouldReceive('make')
                ->andReturnUsing(function ($data) {
                    $mock = mock(\Illuminate\Validation\Validator::class, function (MockInterface $mock) use ($data) {
                        $mock->shouldReceive('validate')->andReturn($data);
                        $mock->shouldReceive('validated')->andReturn([]);
                    });

                    return $mock;
                });

            $deploymentData = DeploymentData::make([
                'networkName' => 'test',
                'internalDomain' => 'test',
                'processes' => [
                    Process::make([
                        'name' => 'svc',
                        'dockerName' => 'svc_1_nginx_mkr_main',
                        'placementNodeId' => 1,
                        'workers' => [
                            Worker::make([
                                'name' => 'main',
                                'dockerName' => 'main',
                                'source' => [
                                    'type' => 'docker_image',
                                    'docker' => [
                                        'image' => 'testimage',
                                    ],
                                ],
                            ]),
                        ],
                        'configFiles' => [
                            ConfigFile::from([
                                'id' => 'config1',
                                'dockerName' => 'config1',
                                'path' => '/config1',
                                'content' => 'contents',
                            ]),
                        ],
                        'secretFiles' => [
                            SecretFile::from([
                                'id' => 'secret1',
                                'dockerName' => 'secret1',
                                'path' => '/secret1',
                                'content' => 'contents',
                            ]),
                        ],
                        'volumes' => [
                            Volume::from([
                                'id' => 'volume1',
                                'name' => 'volume1',
                                'dockerName' => 'volume1',
                                'path' => '/volume1',
                            ]),
                        ],
                    ]),
                ],
            ]);

            $forked = $deploymentData->fork('pr-123', ['name' => 'svc'], ['name' => 'main'], []);

            expect($forked->processes[0]->dockerName)->not()->toBeNull('Process dockerName should not be null');
            expect($forked->processes[0]->workers[0]->dockerName)->toBeNull();
            expect($forked->processes[0]->volumes[0]->dockerName)->toBeNull();
            expect($forked->processes[0]->configFiles[0]->dockerName)->toBeNull();
            expect($forked->processes[0]->secretFiles[0]->dockerName)->toBeNull();
        });

        it('preserves all other data', function () {
            $deploymentData = DeploymentData::make([
                'networkName' => 'test',
                'internalDomain' => 'test',
                'processes' => [
                    Process::make([
                        'name' => 'svc',
                        'workers' => [
                            Worker::make([
                                'name' => 'main',
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
                        ],
                    ]),
                ],
            ]);

            $forked = $deploymentData->fork('ref', ['name' => 'svc'], ['name' => 'main'], []);

            expect($forked->processes[0]->name)->toBe('svc');
            expect($forked->processes[0]->envVars)->toHaveLength(1);
            expect($forked->processes[0]->envVars[0]->name)->toBe('FOO');
            expect($forked->processes[0]->envVars[0]->value)->toBe('BAR');
        });

        it('removes ports and HTTP bindings', function () {
            $deploymentData = DeploymentData::make([
                'networkName' => 'test',
                'internalDomain' => 'test',
                'processes' => [
                    Process::make([
                        'name' => 'svc',
                        'workers' => [
                            Worker::make([
                                'name' => 'main',
                                'source' => [
                                    'type' => 'docker_image',
                                    'docker' => [
                                        'image' => 'testimage',
                                    ],
                                ],
                            ]),
                        ],
                        'ports' => [
                            NodePort::from([
                                'targetPort' => 80,
                                'publishedPort' => 80,
                            ]),
                        ],
                        'caddy' => [
                            Caddy::from([
                                'id' => 'caddy1',
                                'targetProtocol' => 'http',
                                'targetPort' => 80,
                                'publishedPort' => 80,
                                'domain' => 'test',
                                'path' => '/',
                            ]),
                        ],
                    ]),
                ],
            ]);

            $forked = $deploymentData->fork('pr-123', ['name' => 'svc'], ['name' => 'main'], []);

            expect($forked->processes[0]->ports)->toBeEmpty();
            expect($forked->processes[0]->caddy)->toBeEmpty();
        });

        it('sets ref-based internalDomain and dockerName', function () {
            $deploymentData = DeploymentData::make([
                'networkName' => 'test',
                'internalDomain' => 'test',
                'processes' => [
                    Process::make([
                        'name' => 'svc',
                        'dockerName' => 'svc_1_nginx_mkr_main',
                        'workers' => [
                            Worker::make([
                                'name' => 'main',
                                'source' => AppSource::from([
                                    'type' => 'docker_image',
                                    'docker' => [
                                        'image' => 'testimage',
                                    ],
                                ]),
                            ]),
                        ],
                    ]),
                ],
            ]);

            $forked = $deploymentData->fork('pr-123', ['name' => 'svc'], ['name' => 'main'], []);

            expect($forked->internalDomain)->toBe('pr-123.test');
            expect($forked->processes[0]->dockerName)->toBe('svc_1_nginx_mkr_main_pr_123');
        });
    });
});
