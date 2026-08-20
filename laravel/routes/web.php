<?php

use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\InstructionAcknowledgementController;
use App\Http\Controllers\ManagementInstructionController;
use App\Http\Controllers\OccurrenceEntryController;
use App\Http\Controllers\OccurrenceExportController;
use App\Http\Controllers\SetupController;
use Illuminate\Support\Facades\Route;

Route::get('/setup', [SetupController::class, 'index'])->name('setup.index');
Route::post('/setup/database', [SetupController::class, 'database'])->name('setup.database');
Route::post('/setup/admin', [SetupController::class, 'admin'])->name('setup.admin');

Route::middleware('installed')->group(function (): void {
    Route::get('/', [OccurrenceEntryController::class, 'index'])->name('entries.index');
    Route::get('/entries/create', [OccurrenceEntryController::class, 'create'])->name('entries.create');
    Route::post('/entries', [OccurrenceEntryController::class, 'store'])->name('entries.store');
    Route::post('/instructions/{instruction}/acknowledge', [InstructionAcknowledgementController::class, 'store'])->name('instructions.acknowledge');
    Route::middleware('guest')->group(function (): void {
        Route::get('/login', [AuthController::class, 'create'])->name('login');
        Route::post('/login', [AuthController::class, 'store'])->name('login.store');
    });
    Route::middleware('auth')->group(function (): void {
        Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
        Route::get('/instructions/create', [ManagementInstructionController::class, 'create'])->name('instructions.create');
        Route::post('/instructions', [ManagementInstructionController::class, 'store'])->name('instructions.store');
        Route::get('/entries/export', [OccurrenceExportController::class, 'create'])->name('entries.export.create');
        Route::post('/entries/export', [OccurrenceExportController::class, 'store'])->name('entries.export.store');
        Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users.index');
        Route::post('/admin/users', [AdminUserController::class, 'store'])->name('admin.users.store');
        Route::get('/admin/users/{user}/edit', [AdminUserController::class, 'edit'])->name('admin.users.edit');
        Route::put('/admin/users/{user}', [AdminUserController::class, 'update'])->name('admin.users.update');
    });
});
