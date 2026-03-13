<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FarmInventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'user_assigned_id',
        'quantity',
        'quantity_to_remove',
        'current_quantity',
        'reorder_threshold',
        'qr_code',
        'remarks',
        'item_quantity_just_checked_out',
        'request_id',
        'active_status',
        'deleted_status'
    ];

}
