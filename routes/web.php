<?php

use App\Http\Controllers\PublicTimelineController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicTimelineController::class, 'index'])->name('home');
Route::get('/incidents/{incident:public_id}', [PublicTimelineController::class, 'show'])->name('incidents.show');
Route::get('/metodologi', [PublicTimelineController::class, 'methodology'])->name('methodology');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
require __DIR__.'/admin.php';
