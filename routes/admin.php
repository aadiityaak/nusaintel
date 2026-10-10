<?php

use App\Http\Controllers\Admin\IncidentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'can:manage-incidents'])
    ->prefix('admin/incidents')
    ->name('admin.incidents.')
    ->controller(IncidentController::class)
    ->group(function (): void {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{incident}/edit', 'edit')->name('edit');
        Route::put('/{incident}', 'update')->name('update');
        Route::post('/{incident}/transition', 'transition')->name('transition');
    });
