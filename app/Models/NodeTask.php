<?php

namespace App\Models;

use App\Casts\TaskPayloadCast;
use App\Casts\TaskResultCast;
use App\Models\NodeTask\AbstractTaskPayload;
use App\Models\NodeTask\AbstractTaskResult;
use App\Models\NodeTask\CreateNetworkTaskPayload;
use App\Models\NodeTask\InitSwarmTaskPayload;
use App\Models\NodeTask\TaskStatus;
use App\Traits\HasOwningTeam;
use App\Traits\HasTaskStatus;
use DASPRiD\Enum\Exception\IllegalArgumentException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NodeTask extends Model
{
    use HasFactory;
    use HasOwningTeam;
    use HasTaskStatus;

    protected $fillable = [
        'payload'
    ];

    protected $casts = [
        'payload' => TaskPayloadCast::class,
        'result' => TaskResultCast::class
    ];

    protected $appends = [
        'formatted_payload',
        'formatted_result',
    ];

    protected static function booted()
    {
        self::creating(function (NodeTask $nodeTask) {
            $nodeTask->type = TaskPayloadCast::TYPE_BY_PAYLOAD[get_class($nodeTask->payload)];
        });
    }

    public function taskGroup(): BelongsTo
    {
        return $this->belongsTo(NodeTaskGroup::class);
    }

    public function getFormattedPayloadAttribute(): string
    {
        return $this->payload->formattedHtml();
    }

    public function getFormattedResultAttribute(): string | null
    {
        if (is_null($this->result)) {
            return null;
        }

        return $this->result->formattedHtml();
    }

    public function getIsEndedAttribute()
    {
        return $this->status->isEnded();
    }

    public function getIsPendingAttribute()
    {
        return $this->status === TaskStatus::Pending;
    }

    public function getIsFailedAttribute()
    {
        return $this->status === TaskStatus::Failed;
    }

    /**
     * @throws IllegalArgumentException
     */
    public function setPayloadAttribute($payload): void
    {
        if (!($payload instanceof AbstractTaskPayload)) {
            throw new IllegalArgumentException('Payload must be an instance of AbstractTaskPayload');
        }

        $this->attributes['payload'] = $payload;
    }

    public function start(): void
    {
        $this->status = TaskStatus::Running;
        $this->started_at = now();
        $this->save();
    }

    public function complete(AbstractTaskResult $result): void
    {
        $this->status = TaskStatus::Completed;

        $this->endTask($result);
    }

    public function fail(AbstractTaskResult $result): void
    {
        $this->status = TaskStatus::Failed;

        $this->endTask($result);
    }

    protected function endTask(AbstractTaskResult $result): void
    {
        $this->ended_at = now();
        $this->result = $result;
        $this->save();
    }
}
