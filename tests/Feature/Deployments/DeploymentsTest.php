<?php

use App\Models\DeploymentData;
use App\Models\NodeTaskType;
use App\Models\Service;
use App\Models\Swarm;
use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Json;

describe('Secret Vars', function () {
    describe('Deployment Data', function () {
        beforeEach(function () {
            $user = User::factory()->withPersonalTeam()->create();

            $this->actingAs($user);

            $swarm = Swarm::factory()->create([
                'team_id' => $user->personalTeam()->id,
            ]);

            $this->service = Service::factory()->create([
                'swarm_id' => $swarm->id,
                'team_id' => $user->personalTeam()->id,
            ]);
        });

        test('create deployments without any extra data', function () {
            $deployment = $this->service->deploy(DeploymentData::make([

            ]));

            expect($deployment->data->secretVars->dockerName)->toBeEmpty()
                ->and($deployment->data->secretVars->vars)->toHaveCount(0);
        });

        test('add single secret var', function () {
            $this->service->deploy(DeploymentData::make([

            ]));

            $deployment = $this->service->deploy(DeploymentData::make([
                'secretVars' => DeploymentData\SecretVars::from([
                    'vars' => [
                        new DeploymentData\EnvVar('foo', 'var'),
                    ],
                ]),
            ]));

            expect($deployment->data->secretVars->dockerName)->toEqual("svc_{$this->service->id}_dpl_{$deployment->id}_secret_vars");
        });

        test('re-deploy without changing secret vars', function () {
            $previous = $this->service->deploy(DeploymentData::make([
                'secretVars' => DeploymentData\SecretVars::from([
                    'vars' => [
                        new DeploymentData\EnvVar('foo', 'var'),
                    ],
                ]),
            ]));

            $deployment = $this->service->deploy($previous->data);

            expect($deployment->data->secretVars->dockerName)->toEqual("svc_{$this->service->id}_dpl_{$previous->id}_secret_vars");
        });

        test('deploy assigning additional vars', function () {
            $this->service->deploy(DeploymentData::make([
                'secretVars' => DeploymentData\SecretVars::from([
                    'vars' => [
                        new DeploymentData\EnvVar('foo', 'var'),
                    ],
                ]),
            ]));

            $deployment = $this->service->deploy(DeploymentData::make([
                'secretVars' => DeploymentData\SecretVars::from([
                    'vars' => [
                        new DeploymentData\EnvVar('foo', 'var'),
                        new DeploymentData\EnvVar('bar', 'var'),
                    ],
                ]),
            ]));

            expect($deployment->data->secretVars->dockerName)->toEqual("svc_{$this->service->id}_dpl_{$deployment->id}_secret_vars");
        });
    });

    describe('Node Task', function () {
        beforeEach(function () {
            $user = User::factory()->withPersonalTeam()->create();

            $this->actingAs($user);

            $swarm = Swarm::factory()->create([
                'team_id' => $user->personalTeam()->id,
            ]);

            $this->service = Service::factory()->create([
                'swarm_id' => $swarm->id,
                'team_id' => $user->personalTeam()->id,
            ]);
        });

        test('create deployments without any extra data', function () {
            $deployment = $this->service->deploy(DeploymentData::make([

            ]));

            $task = $deployment->taskGroup->tasks()->where('type', NodeTaskType::CreateService)->sole();

            $payload = Json::decode($task->payload);

            expect($payload['SecretVars'])->toBeEmpty();
        });

        test('add single secret var', function () {
            $deployment = $this->service->deploy(DeploymentData::make([
                'secretVars' => DeploymentData\SecretVars::from([
                    'vars' => [
                        new DeploymentData\EnvVar('foo', 'var'),
                    ],
                ]),
            ]));

            $task = $deployment->taskGroup->tasks()->where('type', NodeTaskType::CreateService)->sole();

            $payload = Json::decode($task->payload);

            expect($payload['SecretVars'])->toMatchArray([
                'ConfigName' => "svc_{$this->service->id}_dpl_{$deployment->id}_secret_vars",
                'Values' => [
                    'foo' => 'var',
                ],
            ]);
        });

        test('re-deploy without changing secret vars', function () {
            $previous = $this->service->deploy(DeploymentData::make([
                'secretVars' => DeploymentData\SecretVars::from([
                    'vars' => [
                        new DeploymentData\EnvVar('foo', 'var'),
                    ],
                ]),
            ]));

            $deployment = $this->service->deploy(DeploymentData::make([
                'secretVars' => DeploymentData\SecretVars::from([
                    'vars' => [
                        new DeploymentData\EnvVar('foo', null),
                    ],
                ]),
            ]));

            $task = $deployment->taskGroup->tasks()->where('type', NodeTaskType::UpdateService)->sole();

            $payload = Json::decode($task->payload);

            expect($payload['SecretVars'])->toMatchArray([
                'ConfigName' => "svc_{$this->service->id}_dpl_{$deployment->id}_secret_vars",
                'Values' => [],
                'Preserve' => ['foo'],
                'PreserveFromConfig' => "svc_{$this->service->id}_dpl_{$previous->id}_secret_vars",
            ]);
        });
    });
});
