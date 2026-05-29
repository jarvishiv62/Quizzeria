<?php

namespace Database\Seeders;

use App\Models\Answer;
use App\Models\Attempt;
use App\Models\Option;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Demo Quiz 1 — General Knowledge
        $quiz1 = Quiz::create([
            'title'        => 'General Knowledge Challenge',
            'description'  => 'Test your general knowledge across science, geography, and history.',
            'is_published' => true,
            'total_marks'  => 0,
        ]);

        // Q1: Binary
        $q1 = $quiz1->questions()->create([
            'type'    => 'binary',
            'content' => 'Is the Earth the largest planet in our Solar System?',
            'marks'   => 1,
            'order'   => 0,
        ]);
        $q1->options()->createMany([
            ['label' => 'Yes', 'is_correct' => false, 'order' => 0],
            ['label' => 'No',  'is_correct' => true,  'order' => 1],
        ]);

        // Q2: Single Choice
        $q2 = $quiz1->questions()->create([
            'type'    => 'single_choice',
            'content' => 'Which planet is known as the Red Planet?',
            'marks'   => 2,
            'order'   => 1,
        ]);
        $q2->options()->createMany([
            ['label' => 'Venus',  'is_correct' => false, 'order' => 0],
            ['label' => 'Mars',   'is_correct' => true,  'order' => 1],
            ['label' => 'Saturn', 'is_correct' => false, 'order' => 2],
            ['label' => 'Jupiter','is_correct' => false, 'order' => 3],
        ]);

        // Q3: Multiple Choice
        $q3 = $quiz1->questions()->create([
            'type'    => 'multiple_choice',
            'content' => 'Which of the following are programming languages? (Select all that apply)',
            'marks'   => 3,
            'order'   => 2,
        ]);
        $q3->options()->createMany([
            ['label' => 'PHP',    'is_correct' => true,  'order' => 0],
            ['label' => 'HTML',   'is_correct' => false, 'order' => 1],
            ['label' => 'Python', 'is_correct' => true,  'order' => 2],
            ['label' => 'CSS',    'is_correct' => false, 'order' => 3],
            ['label' => 'Rust',   'is_correct' => true,  'order' => 4],
        ]);

        // Q4: Number Input
        $q4 = $quiz1->questions()->create([
            'type'           => 'number_input',
            'content'        => 'How many continents are there on Earth?',
            'marks'          => 1,
            'order'          => 3,
            'correct_number' => 7,
        ]);

        // Q5: Text Input
        $q5 = $quiz1->questions()->create([
            'type'         => 'text_input',
            'content'      => 'What is the capital city of France?',
            'marks'        => 2,
            'order'        => 4,
            'correct_text' => 'Paris',
        ]);

        $quiz1->recalculateTotalMarks();

        // Demo Quiz 2 — Laravel Basics
        $quiz2 = Quiz::create([
            'title'        => 'Laravel Fundamentals',
            'description'  => 'How well do you know Laravel? Test your knowledge of the framework.',
            'is_published' => true,
            'total_marks'  => 0,
        ]);

        $lq1 = $quiz2->questions()->create([
            'type'    => 'single_choice',
            'content' => 'Which command creates a new Laravel controller?',
            'marks'   => 2,
            'order'   => 0,
        ]);
        $lq1->options()->createMany([
            ['label' => 'php artisan make:controller',  'is_correct' => true,  'order' => 0],
            ['label' => 'php artisan create:controller','is_correct' => false, 'order' => 1],
            ['label' => 'php artisan new:controller',   'is_correct' => false, 'order' => 2],
            ['label' => 'php artisan generate:controller','is_correct' => false,'order' => 3],
        ]);

        $lq2 = $quiz2->questions()->create([
            'type'    => 'binary',
            'content' => 'Does Laravel use the MVC (Model-View-Controller) architectural pattern?',
            'marks'   => 1,
            'order'   => 1,
        ]);
        $lq2->options()->createMany([
            ['label' => 'True',  'is_correct' => true,  'order' => 0],
            ['label' => 'False', 'is_correct' => false, 'order' => 1],
        ]);

        $lq3 = $quiz2->questions()->create([
            'type'         => 'text_input',
            'content'      => 'What is the name of Laravel\'s ORM?',
            'marks'        => 2,
            'order'        => 2,
            'correct_text' => 'Eloquent',
        ]);

        $quiz2->recalculateTotalMarks();

        $this->command->info('✅ Demo quizzes seeded successfully!');
        $this->command->info("Quiz 1: {$quiz1->title} ({$quiz1->total_marks} marks)");
        $this->command->info("Quiz 2: {$quiz2->title} ({$quiz2->total_marks} marks)");
    }
}
