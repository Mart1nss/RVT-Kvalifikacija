<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Http\Requests\StoreNoteRequest;
use Illuminate\Http\Request;
use Auth; 

class NoteController extends Controller
{
    public function store(StoreNoteRequest $request)
    {
        $note = Note::where('product_id', $request->product_id)
            ->where('user_id', Auth::id())
            ->first();

        if (empty($request->note_text)) {
            if ($note) {
                $note->delete();
                return response()->json(['message' => 'Note deleted.']);
            }
            return response()->json(['message' => 'No note to delete.']);
        }

        if ($note) {
            // Update existing note
            $note->note_text = $request->note_text;
            $note->save();
        } else {
            // Create new note
            $note = Note::create([
                'note_text' => $request->note_text,
                'user_id' => Auth::id(),
                'product_id' => $request->product_id
            ]);
        }

        return response()->json(['message' => 'Note saved.', 'note' => $note]);
    }


    public function show($productId)
    {
        $note = Note::where('product_id', $productId)
            ->where('user_id', Auth::id())
            ->first();

        return response()->json($note);
    }

    public function index()
    {
        $notes = Auth::user()->notes()->orderBy('updated_at', 'desc')->get();
        return view('viewnotes', compact('notes'));
    }
}