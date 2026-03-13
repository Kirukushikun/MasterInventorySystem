<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'assigned_by_user_id',
        'assigned_user_id',
        'transaction_type_id',
        'farm_location_id',
        'department_division_id',
        'quantity',
        'transaction_date',
        'notes',
        'active_status',
        'deleted_status',
    ];

    public function farmLocation()
    {
        return $this->belongsTo(FarmLocation::class, 'farm_location_id', 'id');
    }

    public function scopeActive($query)
    {
        return $query->where('active_status', 1);
    }

    public function issuedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by_user_id');
    }

    public function issuedTo()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

}
