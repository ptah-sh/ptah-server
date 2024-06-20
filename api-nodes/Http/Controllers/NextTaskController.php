<?php

namespace ApiNodes\Http\Controllers;

use App\Models\Node;
use App\Models\NodeTask\TaskStatus;
use App\Models\NodeTaskGroup;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Response;

class NextTaskController
{
    public function __invoke(Node $node)
    {
        $taskGroup = $node->taskGroups()->inProgress()->first();

        $task = $taskGroup ? $this->getNextTaskFromGroup($taskGroup) : $this->pickNextTask($node);
        if ($task) {
            return $task;
        }

        return new Response([
            'message' => 'No task found.'
        ], 404);
    }

    protected function getNextTaskFromGroup(NodeTaskGroup $taskGroup)
    {
        if ($taskGroup->tasks()->running()->first()) {
            return new Response([
                'error_message' => 'Another task should be already running.'
            ], 409);
        }

        $task = $taskGroup->tasks()->pending()->first();

        $task->start();

        return $task;
    }

    protected function pickNextTask(Node $node)
    {
        $taskGroup = NodeTaskGroup::where('swarm_id', $node->swarm_id)->where(function (Builder $query) use ($node) {
            return $query->where('node_id', $node->id)->orWhere('node_id', null);
        })->pending()->first();

        $task = $taskGroup->tasks()->first();

        $taskGroup->startTask($node, $task);

        return $task;
    }
}