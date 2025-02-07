<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Kyslik\ColumnSortable\Sortable;

class Customer extends Model
{
    use HasFactory, Sortable;

    protected $fillable = [
        'name',
        'dni',
        'phone',
        'address',
        'photo',
        'city',
    ];
    public $sortable = [
        'name',
        'phone',
        'city',
    ];

    protected $guarded = [
        'id',
    ];

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? false, function ($query, $search) {
            return $query->where('name', 'like', '%' . $search . '%')->orWhere('dni', 'like', '%' . $search . '%');
        });
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    public function loans()
    {
        return $this->hasMany(Loan::class);
    }
}