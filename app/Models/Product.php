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
        'supplier_id',
        'product_code',
        'product_garage',
        'product_image',
        'product_store',
        'buying_date',
        'expire_date',
        'buying_price',
        'bulk_price',
        'price_for_curves',
    ];

    public $sortable = [
        'product_name',
        'bulk_price',
        'price_for_curves',
    ];

    protected $guarded = [
        'id',
    ];

    protected $with = [
        'category',
        'supplier'
    ];

    public function category(){
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function supplier(){
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? false, function ($query, $search) {
            return $query->where(function ($query) use ($search) {
                $query->where('product_name', 'like', '%' . $search . '%')
                      ->orWhere('product_code', 'like', '%' . $search . '%')
                      ->orWhere('product_garage', 'like', '%' . $search . '%');
            });
        })
        ->when($filters['price_min'] ?? false, function ($query, $priceMin) {
            return $query->where('buying_price', '>=', $priceMin);
        })
        ->when($filters['price_max'] ?? false, function ($query, $priceMax) {
            return $query->where('buying_price', '<=', $priceMax);
        })
        ->when($filters['bulk_price_min'] ?? false, function ($query, $bulkPriceMin) {
            return $query->where('bulk_price', '>=', $bulkPriceMin);
        })
        ->when($filters['bulk_price_max'] ?? false, function ($query, $bulkPriceMax) {
            return $query->where('bulk_price', '<=', $bulkPriceMax);
        });
    }
    
}