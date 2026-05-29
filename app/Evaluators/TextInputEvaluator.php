<?php

namespace App\Evaluators;

use App\Contracts\QuestionEvaluatorInterface;
use App\Models\Answer;
use App\Models\Question;

class TextInputEvaluator implements QuestionEvaluatorInterface
{
    public function evaluate(Question $question, Answer $answer): float
    {
        if (! $answer->text_answer || ! $question->correct_text) {
            return 0;
        }

        $given   = mb_strtolower(trim($answer->text_answer));
        $correct = mb_strtolower(trim($question->correct_text));

        return $given === $correct
            ? (float) $question->marks
            : 0;
    }
}
