<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FarmLocation extends Model
{
    use HasFactory;
    protected $fillable = [
        'farm_location',
        'abbreviation',
        'active_status',
        'deleted_status'
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'farm_location_id', 'id');
    }

    // Item
    public function items()
    {
        return $this->hasMany(Item::class);
    }

    // Define a one-to-many relationship with WithdrawalSeries
    public function withdrawalSeries()
    {
        return $this->hasMany(WithdrawalSeries::class);
    }

    public function requestItem()
    {
        return $this->hasMany(RequestItem::class);
    }

    public function users()
    {
        return $this->hasMany(User::class, 'farm_location_id');
    }

    public function access()
    {
        return $this->hasMany(Access::class);
    }
}
