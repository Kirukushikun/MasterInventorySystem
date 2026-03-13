<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemList extends Model
{
    use HasFactory;


    protected $fillable = [
        'request_item_id',
        'item_id',
        'uom_id',
        'item_quantity',
        'item_released_quantity',
        'item_partially_release_quantity',
        'active_status',
        'deleted_status'
    ];

    public function requestItem()
    {
        return $this->belongsto(RequestItem::class);
    }
}
