<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'shop_id',
        'category_id',
        'name',
        'description',
        'price',
        'stock',
        'image',
        'imageJson',
        'status'
    ];

    protected $casts = [
        'price' => 'float',
        'stock' => 'integer',
        'imageJson' => 'array'
    ];

    /* ================= RELATIONS ================= */

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
