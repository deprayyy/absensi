<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\OfficeController;
use App\Http\Controllers\API\AttendanceController;

// ✅ Test route (bisa dicek di browser)
Route::get('/ping', function () {
    return response()->json(['message' => 'API working!']);
});

// ✅ Public Routes (tidak perlu token)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

    // 🏢 Office
    Route::get('/offices', [OfficeController::class, 'index']);
    Route::post('/offices', [OfficeController::class, 'store']);

// ✅ Protected Routes (wajib pakai token Bearer)
Route::middleware('auth:sanctum')->group(function () {

    // 🔑 Auth
    Route::post('/logout', [AuthController::class, 'logout']);

    // 🕒 Attendance (Absen)
    Route::post('/attendance/clock-in', [AttendanceController::class, 'clockIn']);
    Route::post('/attendance/clock-out', [AttendanceController::class, 'clockOut']);
});
