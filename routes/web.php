<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MetropolisController;
use App\Http\Controllers\EffectsController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;

// Authenticatie (alleen voor gasten)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout')->middleware('auth');

// Beveiligde routes
Route::middleware('auth')->group(function () {

    Route::get('/', [MetropolisController::class, 'index'])->name('home');

    Route::get('/taal/{lang}', function ($lang) {
        if (in_array($lang, ['nl', 'en'])) {
            session(['lang' => $lang]);
        }
        return redirect()->back();
    })->name('taal');

    // Grid bewerken — admin + gebruiker
    Route::middleware('role:admin,gebruiker')->group(function () {
        Route::post('/place', [MetropolisController::class, 'place'])->name('place');
        Route::post('/remove', [MetropolisController::class, 'remove'])->name('remove');
        Route::post('/group/link', [MetropolisController::class, 'linkGroup']);
        Route::post('/group/move', [MetropolisController::class, 'moveGroup']);
        Route::post('/grid/clear', [MetropolisController::class, 'clearGrid'])->name('grid.clear');
    });

    // Admin-only — effecten beheren
    Route::middleware('role:admin')->group(function () {
        Route::get('/effecten-beheren', [EffectsController::class, 'page'])->name('effects.manager');
        Route::post('/api/effects/update', [EffectsController::class, 'updateEffect']);
        Route::post('/api/effects/add-function', [EffectsController::class, 'addFunction']);
        Route::post('/api/effects/delete-function', [EffectsController::class, 'deleteFunction']);
        Route::post('/api/effects/reset-defaults', [EffectsController::class, 'resetToDefaults']);
    });
});
