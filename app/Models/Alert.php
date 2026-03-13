<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'alert_type_id',
        'alert_date',
        'message',
        'active_status',
        'deleted_status',
    ];

    
}
