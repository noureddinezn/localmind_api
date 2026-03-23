<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\User;
use App\Models\Favorite;
use App\Models\Response as QuestionResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function profile(Request $request)
    {
        $user = $request->user()->load(['questions', 'favorites', 'responses']);
        
        return response()->json([
            'user' => $user,
            'is_admin' => $user->isAdmin(),
            'stats' => [
                'total_questions' => $user->questions()->count(),
                'total_responses' => $user->responses()->count(),
                'total_favorites' => $user->favorites()->count(),
            ]
        ], 200);
    }

    public function dashboardStats()
    {
        $user = auth()->user();

        $stats = [
            'total_questions' => Question::count(),
            'total_users' => User::count(),
            'total_responses' => QuestionResponse::count(),
            'my_questions' => $user->questions()->count(),
            'my_responses' => $user->responses()->count(),
            'my_favorites' => $user->favorites()->count(),
        ];

        if ($user->isAdmin()) {
            // Get most popular questions
            $popularQuestions = Question::withCount('responses')
                ->orderBy('responses_count', 'desc')
                ->limit(5)
                ->get();

            // Get most active users
            $activeUsers = User::withCount('questions', 'responses')
                ->orderBy('questions_count', 'desc')
                ->limit(5)
                ->get();

            $stats['popular_questions'] = $popularQuestions;
            $stats['active_users'] = $activeUsers;
            $stats['total_favorites'] = Favorite::count();
        }

        return response()->json([
            'stats' => $stats,
            'is_admin' => $user->isAdmin()
        ], 200);
    }
}