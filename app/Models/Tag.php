<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable
     */
    protected $fillable = [
        'name',
        'user_id',
        'order_column'
    ];

    /**
     * Get the user that owns the tag
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The transactions that belong to this tag
     */
    public function transactions(): BelongsToMany
    {
        return $this->belongsToMany(Transaction::class, 'tag_transaction_pivot');
    }
}
