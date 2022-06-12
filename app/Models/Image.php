<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Image extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Owner of the image
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    /**
     * Scopes query to only those images owned by the user
     *
     * @param Builder $query
     * @param User $user
     * @return Builder
     */
    public function scopeOwnedBy(Builder $query, User $user) {
        return $query->where('user_id', $user->id);
    }
}
