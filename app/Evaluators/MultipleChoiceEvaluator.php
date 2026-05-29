<?php

namespace App\Evaluators;

use App\Contracts\QuestionEvaluatorInterface;
use App\Models\Answer;
use App\Models\Question;

class MultipleChoiceEvaluator implements QuestionEvaluatorInterface
{
    public function evaluate(Question $question, Answer $answer): float
    {
        if (empty($answer->selected_option_ids)) {
            return 0;
        }

        $selectedIds = array_map('intval', $answer->selected_option_ids);

        $correctIds = $question->options()
            ->where('is_correct', true)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->sort()
            ->values()
            ->toArray();

        $selectedSorted = collect($selectedIds)->sort()->values()->toArray();

        // All correct options must be selected and NO incorrect options included
        return $selectedSorted === $correctIds
            ? (float) $question->marks
            : 0;
    }
}
