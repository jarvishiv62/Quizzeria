<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Answer extends Model
{
    protected $fillable = [
        'attempt_id',
        'question_id',
        'selected_option_ids',
        'text_answer',
        'number_answer',
        'is_correct',
        'marks_awarded',
    ];

    protected $casts = [
        'selected_option_ids' => 'array',
        'is_correct'          => 'boolean',
        'marks_awarded'       => 'float',
        'number_answer'       => 'float',
    ];

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(Attempt::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class)->withTrashed();
    }
}
