<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Destination;
use App\Models\GridCell;
use App\Services\EffectsCalculator;
use App\Models\DayNightModifier;

class MetropolisController extends Controller
{
    public function index(Request $request)
    {
        // Grid size: uit URL als die er is, anders uit session, anders 12. Clamp 6-64.
        if ($request->has('size')) {
            $size = (int) $request->query('size', 12);
            if ($size < 6)  $size = 6;
            if ($size > 64) $size = 64;
            session(['grid_size' => $size]);
        } else {
            $size = (int) session('grid_size', 12);
        }

        $destinationsByCategory = Destination::all()->groupBy('category');

        // Alleen de eerste N cellen tonen
        $gridCells = GridCell::with('destination', 'approver')
            ->orderBy('position')
            ->take($size)
            ->get();

        // Cellen die als bestemming zijn gemarkeerd (ook zonder functie)
        $bestemmingen = GridCell::with('destination', 'approver')
            ->where('is_destination', true)
            ->orderBy('position')
            ->get();

        // Effecten berekenen via de service.
        $calculator    = new EffectsCalculator();
        $effecten      = $calculator->calculate($gridCells);
        $categories    = $calculator->getCategories();
        $effectsMatrix = $calculator->getMatrix();

        // Day/night modifiers ophalen en herschikken voor frontend
        $dayNightModifiers = DayNightModifier::all()
    ->groupBy('function_name')
    ->map(fn($rows) => $rows->mapWithKeys(fn($r) => [
        $r->category => [
            'day'   => $r->day_modifier,
            'night' => $r->night_modifier,
        ]
    ]));

        return view('metropolis', [
    'destinationsByCategory' => $destinationsByCategory,
    'gridCells'              => $gridCells,
    'effecten'               => $effecten,
    'categories'             => $categories,
    'effectsMatrix'          => $effectsMatrix,
    'gridSize'               => $size,
    'bestemmingen'           => $bestemmingen,
    'dayNightModifiers'      => $dayNightModifiers, 
]);
    }

    public function place(Request $request)
    {
        $request->validate([
            'cell_id'        => 'required|integer|exists:grid_cells,id',
            'destination_id' => 'required|integer|exists:destinations,id',
            'size'           => 'nullable|integer|between:6,64',
        ]);

        $cell        = GridCell::find($request->cell_id);
        $destination = Destination::find($request->destination_id);
        $size        = (int) $request->input('size', session('grid_size', 12));

        // Check verboden buren
        $conflict = $this->findForbiddenNeighbor($cell, $destination->name);
        if ($conflict) {
            return redirect('/?size=' . $size)->with('error',
                __(':a mag niet naast :b worden geplaatst.', [
                    'a' => __($destination->name),
                    'b' => __($conflict),
                ])
            );
        }

        $cell->destination_id = $request->destination_id;
        $cell->save();

        return redirect('/?size=' . $size)->with('success', __('Functie geplaatst!'));
    }


     public function getDayNightModifiers()
    {
        $modifiers = DayNightModifier::all()
            ->groupBy('function_name')
            ->map(fn($rows) => $rows->keyBy('category'));

        return response()->json($modifiers);
    }

   
    public function updateDayNightModifier(Request $request)
    {
        $validated = $request->validate([
            'function_name'  => 'required|string',
            'category'       => 'required|string',
            'day_modifier'   => 'required|integer|between:-5,5',
            'night_modifier' => 'required|integer|between:-5,5',
        ]);

        DayNightModifier::updateOrCreate(
            [
                'function_name' => $validated['function_name'],
                'category'      => $validated['category'],
            ],
            [
                'day_modifier'   => $validated['day_modifier'],
                'night_modifier' => $validated['night_modifier'],
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Modifier updated!'
        ]);
    }

    // Geeft de naam van een verboden buurfunctie terug, of null.
    private function findForbiddenNeighbor(GridCell $cell, string $newFunction): ?string
    {
        $forbidden = config('function-rules.forbidden_pairs', []);
        $cols      = config('function-rules.grid_cols', 4);

        $neighbors = GridCell::with('destination')
            ->whereIn('position', $this->neighborPositions($cell->position, $cols))
            ->get();

        foreach ($neighbors as $n) {
            if (!$n->destination) continue;
            $other = $n->destination->name;

            foreach ($forbidden as $pair) {
                if ($newFunction === $other) continue;
                if (in_array($newFunction, $pair, true) && in_array($other, $pair, true)) {
                    return $other;
                }
            }
        }

        return null;
    }

