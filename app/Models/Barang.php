<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $table = 'barang';
    protected $fillable = [
        'nama_barang', 'kategori', 'satuan',
        'harga_beli', 'harga_jual', 'stok'
    ];

    public function barangMasuks()
    {
        return $this->hasMany(BarangMasuk::class);
    }

    public function penjualanDetails()
    {
        return $this->hasMany(PenjualanDetail::class);
    }
}
