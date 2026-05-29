# ARCHITECTURE.md — QuizBloom

## 1. Database Design

```
┌─────────────┐       ┌──────────────────┐       ┌─────────────┐
│   quizzes   │──────<│    questions     │──────<│   options   │
│─────────────│       │──────────────────│       │─────────────│
│ id          │       │ id               │       │ id          │
│ title       │       │ quiz_id (FK)     │       │ question_id │
│ description │       │ type (enum)      │       │ label       │
│ is_published│       │ content          │       │ image_path  │
│ total_marks │       │ image_path       │       │ is_correct  │
│ deleted_at  │       │ video_url        │       │ order       │
└─────────────┘       │ marks            │       └─────────────┘
                      │ order            │
       ┌──────────────│ correct_number   │
       │              │ correct_text     │
       │              │ deleted_at       │
       │              └──────────────────┘
       │
┌──────┴──────┐       ┌─────────────────┐
│  attempts   │──────<│    answers      │
│─────────────│       │─────────────────│
│ id          │       │ id              │
│ quiz_id(FK) │       │ attempt_id (FK) │
│ guest_name  │       │ question_id(FK) │
│ score       │       │selected_opt_ids │
│ total_marks │       │ text_answer     │
│ started_at  │       │ number_answer   │
│ submitted_at│       │ is_correct      │
└─────────────┘       │ marks_awarded   │
                      └─────────────────┘
```

### Design Decisions
- `questions.type` uses an enum — adding a new type requires one migration line
- `answers.selected_option_ids` is JSON — stores one or many option IDs for both single and multiple choice with the same column
- `questions.correct_number` and `correct_text` store expected answers directly on the question for input-type questions, avoiding a separate table
- `attempts.total_marks` is a snapshot — if quiz marks change later, historical results remain accurate
- Soft deletes on `quizzes` and `questions` protect historical attempt/answer records

---

## 2. Strategy Pattern — Evaluation Engine

The core design principle is that **no controller or service contains hardcoded `if ($type === 'binary')` logic**.

### Interface
```php
// app/Contracts/QuestionEvaluatorInterface.php
interface QuestionEvaluatorInterface {
    public function evaluate(Question $question, Answer $answer): float;
}
```

### Evaluator Classes
Each question type has exactly one evaluator class:

```
app/Evaluators/
├── BinaryEvaluator.php          — checks selected option matches is_correct
├── SingleChoiceEvaluator.php    — checks single selected option
├── MultipleChoiceEvaluator.php  — checks exact set match (all correct, none wrong)
├── NumberInputEvaluator.php     — numeric comparison with ±0.001 tolerance
└── TextInputEvaluator.php       — case-insensitive trimmed string comparison
```

### Registry Map in QuizEvaluationService
```php
private array $evaluatorRegistry = [
    'binary'          => BinaryEvaluator::class,
    'single_choice'   => SingleChoiceEvaluator::class,
    'multiple_choice' => MultipleChoiceEvaluator::class,
    'number_input'    => NumberInputEvaluator::class,
    'text_input'      => TextInputEvaluator::class,
];
```

The service loops through all answers, resolves the evaluator via `app($class)` (Laravel DI), calls `evaluate()`, stores `marks_awarded` and `is_correct` per answer, then sums the total score.

---

## 3. How to Add a New Question Type

Adding a new type (e.g. `ordering`) requires exactly **4 steps** — zero changes to existing code:

**Step 1 — Migration**
```php
// Change enum to include new value
$table->enum('type', ['binary','single_choice','multiple_choice','number_input','text_input','ordering']);
```

**Step 2 — Create Evaluator**
```php
// app/Evaluators/OrderingEvaluator.php
class OrderingEvaluator implements QuestionEvaluatorInterface {
    public function evaluate(Question $question, Answer $answer): float {
        // your logic here
    }
}
```

**Step 3 — Register in QuizEvaluationService**
```php
'ordering' => OrderingEvaluator::class,
```

**Step 4 — Add Blade partial**
Add an `@if($question->type === 'ordering')` block in `attempts/show.blade.php`
for rendering the UI input.

That's it. No other files touched.

---

## 4. Service Layer

### QuizEvaluationService
- Single responsibility: evaluate a submitted attempt
- Resolves evaluators via Laravel's service container
- Persists `marks_awarded` and `is_correct` per answer
- Updates `attempt.score` and `attempt.submitted_at`

### MediaUploadService
- Handles `UploadedFile` → `storage/app/public/quiz-media/`
- Uses UUID filenames to prevent collisions
- `delete()` method for cleanup on quiz edit/delete
- Keeps all file-system concerns out of controllers

---

## 5. Blade Component Architecture

| Component          | Purpose                                              |
|--------------------|------------------------------------------------------|
| `layouts/app`      | Global shell — nav, fonts, Alpine.js, flash alerts   |
| `x-sunflower`      | Reusable decorative SVG — accepts size/opacity/class |
| `x-quiz-card`      | Dashboard quiz card — accepts Quiz model             |

The sunflower SVG is a pure Blade component (no JS, no images) — it renders inline SVG with configurable size, opacity, and CSS class for positioning. This keeps decorative assets as zero-dependency markup.

---

## 6. Evaluation Logic Flow

```
POST /attempts/{attempt}/submit
        │
        ▼
AttemptController@submit
        │
        ▼
QuizEvaluationService@evaluate(Attempt)
        │
        ├── foreach Answer in attempt
        │       │
        │       ├── resolve evaluator from registry[$question->type]
        │       │
        │       ├── $marks = $evaluator->evaluate($question, $answer)
        │       │
        │       └── persist answer.marks_awarded + answer.is_correct
        │
        ├── sum all marks_awarded → attempt.score
        │
        └── set attempt.submitted_at = now()
                │
                ▼
        redirect → AttemptController@result
```

---

## 7. Media Upload Flow

```
QuizController@store (form submission)
        │
        ├── foreach question in request
        │       │
        │       ├── if $files[$i]['image'] exists
        │       │       └── MediaUploadService@uploadFile(UploadedFile)
        │       │               └── stores to storage/app/public/quiz-media/{uuid}.ext
        │       │               └── returns relative path string
        │       │
        │       └── Question::create(['image_path' => $path])
        │
        └── foreach option in question
                └── same pattern for option images

Rendered via:
        Storage::url($question->image_path)
        → /storage/quiz-media/{uuid}.ext  (via storage:link symlink)
```

---

## 8. Routing Structure

All routes are resource-friendly and RESTful:

| Method | URI | Controller | Purpose |
|--------|-----|------------|---------|
| GET | / | QuizController@index | Dashboard |
| GET | /quizzes/create | QuizController@create | Create form |
| POST | /quizzes | QuizController@store | Save quiz |
| GET | /quizzes/{quiz}/edit | QuizController@edit | Edit form |
| PUT | /quizzes/{quiz} | QuizController@update | Update quiz |
| DELETE | /quizzes/{quiz} | QuizController@destroy | Delete quiz |
| GET | /quizzes/{quiz}/attempt | AttemptController@create | Guest form |
| POST | /quizzes/{quiz}/attempt | AttemptController@store | Start attempt |
| GET | /attempts/{attempt}/question/{order} | AttemptController@showQuestion | Show question |
| POST | /attempts/{attempt}/question/{order} | AnswerController@store | Save answer |
| POST | /attempts/{attempt}/submit | AttemptController@submit | Evaluate + finish |
| GET | /attempts/{attempt}/result | AttemptController@result | Show result |
| GET | /quizzes/{quiz}/leaderboard | LeaderboardController@index | Leaderboard |
