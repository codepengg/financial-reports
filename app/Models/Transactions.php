<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transactions extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'category_id',
        'date_transaction',
        'note',
        'amount',
        'image'
    ];

    public function scopeExpenses($query)
    {
        return $this->whereHas('category', function ($query) {
            $query->where('is_expenses', true);
        });
    }
    public function scopeIncomes($query)
    {
        return $this->whereHas('category', function ($query) {
            $query->where('is_expenses', false);
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category() : BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public static function scopeListTransactions(Builder $query): Builder
    {
        $user = auth()->user();
        if (!$user->hasRole('admin')) {
            return $query->where('user_id', $user->id);
        }

        return $query;
    }
}
