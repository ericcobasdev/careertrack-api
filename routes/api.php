<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\JobApplicationController;
use App\Http\Controllers\Api\ApplicationStatsController;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/applications', [JobApplicationController::class, 'index']);
    Route::post('/applications', [JobApplicationController::class, 'store']);
    Route::get('/applications/{jobApplication}', [JobApplicationController::class, 'show']);
    Route::put('/applications/{jobApplication}', [JobApplicationController::class, 'update']);
    Route::delete('/applications/{jobApplication}', [JobApplicationController::class, 'destroy']);

    Route::get('/stats', [ApplicationStatsController::class, 'index']);
});
