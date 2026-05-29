<?php

namespace App\Evaluators;

use App\Contracts\QuestionEvaluatorInterface;
use App\Models\Answer;
use App\Models\Question;

class SingleChoiceEvaluator implements QuestionEvaluatorInterface
{
    public function evaluate(Question $question, Answer $answer): float
    {
        if (empty($answer->selected_option_ids)) {
            return 0;
        }

        $selectedId = (int) $answer->selected_option_ids[0];

        $correctOption = $question->options()
            ->where('is_correct', true)
            ->first();

        if (! $correctOption) {
            return 0;
        }

        return $correctOption->id === $selectedId
            ? (float) $question->marks
            : 0;
    }
}
