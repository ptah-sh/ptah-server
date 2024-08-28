<?php

use ApiNodes\Http\Controllers\EventController;
use ApiNodes\Http\Controllers\NextTaskController;
use ApiNodes\Http\Controllers\TaskController;
use App\Api\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => '/_nodes/v1'], function () {
    Route::group(['prefix' => '/events'], function () {
        Route::post('/started', [EventController::class, 'started']);
    });

    Route::group(['prefix' => '/tasks'], function () {
        Route::get('/next', NextTaskController::class);
        Route::post('/{task}/complete', [TaskController::class, 'complete']);
        Route::post('/{task}/fail', [TaskController::class, 'fail']);
    });
});

Route::group(['prefix' => '/v0', 'middleware' => ['auth:sanctum']], function () {
    Route::post('/services/{service}/deploy', [ServiceController::class, 'deploy'])->middleware('ability:services:deploy');
});
