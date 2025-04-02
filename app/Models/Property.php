<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'title',
        'description',
        'address',
        'city',
        'state',
        'zip_code',
        'country',
        'price',
        'type',
        'status',
        'listing_type',
        'bedrooms',
        'bathrooms',
        'square_feet',
        'features',
        'images',
        'monthly_rent',
        'deposit_amount',
        'available_date',
        'owner_id',
        'manager_id',
    ];

    protected $casts = [
        'features' => 'array',
        'images' => 'array',
        'price' => 'decimal:2',
        'monthly_rent' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'available_date' => 'date',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function leases()
    {
        return $this->hasMany(Lease::class);
    }

    public function maintenanceRequests()
    {
        return $this->hasMany(MaintenanceRequest::class);
    }

    public function activeLease()
    {
        return $this->hasOne(Lease::class)->where('status', 'active');
    }
}
