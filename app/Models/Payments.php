<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payments extends Model
{
    protected $fillable = [
        'order_id',
        'payment_method',
        'payment_channel',
        'transaction_id',
        'amount',
        'status',
        'payment_url',
        'paid_at',
        'paid_amount'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
