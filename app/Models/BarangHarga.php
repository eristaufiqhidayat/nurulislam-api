<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangHarga extends Model
{
    use HasFactory;
protected $table = 'harga_barang';
    protected $fillable = [
        'barang_id',
        'tanggal',
        'harga_beli',
        'harga_jual',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}
