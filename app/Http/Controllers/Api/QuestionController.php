<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Favorite;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function index(Request $request)
    {
        $query = Question::with(['user', 'responses']);

        // Filter by location
        if ($request->has('location') && !empty($request->location)) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        // Search by keyword in title or content
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('content', 'like', '%' . $search . '%');
            });
        }

        // Filter by latitude/longitude (if provided)
        if ($request->has('latitude') && $request->has('longitude') && $request->has('radius')) {
            $latitude = $request->latitude;
            $longitude = $request->longitude;
            $radius = $request->radius; // in km

            // Using Haversine formula for distance calculation
            $query->selectRaw(
                "*, (
                    6371 * acos(cos(radians(?)) * cos(radians(latitude)) *
                    cos(radians(longitude) - radians(?)) +
                    sin(radians(?)) * sin(radians(latitude)))
                ) AS distance",
                [$latitude, $longitude, $latitude]
            )
            ->having('distance', '<=', $radius)
            ->orderBy('distance');
        }

        $questions = $query->latest()->paginate(10);
        return response()->json($questions, 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'location' => 'required|string|max:255',
        ]);

        $question = Question::create([
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'content' => $validated['content'],
            'location' => $validated['location'],
        ]);

        return response()->json([
            'message' => 'Question créée avec succès',
            'question' => $question
        ], 201);
    }

    public function show($id)
    {
        $question = Question::with(['user', 'responses.user', 'favorites'])->findOrFail($id);
        
        $isFavorited = false;
        if (auth()->check()) {
            $isFavorited = Favorite::where('user_id', auth()->id())
                ->where('question_id', $question->id)
                ->exists();
        }

        return response()->json([
            'question' => $question,
            'is_favorited' => $isFavorited,
            'response_count' => $question->responses->count(),
            'favorite_count' => $question->favorites->count(),
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $question = Question::findOrFail($id);

        if ($question->user_id !== auth()->id()) {
            return response()->json(['message' => 'Non autorisé à modifier cette question'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'location' => 'required|string|max:255',
        ]);

        $question->update($validated);

        return response()->json([
            'message' => 'Question mise à jour avec succès',
            'question' => $question
        ], 200);
    }

    public function destroy($id)
    {
        $question = Question::findOrFail($id);
        $user = auth()->user();

        // Allow deletion if user is owner or admin
        if ($question->user_id !== $user->id && !$user->isAdmin()) {
            return response()->json(['message' => 'Non autorisé à supprimer cette question'], 403);
        }

        $question->delete();

        return response()->json(['message' => 'Question supprimée avec succès'], 200);
    }
}