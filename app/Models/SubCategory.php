<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'subcategory_name',
        'subcategory_description',
        'active_status',
        'deleted_status'
    ];

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    //product
    public function products()
    {
        return $this->hasMany(Product::class);
    }
    public function my_category(){
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

}
