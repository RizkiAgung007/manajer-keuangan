<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Budget extends Model
{
    use HasFactory;

    /**
     * The attribute that are mass assignable
     */
    protected $fillable = [
        'user_id',
        'category_id',
        'amount',
        'month',
        'year'
    ];

    /**
     * Get the user that owns the budget
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category that this budget is for
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
