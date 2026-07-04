<?php

use App\Http\Controllers\Api\ApplicationStatsController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\JobApplicationController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    Route::apiResource('applications', JobApplicationController::class)
        ->parameters(['applications' => 'jobApplication']);

    Route::get('/stats', [ApplicationStatsController::class, 'index']);
});
