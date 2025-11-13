<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PengajuanCutiController;
use App\Http\Controllers\Api\ApprovalController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\HariLiburController;

// Public routes
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);

    // Pengajuan Cuti
    Route::apiResource('pengajuan-cuti', PengajuanCutiController::class);

    // Approval
    Route::prefix('approval')->group(function () {
        Route::get('/', [ApprovalController::class, 'index']);
        Route::post('/{pengajuan}/approve', [ApprovalController::class, 'approve']);
        Route::post('/{pengajuan}/reject', [ApprovalController::class, 'reject']);
        Route::get('/{pengajuan}/history', [ApprovalController::class, 'history']);
    });

    // Notifications
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/unread-count', [NotificationController::class, 'unreadCount']);
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead']);
    });

    // Hari Libur
    Route::get('hari-libur', [HariLiburController::class, 'index']);
    Route::post('hari-libur/sync', [HariLiburController::class, 'syncFromApi'])->middleware('admin');
});
