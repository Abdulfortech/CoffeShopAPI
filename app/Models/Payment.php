<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'userID',
        'orderID',
        'amount',
        'gateway',
        'reference',
        'payment_date',
        'payment_method',
        'status',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'orderID');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
