<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'name',
        'farm_location_id',
        'department_division_id',
        'active_status',
        'deleted_status',
        'password',
        'role'
    ];

    /**
     * A description of the access() PHP function.
     *
     * @return Access[]
     */
    public function access()
    {
        return $this->hasMany(Access::class);
    }

    public function farmLocation()
    {
        return $this->belongsTo(FarmLocation::class, 'farm_location_id');
    }

    public function departmentDivision()
    {
        return $this->belongsTo(DepartmentDivision::class, 'department_division_id');
    }

    public function requestItem()
    {
        return $this->hasMany(RequestItem::class);
    }


}
