<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'is_expenses', 'image'
    ];

    protected $casts = [
        'is_expenses' => 'boolean'
    ];

    public function createdBy(): BelongsTo {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function users(): BelongsToMany {
        return $this->belongsToMany(User::class, UserHasCategory::class, 'category_id', 'user_id');
    }

    public static function scopeListCategory(Builder $query): Builder
    {
        $user = auth()->user();
        if (!$user->hasRole('admin')) {
            return $query->whereHas('users', function (Builder $query) use ($user) {
                $query->where('user_id', $user->id);
            });
        }

        return $query;
    }
}
