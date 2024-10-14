<?php

namespace App\Actions\Admin\Teams;

/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(\App\Models\Team $team, \App\Models\QuotasOverride $quotasOverride)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(\App\Models\Team $team, \App\Models\QuotasOverride $quotasOverride)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(\App\Models\Team $team, \App\Models\QuotasOverride $quotasOverride)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, \App\Models\Team $team, \App\Models\QuotasOverride $quotasOverride)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, \App\Models\Team $team, \App\Models\QuotasOverride $quotasOverride)
 * @method static dispatchSync(\App\Models\Team $team, \App\Models\QuotasOverride $quotasOverride)
 * @method static dispatchNow(\App\Models\Team $team, \App\Models\QuotasOverride $quotasOverride)
 * @method static dispatchAfterResponse(\App\Models\Team $team, \App\Models\QuotasOverride $quotasOverride)
 * @method static mixed run(\App\Models\Team $team, \App\Models\QuotasOverride $quotasOverride)
 */
class OverrideTeamQuotas {}

namespace App\Actions\Nodes;

/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(\App\Models\User $user, \App\Models\Node $node, string $advertiseAddr)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(\App\Models\User $user, \App\Models\Node $node, string $advertiseAddr)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(\App\Models\User $user, \App\Models\Node $node, string $advertiseAddr)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, \App\Models\User $user, \App\Models\Node $node, string $advertiseAddr)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, \App\Models\User $user, \App\Models\Node $node, string $advertiseAddr)
 * @method static dispatchSync(\App\Models\User $user, \App\Models\Node $node, string $advertiseAddr)
 * @method static dispatchNow(\App\Models\User $user, \App\Models\Node $node, string $advertiseAddr)
 * @method static dispatchAfterResponse(\App\Models\User $user, \App\Models\Node $node, string $advertiseAddr)
 * @method static mixed run(\App\Models\User $user, \App\Models\Node $node, string $advertiseAddr)
 */
class InitCluster {}
/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(\App\Models\Team $team, \App\Models\NodeTaskGroup $taskGroup, \App\Models\Deployment $deployment)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(\App\Models\Team $team, \App\Models\NodeTaskGroup $taskGroup, \App\Models\Deployment $deployment)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(\App\Models\Team $team, \App\Models\NodeTaskGroup $taskGroup, \App\Models\Deployment $deployment)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, \App\Models\Team $team, \App\Models\NodeTaskGroup $taskGroup, \App\Models\Deployment $deployment)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, \App\Models\Team $team, \App\Models\NodeTaskGroup $taskGroup, \App\Models\Deployment $deployment)
 * @method static dispatchSync(\App\Models\Team $team, \App\Models\NodeTaskGroup $taskGroup, \App\Models\Deployment $deployment)
 * @method static dispatchNow(\App\Models\Team $team, \App\Models\NodeTaskGroup $taskGroup, \App\Models\Deployment $deployment)
 * @method static dispatchAfterResponse(\App\Models\Team $team, \App\Models\NodeTaskGroup $taskGroup, \App\Models\Deployment $deployment)
 * @method static mixed run(\App\Models\Team $team, \App\Models\NodeTaskGroup $taskGroup, \App\Models\Deployment $deployment)
 */
class RebuildCaddy {}

namespace App\Actions\Services;

/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(\App\Models\User $user, \App\Models\Team $team, string $name, \App\Models\DeploymentData $deploymentData)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(\App\Models\User $user, \App\Models\Team $team, string $name, \App\Models\DeploymentData $deploymentData)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(\App\Models\User $user, \App\Models\Team $team, string $name, \App\Models\DeploymentData $deploymentData)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, \App\Models\User $user, \App\Models\Team $team, string $name, \App\Models\DeploymentData $deploymentData)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, \App\Models\User $user, \App\Models\Team $team, string $name, \App\Models\DeploymentData $deploymentData)
 * @method static dispatchSync(\App\Models\User $user, \App\Models\Team $team, string $name, \App\Models\DeploymentData $deploymentData)
 * @method static dispatchNow(\App\Models\User $user, \App\Models\Team $team, string $name, \App\Models\DeploymentData $deploymentData)
 * @method static dispatchAfterResponse(\App\Models\User $user, \App\Models\Team $team, string $name, \App\Models\DeploymentData $deploymentData)
 * @method static \App\Models\Service run(\App\Models\User $user, \App\Models\Team $team, string $name, \App\Models\DeploymentData $deploymentData)
 */
class CreateService {}
/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(\App\Models\User $user, \App\Models\Service $service, \App\Models\DeploymentData $deploymentData)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(\App\Models\User $user, \App\Models\Service $service, \App\Models\DeploymentData $deploymentData)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(\App\Models\User $user, \App\Models\Service $service, \App\Models\DeploymentData $deploymentData)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, \App\Models\User $user, \App\Models\Service $service, \App\Models\DeploymentData $deploymentData)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, \App\Models\User $user, \App\Models\Service $service, \App\Models\DeploymentData $deploymentData)
 * @method static dispatchSync(\App\Models\User $user, \App\Models\Service $service, \App\Models\DeploymentData $deploymentData)
 * @method static dispatchNow(\App\Models\User $user, \App\Models\Service $service, \App\Models\DeploymentData $deploymentData)
 * @method static dispatchAfterResponse(\App\Models\User $user, \App\Models\Service $service, \App\Models\DeploymentData $deploymentData)
 * @method static \App\Models\Deployment run(\App\Models\User $user, \App\Models\Service $service, \App\Models\DeploymentData $deploymentData)
 */
class StartDeployment {}

namespace App\Actions\Workers;

/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(\App\Models\Service $service, \App\Models\DeploymentData\Process $process, \App\Models\DeploymentData\Worker $worker, \App\Models\Backup $backup = null)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(\App\Models\Service $service, \App\Models\DeploymentData\Process $process, \App\Models\DeploymentData\Worker $worker, \App\Models\Backup $backup = null)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(\App\Models\Service $service, \App\Models\DeploymentData\Process $process, \App\Models\DeploymentData\Worker $worker, \App\Models\Backup $backup = null)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, \App\Models\Service $service, \App\Models\DeploymentData\Process $process, \App\Models\DeploymentData\Worker $worker, \App\Models\Backup $backup = null)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, \App\Models\Service $service, \App\Models\DeploymentData\Process $process, \App\Models\DeploymentData\Worker $worker, \App\Models\Backup $backup = null)
 * @method static dispatchSync(\App\Models\Service $service, \App\Models\DeploymentData\Process $process, \App\Models\DeploymentData\Worker $worker, \App\Models\Backup $backup = null)
 * @method static dispatchNow(\App\Models\Service $service, \App\Models\DeploymentData\Process $process, \App\Models\DeploymentData\Worker $worker, \App\Models\Backup $backup = null)
 * @method static dispatchAfterResponse(\App\Models\Service $service, \App\Models\DeploymentData\Process $process, \App\Models\DeploymentData\Worker $worker, \App\Models\Backup $backup = null)
 * @method static void run(\App\Models\Service $service, \App\Models\DeploymentData\Process $process, \App\Models\DeploymentData\Worker $worker, \App\Models\Backup $backup = null)
 */
class ExecuteWorker {}

namespace Lorisleiva\Actions\Concerns;

/**
 * @method void asController()
 */
trait AsController {}
/**
 * @method void asListener()
 */
trait AsListener {}
/**
 * @method void asJob()
 */
trait AsJob {}
/**
 * @method void asCommand(\Illuminate\Console\Command $command)
 */
trait AsCommand {}
