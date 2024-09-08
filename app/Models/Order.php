<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'userID',
        'name',
        'phone',
        'address',
        'quantity',
        'total',
        'payment_method',
        'payment_status',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class, 'orderID');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class,'orderID');
    }
}
