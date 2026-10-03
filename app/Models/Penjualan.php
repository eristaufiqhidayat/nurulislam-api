<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    protected $table = 'penjualan';
    protected $fillable = ['pembeli_id', 'tanggal', 'total', 'margin_total'];

    public function pembeli()
    {
        return $this->belongsTo(Pembeli::class);
    }

    public function details()
    {
        return $this->hasMany(PenjualanDetail::class);
    }
}
