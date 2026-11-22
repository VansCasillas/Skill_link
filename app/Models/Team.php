<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    protected $fillable = [
        'owner_id',
        'name',
        'competition',
        'description',
        'skills',
        'capacity',
        'members_count',
    ];

    protected function casts(): array
    {
        return [
            'skills' => 'array',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function joinRequests(): HasMany
    {
        return $this->hasMany(TeamJoinRequest::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(TeamMessage::class);
    }
}
