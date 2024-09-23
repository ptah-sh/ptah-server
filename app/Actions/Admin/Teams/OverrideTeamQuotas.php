<?php

namespace App\Actions\Admin\Teams;

use App\Models\QuotasOverride;
use App\Models\Team;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

class OverrideTeamQuotas
{
    use AsAction;

    public function authorize(Request $request): bool
    {
        return $request->user()->isAdmin();
    }

    public function rules(Request $request): array
    {
        return [
            'quotas' => QuotasOverride::getValidationRules($request->get('quotas')),
        ];
    }

    public function handle(Team $team, QuotasOverride $quotasOverride)
    {
        $team->quotas_override = $quotasOverride;

        $team->save();
    }

    public function asController(Team $team, Request $request)
    {
        $this->handle($team, QuotasOverride::from($request->get('quotas')));

        return redirect()->route('admin.teams.show', $team->id)->with('message', 'Team quotas updated successfully.');
    }
}
