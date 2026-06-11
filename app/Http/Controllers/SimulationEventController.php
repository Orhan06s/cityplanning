<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GridCell;
use App\Models\SimulationEvent;

class SimulationEventController extends Controller
{
    // Eigen pagina: overzicht van alle evenementen + formulier om er een toe te voegen
    public function index(Request $request)
    {
        $size = (int) $request->query('size', 12);
        if ($size < 6)  $size = 6;
        if ($size > 64) $size = 64;

        $events = SimulationEvent::orderBy('start_date')->get();

        $gridCells = GridCell::orderBy('position')->take($size)->get();

        return view('evenementen', [
            'events'     => $events,
            'eventTypes' => config('simulation-events.types', []),
            'gridCells'  => $gridCells,
            'gridSize'   => $size,
            'gridCols'   => (int) ceil(sqrt($size)),
        ]);
    }

    // Nieuw evenement toevoegen aan de simulatie
    public function store(Request $request)
    {
        $types   = array_keys(config('simulation-events.types', []));
        $types[] = 'custom';

        $request->validate([
            'type'         => 'required|string|in:' . implode(',', $types),
            'custom_label' => 'required_if:type,custom|nullable|string|max:100',
            'custom_icon'  => 'nullable|string|max:10',
            'start_date'   => 'required|date',
            'start_time'   => 'nullable|date_format:H:i',
            'end_date'     => 'required|date|after_or_equal:start_date',
            'end_time'     => 'nullable|date_format:H:i',
            'location'     => 'nullable|integer|min:0',
        ]);

        SimulationEvent::create([
            'type'         => $request->input('type'),
            'custom_label' => $request->input('type') === 'custom' ? $request->input('custom_label') : null,
            'custom_icon'  => $request->input('type') === 'custom' ? ($request->input('custom_icon') ?: '🎉') : null,
            'location'     => $request->input('location'),
            'start_date'   => $request->input('start_date'),
            'start_time'   => $request->input('start_time'),
            'end_date'     => $request->input('end_date'),
            'end_time'     => $request->input('end_time'),
            'is_active'    => true,
        ]);

        return response()->json(['success' => true]);
    }

    // Eén evenement verwijderen (zet de grid weer terug naar normaal voor dat evenement)
    public function destroy(Request $request, SimulationEvent $event)
    {
        $event->delete();

        return response()->json(['success' => true]);
    }

    // Alle evenementen verwijderen
    public function clear(Request $request)
    {
        SimulationEvent::query()->delete();

        return response()->json(['success' => true]);
    }
}
