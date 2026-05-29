<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Option extends Model
{
    protected $fillable = [
        'question_id',
        'label',
        'image_path',
        'is_correct',
        'order',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'order'      => 'integer',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
