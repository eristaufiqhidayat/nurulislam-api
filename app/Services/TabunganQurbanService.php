<?php

namespace App\Services;

use App\Repositories\tabunganQurban\TabunganQurbanRepository;
use App\Repositories\tabunganQurban\TabunganQurbanDetailRepository;
use DB;

class TabunganQurbanService
{
    protected $tabunganRepo;
    protected $detailRepo;

    public function __construct(
        TabunganQurbanRepository $tabunganRepo,
        TabunganQurbanDetailRepository $detailRepo
    ) {
        $this->tabunganRepo = $tabunganRepo;
        $this->detailRepo = $detailRepo;
    }

    public function list($perPage = 10)
    {
        return $this->tabunganRepo->paginate($perPage);
    }

    public function detail($id)
    {
        return $this->tabunganRepo->find($id);
    }

    public function create(array $data)
    {
        return $this->tabunganRepo->create($data);
    }

    public function update($id, array $data)
    {
        return $this->tabunganRepo->update($id, $data);
    }

    public function delete($id)
    {
        return $this->tabunganRepo->delete($id);
    }
    public function deleteDetail($detailId)
    {
        return DB::transaction(function () use ($detailId) {

            // 1. Ambil detail setoran
            $detail = $this->detailRepo->find($detailId);
            $tabunganId = $detail->tabungan_id;

            // 2. Hapus detail
            $this->detailRepo->delete($detailId);

            // 3. Hitung ulang total setoran
            $total = $this->detailRepo->sumByTabunganId($tabunganId);

            // 4. Update tabungan
            $tabungan = $this->tabunganRepo->find($tabunganId);
            $tabungan->update([
                'total_setoran' => $total,
                'status' => $total >= $tabungan->target_nominal ? 'lunas' : 'aktif'
            ]);

            return true;
        });
    }
    /**
     * Tambah setoran & update total otomatis
     */
    // public function addSetoran(array $data)
    // {
    //     return DB::transaction(function () use ($data) {
    //         $detail = $this->detailRepo->create($data);

    //         $tabungan = $this->tabunganRepo->find($data['tabungan_id']);
    //         $tabunganTotal = $this->detailRepo->sumByTabunganId($data['tabungan_id']);
    //         $tabungan->increment('total_setoran', $data['nominal']);


    //         // auto lunas
    //         if ($tabungan->total_setoran >= $tabungan->target_nominal) {
    //             $tabungan->update(['status' => 'lunas']);
    //         }

    //         return $detail;
    //     });
    // }
    public function addSetoran(array $data)
    {
        return DB::transaction(function () use ($data) {

            $detail = $this->detailRepo->create($data);

            $total = $this->detailRepo->sumByTabunganId($data['tabungan_id']);

            $tabungan = $this->tabunganRepo->find($data['tabungan_id']);
            $tabungan->update([
                'total_setoran' => $total
            ]);

            if ($total >= $tabungan->target_nominal) {
                $tabungan->update(['status' => 'lunas']);
            }

            return $detail;
        });
    }
}
