<?php

namespace App\Models;

use Database\Factories\CompetitionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name',
    'organizer',
    'description',
    'category',
    'level',
    'opens_at',
    'deadline',
    'fee',
    'registration_url',
    'poster_path',
    'status',
])]
class Competition extends Model
{
    /** @use HasFactory<CompetitionFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'opens_at' => 'date',
            'deadline' => 'date',
            'fee' => 'integer',
        ];
    }

    public function getEffectiveStatusAttribute(): string
    {
        if ($this->deadline !== null && $this->deadline->isBefore(today())) {
            return 'ended';
        }

        return $this->status;
    }

    public function isCompleteForPublication(): bool
    {
        return filled($this->name)
            && filled($this->organizer)
            && filled($this->description)
            && filled($this->category)
            && filled($this->level)
            && $this->opens_at !== null
            && $this->deadline !== null
            && filled($this->registration_url)
            && $this->deadline->greaterThanOrEqualTo(today());
    }
}
