<?php

use App\Http\Middleware\NodeAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => NodeAuth::class, 'prefix' => '/api:node/v1'], function () {

});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');