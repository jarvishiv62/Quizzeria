<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['binary', 'single_choice', 'multiple_choice', 'number_input', 'text_input']);
            $table->text('content');
            $table->string('image_path')->nullable();
            $table->string('video_url')->nullable();
            $table->unsignedInteger('marks')->default(1);
            $table->unsignedInteger('order')->default(0);
            $table->decimal('correct_number', 15, 4)->nullable();
            $table->string('correct_text')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
