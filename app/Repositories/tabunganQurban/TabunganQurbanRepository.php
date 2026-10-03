<?php

namespace App\Repositories\tabunganQurban;

use App\Models\TabunganQurban;

class TabunganQurbanRepository
{
    public function paginate($perPage = 10)
    {
        return TabunganQurban::with([
            'jamaah:id,name,email',
            'detail'
        ])->paginate($perPage);
    }

    public function find($id)
    {
        return TabunganQurban::with('detail')->findOrFail($id);
    }

    public function create(array $data)
    {
        return TabunganQurban::create($data);
    }

    public function update($id, array $data)
    {
        $tabungan = $this->find($id);
        $tabungan->update($data);
        return $tabungan;
    }

    public function delete($id)
    {
        return TabunganQurban::destroy($id);
    }
}
