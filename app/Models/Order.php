<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'customer_id',
        'paid',
        'created_at',
        'updated_at'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    public function orderProducts()
    {
        return $this->hasMany(OrderProduct::class);
    }
}
