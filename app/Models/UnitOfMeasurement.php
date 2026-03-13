<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitOfMeasurement extends Model
{
    use HasFactory;

     protected $fillable = [
        'terminology',
        'abbreviation',
        'active_status',
        'deleted_status',
    ];

    public function requestItem()
    {
        return $this->hasMany(RequestItem::class);
    }
}
