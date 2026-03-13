<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'location_id',
        'quantity_at_location',
        'active_status',
        'deleted_status',
    ];
}
