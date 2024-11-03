<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone'
    ];

    /**
     * The attributes that should be cast.
     * 
     * @var array
     */
    protected $casts = [
        'phone' => 'string',
    ];

    /**
     * Define a one-to-many relationship with order.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Scope for filtering customers by order status.
     * 
     * @param mixed $query
     * @param mixed $status
     * @return mixed
     */
    public function scopeStatus($query, $status)
    {
        return $query->whereRelation('orders', 'status', $status);
    }

    /**
     * Scope for filtering customers by order date range.
     * 
     * @param mixed $query
     * @param mixed $startDate
     * @param mixed $endDate
     * @return mixed
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereRelation('orders', function ($query) use ($startDate, $endDate) {
            $query->whereBetween('order_date', [$startDate, $endDate]);
        });
    }
}
