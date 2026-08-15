<?php

use App\Http\Controllers\OccurrenceEntryController;
use Illuminate\Support\Facades\Route;

Route::get('/', [OccurrenceEntryController::class, 'index'])->name('entries.index');
Route::get('/entries/create', [OccurrenceEntryController::class, 'create'])->name('entries.create');
Route::post('/entries', [OccurrenceEntryController::class, 'store'])->name('entries.store');
