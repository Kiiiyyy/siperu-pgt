<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\MasterController;
use Illuminate\Support\Facades\Route;

// ==================== AKSES PUBLIK (Tanpa Login) ====================
Route::get('/dashboard', [RoomController::class, 'index'])->name('dashboard');
Route::get('/api/calendar-events', [RoomController::class, 'getKalenderKetersediaan']);

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'authenticate']);
});

// ==================== AKSES WAJIB LOGIN ====================
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    
    // Mahasiswa (Mengajukan Peminjaman)
    Route::get('/peminjaman/baru', [ReservationController::class, 'create'])->name('reservation.create');
    Route::post('/peminjaman/baru', [ReservationController::class, 'store'])->name('reservation.store');
    Route::get('/peminjaman/riwayat', [ReservationController::class, 'history'])->name('reservation.history');

    // Dosen (Verifikasi & WA)
    Route::get('/dosen/persetujuan', [ReservationController::class, 'approvalList'])->name('reservation.approval');
    Route::post('/dosen/persetujuan/{id}/approve', [ReservationController::class, 'approve'])->name('reservation.approve');
    Route::post('/dosen/persetujuan/{id}/reject', [ReservationController::class, 'reject'])->name('reservation.reject');
    Route::get('/dosen/laporan', [ReservationController::class, 'reportIndex'])->name('reservation.report');
    Route::get('/dosen/laporan/pdf', [ReservationController::class, 'exportPDF'])->name('reservation.export_pdf');

    // Admin (Mengelola Data Master - Proteksi ditangani internal di MasterController)
    Route::get('/admin/master', [MasterController::class, 'index'])->name('admin.master');
    Route::post('/admin/room', [MasterController::class, 'storeRoom'])->name('admin.room.store');
    Route::put('/admin/room/{id}', [MasterController::class, 'updateRoom'])->name('admin.room.update');
    Route::delete('/admin/room/{id}', [MasterController::class, 'destroyRoom'])->name('admin.room.destroy');
    Route::post('/admin/user', [MasterController::class, 'storeUser'])->name('admin.user.store');
    Route::put('/admin/user/{id}', [MasterController::class, 'updateUser'])->name('admin.user.update');
    Route::delete('/admin/user/{id}', [MasterController::class, 'destroyUser'])->name('admin.user.destroy');
});