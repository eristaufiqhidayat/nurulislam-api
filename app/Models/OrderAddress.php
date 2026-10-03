<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class OrderAddress extends Model
{
    protected $fillable = [
        'order_id',
        'receiver_name',
        'phone',
        'address',
        'city',
        'postal_code'
    ];
}