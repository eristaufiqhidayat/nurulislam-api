<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPenjualan extends Model
{
    use HasFactory;

    protected $table = 'detail_penjualan';

    protected $fillable = [
        'penjualan_id',
        'barang_id',
        'jumlah',
        'harga_jual',
        'harga_beli',
        'margin',
    ];

    // Relasi ke tabel Penjualan
    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class, 'penjualan_id');
    }

    // Relasi ke tabel Barang
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }
}
