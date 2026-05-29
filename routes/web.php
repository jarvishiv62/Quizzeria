<?php

use App\Http\Controllers\AnswerController;
use App\Http\Controllers\AttemptController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\QuizController;
use Illuminate\Support\Facades\Route;

// Dashboard
Route::get('/', [QuizController::class, 'index'])->name('quizzes.index');

// Quiz CRUD
Route::get('/quizzes/create',        [QuizController::class, 'create'])->name('quizzes.create');
Route::post('/quizzes',              [QuizController::class, 'store'])->name('quizzes.store');
Route::get('/quizzes/{quiz}',        [QuizController::class, 'show'])->name('quizzes.show');
Route::get('/quizzes/{quiz}/edit',   [QuizController::class, 'edit'])->name('quizzes.edit');
Route::put('/quizzes/{quiz}',        [QuizController::class, 'update'])->name('quizzes.update');
Route::delete('/quizzes/{quiz}',     [QuizController::class, 'destroy'])->name('quizzes.destroy');

// Leaderboard
Route::get('/quizzes/{quiz}/leaderboard', [LeaderboardController::class, 'index'])->name('quizzes.leaderboard');

// Quiz Attempt
Route::get('/quizzes/{quiz}/attempt',  [AttemptController::class, 'create'])->name('attempts.create');
Route::post('/quizzes/{quiz}/attempt', [AttemptController::class, 'store'])->name('attempts.store');

// Progressive question display + answer saving
Route::get('/attempts/{attempt}/question/{order}',
    [AttemptController::class, 'showQuestion'])->name('attempts.question');
Route::post('/attempts/{attempt}/question/{order}',
    [AnswerController::class, 'store'])->name('attempts.answer');

// Submit & Result
Route::post('/attempts/{attempt}/submit', [AttemptController::class, 'submit'])->name('attempts.submit');
Route::get('/attempts/{attempt}/result',  [AttemptController::class, 'result'])->name('attempts.result');
