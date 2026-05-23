<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MetropolisController;
use App\Http\Controllers\EffectsController;

Route::get('/', [MetropolisController::class, 'index'])->name('home');
Route::post('/place', [MetropolisController::class, 'place'])->name('place');
Route::post('/remove', [MetropolisController::class, 'remove'])->name('remove');

// Effects Manager — aparte pagina
Route::get('/effecten-beheren', [EffectsController::class, 'page'])->name('effects.manager');
Route::post('/api/effects/update', [EffectsController::class, 'updateEffect']);
Route::post('/api/effects/add-function', [EffectsController::class, 'addFunction']);
Route::post('/api/effects/delete-function', [EffectsController::class, 'deleteFunction']);

// Taal wisselen
Route::get('/taal/{lang}', function($lang) {
    if (in_array($lang, ['nl', 'en'])) {
        session(['lang' => $lang]);
    }
    return redirect()->back();
})->name('taal');
