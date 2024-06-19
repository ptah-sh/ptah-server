<?php

use ApiNodes\Http\Controllers\EventController;
use ApiNodes\Http\Middleware\AgentTokenAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => AgentTokenAuth::class, 'prefix' => '/_nodes/v1'], function () {
    Route::group(['prefix' => '/events'], function () {
        Route::post('/started', [EventController::class, 'started']);
    });
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');