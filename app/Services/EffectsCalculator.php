<?php

namespace App\Services;

use App\Models\FunctionEffect;

class EffectsCalculator
{
    private $categories;
    private $functions;

    public function __construct(?array $matrix = null)
    {
        if ($matrix === null) {
            try {
                self::ensureSeeded();
                $effects = FunctionEffect::all();
                if ($effects->isNotEmpty()) {
                    $this->functions  = FunctionEffect::getMatrixByFunction();
                    $this->categories = config('effects.categories');
                    return;
                }
            } catch (\Exception $e) {
                // Database niet beschikbaar — val terug op config
            }

            $matrix = [
                'categories' => config('effects.categories'),
                'functions'  => config('effects.functions'),
            ];
        }

        $this->categories = $matrix['categories'] ?? [];
        $this->functions  = $matrix['functions']  ?? [];
    }

    /**
     * Vult de function_effects tabel met de waarden uit config/effects.php
     * als er nog geen "echte" data is. Veilig om elke request aan te roepen:
     * als er ook maar één row met points != 0 in een geldige categorie staat,
     * doen we niets (zo blijven user-customizations behouden).
     */
    public static function ensureSeeded(): void
    {
        $categories = config('effects.categories', []);
        $functions  = config('effects.functions', []);

        if (empty($categories) || empty($functions)) {
            return;
        }

        $hasRealData = FunctionEffect::whereIn('category', $categories)
            ->where('points', '!=', 0)
            ->exists();

        if ($hasRealData) {
            return;
        }

        // Geen bruikbare data — wipe en herseed uit config
        FunctionEffect::truncate();
        foreach ($functions as $functionName => $effects) {
            foreach ($effects as $category => $points) {
                FunctionEffect::create([
                    'function_name' => $functionName,
                    'category'      => $category,
                    'points'        => $points,
                ]);
            }
        }
    }

    public function calculate($gridCells)
    {
        $totals = [];
        foreach ($this->categories as $cat) {
            $totals[$cat] = 0;
        }

        foreach ($gridCells as $cell) {
            if (!$cell->destination) {
                continue;
            }

            $naam = $cell->destination->name;

            if (!isset($this->functions[$naam])) {
                continue;
            }

            foreach ($this->functions[$naam] as $cat => $punten) {
                if (isset($totals[$cat])) {
                    $totals[$cat] += $punten;
                }
            }
        }

        return $totals;
    }

    public function validate()
    {
        $errors = [];

        foreach ($this->functions as $naam => $effecten) {
            foreach ($this->categories as $cat) {
                if (!array_key_exists($cat, $effecten)) {
                    $errors[] = "Functie '$naam' mist categorie '$cat'";
                }
            }

            foreach (array_keys($effecten) as $cat) {
                if (!in_array($cat, $this->categories)) {
                    $errors[] = "Functie '$naam' heeft onbekende categorie '$cat'";
                }
            }
        }

        return $errors;
    }

    public function getCategories()
    {
        return $this->categories;
    }

    public function getMatrix()
    {
        return $this->functions;
    }
}