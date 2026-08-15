<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\InstructionAcknowledgementController;
use App\Http\Controllers\ManagementInstructionController;
use App\Http\Controllers\OccurrenceEntryController;
use Illuminate\Support\Facades\Route;

Route::get('/', [OccurrenceEntryController::class, 'index'])->name('entries.index');
Route::get('/entries/create', [OccurrenceEntryController::class, 'create'])->name('entries.create');
Route::post('/entries', [OccurrenceEntryController::class, 'store'])->name('entries.store');

Route::post('/instructions/{instruction}/acknowledge', [InstructionAcknowledgementController::class, 'store'])
    ->name('instructions.acknowledge');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
    Route::get('/instructions/create', [ManagementInstructionController::class, 'create'])->name('instructions.create');
    Route::post('/instructions', [ManagementInstructionController::class, 'store'])->name('instructions.store');
});
