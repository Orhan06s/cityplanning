<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PolicyMakerController extends Controller
{
    /**
     * Toon het dashboard voor de beleidsmaker.
     */
    public function index(): View
    {
        return view('beleidsmaker.dashboard');
    }
}
