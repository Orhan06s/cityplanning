<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FunctionEffect;
use App\Models\GridCell;
use App\Services\EffectsCalculator;

class EffectsController extends Controller
{
    public function page()
    {
        $effects = FunctionEffect::orderBy('function_name')->orderBy('category')->get();
        $groupedByFunction = $effects->groupBy('function_name');

        $gridCells  = GridCell::with('destination')->get();
        $calculator = new EffectsCalculator();
        $effecten   = $calculator->calculate($gridCells);
        $categories = $calculator->getCategories();

        return view('effecten-beheren', [
            'groupedByFunction' => $groupedByFunction,
            'categories'        => $categories,
            'effecten'          => $effecten,
        ]);
    }

    public function updateEffect(Request $request)
    {
        $validated = $request->validate([
            'function_name' => 'required|string',
            'category'      => 'required|string',
            'points'        => 'required|integer|between:-5,5',
        ]);

        FunctionEffect::updateOrCreate(
            [
                'function_name' => $validated['function_name'],
                'category'      => $validated['category'],
            ],
            ['points' => $validated['points']]
        );

        return response()->json([
            'success' => true,
            'totals'  => $this->recalculateTotals(),
        ]);
    }

    public function addFunction(Request $request)
    {
        $validated = $request->validate([
            'function_name' => 'required|string|max:50|unique:function_effects,function_name',
        ]);

        $name = trim($validated['function_name']);

        $calculator = new EffectsCalculator();
        $categories = $calculator->getCategories();

        $rowEffects = [];
        foreach ($categories as $category) {
            $effect = FunctionEffect::create([
                'function_name' => $name,
                'category'      => $category,
                'points'        => 0,
            ]);
            $rowEffects[$category] = $effect->points;
        }

        return response()->json([
            'success'       => true,
            'function_name' => $name,
            'categories'    => $categories,
            'effects'       => $rowEffects,
            'totals'        => $this->recalculateTotals(),
        ]);
    }

    public function deleteFunction(Request $request)
    {
        $validated = $request->validate([
            'function_name' => 'required|string',
        ]);

        FunctionEffect::where('function_name', $validated['function_name'])->delete();

        return response()->json([
            'success' => true,
            'totals'  => $this->recalculateTotals(),
        ]);
    }

    private function recalculateTotals()
    {
        $gridCells  = GridCell::with('destination')->get();
        $calculator = new EffectsCalculator();
        return $calculator->calculate($gridCells);
    }
}