    // Posities van de directe buren (boven, onder, links, rechts).
    private function neighborPositions(int $pos, int $cols): array
    {
        $col = $pos % $cols;
        $list = [$pos + $cols, $pos - $cols]; // onder, boven
        if ($col > 0)         $list[] = $pos - 1;
        if ($col < $cols - 1) $list[] = $pos + 1;
        return $list;
    }

    public function remove(Request $request)
    {
        $request->validate([
            'cell_id' => 'required|integer|exists:grid_cells,id',
            'size'    => 'nullable|integer|between:6,64',
        ]);

        $cell = GridCell::find($request->cell_id);

        if ($cell->linked_group_id) {
            // Verwijder de hele gekoppelde groep, ook approval
            GridCell::where('linked_group_id', $cell->linked_group_id)
                ->update(['destination_id' => null, 'linked_group_id' => null, 'approved_at' => null, 'approved_by' => null]);
        } else {
            $cell->destination_id = null;
            $cell->approved_at = null;
            $cell->approved_by = null;
            $cell->save();
        }

        $size = (int) $request->input('size', session('grid_size', 12));
        return redirect('/?size=' . $size)->with('success', __('Functie verwijderd!'));
    }

    // Twee cellen koppelen tot één gekoppelde groep (of een cel weer loskoppelen)
    public function linkGroup(Request $request)
    {
        $request->validate([
            'source_cell_id' => 'required|integer|exists:grid_cells,id',
            'target_cell_id' => 'required|integer|exists:grid_cells,id',
        ]);

        if ($request->source_cell_id === $request->target_cell_id) {
            return response()->json(['success' => false, 'message' => __('Selecteer twee verschillende cellen.')], 422);
        }

        $source = GridCell::find($request->source_cell_id);
        $target = GridCell::find($request->target_cell_id);

        if (!$source->destination || !$target->destination) {
            return response()->json(['success' => false, 'message' => __('Beide cellen moeten een functie bevatten.')], 422);
        }

        $sourceGroup = $source->linked_group_id;
        $targetGroup = $target->linked_group_id;

        // Beide al in dezelfde groep → de target loskoppelen
        if ($sourceGroup && $sourceGroup === $targetGroup) {
            $target->linked_group_id = null;
            $target->save();

            if (GridCell::where('linked_group_id', $sourceGroup)->count() <= 1) {
                GridCell::where('linked_group_id', $sourceGroup)->update(['linked_group_id' => null]);
            }

            return response()->json(['success' => true, 'action' => 'unlinked']);
        }

        // Eén van beide zit al in een groep → de andere erbij voegen
        if ($sourceGroup || $targetGroup) {
            $groupId = $sourceGroup ?: $targetGroup;

            if (!$sourceGroup) {
                $source->linked_group_id = $groupId;
                $source->save();
            }

            if (!$targetGroup) {
                $target->linked_group_id = $groupId;
                $target->save();
            }

            // Twee verschillende groepen → samenvoegen
            if ($sourceGroup && $targetGroup && $sourceGroup !== $targetGroup) {
                GridCell::where('linked_group_id', $targetGroup)
                    ->update(['linked_group_id' => $groupId]);
            }

            return response()->json(['success' => true, 'action' => 'linked']);
        }

        // Geen van beide in een groep → nieuwe groep aanmaken
        $groupId = (int) GridCell::max('linked_group_id') + 1;
        $source->linked_group_id = $groupId;
        $target->linked_group_id = $groupId;
        $source->save();
        $target->save();

        return response()->json(['success' => true, 'action' => 'linked']);
    }

