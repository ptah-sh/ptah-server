<?php

use ApiNodes\Http\Controllers\TaskController;
use ApiNodes\Http\Controllers\EventController;
use ApiNodes\Http\Controllers\NextTaskController;
use ApiNodes\Http\Middleware\AgentTokenAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prependMiddlewareToGroup('api', AgentTokenAuth::class)->group(['prefix' => '/_nodes/v1'], function () {
    Route::group(['prefix' => '/events'], function () {
        Route::post('/started', [EventController::class, 'started']);
    });

    Route::group(['prefix' => '/tasks'], function () {
        Route::get('/next', NextTaskController::class);
        Route::post('/{task}/complete', [TaskController::class, 'complete']);
        Route::post('/{task}/fail', [TaskController::class, 'fail']);
    });
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');