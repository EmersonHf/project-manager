<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Project extends Model
{
    use HasApiTokens;
    protected $fillable = [
        'name',
        'description',
        'address',
        'latitude',
        'longitude'
    ];
}
