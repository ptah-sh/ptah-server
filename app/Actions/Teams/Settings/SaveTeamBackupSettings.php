<?php

namespace App\Actions\Teams\Settings;

use App\Models\Team;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class SaveTeamBackupSettings
{
    use AsAction;

    public function handle(Team $team, int $retentionDays): void
    {
        $team->update(['backup_retention_days' => $retentionDays]);
    }

    public function rules(): array
    {
        return [
            'retentionDays' => ['required', 'integer', 'min:1', 'max:30'],
        ];
    }

    public function asController(ActionRequest $request, Team $team): void
    {
        $this->handle($team, $request->input('retentionDays'));
    }
}
