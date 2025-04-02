<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'reported_by',
        'title',
        'description',
        'priority',
        'status',
        'scheduled_date',
        'completed_date',
        'cost',
        'notes',
        'attachments',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'completed_date' => 'date',
        'cost' => 'decimal:2',
        'attachments' => 'array',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }
}
