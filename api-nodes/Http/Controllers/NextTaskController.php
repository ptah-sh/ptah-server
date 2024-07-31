<?php

namespace ApiNodes\Http\Controllers;

use App\Models\Node;
use App\Models\NodeTaskGroup;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Response;

class NextTaskController
{
    public function __invoke(Node $node)
    {
        if ($node->swarm_id === null) {
            return new Response([
                'message' => 'No task found.',
            ], 204);
        }

        $taskGroup = $node->taskGroups()->inProgress()->first();

        $task = $taskGroup ? $this->getNextTaskFromGroup($node, $taskGroup) : $this->pickNextTask($node);
        if ($task) {
            if ($task instanceof Response) {
                return $task;
            }

            return $task->only([
                'id',
                'type',
                'payload',
            ]);
        }

        return new Response([
            'message' => 'No task found.',
        ], 204);
    }

    protected function getNextTaskFromGroup(Node $node, NodeTaskGroup $taskGroup)
    {
        if ($taskGroup->tasks()->running()->first()) {
            return new Response([
                'error_message' => 'Another task should be already running.',
            ], 409);
        }

        $task = $taskGroup->tasks()->pending()->first();

        if ($task) {
            $task->start($node);

            return $task;
        }

        return null;
    }

    protected function pickNextTask(Node $node)
    {
        $taskGroup = NodeTaskGroup::where('swarm_id', $node->swarm_id)->where(function (Builder $query) use ($node) {
            return $query->where('node_id', $node->id)->orWhere('node_id', null);
        })->pending()->first();

        if (! $taskGroup) {
            return null;
        }

        $task = $taskGroup->tasks()->first();

        $task->start($node);

        return $task;
    }
}
