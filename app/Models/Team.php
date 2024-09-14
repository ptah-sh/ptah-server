<?php

namespace App\Models;

use App\Models\PricingPlan\ItemQuota;
use App\Models\PricingPlan\Plan;
use App\Models\PricingPlan\UsageQuotas;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Laravel\Jetstream\Events\TeamCreated;
use Laravel\Jetstream\Events\TeamDeleted;
use Laravel\Jetstream\Events\TeamUpdated;
use Laravel\Jetstream\Team as JetstreamTeam;
use Laravel\Paddle\Billable;
use Laravel\Paddle\Subscription;

class Team extends JetstreamTeam
{
    use Billable;
    use HasFactory;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'personal_team',
        'billing_name',
        'billing_email',
    ];

    protected $appends = [
        'billing',
    ];

    /**
     * The event map for the model.
     *
     * @var array<string, class-string>
     */
    protected $dispatchesEvents = [
        'created' => TeamCreated::class,
        'updated' => TeamUpdated::class,
        'deleted' => TeamDeleted::class,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'personal_team' => 'boolean',
        ];
    }

    protected static function booted()
    {
        self::deleting(function (Team $team) {
            foreach ($team->nodes as $node) {
                $node->delete();
            }

            if ($team->subscription()?->valid()) {
                $team->subscription()->cancelNow();
            }
        });
    }

    public function swarms(): HasMany
    {
        return $this->hasMany(Swarm::class);
    }

    public function nodes(): HasMany
    {
        return $this->hasMany(Node::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function deployments(): HasMany
    {
        return $this->hasMany(Deployment::class);
    }

    /**
     * Get the name of the team.
     */
    protected function paddleName(): string
    {
        if ($this->billing_name) {
            return $this->billing_name;
        }

        return $this->owner->name;
    }

    /**
     * Get the email address of the team.
     */
    protected function paddleEmail(): string
    {
        if ($this->billing_email) {
            return $this->billing_email;
        }

        return $this->owner->email;
    }

    protected function getBillingAttribute(): ?array
    {
        if ($this->onTrial()) {
            return [
                'status' => 'trialing',
                'trial_ends_at' => $this->trialEndsAt(),
                'ends_at' => null,
            ];
        }

        if ($this->subscription() === null) {
            return null;
        }

        return collect($this->subscription()->toArray())->only([
            'status',
            'trial_ends_at',
            'ends_at',
        ])->toArray();
    }

    public function routeNotificationForMail(Notification $notification): array|string
    {
        return [$this->customer->email => $this->customer->name];
    }

    public function currentPlan(): Plan
    {
        $plans = config('billing.paddle.plans');
        $trialPlan = config('billing.paddle.trialPlan');
        $selfHostedPlan = config('billing.paddle.selfHostedPlan');
        $subscription = $this->subscription();

        if (! config('billing.enabled')) {
            return Plan::from($selfHostedPlan);
        }

        if ($this->onTrial() || ! $this->hasValidSubscription()) {
            return Plan::from($trialPlan);
        }

        foreach ($plans as $plan) {
            if ($subscription->hasProduct($plan['product_id'])) {
                return Plan::from($plan);
            }
        }

        // If no matching plan found, return the trial plan as default
        return Plan::from($trialPlan);
    }

    public function quotas(): UsageQuotas
    {
        $plan = $this->currentPlan();

        return new UsageQuotas(
            new ItemQuota(
                name: 'Nodes',
                maxUsage: $plan->quotas['nodes']['limit'],
                getCurrentUsage: fn () => $this->nodes()->count(),
                isSoftQuota: $plan->quotas['nodes']['soft']
            ),
            new ItemQuota(
                name: 'Swarms',
                maxUsage: $plan->quotas['swarms']['limit'],
                getCurrentUsage: fn () => $this->swarms()->count(),
                isSoftQuota: $plan->quotas['swarms']['soft']
            ),
            new ItemQuota(
                name: 'Services',
                maxUsage: $plan->quotas['services']['limit'],
                getCurrentUsage: fn () => $this->services()->count(),
                isSoftQuota: $plan->quotas['services']['soft']
            ),
            new ItemQuota(
                name: 'Deployments',
                maxUsage: $plan->quotas['deployments']['limit'],
                getCurrentUsage: fn () => $this->deployments()->count(),
                isSoftQuota: $plan->quotas['deployments']['soft']
            )
        );
    }

    public function nodesLimitReached(): bool
    {
        return $this->nodes()->count() >= $this->quotas()->nodes;
    }

    public function validSubscription(): ?Subscription
    {
        if (! config('billing.enabled')) {
            throw new Exception('Billing is not enabled. Check with hasValidSubscription() first.');
        }

        return $this->subscription()?->valid() ? $this->subscription() : null;
    }

    public function hasValidSubscription(): bool
    {
        if (config('billing.enabled')) {
            return $this->validSubscription() !== null;
        }

        return true;
    }
}
