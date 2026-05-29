<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attempt extends Model
{
    protected $fillable = [
        'quiz_id',
        'guest_name',
        'score',
        'total_marks',
        'started_at',
        'submitted_at',
    ];

    protected $casts = [
        'score'        => 'float',
        'total_marks'  => 'integer',
        'started_at'   => 'datetime',
        'submitted_at' => 'datetime',
    ];

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    public function isSubmitted(): bool
    {
        return $this->submitted_at !== null;
    }

    public function getPercentage(): float
    {
        if ($this->total_marks <= 0) return 0;
        return round(($this->score / $this->total_marks) * 100, 1);
    }

    public function isPassed(): bool
    {
        return $this->getPercentage() >= 50;
    }

    public function getTimeTaken(): ?string
    {
        if (! $this->started_at || ! $this->submitted_at) return null;
        $seconds = $this->submitted_at->diffInSeconds($this->started_at);
        $minutes = intdiv($seconds, 60);
        $secs    = $seconds % 60;
        return "{$minutes}m {$secs}s";
    }
}
