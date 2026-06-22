<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ArsipPajakController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\JenisPajakController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated Routes
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profil', [UserController::class, 'profile'])->name('profile');
    Route::put('/profil', [UserController::class, 'updateProfile'])->name('profile.update');

    // Arsip Pajak - Staff & Pimpinan
    Route::resource('arsip', ArsipPajakController::class);
    Route::post('/arsip/{arsip}/kirim', [ArsipPajakController::class, 'kirimApproval'])->name('arsip.kirim');

    // Laporan - Staff & Pimpinan
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');

    // Approval - Pimpinan Only
    Route::middleware(['role:pimpinan'])->group(function () {
        Route::get('/approval', [ApprovalController::class, 'index'])->name('approval.index');
        Route::get('/approval/{arsip}', [ApprovalController::class, 'show'])->name('approval.show');
        Route::post('/approval/{arsip}/setujui', [ApprovalController::class, 'approve'])->name('approval.approve');
        Route::post('/approval/{arsip}/tolak', [ApprovalController::class, 'reject'])->name('approval.reject');

        // Master Jenis Pajak
        Route::resource('jenis-pajak', JenisPajakController::class)->except(['show', 'create', 'edit']);

        // Manajemen User
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });
});
