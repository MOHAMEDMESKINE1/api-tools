<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Note;
use Illuminate\Http\Request;
use App\Http\Requests\NoteRequest;
use App\Http\Resources\NoteResource;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = $request->input('search');
        if ($query) {
            $notes = Note::where('title', 'LIKE', "%{$query}%")
                ->orWhere('content', 'LIKE', "%{$query}%")
                ->get();
        } else {
            $notes = Note::all();
        }

        return NoteResource::collection($notes);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(NoteRequest $request)
    {
        try {
            $note = Note::create($request->validated());

            return new NoteResource($note);
        } catch (Exception $ex) {
            return response()->json($ex->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Note $note)
    {
        return new NoteResource($note);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(NoteRequest $request, Note $note)
    {
        try {
            $note->update($request->validated());

            return new NoteResource($note);
        } catch (Exception $ex) {
            return response()->json($ex->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Note $note)
    {
        $note->delete();

        return new NoteResource($note);
    }
}
