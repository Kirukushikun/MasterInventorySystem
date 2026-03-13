<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Access extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','farm_location_id','department_division_id','access','action'];

    /**
     * Retrieves the associated user for this instance.
     *
     * @return User The associated user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    } 

}
