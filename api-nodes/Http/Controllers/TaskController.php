<?php

namespace ApiNodes\Http\Controllers;

use App\Casts\TaskResultCast;
use App\Models\NodeTask;
use App\Models\NodeTask\ErrorResult;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TaskController
{
    public function complete(NodeTask $task, Request $request)
    {
        if ($task->is_ended) {
            return new Response(['error' => 'Already ended.'], 409);
        }

        if ($task->is_pending) {
            return new Response(['error' => "Task didn't start yet."], 409);
        }

        $result = TaskResultCast::RESULT_BY_TYPE[$task->type]::validateAndCreate($request->all());

        $task->complete($result);

        return new Response('', 204);
    }

    public function fail(NodeTask $task, Request $request)
    {
        if ($task->is_ended) {
            return new Response(['error' => 'Already ended.'], 409);
        }

        if ($task->is_pending) {
            return new Response(['error' => "Task didn't start yet."], 409);
        }

        $result = ErrorResult::validateAndCreate($request->all());

        $task->fail($result);

        return new Response('', 204);
    }
}