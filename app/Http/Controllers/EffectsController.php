<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FunctionEffect;
use App\Services\EffectsCalculator;

class EffectsController extends Controller
{
    public function getManager()
    {
        $effects = FunctionEffect::orderBy('function_name')->orderBy('category')->get();
        $groupedByFunction = $effects->groupBy('function_name');

        $calculator = new EffectsCalculator();
        $categories = $calculator->getCategories();

        return view('partials.effects-manager', [
            'groupedByFunction' => $groupedByFunction,
            'categories' => $categories,
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

        return response()->json(['success' => true]);
    }

    public function addFunction(Request $request)
    {
        $validated = $request->validate([
            'function_name' => 'required|string|unique:function_effects,function_name',
        ]);

        $calculator = new EffectsCalculator();
        $categories = $calculator->getCategories();

        foreach ($categories as $category) {
            FunctionEffect::create([
                'function_name' => $validated['function_name'],
                'category'      => $category,
                'points'        => 0,
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function deleteFunction(Request $request)
    {
        $validated = $request->validate([
            'function_name' => 'required|string',
        ]);

        FunctionEffect::where('function_name', $validated['function_name'])->delete();

        return response()->json(['success' => true]);
    }
}
