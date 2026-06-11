<?php

namespace App\Http\Controllers;

use App\Models\GridFeedback;
use App\Models\GridCell;
use Illuminate\Http\Request;

class GridFeedbackController extends Controller
{
    // Feedback-pagina voor de beleidsmaker (grid + formulier + lijst)
    public function index(Request $request)
    {
        $size      = max(6, min(64, (int) $request->query('size', 12)));
        $gridCols  = (int) ceil(sqrt($size));
        $gridSize  = $size; // ← dit toevoegen
        $gridCells = GridCell::with('destination')->orderBy('position')->take($size)->get();
        $feedback  = GridFeedback::where('user_id', auth()->id())->orderBy('created_at', 'desc')->get();
        $submitted = $feedback->where('submitted', true)->first();
        
        return view('beleidsmaker.feedback', compact('gridCells', 'gridCols', 'gridSize', 'feedback', 'submitted', 'size'));
    }

    // Sla een nieuwe opmerking of verbetering op
    public function store(Request $request)
    {
        $request->validate([
            'comment'  => 'required|string|max:1000',
            'type'     => 'required|in:comment,improvement',
            'position' => 'nullable|integer|min:0',
        ]);

        $item = GridFeedback::create([
            'user_id'  => auth()->id(),
            'position' => $request->input('position'),
            'comment'  => $request->input('comment'),
            'type'     => $request->input('type'),
        ]);

        return response()->json(['success' => true, 'id' => $item->id]);
    }

    // Verwijder één feedbackitem
    public function destroy(GridFeedback $feedback)
    {
        abort_unless($feedback->user_id === auth()->id(), 403);
        $feedback->delete();
        return response()->json(['success' => true]);
    }

    // Stel de overall simulatiestatus in en verstuur naar de stedenbouwers
    public function submit(Request $request)
    {
        $request->validate([
            'status' => 'required|in:approved,declined',
        ]);

        GridFeedback::where('user_id', auth()->id())->update([
            'status'    => $request->input('status'),
            'submitted' => true,
        ]);

        return response()->json(['success' => true]);
    }
}