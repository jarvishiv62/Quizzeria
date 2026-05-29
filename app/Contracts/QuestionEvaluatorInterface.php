<?php

namespace App\Contracts;

use App\Models\Answer;
use App\Models\Question;

interface QuestionEvaluatorInterface
{
    /**
     * Evaluate an answer against the question's correct answer.
     * Returns marks awarded (0 or full marks).
     */
    public function evaluate(Question $question, Answer $answer): float;
}
