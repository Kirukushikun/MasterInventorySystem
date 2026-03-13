<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_name',
        'contact_person',
        'contact_email',
        'contact_phone',
        'contact_tel_no',
        'active_status', // 1 = active, 0 = inactive
        'deleted_status', // 1 = active, 0 = inactive
    ];
}
