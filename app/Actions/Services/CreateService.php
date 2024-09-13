<?php

namespace App\Actions\Services;

use App\Models\DeploymentData;
use App\Models\Service;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateService
{
    use AsAction;

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'deploymentData' => ['required', 'array'],
        ];
    }

    public function authorize(ActionRequest $request): bool
    {
        return true; // Authorization will be handled by policies
    }

    public function handle(User $user, Team $team, string $name, DeploymentData $deploymentData): Service
    {
        return DB::transaction(function () use ($user, $team, $name, $deploymentData) {
            $swarm = $team->swarms()->firstOrFail();

            $service = new Service([
                'name' => $name,
                'team_id' => $team->id,
                'swarm_id' => $swarm->id,
            ]);

            $service->save();

            StartDeployment::run($user, $service, $deploymentData);

            return $service;
        });
    }

    public function asController(ActionRequest $request)
    {
        $user = $request->user();
        $team = $user->currentTeam;
        $quotas = $team->quotas();

        $quotas->services->ensureQuota();

        $validated = $request->validated();
        $deploymentData = DeploymentData::validateAndCreate($validated['deploymentData']);

        $service = $this->handle($user, $team, $validated['name'], $deploymentData);

        return redirect()->route('services.deployments', $service)
            ->with('success', 'Service created and deployment scheduled successfully.');
    }
}
