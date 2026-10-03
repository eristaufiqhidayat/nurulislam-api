<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TabunganQurbanDetail extends Model
{
    use HasFactory;

    protected $table = 'tabungan_qurban_detail';

    protected $fillable = [
        'tabungan_id',
        'tanggal',
        'nominal',
        'metode',
        'keterangan',
    ];

    public function tabungan()
    {
        return $this->belongsTo(TabunganQurban::class, 'tabungan_id');
    }
}
