<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'quiz_id',
        'type',
        'content',
        'image_path',
        'video_url',
        'marks',
        'order',
        'correct_number',
        'correct_text',
    ];

    protected $casts = [
        'marks'          => 'integer',
        'order'          => 'integer',
        'correct_number' => 'float',
    ];

    // Valid question types — extend here for new types
    public const TYPES = [
        'binary'          => 'Binary (Yes / No)',
        'single_choice'   => 'Single Choice',
        'multiple_choice' => 'Multiple Choice',
        'number_input'    => 'Number Input',
        'text_input'      => 'Text Input',
    ];

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(Option::class)->orderBy('order');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    public function hasOptions(): bool
    {
        return in_array($this->type, ['binary', 'single_choice', 'multiple_choice']);
    }

    public function getEmbedVideoUrl(): ?string
    {
        if (! $this->video_url) {
            return null;
        }
        // Convert YouTube watch URL to embed URL
        $url = $this->video_url;
        if (str_contains($url, 'watch?v=')) {
            $url = str_replace('watch?v=', 'embed/', $url);
            // Remove extra query params after video id
            if (str_contains($url, '&')) {
                $url = substr($url, 0, strpos($url, '&'));
            }
        } elseif (str_contains($url, 'youtu.be/')) {
            $id  = substr($url, strrpos($url, '/') + 1);
            $url = 'https://www.youtube.com/embed/' . $id;
        }
        return $url;
    }
}
