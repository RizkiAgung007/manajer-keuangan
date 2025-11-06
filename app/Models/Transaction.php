<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    /**
     * The attribute that are mass assignable
     */
    protected $fillable = [
        'category_id',
        'amount',
        'description',
        'transaction_date',
        'user_id'
    ];

    /**
     * Get the user that owns the transaction
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category that owns the transactions
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
