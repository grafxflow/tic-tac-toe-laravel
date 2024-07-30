<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Game extends Model
{
    use HasFactory;

    public $fillable = [
        'end_date',
        'winner_id',
        'player_1_user_id',
        'player_2_user_id',
    ];

    /**
     * Get the comments for the blog post.
     */
    public function turns(): HasMany
    {
        return $this->hasMany(Turn::class, 'game_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }









    public function scopeFinishedGame($query, $id)
    {
        return $query->whereNotNull('end_date');
    }

    public function scopeActiveGames($query)
    {
        return $query->whereNull('end_date')
        ->whereNull('winner_id');
    }

    public function scopeCurrentGames($query, $id)
    {
        return $query->where('player_1_user_id', $id)
        ->orWhere('player_2_user_id', $id);
    }
}
