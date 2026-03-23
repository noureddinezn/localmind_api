<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\Question;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index()
    {
        $favorites = Favorite::where('user_id', auth()->id())
            ->with('question')
            ->latest()
            ->paginate(10);

        return response()->json($favorites, 200);
    }

    public function toggle(Request $request, $questionId)
    {
        $question = Question::findOrFail($questionId);

        $favorite = Favorite::where('user_id', auth()->id())
            ->where('question_id', $question->id)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json(['message' => 'Retiré des favoris'], 200);
        }

        Favorite::create([
            'user_id' => auth()->id(),
            'question_id' => $question->id,
        ]);

        return response()->json(['message' => 'Ajouté aux favoris'], 201);
    }
}