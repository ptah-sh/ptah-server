<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSwarmRequest;
use App\Http\Requests\UpdateSwarmRequest;
use App\Models\DockerConfigKind;
use App\Models\NodeTaskGroup;
use App\Models\NodeTaskGroupType;
use App\Models\NodeTasks\CheckRegistryAuth\CheckRegistryAuthMeta;
use App\Models\NodeTasks\CreateConfig\CreateConfigMeta;
use App\Models\NodeTasks\CreateRegistryAuth\CreateRegistryAuthMeta;
use App\Models\NodeTaskType;
use App\Models\Swarm;
use App\Models\SwarmData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SwarmController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSwarmRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Swarm $swarm)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Swarm $swarm)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSwarmRequest $request, Swarm $swarm)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Swarm $swarm)
    {
        //
    }

    public function updateDockerRegistries(Swarm $swarm, Request $request)
    {
        // TODO: check if the registry is in use before deleting it
        DB::transaction(function () use ($swarm, $request) {
            $swarmData = SwarmData::validateAndCreate([
                ...$swarm->data->toArray(),
                'registries' => $request->get('registries'),
            ]);

            $swarmData->registriesRev += 1;

            $tasks = [];

            foreach ($swarmData->registries as $registry) {
                $previous = $registry->dockerName ? $swarm->data->findRegistry($registry->dockerName) : null;
                if ($previous) {
                    if ($registry->sameAs($previous)) {
                        $registry->dockerName = $previous->dockerName;

                        continue;
                    }
                }

                $registry->dockerName = dockerize_name('registry_r' . $swarmData->registriesRev . '_' . $registry->name);

                $taskMeta = [
                    'registryName' => $registry->name,
                ];

                $tasks[] = [
                    'type' => NodeTaskType::CreateRegistryAuth,
                    'meta' => CreateRegistryAuthMeta::validateAndCreate($taskMeta),
                    'payload' => [
                        'PrevConfigName' => $previous?->dockerName,
                        'AuthConfigSpec' => [
                            'ServerAddress' => $registry->serverAddress,
                            'Username' => $registry->username,
                            'Password' => $registry->password,
                        ],
                        'SwarmConfigSpec' => [
                            'Name' => $registry->dockerName,
                            'Labels' => dockerize_labels([
                                'kind' => DockerConfigKind::RegistryCredentials->value,
                                'revision' => $swarmData->registriesRev,
                            ]),
                        ],
                    ]
                ];

                $tasks[] = [
                    'type' => NodeTaskType::CheckRegistryAuth,
                    'meta' => CheckRegistryAuthMeta::validateAndCreate($taskMeta),
                    'payload' => [
                        'RegistryConfigName' => $registry->dockerName,
                    ]
                ];
            }

            $swarm->data = $swarmData;
            $swarm->save();

            if (!empty($tasks)) {
                $taskGroup = NodeTaskGroup::create([
                    'type' => NodeTaskGroupType::UpdateDockerRegistries,
                    'swarm_id' => $swarm->id,
                    'invoker_id' => auth()->user()->id,
                ]);

                $taskGroup->tasks()->createMany($tasks);
            }
        });

//        return back();
    }
}
