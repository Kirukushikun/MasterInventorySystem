<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FarmItemHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'farm_item_id',
        'transaction_id',
        'previous_quantity',
        'new_quantity',
        'change_date',
        'change_reason',
        'old_purchase_date',
        'new_purchase_date',
        'old_expiry_date',
        'new_expiry_date',
        'user_id',
        'active_status',
        'deleted_status',
    ];
}
