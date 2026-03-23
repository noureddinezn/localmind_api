<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Response as QuestionResponse;
use Illuminate\Http\Request;

class ResponseController extends Controller
{
    public function index($questionId)
    {
        $question = Question::findOrFail($questionId);
        
        $responses = $question->responses()
            ->with('user')
            ->latest()
            ->paginate(10);

        return response()->json([
            'question_id' => $questionId,
            'responses' => $responses
        ], 200);
    }

    public function store(Request $request, $questionId)
    {
        $question = Question::findOrFail($questionId);

        $validated = $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        $response = QuestionResponse::create([
            'user_id' => auth()->id(),
            'question_id' => $question->id,
            'content' => $validated['content'],
        ]);

        return response()->json([
            'message' => 'Réponse ajoutée avec succès',
            'response' => $response
        ], 201);
    }

    public function destroy($id)
    {
        $response = QuestionResponse::findOrFail($id);
        $user = auth()->user();

        // Allow deletion if user is owner or admin
        if ($response->user_id !== $user->id && !$user->isAdmin()) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $response->delete();

        return response()->json(['message' => 'Réponse supprimée avec succès'], 200);
    }
}