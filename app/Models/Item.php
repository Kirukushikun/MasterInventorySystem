<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'subcategory_id',
        'product_id',
        'item_name_id',
        'location_id',
        'model_number',
        'item_number',
        'order_number',
        'supplier_id',
        'uom_id',
        'quantity',
        'current_quantity',
        'reorder_threshold',
        'average_lead_time',
        'average_consumption',
        'purchase_date',
        'expiry_date',
        'purchase_cost',
        'remarks',
        'qr_code',
        'item_image',
        'item_image_path',
        'approval_id',
        'active_status',
        'deleted_status',
    ];

    public function scopeActive($query)
    {
        return $query->where('active_status', 1);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }


    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function itemName()
    {
        return $this->belongsTo(ItemName::class);
    }

    public function uom()
    {
        return $this->belongsTo(UnitOfMeasurement::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(SubCategory::class, 'subcategory_id', 'id');
    }

    // relation to farm, possible to many farms
    public function farmLocation()
    {
        return $this->belongsTo(FarmLocation::class);
    }

}
