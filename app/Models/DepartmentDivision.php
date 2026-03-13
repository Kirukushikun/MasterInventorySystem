<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepartmentDivision extends Model
{
    use HasFactory;
    protected $fillable = [
        'department_division',
        'abbreviation',
        'active_status',
        'deleted_status'
    ];

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
        return $this->hasMany(User::class, 'department_division_id');
    }

    public function access()
    {
        return $this->hasMany(Access::class);
    }
}
