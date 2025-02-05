<?php

namespace App\Models;

use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory, Sortable;

    protected $fillable = [
        'product_name',
        'category_id',
        'brand_id',
        'product_code',
        'product_image',
        'product_store',
        'buying_date',
        'buying_price',
        'selling_price',
    ];

    public $sortable = [
        'product_name',
        'selling_price',
    ];

    protected $guarded = [
        'id',
    ];

    protected $with = [
        'category',
    ];
    protected $casts = [
        'buying_date' => 'date',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? false, function ($query, $search) {
            return $query->where('product_name', 'like', '%' . $search . '%');
        });
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetails::class, 'product_id', 'id');
    }
}
