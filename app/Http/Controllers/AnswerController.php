<?php

namespace App\Http\Controllers;

use App\Models\Attempt;
use Illuminate\Http\Request;

class AnswerController extends Controller
{
    /** Save answer for current question and advance to next */
    public function store(Request $request, Attempt $attempt, int $order)
    {
        if ($attempt->isSubmitted()) {
            return redirect()->route('attempts.result', $attempt);
        }

        $questionId = $request->input('question_id');

        if ($questionId) {
            $answer = $attempt->answers()->firstOrNew(['question_id' => $questionId]);
            $answer->selected_option_ids = $request->input('selected_option_ids');
            $answer->text_answer         = $request->input('text_answer');
            $answer->number_answer       = $request->input('number_answer');
            $answer->save();
        }

        $total   = $attempt->quiz->questions()->count();
        $nextOrder = $order + 1;

        if ($nextOrder >= $total) {
            return redirect()->route('attempts.submit', $attempt);
        }

        return redirect()->route('attempts.question', [
            'attempt' => $attempt->id,
            'order'   => $nextOrder,
        ]);
    }
}
