<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'transaction_id',
        'previous_quantity',
        'new_quantity',
        'change_date',
        'change_reason',
        'old_unit_price',
        'new_unit_price',

        'old_purchase_date',
        'new_purchase_date',

        'old_expiry_date',
        'new_expiry_date',

        'user_id',
        'active_status',
        'deleted_status',
    ];
}
