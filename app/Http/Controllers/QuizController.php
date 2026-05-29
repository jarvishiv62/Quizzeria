<?php

namespace App\Http\Controllers;

use App\Models\Attempt;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Option;
use App\Services\MediaUploadService;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function __construct(private MediaUploadService $mediaService) {}

    public function index()
    {
        $quizzes        = Quiz::withCount(['questions', 'attempts'])->latest()->get();
        $totalQuizzes   = Quiz::count();
        $totalAttempts  = Attempt::count();
        $avgScore       = Attempt::whereNotNull('score')->get()
            ->filter(fn ($a) => $a->total_marks > 0)
            ->avg(fn ($a) => ($a->score / $a->total_marks) * 100);
        $recentAttempts = Attempt::with('quiz')->whereNotNull('submitted_at')
            ->latest()->limit(10)->get();

        return view('quizzes.index', compact(
            'quizzes', 'totalQuizzes', 'totalAttempts', 'avgScore', 'recentAttempts'
        ));
    }

    public function create()
    {
        $questionTypes = Question::TYPES;
        return view('quizzes.create', compact('questionTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'                          => 'required|string|max:255',
            'description'                    => 'nullable|string',
            'questions'                      => 'required|array|min:1',
            'questions.*.type'               => 'required|in:' . implode(',', array_keys(Question::TYPES)),
            'questions.*.content'            => 'required|string',
            'questions.*.marks'              => 'required|integer|min:1',
            'questions.*.video_url'          => 'nullable|url',
            'questions.*.correct_number'     => 'nullable|numeric',
            'questions.*.correct_text'       => 'nullable|string',
            'questions.*.image'              => 'nullable|image|max:2048',
            'questions.*.options'            => 'nullable|array',
            'questions.*.options.*.label'    => 'nullable|string',
            'questions.*.options.*.image'    => 'nullable|image|max:2048',
        ]);

        $quiz = Quiz::create([
            'title'       => $request->title,
            'description' => $request->description,
        ]);

        $this->syncQuestions($quiz, $request);

        $quiz->recalculateTotalMarks();

        return redirect()->route('quizzes.index')
            ->with('success', 'Quiz "' . $quiz->title . '" created successfully!');
    }

    public function show(Quiz $quiz)
    {
        $quiz->loadCount(['questions', 'attempts']);
        return view('quizzes.show', compact('quiz'));
    }

    public function edit(Quiz $quiz)
    {
        $quiz->load(['questions.options']);
        $questionTypes = Question::TYPES;
        return view('quizzes.edit', compact('quiz', 'questionTypes'));
    }

    public function update(Request $request, Quiz $quiz)
    {
        $request->validate([
            'title'                          => 'required|string|max:255',
            'description'                    => 'nullable|string',
            'is_published'                   => 'nullable|boolean',
            'questions'                      => 'required|array|min:1',
            'questions.*.type'               => 'required|in:' . implode(',', array_keys(Question::TYPES)),
            'questions.*.content'            => 'required|string',
            'questions.*.marks'              => 'required|integer|min:1',
            'questions.*.video_url'          => 'nullable|url',
            'questions.*.correct_number'     => 'nullable|numeric',
            'questions.*.correct_text'       => 'nullable|string',
            'questions.*.image'              => 'nullable|image|max:2048',
            'questions.*.options'            => 'nullable|array',
            'questions.*.options.*.label'    => 'nullable|string',
            'questions.*.options.*.image'    => 'nullable|image|max:2048',
        ]);

        $quiz->update([
            'title'        => $request->title,
            'description'  => $request->description,
            'is_published' => $request->boolean('is_published'),
        ]);

        // Delete existing questions and rebuild
        foreach ($quiz->questions as $q) {
            foreach ($q->options as $o) {
                $this->mediaService->delete($o->image_path);
            }
            $this->mediaService->delete($q->image_path);
            $q->options()->delete();
            $q->forceDelete();
        }

        $this->syncQuestions($quiz, $request);
        $quiz->recalculateTotalMarks();

        return redirect()->route('quizzes.edit', $quiz)
            ->with('success', 'Quiz updated successfully!');
    }

    public function destroy(Quiz $quiz)
    {
        $quiz->delete();
        return redirect()->route('quizzes.index')
            ->with('success', 'Quiz deleted successfully.');
    }

    private function syncQuestions(Quiz $quiz, Request $request): void
    {
        $questionsData = $request->input('questions', []);
        $files         = $request->file('questions', []);

        foreach ($questionsData as $index => $qData) {
            $imagePath = null;
            if (isset($files[$index]['image']) && $files[$index]['image']->isValid()) {
                $imagePath = $this->mediaService->uploadFile($files[$index]['image']);
            }

            $question = $quiz->questions()->create([
                'type'           => $qData['type'],
                'content'        => $qData['content'],
                'image_path'     => $imagePath,
                'video_url'      => $qData['video_url'] ?? null,
                'marks'          => $qData['marks'] ?? 1,
                'order'          => $index,
                'correct_number' => $qData['correct_number'] ?? null,
                'correct_text'   => $qData['correct_text'] ?? null,
            ]);

            $this->syncOptions($question, $qData, $files[$index] ?? []);
        }
    }

    private function syncOptions(Question $question, array $qData, array $qFiles): void
    {
        if ($question->type === 'binary') {
            $binaryOptions = $qData['binary_style'] === 'yes_no'
                ? [['label' => 'Yes', 'is_correct' => false], ['label' => 'No', 'is_correct' => false]]
                : [['label' => 'True', 'is_correct' => false], ['label' => 'False', 'is_correct' => false]];

            $correctIndex = (int) ($qData['binary_correct'] ?? 0);
            $binaryOptions[$correctIndex]['is_correct'] = true;

            foreach ($binaryOptions as $i => $opt) {
                $question->options()->create([
                    'label'      => $opt['label'],
                    'is_correct' => $opt['is_correct'],
                    'order'      => $i,
                ]);
            }
            return;
        }

        if (! in_array($question->type, ['single_choice', 'multiple_choice'])) {
            return;
        }

        $options = $qData['options'] ?? [];
        foreach ($options as $oi => $optData) {
            $optImagePath = null;
            if (isset($qFiles['options'][$oi]['image']) && $qFiles['options'][$oi]['image']->isValid()) {
                $optImagePath = $this->mediaService->uploadFile($qFiles['options'][$oi]['image']);
            }

            $question->options()->create([
                'label'      => $optData['label'] ?? '',
                'image_path' => $optImagePath,
                'is_correct' => isset($optData['is_correct']) && $optData['is_correct'],
                'order'      => $oi,
            ]);
        }
    }
}
