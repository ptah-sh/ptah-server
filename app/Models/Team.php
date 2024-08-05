<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Laravel\Jetstream\Events\TeamCreated;
use Laravel\Jetstream\Events\TeamDeleted;
use Laravel\Jetstream\Events\TeamUpdated;
use Laravel\Jetstream\Team as JetstreamTeam;
use Laravel\Paddle\Billable;

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

    protected function nodes(): HasMany
    {
        return $this->hasMany(Node::class);
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
}
