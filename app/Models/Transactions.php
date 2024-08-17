<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transactions extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
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

    public function category() : BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }
}
