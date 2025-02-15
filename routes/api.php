<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\AssignmentController;

// Exclude RoleMiddleware from the 'login' route
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// Routes for Admin (with RoleMiddleware for 'admin' role)
// Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {

    Route::post('register', [UserController::class, 'register']);
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::post('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);

    Route::post('register', [AuthController::class, 'register']);
    Route::get('/leads', [LeadController::class, 'index']);
    Route::post('/leads', [LeadController::class, 'store']);
    Route::get('/leads/{id}', [LeadController::class, 'show']);
    Route::post('/leads/{id}', [LeadController::class, 'update']);
    Route::delete('/leads/{id}', [LeadController::class, 'destroy']);
    Route::post('/assign-lead', [AssignmentController::class, 'assignLead']);
// });

// Routes for Counselor (with RoleMiddleware for 'counselor' role)
// Route::middleware(['auth:sanctum', 'role:counselor'])->group(function () {
    Route::post('/leads/{id}/status', [LeadController::class, 'updateStatus']); // Update lead status
    Route::post('/move-to-application', [LeadController::class, 'moveToApplication']); // Move to application
// });
