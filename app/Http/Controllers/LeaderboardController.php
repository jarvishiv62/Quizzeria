<?php

namespace App\Http\Controllers;

use App\Models\Attempt;
use App\Models\Quiz;

class LeaderboardController extends Controller
{
    public function index(Quiz $quiz)
    {
        $attempts = Attempt::where('quiz_id', $quiz->id)
            ->whereNotNull('submitted_at')
            ->orderByDesc('score')
            ->orderBy('submitted_at')
            ->paginate(20);

        return view('leaderboard.index', compact('quiz', 'attempts'));
    }
}
