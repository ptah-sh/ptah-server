<?php

return [
    /**
     * It is possible to skip the PHP reflection analysis of data objects
     * when running in production. This will speed up the package. You
     * can configure where data objects are stored and which cache
     * store should be used.
     *
     * Structures are cached forever as they'll become stale when your
     * application is deployed with changes. You can set a duration
     * in seconds if you want the cache to clear after a certain
     * timeframe.
     */
    'structure_caching' => [
        'enabled' => true,
        'directories' => [app_path('')],
        'cache' => [
            'store' => 'file',
            'prefix' => 'laravel-data',
            'duration' => null,
        ],
        'reflection_discovery' => [
            'enabled' => true,
            'base_path' => base_path(),
            'root_namespace' => null,
        ],
    ],
];
