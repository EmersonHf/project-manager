<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'lease_id',
        'amount',
        'type',
        'status',
        'due_date',
        'paid_date',
        'payment_method',
        'transaction_id',
        'notes',
        'attachments',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'paid_date' => 'date',
        'attachments' => 'array',
    ];

    public function lease()
    {
        return $this->belongsTo(Lease::class);
    }
}
