<?php

namespace App\Actions\HouseKeeping;

use App\Models\NodeTaskGroup;
use App\Models\NodeTaskGroupType;
use App\Models\NodeTasks\PruneDockerRegistry\PruneDockerRegistryMeta;
use App\Models\NodeTaskType;
use App\Models\Scopes\TeamScope;
use App\Models\Service;
use Illuminate\Support\Str;

class PruneStaleDockerData
{
    public function prune(): void
    {
        $imagesToKeep = [];

        Service::withoutGlobalScope(TeamScope::class)->with(['latestDeployment'])->chunk(100, function (
            /* @var Service[] $services */
            $services
        ) use (&$imagesToKeep) {
            foreach ($services as $service) {
                if (! isset($imagesToKeep[$service->swarm_id])) {
                    $imagesToKeep[$service->swarm_id] = [
                        'swarm_id' => $service->swarm_id,
                        'team_id' => $service->team_id,
                        'images' => [],
                    ];
                }

                foreach ($service->latestDeployment->data->processes as $process) {
                    foreach ($process->workers as $worker) {
                        $image = $worker->getDockerImage($process);

                        if (Str::startsWith($image, 'registry.ptah.local:5050')) {
                            $imagesToKeep[$service->swarm_id]['images'][] = $image;
                        }
                    }
                }
            }
        });

        foreach ($imagesToKeep as $swarm) {
            if (count($swarm['images']) === 0) {
                continue;
            }

            $taskGroup = NodeTaskGroup::create([
                'type' => NodeTaskGroupType::PruneDockerData,
                'swarm_id' => $swarm['swarm_id'],
                'team_id' => $swarm['team_id'],
                'invoker_id' => $swarm['team_id'],
            ]);

            $taskGroup->tasks()->create([
                'type' => NodeTaskType::PruneDockerRegistry,
                'meta' => PruneDockerRegistryMeta::from([]),
                'payload' => [
                    'KeepImages' => $swarm['images'],
                ],
            ]);
        }
    }
}
