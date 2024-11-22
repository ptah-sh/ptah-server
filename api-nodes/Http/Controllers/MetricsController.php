<?php

namespace ApiNodes\Http\Controllers;

use App\Models\DeploymentData\Caddy;
use App\Models\DeploymentData\Process;
use App\Models\Node;
use App\Services\Metrics;
use App\Util\Promexport;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Log\Logger;
use Illuminate\Support\Str;

class MetricsController
{
    const HISTOGRAM_LABELS = [
        '0.005',
        '0.01',
        '0.025',
        '0.05',
        '0.1',
        '0.25',
        '0.5',
        '1',
        '2.5',
        '5',
        '10',
        '+Inf',
    ];

    const DISK_USAGE_PATHS = [
        '/',
    ];

    const DISK_IO_DEVICES = [
        'sda',
        'sdb',
        'sdc',
    ];

    const DISK_IO_OPERATIONS = [
        'reads',
        'writes',
    ];

    // Please don't judge me for this code. I'm a PHP developer.
    public function __invoke(Request $request, Logger $log, Node $node)
    {
        // TODO: cache this with the new Laravel cache system (stale-while-revalidate from the recent release)
        $interfaces = collect($node->data->host->networks)->pluck('if_name')->filter(function ($interface) {
            // Collect only the interfaces that start with 'eth' for now
            return Str::startsWith($interface, 'eth');
        })->unique()->toArray();

        $services = $node->team->services->mapWithKeys(function ($service) {
            $processes = collect($service->latestDeployment->data->processes);

            $ruleIds = $processes->mapWithKeys(function (Process $process) {
                $caddyIds = collect($process->caddy)->pluck('id');
                $redirectRuleIds = collect($process)
                    ->flatMap(fn (Process $process) => collect($process->caddy)->flatMap(fn (Caddy $caddy) => collect($caddy->redirectRules)->pluck('id')))
                    ->unique()
                    ->toArray();
                $rewriteRuleIds = collect($process)
                    ->flatMap(fn (Process $process) => collect($process->caddy)->flatMap(fn (Caddy $caddy) => collect($caddy->rewriteRules)->pluck('id')))
                    ->unique()
                    ->toArray();

                $ruleIds = $caddyIds->merge($redirectRuleIds)->merge($rewriteRuleIds)->toArray();

                $serversNames = collect($process->caddy)->pluck('publishedPort')->unique()->map(function ($port) {
                    return match ($port) {
                        80 => 'http',
                        443 => 'https',
                        default => 'listen_'.$port,
                    };
                })->toArray();

                return [
                    $process->name => [
                        'ruleIds' => $ruleIds,
                        'serversNames' => $serversNames,
                    ],
                ];
            })->toArray();

            return [$service->id => $ruleIds];
        })->toArray();

        $services['ptah_404'] = [
            'ptah_404' => [
                'ruleIds' => ['ptah_404'],
                'serversNames' => ['http', 'https'],
            ],
        ];

        foreach ($request->all() as $metricsDoc) {
            if ($metricsDoc === null) {
                continue;
            }

            $ingestMetrics = [];

            $lines = explode("\n", $metricsDoc);
            foreach ($lines as $line) {
                if (empty($line) || strpos($line, '#') === 0) {
                    continue;
                }

                $metric = Promexport::parseLine($line);
                if ($metric) {
                    $labels = $metric['labels'];

                    switch ($metric['metric']) {
                        case 'ptah_caddy_http_requests_duration_bucket':
                        case 'ptah_caddy_http_requests_ttfb_bucket':
                            if (empty($labels['le'])) {
                                break;
                            }

                            if (! in_array($labels['le'], self::HISTOGRAM_LABELS)) {
                                break;
                            }

                            // no break, fall through
                        case 'ptah_caddy_http_requests_count':
                        case 'ptah_caddy_http_requests_duration_count':
                        case 'ptah_caddy_http_requests_duration_sum':
                        case 'ptah_caddy_http_requests_ttfb_count':
                        case 'ptah_caddy_http_requests_ttfb_sum':
                            if (empty($labels['status_code'])) {
                                break;
                            }

                            if ($labels['status_code'] < 100 || $labels['status_code'] > 599) {
                                break;
                            }

                            // no break, fall through
                        case 'ptah_caddy_http_requests_in_flight':
                            if (empty($labels['service_id']) || empty($labels['process_id']) || empty($labels['server_name']) || empty($labels['rule_id'])) {
                                break;
                            }

                            if (! isset($services[$labels['service_id']]) && $labels['service_id'] !== 'ptah_404') {
                                break;
                            }

                            $service = $services[$labels['service_id']];

                            if (! isset($service[$labels['process_id']])) {
                                break;
                            }

                            $process = $service[$labels['process_id']];

                            if (! in_array($labels['server_name'], $process['serversNames'])) {
                                break;
                            }

                            if (! in_array($labels['rule_id'], $process['ruleIds'])) {
                                break;
                            }

                            $ingestMetrics[] = $line;

                            break;
                        case 'ptah_node_disk_io_ops_count':
                            if (empty($labels['device']) || empty($labels['operation'])) {
                                break;
                            }

                            if (! in_array($labels['device'], self::DISK_IO_DEVICES) || ! in_array($labels['operation'], self::DISK_IO_OPERATIONS)) {
                                break;
                            }

                            $ingestMetrics[] = $line;

                            break;
                        case 'ptah_node_network_rx_bytes':
                        case 'ptah_node_network_tx_bytes':
                            if (empty($labels['interface'])) {
                                break;
                            }

                            if (! in_array($labels['interface'], $interfaces)) {
                                break;
                            }

                            $ingestMetrics[] = $line;

                            break;
                        case 'ptah_node_disk_free_bytes':
                        case 'ptah_node_disk_total_bytes':
                        case 'ptah_node_disk_used_bytes':
                            if (empty($labels['path'])) {
                                break;
                            }

                            if (! in_array($labels['path'], self::DISK_USAGE_PATHS)) {
                                break;
                            }

                            $ingestMetrics[] = $line;

                            break;
                        case 'ptah_node_cpu_idle':
                        case 'ptah_node_cpu_nice':
                        case 'ptah_node_cpu_system':
                        case 'ptah_node_cpu_total':
                        case 'ptah_node_cpu_user':
                        case 'ptah_node_load_avg_1m':
                        case 'ptah_node_load_avg_5m':
                        case 'ptah_node_load_avg_15m':
                        case 'ptah_node_memory_total_bytes':
                        case 'ptah_node_memory_used_bytes':
                        case 'ptah_node_memory_free_bytes':
                        case 'ptah_node_swap_total_bytes':
                        case 'ptah_node_swap_used_bytes':
                        case 'ptah_node_swap_free_bytes':
                        case 'ptah_node_uptime_seconds':
                            $ingestMetrics[] = $line;

                            break;
                        default:
                            // unknown metric

                            break;
                    }
                }
            }

            $response = Metrics::importPrometheusMetrics($node->id, $ingestMetrics);
        }

        $response = new Response('{}', 204);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
