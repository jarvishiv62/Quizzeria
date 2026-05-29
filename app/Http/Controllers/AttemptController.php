<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Attempt;
use App\Models\Quiz;
use App\Services\QuizEvaluationService;
use Illuminate\Http\Request;

class AttemptController extends Controller
{
    public function __construct(private QuizEvaluationService $evaluationService) {}

    /** Show guest name entry form */
    public function create(Quiz $quiz)
    {
        $quiz->loadCount('questions');
        return view('attempts.guest-form', compact('quiz'));
    }

    /** Create attempt record and redirect to first question */
    public function store(Request $request, Quiz $quiz)
    {
        $request->validate([
            'guest_name' => 'required|string|max:100',
        ]);

        $attempt = Attempt::create([
            'quiz_id'     => $quiz->id,
            'guest_name'  => trim($request->guest_name),
            'total_marks' => $quiz->total_marks,
            'started_at'  => now(),
        ]);

        return redirect()->route('attempts.question', ['attempt' => $attempt->id, 'order' => 0]);
    }

    /** Show a single question by order index */
    public function showQuestion(Attempt $attempt, int $order)
    {
        if ($attempt->isSubmitted()) {
            return redirect()->route('attempts.result', $attempt);
        }

        $questions = $attempt->quiz->questions()->orderBy('order')->get();
        $total     = $questions->count();

        if ($order < 0 || $order >= $total) {
            return redirect()->route('attempts.question', ['attempt' => $attempt->id, 'order' => 0]);
        }

        $question = $questions[$order];

        // Load existing answer for this question if any
        $existingAnswer = $attempt->answers()
            ->where('question_id', $question->id)
            ->first();

        return view('attempts.show', compact(
            'attempt', 'question', 'questions', 'order', 'total', 'existingAnswer'
        ));
    }

    /** Submit the entire quiz → evaluate and redirect to result */
    public function submit(Request $request, Attempt $attempt)
    {
        if ($attempt->isSubmitted()) {
            return redirect()->route('attempts.result', $attempt);
        }

        // Save the last answer if submitted alongside
        if ($request->has('question_id')) {
            $this->saveAnswer($request, $attempt);
        }

        $this->evaluationService->evaluate($attempt);

        return redirect()->route('attempts.result', $attempt);
    }

    /** Show result page */
    public function result(Attempt $attempt)
    {
        $attempt->load(['quiz', 'answers.question.options']);

        $leaderboard = Attempt::where('quiz_id', $attempt->quiz_id)
            ->whereNotNull('submitted_at')
            ->orderByDesc('score')
            ->orderBy('submitted_at')
            ->limit(10)
            ->get();

        return view('attempts.result', compact('attempt', 'leaderboard'));
    }

    private function saveAnswer(Request $request, Attempt $attempt): void
    {
        $questionId = $request->input('question_id');

        $answer = $attempt->answers()->firstOrNew(['question_id' => $questionId]);
        $answer->selected_option_ids = $request->input('selected_option_ids');
        $answer->text_answer         = $request->input('text_answer');
        $answer->number_answer       = $request->input('number_answer');
        $answer->save();
    }
}
