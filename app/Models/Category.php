<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_name',
        'category_description',
        'active_status',
        'deleted_status'
    ];

    public function subcategories()
    {
        return $this->hasMany(SubCategory::class);
    }

    public function scopeActive($query)
    {
        return $query->where('active_status', 1);
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }

}
