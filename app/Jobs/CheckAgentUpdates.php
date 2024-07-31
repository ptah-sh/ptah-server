<?php

namespace App\Jobs;

use App\Models\AgentRelease;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class CheckAgentUpdates implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct() {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $json = Http::get('https://api.github.com/repos/ptah-sh/ptah-agent/releases/latest')->json();

        foreach ($json['assets'] as $asset) {
            preg_match('/^ptah-agent-(?<os>.+)-(?<arch>.+).bin$/', $asset['name'], $matches);
            if (empty($matches)) {
                continue;
            }

            $attrs = [
                'tag_name' => $json['tag_name'],
                'download_url' => $asset['browser_download_url'],
                'os' => $matches['os'],
                'arch' => $matches['arch'],
            ];

            AgentRelease::firstOrCreate($attrs, $attrs);
        }
    }
}
