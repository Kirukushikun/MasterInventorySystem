<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'subcategory_id',
        'product_name',
        'product_description',
        'active_status',
        'deleted_status'
    ];

    public function subCat(){
        return $this->belongsTo(SubCategory::class);
    }
}
