<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenjualanDetail extends Model
{
protected $table = 'detail_penjualan';
    protected $fillable = [
        'penjualan_id', 'barang_id', 'jumlah', 'harga_jual', 'subtotal', 'margin'
    ];

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class);
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}
