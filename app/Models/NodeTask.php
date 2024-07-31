<?php

namespace App\Models;

use App\Models\NodeTasks\AbstractTaskResult;
use App\Models\NodeTasks\ErrorResult;
use App\Models\NodeTasks\TaskStatus;
use App\Traits\HasOwningTeam;
use App\Traits\HasTaskStatus;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\LaravelData\Data;

class NodeTask extends Model
{
    use HasFactory;
    use HasOwningTeam;
    use HasTaskStatus;

    protected $fillable = [
        'type',
        'meta',
        'payload',
        'result',
    ];

    protected $casts = [
        'type' => NodeTaskType::class,
    ];

    protected $appends = [
        'formatted_meta',
        'formatted_result',
    ];

    protected static function booted(): void
    {
        self::creating(function (NodeTask $nodeTask) {
            $nodeTask->team_id = $nodeTask->taskGroup->team_id;
        });
    }

    public function taskGroup(): BelongsTo
    {
        return $this->belongsTo(NodeTaskGroup::class);
    }

    public function getFormattedMetaAttribute(): string
    {
        return $this->meta->formattedHtml();
    }

    public function getFormattedResultAttribute(): ?string
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

    public function getIsPendingAttribute(): bool
    {
        return $this->status === TaskStatus::Pending;
    }

    public function getIsFailedAttribute()
    {
        return $this->status === TaskStatus::Failed;
    }

    public function getMetaAttribute()
    {
        $class = $this->type->meta();

        return $class::from(Json::decode($this->attributes['meta']));
    }

    public function setMetaAttribute($value): void
    {
        if ($value instanceof Data) {
            $value = $value->toArray();
        }

        if (is_array($value)) {
            $value = Json::encode($value);
        }

        $this->attributes['meta'] = $value;
    }

    public function setPayloadAttribute($value): void
    {
        if (is_array($value)) {
            $value = Json::encode($value);
        }

        $this->attributes['payload'] = $value;
    }

    public function getResultAttribute()
    {
        if (empty($this->attributes['result'])) {
            return null;
        }

        $result = $this->attributes['result'];
        if ($this->status === TaskStatus::Failed) {
            return ErrorResult::from($result);
        }

        $class = $this->type->result();

        return $class::from($result);
    }

    public function setResultAttribute($value): void
    {
        if ($value instanceof Data) {
            $value = $value->toArray();
        }

        if (is_array($value)) {
            $value = Json::encode($value);
        }

        $this->attributes['result'] = $value;
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
            $this->taskGroup->ended_at = now();
            $this->taskGroup->status = TaskStatus::Completed;
            $this->taskGroup->save();
        }

        $event = $this->type->completed();
        event(new $event($this));
    }

    public function fail(AbstractTaskResult $result): void
    {
        $this->status = TaskStatus::Failed;
        $this->ended_at = now();
        $this->result = $result;
        $this->save();

        $this->taskGroup->ended_at = now();
        $this->taskGroup->status = TaskStatus::Failed;
        $this->taskGroup->save();

        $this->taskGroup->tasks()->pending()->update(['status' => TaskStatus::Canceled]);

        $event = $this->type->failed();
        event(new $event($this));
    }
}
