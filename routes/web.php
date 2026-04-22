<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MetropolisController;

Route::get('/', [MetropolisController::class, 'index'])->name('home');
Route::post('/place', [MetropolisController::class, 'place'])->name('place');
Route::post('/remove', [MetropolisController::class, 'remove'])->name('remove');

// Taal wisselen
Route::get('/taal/{lang}', function($lang) {
    if (in_array($lang, ['nl', 'en'])) {
        session(['lang' => $lang]);
    }
    return redirect()->back();
})->name('taal');
