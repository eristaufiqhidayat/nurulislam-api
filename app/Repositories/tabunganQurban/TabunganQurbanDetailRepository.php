<?php

namespace App\Repositories\tabunganQurban;

use App\Models\TabunganQurbanDetail;

class TabunganQurbanDetailRepository
{
    public function create(array $data)
    {
        return TabunganQurbanDetail::create($data);
    }
    public function find($id)
    {
        return TabunganQurbanDetail::findOrFail($id);
    }
    public function getByTabungan($tabunganId)
    {
        return TabunganQurbanDetail::where('tabungan_id', $tabunganId)->get();
    }
    public function delete($id)
    {
        return TabunganQurbanDetail::destroy($id);
    }
    public function sumByTabunganId($tabunganId)
    {
        return TabunganQurbanDetail::where('tabungan_id', $tabunganId)
            ->sum('nominal');
    }
}
