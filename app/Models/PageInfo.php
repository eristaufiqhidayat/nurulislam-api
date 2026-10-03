<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageInfo extends Model
{
    use HasFactory;

    protected $table = 'pageinfo'; // Sesuai dengan tabel di database
    protected $primaryKey = 'id'; // Primary key
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'id',
        'title',
        'description',
        'image',
        'icon',
        'category'
    ];
}
