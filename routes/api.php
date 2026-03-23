<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\QuestionController;
use App\Http\Controllers\Api\ResponseController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\UserController;
Route::get('/hello',function(){
    return response()->json(['message'=>'hello world']);
});

Route::post('/register',[AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/questions', [QuestionController::class, 'index']);
Route::get('/questions/{id}', [QuestionController::class, 'show']);
Route::get('/questions/{id}/responses', [ResponseController::class, 'index']);

Route::middleware('auth:sanctum')->group(function(){
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user',[UserController::class, 'profile']);
    Route::get('/dashboard/stats',[UserController::class, 'dashboardStats']);
    Route::post('/questions',[QuestionController::class, 'store']);
    Route::put('/questions/{id}', [QuestionController::class, 'update']);
    Route::delete('/questions/{id}', [QuestionController::class, 'destroy']);
    Route::post('/questions/{questionId}/responses', [ResponseController::class, 'store']);
    Route::delete('/responses/{id}', [ResponseController::class, 'destroy']);
    Route::get('/favorites', [FavoriteController::class, 'index']);
    Route::post('/questions/{questionId}/favorite',[FavoriteController::class, 'toggle']);
});

