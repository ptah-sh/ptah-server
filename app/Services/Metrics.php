<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

// Multitenancy for single node: https://docs.victoriametrics.com/single-server-victoriametrics/#prometheus-querying-api-enhancements
class Metrics
{
    public static function importPrometheusMetrics($nodeId, $metrics)
    {
        $query = '';
        $query .= '?extra_label=node_id='.$nodeId;

        return Http::withBody(implode("\n", $metrics), 'text/plain')->post(config('database.victoriametrics.url').'/api/v1/import/prometheus'.$query);
    }

    public static function getMetrics($query, $nodeIds)
    {
        return Http::asForm()->post(config('database.victoriametrics.url').'/api/v1/query', [
            'query' => $query,
            'step' => '30s',
            'extra_filters' => '{node_id=~"'.implode('|', $nodeIds).'"}',
            'latency_offset' => '5s',
        ])->json();
    }

    public static function getMetricsRange($query, $nodeIds)
    {
        return Http::asForm()->post(config('database.victoriametrics.url').'/api/v1/query_range', [
            'query' => $query,
            'start' => now()->subMinutes(5)->subSeconds(30)->getTimestampMs() / 1000,
            'end' => now()->subSeconds(30)->getTimestampMs() / 1000,
            'step' => '5s',
            'extra_filters' => '{node_id=~"'.implode('|', $nodeIds).'"}',
            'latency_offset' => '5s',
        ])->json();
    }
}
