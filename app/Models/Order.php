<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_name',
        'quantity',
        'price',
        'status',
        'customer_id',
        'order_date'
    ];

    /**
     * The attributes that should be cast.
     * 
     * @var array
     */
    protected $casts = [
        'customer_id' => 'integer',
        'price' => 'float',
        'order_date' => 'date',
    ];

    /**
     * Accessor for formatted order date.
     * 
     * @return string
     */
    public function getOrderDateAttribute($value)
    {
        return Carbon::parse($value)->format('d-m-Y');
    }

    /**
     * Define a one-to-many relationship with customer.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Scope for filtering orders by product.
     * 
     * @param mixed $query
     * @param mixed $productName
     * @return mixed
     */
    public function scopeProduct($query, $productName)
    {
        return $query->where('product_name', 'like', '%' . $productName . '%');
    }
}
