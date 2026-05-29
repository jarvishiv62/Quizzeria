<?php

namespace App\Evaluators;

use App\Contracts\QuestionEvaluatorInterface;
use App\Models\Answer;
use App\Models\Question;

class NumberInputEvaluator implements QuestionEvaluatorInterface
{
    private const TOLERANCE = 0.001;

    public function evaluate(Question $question, Answer $answer): float
    {
        if ($answer->number_answer === null || $question->correct_number === null) {
            return 0;
        }

        $diff = abs((float) $answer->number_answer - (float) $question->correct_number);

        return $diff <= self::TOLERANCE
            ? (float) $question->marks
            : 0;
    }
}
