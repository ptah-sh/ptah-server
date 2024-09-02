<?php

namespace App\Console\Commands;

use App\Actions\Nodes\InitCluster;
use App\Actions\Services\StartDeployment;
use App\Models\DeploymentData;
use App\Models\DeploymentData\LaunchMode;
use App\Models\Network;
use App\Models\Node;
use App\Models\NodeTask;
use App\Models\NodeTasks\DummyTaskResult;
use App\Models\Service;
use App\Models\Team;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SelfHostPtah extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:self-host-ptah';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate installation data for self-hosting';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user = User::create([
            'name' => 'Self Host',
            'email' => 'self-hosted@localhost',
            'password' => 'self_hosted_password',
        ]);

        User::where('email', 'self-hosted@localhost')->update(['password' => 'self_hosted_password']);

        $team = Team::make()->forceFill([
            'personal_team' => true,
            'billing_name' => 'Self Host',
            'billing_email' => 'self-host@localhost',
            'name' => 'Self Host',
            'user_id' => $user->id,
        ]);

        $team->save();

        config([
            'billing.enabled' => false,
            'ptah.services.slug.vocabulary' => ['ptah'],
            'ptah.services.slug.adjectives' => ['happy'],
        ]);

        $node = Node::create([
            'name' => 'default',
            'ip' => '192.168.1.1',
            'team_id' => $team->id,
        ]);

        $node->update(['agent_token' => 'AGENT_TOKEN']);

        InitCluster::run($user, $node, '192.168.1.1');

        $network = Network::first();

        $service = Service::create([
            'name' => 'ptah',
            'team_id' => $team->id,
            'swarm_id' => $node->swarm_id,
        ]);

        StartDeployment::run($user, $service, DeploymentData::validateAndCreate([
            'networkName' => $network->docker_name,
            'internalDomain' => 'server.ptah.local',
            'placementNodeId' => $node->id,
            'processes' => [
                [
                    'name' => 'pg',
                    'launchMode' => LaunchMode::Daemon->value,
                    'dockerRegistryId' => null,
                    'dockerImage' => 'bitnami/postgresql:16',
                    'releaseCommand' => [
                        'command' => null,
                    ],
                    'command' => null,
                    'backups' => [],
                    'workers' => [],
                    'envVars' => [
                        [
                            'name' => 'POSTGRESQL_USERNAME',
                            'value' => 'ptah_sh',
                        ],
                        [
                            'name' => 'POSTGRESQL_PASSWORD',
                            'value' => 'ptah_sh',
                        ],
                        [
                            'name' => 'POSTGRESQL_DATABASE',
                            'value' => 'ptah_sh',
                        ],
                    ],
                    'secretVars' => [
                        'vars' => [],
                    ],
                    'configFiles' => [],
                    'secretFiles' => [],
                    'volumes' => [
                        [
                            'id' => 'volume-'.Str::random(11),
                            'name' => 'data',
                            'path' => '/bitnami/postgresql',
                        ],
                    ],
                    'replicas' => 1,
                    'ports' => [],
                    'caddy' => [],
                    'fastcgiVars' => null,
                    'redirectRules' => [],
                ],
                [
                    'name' => 'pool',
                    'launchMode' => LaunchMode::Daemon->value,
                    'dockerRegistryId' => null,
                    'dockerImage' => 'bitnami/pgbouncer',
                    'releaseCommand' => [
                        'command' => null,
                    ],
                    'command' => null,
                    'backups' => [],
                    'workers' => [],
                    'envVars' => [
                        [
                            'name' => 'PGBOUNCER_POOL_MODE',
                            'value' => 'session',
                        ],
                        [
                            'name' => 'POSTGRESQL_USERNAME',
                            'value' => 'ptah_sh',
                        ],
                        [
                            'name' => 'POSTGRESQL_PASSWORD',
                            'value' => 'ptah_sh',
                        ],
                        [
                            'name' => 'POSTGRESQL_DATABASE',
                            'value' => 'ptah_sh',
                        ],
                        [
                            'name' => 'POSTGRESQL_HOST',
                            'value' => 'pg.server.ptah.local',
                        ],
                        [
                            'name' => 'PGBOUNCER_PORT',
                            'value' => '5432',
                        ],
                        [
                            'name' => 'PGBOUNCER_DATABASE',
                            'value' => 'ptah_sh',
                        ],
                    ],
                    'secretVars' => [
                        'vars' => [],
                    ],
                    'configFiles' => [],
                    'secretFiles' => [],
                    'volumes' => [
                        [
                            'id' => 'volume-'.Str::random(11),
                            'name' => 'data',
                            'path' => '/bitnami/postgresql',
                        ],
                    ],
                    'replicas' => 1,
                    'ports' => [],
                    'caddy' => [],
                    'fastcgiVars' => null,
                    'redirectRules' => [],
                ],
                [
                    'name' => 'app',
                    'launchMode' => LaunchMode::Daemon->value,
                    'dockerRegistryId' => null,
                    'dockerImage' => 'ghcr.io/ptah-sh/ptah-server:v0.22.8',
                    'releaseCommand' => [
                        'command' => 'php artisan config:cache && php artisan migrate --no-interaction --verbose --ansi --force',
                    ],
                    'command' => null,
                    'backups' => [],
                    'workers' => [
                        [
                            'name' => 'schedule',
                            'replicas' => 1,
                            'command' => 'php artisan config:cache && php artisan schedule:work',
                        ],
                        [
                            'name' => 'queue',
                            'replicas' => 1,
                            'command' => 'php artisan config:cache && php artisan queue:work',
                        ],
                    ],
                    'envVars' => [
                        [
                            'name' => 'APP_ENV',
                            'value' => 'production',
                        ],
                        [
                            'name' => 'APP_KEY',
                            'value' => 'base64:APP_KEY',
                        ],
                        [
                            'name' => 'BCRYPT_ROUNDS',
                            'value' => '12',
                        ],
                        [
                            'name' => 'DB_CONNECTION',
                            'value' => 'pgsql',
                        ],
                        [
                            'name' => 'DB_HOST',
                            'value' => 'pool.server.ptah.local',
                        ],
                        [
                            'name' => 'DB_DATABASE',
                            'value' => 'ptah_sh',
                        ],
                        [
                            'name' => 'DB_USERNAME',
                            'value' => 'ptah_sh',
                        ],
                        [
                            'name' => 'DB_PASSWORD',
                            'value' => 'ptah_sh',
                        ],
                        [
                            'name' => 'LOG_CHANNEL',
                            'value' => 'errorlog',
                        ],
                        [
                            'name' => 'APP_URL',
                            'value' => 'localhost',
                        ],
                        [
                            'name' => 'BILLING_ENABLED',
                            'value' => 'false',
                        ],
                    ],
                    'secretVars' => [
                        'vars' => [],
                    ],
                    'configFiles' => [],
                    'secretFiles' => [],
                    'volumes' => [],
                    'replicas' => 2,
                    'ports' => [],
                    'caddy' => [
                        [
                            'id' => 'caddy-'.Str::random(11),
                            'targetProtocol' => 'http',
                            'targetPort' => 8080,
                            'publishedPort' => 80,
                            'domain' => 'ptah.localhost',
                            'path' => '/*',
                        ],
                    ],
                    'fastcgiVars' => null,
                    'redirectRules' => [],
                ],
            ],
        ]));

        $this->updateNodeTasksStatus($node);

        $this->updateDates();

        $tasks = NodeTask::all()->map(fn (NodeTask $task) => $task->only(['id', 'type', 'payload']));

        $this->line(Json::encode($tasks));
    }

    public function updateDates()
    {
        $tables = Schema::getTableListing();
        $targetDate = '2024-09-01 00:00:00';

        foreach ($tables as $table) {
            if (in_array($table, ['jobs', 'job_batches'])) {
                continue;
            }

            $query = DB::table($table);

            if (Schema::hasColumn($table, 'created_at')) {
                $query->update(['created_at' => $targetDate]);
            }

            if (Schema::hasColumn($table, 'updated_at')) {
                $query->update(['updated_at' => $targetDate]);
            }

            if (Schema::hasColumn($table, 'started_at')) {
                $query->update(['started_at' => $targetDate]);
            }

            if (Schema::hasColumn($table, 'ended_at')) {
                $query->update(['ended_at' => $targetDate]);
            }

            if (Schema::hasColumn($table, 'email_verified_at')) {
                $query->update(['email_verified_at' => $targetDate]);
            }
        }
    }

    public function updateNodeTasksStatus(Node $node)
    {
        NodeTask::all()->each(function (NodeTask $task) use ($node) {
            $task->start($node);
            // TODO: the complete call once https://github.com/ptah-sh/ptah-agent/issues/33 is done
            $task->complete(new DummyTaskResult);
        });
    }
}
