<?php

namespace App\Services;

use App\Models\FunctionEffect;
use Illuminate\Support\Facades\DB;

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
     * Vult function_effects met config-waarden als de data "kapot" is:
     * - tabel leeg, OF
     * - minder dan 10 rijen met juiste categorie + non-zero punten
     *   (default config heeft ~50 non-zero rows; <10 = duidelijk verkeerde data)
     *
     * Gebruikt force delete + raw insert om foreign-key/cascade issues te omzeilen.
     */
    public static function ensureSeeded(): void
    {
        $categories = config('effects.categories', []);
        $functions  = config('effects.functions', []);

        if (empty($categories) || empty($functions)) {
            return;
        }

        $nonZeroCount = FunctionEffect::whereIn('category', $categories)
            ->where('points', '!=', 0)
            ->count();

        // Genoeg bruikbare data → niet aanraken (respecteert user customizations)
        if ($nonZeroCount >= 10) {
            return;
        }

        // Data is leeg of helemaal kapot → wipe + herbouwen uit config
        DB::table('function_effects')->delete();

        $now  = now();
        $rows = [];
        foreach ($functions as $functionName => $effects) {
            foreach ($effects as $category => $points) {
                $rows[] = [
                    'function_name' => $functionName,
                    'category'      => $category,
                    'points'        => $points,
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ];
            }
        }

        DB::table('function_effects')->insert($rows);
    }

    public function calculate($gridCells)
    {
        $totals = [];
        foreach ($this->categories as $cat) {
            $totals[$cat] = 0;
        }

        // Basis-punten per functie
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

        // Bonus-punten voor functies die naast elkaar staan (BES.3.3)
        $this->applyAdjacencyBonuses($gridCells, $totals);

        return $totals;
    }

    private function applyAdjacencyBonuses($gridCells, array &$totals): void
    {
        $bonusPairs = config('function-rules.bonus_pairs', []);
        $cols       = config('function-rules.grid_cols', 4);

        if (empty($bonusPairs)) return;

        // Map positie -> functienaam
        $byPos = [];
        foreach ($gridCells as $cell) {
            if ($cell->destination) {
                $byPos[$cell->position] = $cell->destination->name;
            }
        }

        // Per cel naar rechts/onder kijken — zo telt elk paar maar 1x
        foreach ($byPos as $pos => $name) {
            $col = $pos % $cols;
            $neighbors = [$pos + $cols]; // onder
            if ($col < $cols - 1) $neighbors[] = $pos + 1; // rechts

            foreach ($neighbors as $nPos) {
                if (!isset($byPos[$nPos])) continue;
                $other = $byPos[$nPos];
                if ($other === $name) continue;

                foreach ($bonusPairs as $bp) {
                    if (in_array($name, $bp['functions'], true) && in_array($other, $bp['functions'], true)) {
                        if (isset($totals[$bp['category']])) {
                            $totals[$bp['category']] += $bp['points'];
                        }
                    }
                }
            }
        }
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