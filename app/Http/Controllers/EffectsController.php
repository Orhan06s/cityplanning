<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\NewFunctionAdded;
use App\Models\Admin;
use App\Models\Destination;
use App\Models\FunctionEffect;
use App\Models\GridCell;
use App\Services\EffectsCalculator;
use App\Models\DayNightModifier;

class EffectsController extends Controller
{
    // Standaard icoon per categorie
    private const CATEGORY_ICONS = [
        'veiligheid'      => '🚓',
        'recreatie'       => '🌳',
        'milieukwaliteit' => '💧',
        'voorzieningen'   => '🏥',
        'mobiliteit'      => '🚉',
    ];

    // Emoji's die in de modal te kiezen zijn
    private const PRESET_ICONS = [
        'veiligheid'      => ['🚓', '🚒', '🚨', '🛡️', '👮', '🏛️'],
        'recreatie'       => ['🌳', '🎬', '⚽', '🎨', '🎭', '🎢', '🏊', '🌸'],
        'milieukwaliteit' => ['💧', '🌱', '♻️', '🌍', '🌊', '🌲'],
        'voorzieningen'   => ['🏫', '🏬', '🏥', '⛪', '🏪', '🏨', '🏢'],
        'mobiliteit'      => ['🚉', '🛣️', '🚲', '⛽', '🚌', '🚇', '✈️', '⚓'],
    ];

    public function page()
    {
        EffectsCalculator::ensureSeeded();

        $effects = FunctionEffect::orderBy('function_name')->orderBy('category')->get();
        $groupedByFunction = $effects->groupBy('function_name');

        $calculator = new EffectsCalculator();
        $categories = $calculator->getCategories();

        $dayNightModifiers = DayNightModifier::all()
            ->groupBy('function_name')
            ->map(fn($rows) => $rows->keyBy('category'));

        return view('effecten-beheren', [
            'groupedByFunction' => $groupedByFunction,
            'categories'        => $categories,
            'presetIcons'       => self::PRESET_ICONS,
            'categoryIcons'     => self::CATEGORY_ICONS,
            'dayNightModifiers'  => $dayNightModifiers,
        ]);
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

    return response()->json(['success' => true]);
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
        $calculator = new EffectsCalculator();
        $categories = $calculator->getCategories();

        $validated = $request->validate([
            'function_name' => 'required|string|max:50|unique:destinations,name|unique:function_effects,function_name',
            'name_en'       => 'nullable|string|max:50',
            'category'      => 'required|string|in:' . implode(',', $categories),
            'description'   => 'nullable|string|max:255',
            'icon'          => 'nullable|string|max:10',
            'icon_file'     => 'nullable|image|mimes:png,jpg,jpeg,gif,webp,svg|max:2048',
            'effects'       => 'nullable|array',
            'effects.*'     => 'integer|between:-5,5',
        ]);

        $name     = trim($validated['function_name']);
        $nameEn   = !empty($validated['name_en']) ? trim($validated['name_en']) : $name;
        $category = $validated['category'];
        $icon     = $this->resolveIcon($request, $validated, $category);
        $effects  = $validated['effects'] ?? [];

        DB::transaction(function () use ($name, $nameEn, $category, $validated, $icon, $categories, $effects) {
            Destination::create([
                'name'        => $name,
                'name_en'     => $nameEn,
                'category'    => $category,
                'icon'        => $icon,
                'description' => $validated['description'] ?? null,
            ]);

            foreach ($categories as $cat) {
                FunctionEffect::create([
                    'function_name' => $name,
                    'category'      => $cat,
                    'points'        => isset($effects[$cat]) ? (int) $effects[$cat] : 0,
                ]);
            }
        });

        // Stuur een notificatie-mail naar alle geregistreerde admins
        // Try-catch zodat een mail-fout nooit de functie-toevoeging blokkeert
        try {
            Admin::all()->each(function (Admin $admin) use ($name, $category, $validated, $icon) {
                Mail::to($admin->email, $admin->name)
                    ->send(new NewFunctionAdded(
                        $name,
                        $category,
                        $validated['description'] ?? null,
                        $icon,
                    ));
            });
        } catch (\Exception $e) {
            \Log::warning('Mail kon niet verstuurd worden: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => __('Functie toegevoegd!'),
        ]);
    }

    public function deleteFunction(Request $request)
    {
        $validated = $request->validate([
            'function_name' => 'required|string',
        ]);

        DB::transaction(function () use ($validated) {
            FunctionEffect::where('function_name', $validated['function_name'])->delete();
            Destination::where('name', $validated['function_name'])->delete();
        });

        session()->flash('success', __('Functie verwijderd!'));

        return response()->json(['success' => true]);
    }

    public function resetToDefaults()
    {
        DB::table('function_effects')->delete();

        $functions = config('effects.functions', []);
        $now = now();
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

         app(\Database\Seeders\DayNightModifiersSeeder::class)->run();

        session()->flash('success', __('Effecten gereset naar standaardwaarden!'));

        return response()->json(['success' => true]);
    }

    // Bepaalt welk icoon gebruikt wordt
    private function resolveIcon(Request $request, array $validated, string $category): string
    {
        if ($request->hasFile('icon_file') && $request->file('icon_file')->isValid()) {
            $file = $request->file('icon_file');
            $ext  = strtolower($file->getClientOriginalExtension());
            $base = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) ?: 'icon';
            $filename = $base . '-' . time() . '.' . $ext;

            $dest = public_path('icons');
            if (!is_dir($dest)) {
                @mkdir($dest, 0775, true);
            }
            $file->move($dest, $filename);

            return '/icons/' . $filename;
        }

        if (!empty($validated['icon'])) {
            return $validated['icon'];
        }

        return self::CATEGORY_ICONS[$category] ?? '📍';
    }

    private function recalculateTotals()
    {
        $gridCells  = GridCell::with('destination')->get();
        $calculator = new EffectsCalculator();
        return $calculator->calculate($gridCells);
    }
}
