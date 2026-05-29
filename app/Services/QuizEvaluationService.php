<?php

namespace App\Services;

use App\Contracts\QuestionEvaluatorInterface;
use App\Evaluators\BinaryEvaluator;
use App\Evaluators\MultipleChoiceEvaluator;
use App\Evaluators\NumberInputEvaluator;
use App\Evaluators\SingleChoiceEvaluator;
use App\Evaluators\TextInputEvaluator;
use App\Models\Answer;
use App\Models\Attempt;
use App\Models\Question;

class QuizEvaluationService
{
    /**
     * Registry: question type string → evaluator class
     * To add a new type: add one entry here + create the Evaluator class.
     */
    private array $evaluatorRegistry = [
        'binary'          => BinaryEvaluator::class,
        'single_choice'   => SingleChoiceEvaluator::class,
        'multiple_choice' => MultipleChoiceEvaluator::class,
        'number_input'    => NumberInputEvaluator::class,
        'text_input'      => TextInputEvaluator::class,
    ];

    public function evaluate(Attempt $attempt): Attempt
    {
        $answers = $attempt->answers()->with('question')->get();

        $totalScore = 0;

        foreach ($answers as $answer) {
            $question = $answer->question;

            if (! $question) {
                continue;
            }

            $evaluator    = $this->resolveEvaluator($question->type);
            $marksAwarded = $evaluator->evaluate($question, $answer);

            $answer->marks_awarded = $marksAwarded;
            $answer->is_correct    = $marksAwarded > 0;
            $answer->save();

            $totalScore += $marksAwarded;
        }

        $attempt->score        = $totalScore;
        $attempt->submitted_at = now();
        $attempt->save();

        return $attempt;
    }

    private function resolveEvaluator(string $type): QuestionEvaluatorInterface
    {
        if (! isset($this->evaluatorRegistry[$type])) {
            throw new \InvalidArgumentException("No evaluator registered for question type: {$type}");
        }

        $evaluatorClass = $this->evaluatorRegistry[$type];
        return app($evaluatorClass);
    }
}
