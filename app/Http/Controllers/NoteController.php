<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Http\Requests\StoreNoteRequest;
use Illuminate\Http\Request;
use Auth; 

class NoteController extends Controller
{
    /**
     * Saglabā vai atjaunina piezīmi konkrētam produktam (grāmatai).
     * Ja piezīmes teksts ir tukšs un piezīme pastāv, tā tiek dzēsta.
     *
     * @param  \App\Http\Requests\StoreNoteRequest
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreNoteRequest $request)
    {
        $note = Note::where('product_id', $request->product_id)
            ->where('user_id', Auth::id())
            ->first();

        // Ja pieprasījumā nav piezīmes teksta
        if (empty($request->note_text)) {
            if ($note) {
                $note->delete();
                return response()->json(['message' => 'Piezīme dzēsta.']);
            }
            return response()->json(['message' => 'Nav piezīmes, ko dzēst.']);
        }

        // Ja piezīme pastāv, atjaunina tās tekstu
        if ($note) {
            $note->note_text = $request->note_text;
            $note->save();
        } else {
            $note = Note::create([
                'note_text' => $request->note_text,
                'user_id' => Auth::id(),
                'product_id' => $request->product_id
            ]);
        }

        return response()->json(['message' => 'Piezīme saglabāta.', 'note' => $note]);
    }

    /**
     * Parāda konkrēta produkta piezīmi autentificētam lietotājam.
     *
     * @param  int 
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($productId)
    {
        $note = Note::where('product_id', $productId)
            ->where('user_id', Auth::id())
            ->first();

        return response()->json($note);
    }

    /**
     * Parāda visas autentificētā lietotāja piezīmes.
     * Piezīmes tiek kārtotas pēc pēdējās atjaunināšanas datuma dilstošā secībā.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $notes = Auth::user()->notes()->orderBy('updated_at', 'desc')->get();
        return view('viewnotes', compact('notes'));
    }

    /**
     * Dzēš konkrētu piezīmi.
     *
     * @param  \App\Models\Note  $note
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Note $note)
    {
        // Pārbauda, vai autentificētais lietotājs ir piezīmes īpašnieks
        if (Auth::id() !== $note->user_id) {
            return redirect()->route('notes.index')->with('error', 'You are not authorized to delete this note.');
        }

        $note->delete();

        return redirect()->route('notes.index')->with('success', 'Note deleted successfully.');
    }
}
