<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\VisitController;
use Illuminate\Support\Facades\Route;

// ── Routes عامة (بدون تسجيل دخول) ──────────────────────────────
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// ── Routes محمية (تحتاج تسجيل دخول) ────────────────────────────
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout',      [AuthController::class, 'logout']);
    Route::get('/user',         [AuthController::class, 'user']);
    Route::match(['put', 'patch'], '/user', [AuthController::class, 'update']);
    Route::post('/user/avatar', [AuthController::class, 'updateAvatar']); // ← هذا
    Route::apiResource('visits', VisitController::class);
});