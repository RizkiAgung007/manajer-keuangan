<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecurringTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'amount',
        'description',
        'frequency',
        'day_of_month',
        'start_date',
        'last_processed_at',
        'is_active',
    ];

    protected $cast = [
        'start_date'        => 'date',
        'last_processed_at' => 'datetime',
        'is_active'         => 'boolean',
        'amount'            => 'integer',
        'day_of_month'      => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
