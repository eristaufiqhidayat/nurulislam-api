<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangMasukInv extends Model
{
    protected $table = 'barang_masuk_invoice'; // sesuaikan dengan nama tabel Anda

    protected $fillable = [
        'id_supplier',
        'tanggal',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'id_supplier');
    }
    public function barangMasuk()
    {
        return $this->hasMany(BarangMasuk::class, 'id_inv', 'id');
    }
}
