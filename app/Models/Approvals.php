<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Approvals extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'active_status',
        'deleted_status',
    ];


    public function requestItem()
    {
        return $this->hasMany(RequestItem::class);
    }

    public function relatedRIs()
    {
        return $this->hasMany(RI::class, 'approval_id', 'id');
    }
}
