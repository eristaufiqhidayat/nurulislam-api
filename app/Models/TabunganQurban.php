<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TabunganQurban extends Model
{
    use HasFactory;

    protected $table = 'tabungan_qurban';

    protected $fillable = [
        'jamaah_id',
        'tahun_qurban',
        'target_hewan',
        'target_nominal',
        'total_setoran',
        'status',
    ];
    public function jamaah()
    {
        return $this->belongsTo(User::class, 'jamaah_id', 'id');
    }
    public function detail()
    {
        return $this->hasMany(TabunganQurbanDetail::class, 'tabungan_id');
    }
}
