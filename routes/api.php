<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\UserController;

// Public routes (no middleware)
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

// Protected routes (requires authentication)
Route::group(['middleware' => ['jwt.auth']], function () {
    Route::post('logout', [AuthController::class, 'logout']);

    // Admin routes (requires 'admin' role)
    Route::group(['middleware' => ['role:admin']], function () {
        Route::get('/users', [UserController::class, 'index']);
        Route::get('/users/{id}', [UserController::class, 'show']);
        Route::post('/users/{id}', [UserController::class, 'update']);
        Route::delete('/users/{id}', [UserController::class, 'destroy']);

        Route::get('/leads', [LeadController::class, 'index']);
        Route::post('/leads', [LeadController::class, 'store']);
        Route::get('/leads/{id}', [LeadController::class, 'show']);
        Route::post('/leads/{id}', [LeadController::class, 'update']);
        Route::delete('/leads/{id}', [LeadController::class, 'destroy']);

        Route::post('/assign-lead', [AssignmentController::class, 'assignLead']);
    });

    // Counselor routes (requires 'counselor' role)
    Route::group(['middleware' => ['role:counselor']], function () {
        Route::post('/leads/{id}/status', [LeadController::class, 'updateStatus']); // Update lead status
        Route::post('/move-to-application', [ApplicationController::class, 'moveToApplication']); // Move to application
    });
});