    // Een gekoppelde groep verplaatsen naar een nieuwe positie op het grid
    public function moveGroup(Request $request)
    {
        $request->validate([
            'source_cell_id' => 'required|integer|exists:grid_cells,id',
            'target_cell_id' => 'required|integer|exists:grid_cells,id',
        ]);

        $source = GridCell::find($request->source_cell_id);
        $target = GridCell::find($request->target_cell_id);

        if (!$source->linked_group_id) {
            return response()->json(['success' => false, 'message' => __('Selecteer eerst een gekoppelde groep.')], 422);
        }

        if ($target->destination_id) {
            return response()->json(['success' => false, 'message' => __('De doelcel moet leeg zijn.')], 422);
        }

        $groupId = $source->linked_group_id;

        // GridCell-rijen zijn vaste sloten. Verplaats de inhoud (destination_id +
        // linked_group_id) tussen sloten in plaats van de position-waarden te
        // veranderen (dat zou duplicaten geven).
        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\GridCell> $groupCells */
        $groupCells = GridCell::where('linked_group_id', $groupId)->orderBy('position')->get();
        $anchorCell = $groupCells->first();
        $delta = $target->position - $anchorCell->position;

        // Maximale positie = aantal cellen in DB
        $maxPos = (int) GridCell::count();

        $newPositions = [];
        foreach ($groupCells as $cell) {
            $newPos = $cell->position + $delta;
            if ($newPos < 0 || $newPos >= $maxPos) {
                return response()->json(['success' => false, 'message' => __('Groep valt buiten het grid.')], 422);
            }
            $newPositions[$cell->id] = $newPos;
        }

        $occupied = GridCell::whereNotIn('id', $groupCells->pluck('id'))
            ->whereIn('position', array_values($newPositions))
            ->whereNotNull('destination_id')
            ->exists();

        if ($occupied) {
            return response()->json(['success' => false, 'message' => __('De groep kan niet verplaatst worden omdat de bestemming bezet is.')], 422);
        }

        DB::transaction(function() use ($groupCells, $newPositions, $groupId) {
            // Bouw mapping: target positie -> destination_id
            $mapping = [];
            foreach ($groupCells as $cell) {
                $mapping[$newPositions[$cell->id]] = $cell->destination_id;
            }

            // Maak de originele groep-cellen leeg
            foreach ($groupCells as $cell) {
                $cell->destination_id = null;
                $cell->linked_group_id = null;
                $cell->save();
            }

            // Vul de doel-sloten
            foreach ($mapping as $pos => $destinationId) {
                /** @var GridCell|null $slot */
                $slot = GridCell::where('position', $pos)->first();
                if ($slot) {
                    $slot->destination_id = $destinationId;
                    $slot->linked_group_id = $groupId;
                    $slot->save();
                }
            }
        });

        return response()->json(['success' => true]);
    }

    // Alle functies van het grid verwijderen
    public function clearGrid(Request $request)
    {
        GridCell::query()->update(['destination_id' => null, 'linked_group_id' => null]);
        return response()->json(['success' => true]);
    }

    // Cel markeren als bestemming
    public function markDestination(Request $request)
    {
        $request->validate(['cell_id' => 'required|integer|exists:grid_cells,id']);
        GridCell::where('id', $request->cell_id)->update(['is_destination' => true]);
        return response()->json(['success' => true]);
    }

    // Bestemming-markering verwijderen van een cel
    public function unmarkDestination(Request $request)
    {
        $request->validate(['cell_id' => 'required|integer|exists:grid_cells,id']);
        GridCell::where('id', $request->cell_id)->update(['is_destination' => false]);
        return response()->json(['success' => true]);
    }

    // Beleidsmaker keurt de bestemming op een cel goed
    public function approveDestination(Request $request)
    {
        $request->validate(['cell_id' => 'required|integer|exists:grid_cells,id']);

        $cell = GridCell::find($request->cell_id);

        if (!$cell->destination_id) {
            return response()->json([
                'success' => false,
                'message' => __('Deze cel heeft geen functie om goed te keuren.'),
            ], 422);
        }

        $cell->approved_at = now();
        $cell->approved_by = auth()->id();
        $cell->save();

        return response()->json(['success' => true]);
    }

    // Beleidsmaker trekt goedkeuring weer in
    public function unapproveDestination(Request $request)
    {
        $request->validate(['cell_id' => 'required|integer|exists:grid_cells,id']);

        $cell = GridCell::find($request->cell_id);
        $cell->approved_at = null;
        $cell->approved_by = null;
        $cell->save();

        return response()->json(['success' => true]);
    }

    // PDF export pagina: print-vriendelijke weergave van grid + totalen
    public function exportPdf(Request $request)
    {
        $size = (int) session('grid_size', 12);
        if ($size < 6)  $size = 6;
        if ($size > 64) $size = 64;

        $gridCells = GridCell::with('destination')
            ->orderBy('position')
            ->take($size)
            ->get();

        $calculator = new EffectsCalculator();
        $effecten   = $calculator->calculate($gridCells);
        $categories = $calculator->getCategories();

        return view('export', [
            'gridCells'  => $gridCells,
            'effecten'   => $effecten,
            'categories' => $categories,
            'gridSize'   => $size,
            'datum'      => now()->format('d-m-Y H:i'),
        ]);
    }
}
