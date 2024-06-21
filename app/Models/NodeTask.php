<?php

namespace App\Models;

use App\Casts\TaskMetaCast;
use App\Casts\TaskPayloadCast;
use App\Casts\TaskResultCast;
use App\Events\Tasks\CreateNetworkCompleted;
use App\Events\Tasks\CreateNetworkFailed;
use App\Events\Tasks\InitSwarmCompleted;
use App\Events\Tasks\InitSwarmFailed;
use App\Models\NodeTask\AbstractTaskPayload;
use App\Models\NodeTask\AbstractTaskResult;
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

    const TYPE_TO_EVENT_COMPLETED = [
        0 => CreateNetworkCompleted::class,
        1 => InitSwarmCompleted::class,
    ];
    const TYPE_TO_EVENT_FAILED = [
        0 => CreateNetworkFailed::class,
        1 => InitSwarmFailed::class,
    ];

    protected $fillable = [
        'meta',
        'payload'
    ];

    protected $casts = [
        'meta' => TaskMetaCast::class,
        'payload' => TaskPayloadCast::class,
        'result' => TaskResultCast::class
    ];

    protected $appends = [
        'formatted_payload',
        'formatted_result',
    ];

    protected static function booted()
    {
//        self::creating(function (NodeTask $nodeTask) {
//            $payload = $nodeTask->payload;
//
//            if (!($payload instanceof AbstractTaskPayload)) {
//                throw new IllegalArgumentException('Payload must be an instance of AbstractTaskPayload');
//            }
//
//            $nodeTask->type = TaskPayloadCast::TYPE_BY_PAYLOAD[get_class($payload)];
//        });
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

    public function start(Node $node): void
    {
        if ($this->taskGroup->is_pending) {
            $this->taskGroup->start($node);
        }

        $this->status = TaskStatus::Running;
        $this->started_at = now();
        $this->save();
    }

    public function complete(AbstractTaskResult $result): void
    {
        $this->status = TaskStatus::Completed;
        $this->ended_at = now();
        $this->result = $result;
        $this->save();

        if ($this->taskGroup->allTasksEnded()) {
            $this->taskGroup->status = TaskStatus::Completed;
            $this->taskGroup->save();
        }

        $eventMap = self::TYPE_TO_EVENT_COMPLETED;
        event(new $eventMap[$this->type]($this));
    }

    public function fail(AbstractTaskResult $result): void
    {
        $this->status = TaskStatus::Failed;
        $this->ended_at = now();
        $this->result = $result;
        $this->save();

        $this->taskGroup->status = TaskStatus::Failed;
        $this->taskGroup->save();

        $this->taskGroup->tasks()->pending()->update(['status' => TaskStatus::Canceled]);

        $eventMap = self::TYPE_TO_EVENT_FAILED;
        event(new $eventMap[$this->type]($this));
    }
}
