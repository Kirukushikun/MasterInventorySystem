<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsedSeries extends Model
{
    use HasFactory;

    protected $fillable = [
        'used_series',
        'farm_location',
        'department_division'
    ];
}
