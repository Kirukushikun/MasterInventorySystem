<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'series_number',
        'approver_id',
        'requested_by_id',
        'farm_location_id',
        'department_division_id',
        'approval_id',
        'remarks',
        'comment',
        'jl_pdf',
        'date_requested',
        'date_needed',
        'active_status',
        'deleted_status',
    ];

    public function items()
    {
        return $this->hasMany(ItemList::class);
    }

    public function departmentDivision()
    {
        return $this->belongsTo(DepartmentDivision::class);
    }

    public function farmLocation()
    {
        return $this->belongsTo(FarmLocation::class);
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class);
    }

    public function approval()
    {
        return $this->belongsTo(Approvals::class, 'approval_id', 'id');
    }
}
