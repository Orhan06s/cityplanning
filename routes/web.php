<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MetropolisController;
use App\Http\Controllers\EffectsController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PolicyMakerController;
use App\Http\Controllers\SimulationEventController;
use App\Http\Controllers\GridFeedbackController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;

// Authenticatie (alleen voor gasten)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout')->middleware('auth');

// Beveiligde routes
Route::middleware('auth')->group(function () {

    Route::get('/', [MetropolisController::class, 'index'])->name('home');

    Route::get('/evenementen', [SimulationEventController::class, 'index'])->name('events.index');

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
        Route::post('/cel/bestemming', [MetropolisController::class, 'markDestination'])->name('cel.bestemming');
        Route::post('/cel/bestemming/verwijder', [MetropolisController::class, 'unmarkDestination'])->name('cel.bestemming.verwijder');

        // Simulatie-evenementen
        Route::post('/events', [SimulationEventController::class, 'store'])->name('events.store');
        Route::delete('/events/{event}', [SimulationEventController::class, 'destroy'])->name('events.destroy');
        Route::post('/events/clear', [SimulationEventController::class, 'clear'])->name('events.clear');
    });

    // Admin-only — effecten beheren + gebruikersbeheer
    Route::middleware('role:admin')->group(function () {
        Route::get('/effecten-beheren', [EffectsController::class, 'page'])->name('effects.manager');
        Route::post('/api/effects/update', [EffectsController::class, 'updateEffect']);
        Route::post('/api/effects/add-function', [EffectsController::class, 'addFunction']);
        Route::post('/api/effects/delete-function', [EffectsController::class, 'deleteFunction']);
        Route::post('/api/effects/reset-defaults', [EffectsController::class, 'resetToDefaults']);

        Route::get('/admin/gebruikers', [AdminController::class, 'users'])->name('admin.users');
        Route::get('/admin/gebruikers/nieuw', [RegisteredUserController::class, 'create'])->name('admin.users.create');
        Route::post('/admin/gebruikers/nieuw', [RegisteredUserController::class, 'store'])->name('admin.users.store');
        Route::post('/admin/gebruikers/{user}/verwijderen', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
    });

    // PDF export voor city planner en admin
    Route::middleware('role:gebruiker,admin')->group(function () {
        Route::get('/export-pdf', [MetropolisController::class, 'exportPdf'])->name('export.pdf');
    });

    // Beleidsmaker eigen dashboard + feedback
    Route::middleware('role:beleidsmaker')->group(function () {
        Route::get('/beleidsmaker/dashboard', [PolicyMakerController::class, 'index'])->name('beleidsmaker.dashboard');

        Route::get('/beleidsmaker/feedback', [GridFeedbackController::class, 'index'])->name('beleidsmaker.feedback');
        Route::post('/beleidsmaker/feedback', [GridFeedbackController::class, 'store'])->name('beleidsmaker.feedback.store');
        Route::delete('/beleidsmaker/feedback/{feedback}', [GridFeedbackController::class, 'destroy'])->name('beleidsmaker.feedback.destroy');
        Route::post('/beleidsmaker/feedback/submit', [GridFeedbackController::class, 'submit'])->name('beleidsmaker.feedback.submit');
    });

    // Goedkeuren bestemmingen: beleidsmaker en admin
    Route::middleware('role:beleidsmaker,admin')->group(function () {
        Route::post('/cel/goedkeuren', [MetropolisController::class, 'approveDestination'])->name('cel.goedkeuren');
        Route::post('/cel/goedkeuren/intrekken', [MetropolisController::class, 'unapproveDestination'])->name('cel.goedkeuren.intrekken');
    });
});
