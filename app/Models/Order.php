<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'total_price',
        'status',
        'email',
        'transaction_id'
    ];

    public function items()
    {
        return $this->hasMany(OrderItems::class);
    }

    public function payment()
    {
        return $this->hasOne(Payments::class);
    }
}
