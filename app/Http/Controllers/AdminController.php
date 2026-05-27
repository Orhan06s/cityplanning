<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Overzicht van alle gebruikers (alleen voor admins).
     */
    public function users()
    {
        $users = User::orderBy('role')->orderBy('name')->get();
        return view('admin.users', compact('users'));
    }

    /**
     * Verwijder een gebruiker.
     */
    public function deleteUser(User $user)
    {
        // Voorkom dat een admin zichzelf verwijdert
        if ($user->id === auth()->id()) {
            return back()->with('error', __('Je kunt je eigen account niet verwijderen.'));
        }

        $user->delete();
        return back()->with('success', __('Gebruiker verwijderd.'));
    }
}
