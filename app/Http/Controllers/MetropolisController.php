<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Destination;
use App\Models\GridCell;
use App\Models\FunctionEffect;
use App\Services\EffectsCalculator;

class MetropolisController extends Controller
{
    public function index()
    {
        // Altijd alle bestemmingen laden (zoeken gebeurt in de browser via JS)
        $destinationsByCategory = Destination::all()->groupBy('category');
        $gridCells = GridCell::with('destination')->orderBy('position')->get();

        // Effecten berekenen via de service.
        // Output: ['veiligheid' => X, 'recreatie' => Y, ...]  → klaar voor de Effects-view.
        $calculator = new EffectsCalculator();
        $effecten   = $calculator->calculate($gridCells);
        $categories = $calculator->getCategories();

        // Effects manager data
        $effects = FunctionEffect::orderBy('function_name')->orderBy('category')->get();
        $groupedByFunction = $effects->groupBy('function_name');

        return view('metropolis', [
            'destinationsByCategory' => $destinationsByCategory,
            'gridCells'              => $gridCells,
            'effecten'               => $effecten,
            'categories'             => $categories,
            'groupedByFunction'      => $groupedByFunction,
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
}