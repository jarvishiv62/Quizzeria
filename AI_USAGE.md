# AI_USAGE.md — QuizBloom

## AI Tools Used

- **Claude (Anthropic)** — primary tool for architecture planning, code generation, and documentation
- **GitHub Copilot** — inline code completions during development

---

## Prompts Used

### Prompt 1 — Architecture Planning
> "I am building a Laravel Quiz System for a job interview assignment. It needs to support 5 question types: Binary, Single Choice, Multiple Choice, Number Input, and Text Input. The system must be extensible for future types without hardcoded logic. Design me a Strategy Pattern-based architecture with an evaluator interface, per-type evaluator classes, and a service that resolves them via a registry map. Also design the database schema with 5 tables: quizzes, questions, options, attempts, answers."

**Used for:** `app/Contracts/QuestionEvaluatorInterface.php`, all 5 evaluator classes, `QuizEvaluationService.php`, and the initial migration structure.

---

### Prompt 2 — Dynamic Form Builder (Alpine.js)
> "Write an Alpine.js `quizBuilder()` function for a Laravel Blade form that dynamically adds/removes question blocks. Each question has: a type selector (radio buttons), content textarea, marks input, image upload, and a dynamic options section that only shows for single_choice and multiple_choice types. Binary type should show a separate binary_style selector. Single choice options use radio buttons for is_correct, multiple choice uses checkboxes."

**Used for:** The `quizBuilder()` JavaScript function in `quizzes/create.blade.php` and `quizzes/edit.blade.php`.

---

### Prompt 3 — Progressive Attempt View
> "Design a Laravel Blade view for a one-question-at-a-time quiz attempt. It should show: a gradient progress bar at the top, the current question number and total, the question content rendered as HTML, media (image or YouTube embed if present), and answer inputs that differ by question type (binary = 2 large clickable cards, single = radio cards, multiple = checkbox cards, number = number input, text = text input). Include Previous and Next navigation buttons. On the last question replace Next with a Submit button."

**Used for:** `attempts/show.blade.php` including all question type rendering blocks.

---

### Prompt 4 — Results Page Score Ring
> "Generate an SVG score ring for a quiz results page. It should be a circular progress ring (SVG stroke-dasharray technique) that fills based on percentage. Use yellow (#EAB308) for passing scores and rose/red for failing. Show the percentage in the center with a Passed/Failed label. Place a decorative sunflower SVG behind the ring at low opacity."

**Used for:** The score ring SVG in `attempts/result.blade.php`.

---

### Prompt 5 — Sunflower SVG Component
> "Create a Blade component for a decorative sunflower SVG. It should have outer petals, inner petals, a center disc with seed dots, two leaves, and a stem. Use yellow (#EAB308) for petals and violet (#7C3AED) for inner petals. The component should accept size, opacity, and class props for flexible positioning."

**Used for:** `resources/views/components/sunflower.blade.php`.

---

### Prompt 6 — Leaderboard Podium
> "Design an HTML/Tailwind leaderboard podium section for a quiz results page. Show 3 columns: 2nd place on the left (medium height), 1st place in the center (tallest, yellow gradient background), 3rd place on the right (shortest). Each column shows the guest's initial in a colored avatar, their name, score, and an emoji medal (🥇🥈🥉). Below the podium show a full ranked table with rank, name, score, percentage, time taken, and date columns."

**Used for:** `leaderboard/index.blade.php` podium section and ranking table.

---

## Generated Code Areas

| File | AI Contribution |
|------|----------------|
| `QuestionEvaluatorInterface.php` | Fully generated — interface contract |
| `*Evaluator.php` (all 5) | Fully generated — evaluation logic |
| `QuizEvaluationService.php` | Fully generated — registry pattern |
| `MediaUploadService.php` | Fully generated — upload/delete logic |
| `sunflower.blade.php` | Fully generated — SVG paths and Blade props |
| `attempts/show.blade.php` | Generated and refined — all question type rendering blocks |
| `attempts/result.blade.php` | Generated and refined — score ring SVG math |
| `quizBuilder()` Alpine.js | Generated and manually debugged |
| All migrations | Generated and reviewed |

---

## Manual Corrections and Refinements

### 1. Score Ring SVG Fix
The initial AI-generated score ring had the `rotate(-90deg)` CSS applied to the outer `<svg>` wrapper, which rotated the center text too. Fixed by applying the rotation only to the progress arc circle, keeping center text upright.

### 2. Alpine.js `setSingleCorrect` Bug
The generated Alpine function toggled `is_correct` on all options independently per radio button click. This meant multiple options could be marked correct for single-choice questions. Fixed by adding `setSingleCorrect(q, selectedIndex)` which explicitly sets all options to false first, then sets only the selected one to true.

### 3. Answer Persistence on Previous Navigation
AI initially suggested redirecting "Previous" through a POST to save the answer. This caused form re-submission warnings on browser back. Changed "Previous" to a plain `<a>` GET link (answer already saved on "Next" click), which is cleaner UX.

### 4. MultipleChoiceEvaluator Edge Case
The generated evaluator used `array_diff` to compare selected vs correct IDs, but didn't account for type mismatch (string IDs from JSON vs integer IDs from database). Fixed by explicitly casting both arrays to integers before comparison using `array_map('intval', ...)`.

### 5. YouTube Embed URL Parsing
The AI-generated `getEmbedVideoUrl()` method didn't handle short `youtu.be/` URLs. Extended the method to detect and convert both `watch?v=` and `youtu.be/` formats. Also stripped extra query params after the video ID (e.g. `&t=30s`).

### 6. Sunflower SVG in Blade (Foreach Issue)
Using `@for` loops in Blade for SVG petal generation caused issues with Blade's templating when attributes contained computed PHP values. Switched petal positions to pre-computed `@foreach` arrays for the seed dot positions and simplified the petal `transform` to be directly inline.

---

## Design Decisions AI Helped With

- Choosing Strategy Pattern over switch/case in a single service method
- Using JSON column for `selected_option_ids` instead of a pivot table (simpler queries, same expressiveness for this use case)
- Snapshot pattern for `attempts.total_marks` (copy marks at time of attempt, not recalculated)
- Placing `correct_number` and `correct_text` directly on the `questions` table instead of a polymorphic answer table
- Using Alpine.js `x-data` with a factory function pattern for the quiz builder to allow multiple independent instances

## Decisions Made Manually (Not AI)

- Color palette: Yellow (#EAB308) + Violet (#7C3AED) chosen manually for punchy, warm aesthetic
- Sunflower theme chosen to match the color palette and give the app a distinctive personality
- Progressive one-question-at-a-time attempt flow (AI suggested paginated, changed to progressive for better UX)
- Answer saving on every "Next" click (not on Submit) for better UX resilience
- Guest name appearing on leaderboard as an avatar initial with gradient background
