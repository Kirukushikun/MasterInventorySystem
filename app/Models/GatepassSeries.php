<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GatepassSeries extends Model
{
    use HasFactory;

    protected $fillable = [
        'from',
        'to',
        'farm_location_id',
        'department_division_id',
        'active_status',
        'deleted_status'
    ];

    // Define a many-to-one relationship with FarmLocation
    public function farmLocation()
    {
        return $this->belongsTo(FarmLocation::class, 'farm_location');
    }

    // Define a many-to-one relationship with DepartmentDivision
    public function departmentDivision()
    {
        return $this->belongsTo(DepartmentDivision::class, 'department_division');
    }
}
