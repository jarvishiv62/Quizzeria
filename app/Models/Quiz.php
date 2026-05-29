<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quiz extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'is_published',
        'total_marks',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'total_marks'  => 'integer',
    ];

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('order');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(Attempt::class);
    }

    public function recalculateTotalMarks(): void
    {
        $this->total_marks = $this->questions()->sum('marks');
        $this->save();
    }
}
