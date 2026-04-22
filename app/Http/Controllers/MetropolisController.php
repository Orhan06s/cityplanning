<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Destination;
use App\Models\GridCell;

class MetropolisController extends Controller
{
    public function index()
    {
        // Altijd alle bestemmingen laden (zoeken gebeurt in de browser via JS)
        $destinationsByCategory = Destination::all()->groupBy('category');
        $gridCells = GridCell::with('destination')->orderBy('position')->get();
        $effecten = $this->berekenEffecten($gridCells);

        return view('metropolis', [
            'destinationsByCategory' => $destinationsByCategory,
            'gridCells'              => $gridCells,
            'effecten'               => $effecten,
        ]);
    }

    public function place(Request $request)
    {
        $request->validate([
            'cell_id'        => 'required|integer|exists:grid_cells,id',
            'destination_id' => 'required|integer|exists:destinations,id',
        ]);

        $cell = GridCell::find($request->cell_id);
        $cell->destination_id = $request->destination_id;
        $cell->save();

        return redirect('/')->with('success', __('Functie geplaatst!'));
    }

    public function remove(Request $request)
    {
        $request->validate([
            'cell_id' => 'required|integer|exists:grid_cells,id',
        ]);

        $cell = GridCell::find($request->cell_id);
        $cell->destination_id = null;
        $cell->save();

        return redirect('/')->with('success', __('Functie verwijderd!'));
    }

    private function berekenEffecten($gridCells)
    {
        $leefbaarheid = 0;
        $welzijn = 0;

        foreach ($gridCells as $cell) {
            if ($cell->destination) {
                if ($cell->destination->category === 'parks') {
                    $leefbaarheid += 10;
                    $welzijn += 5;
                }
                if ($cell->destination->category === 'public_services') {
                    $leefbaarheid += 5;
                    $welzijn += 10;
                }
                if ($cell->destination->category === 'emergency_services') {
                    $welzijn += 8;
                }
                if ($cell->destination->category === 'buildings') {
                    $leefbaarheid += 2;
                }
            }
        }

        return [
            'leefbaarheid' => $leefbaarheid,
            'welzijn'      => $welzijn,
            'max'          => 120,
        ];
    }
}
