<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TeamMessage extends Model
{
    protected $fillable = ['team_id', 'user_id', 'body'];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function hiddenByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'hidden_team_messages')->withTimestamps();
    }
}
