<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transactions';

    protected $fillable = [
        'user_id',
        'type',
        'transaction_time',
        'amount',
        'created_by',
        'updated_by',
        'is_user_updated'
    ];

    protected $casts = [
        'transaction_time' => 'datetime'
    ];

    // Define relationships

    /**
     * Get the user that owns the transaction.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
