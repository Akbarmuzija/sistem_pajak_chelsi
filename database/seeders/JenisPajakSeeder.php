<?php

namespace Database\Seeders;

use App\Models\JenisPajak;
use Illuminate\Database\Seeder;

class JenisPajakSeeder extends Seeder
{
    public function run(): void
    {
        $jenis = [
            ['kode' => 'PPN',       'nama_jenis' => 'Pajak Pertambahan Nilai (PPN)',       'deskripsi' => 'Pajak yang dikenakan atas setiap pertambahan nilai dari barang atau jasa.'],
            ['kode' => 'PPH21',     'nama_jenis' => 'PPh Pasal 21',                         'deskripsi' => 'Pajak penghasilan atas penghasilan berupa gaji, upah, honorarium, tunjangan.'],
            ['kode' => 'PPH22',     'nama_jenis' => 'PPh Pasal 22',                         'deskripsi' => 'Pajak penghasilan atas impor barang dan kegiatan usaha di bidang lain.'],
            ['kode' => 'PPH23',     'nama_jenis' => 'PPh Pasal 23',                         'deskripsi' => 'Pajak penghasilan atas dividen, bunga, royalti, hadiah, sewa, dan jasa.'],
            ['kode' => 'PPH25',     'nama_jenis' => 'PPh Pasal 25',                         'deskripsi' => 'Angsuran pajak penghasilan yang harus dibayar sendiri setiap bulan.'],
            ['kode' => 'PPH29',     'nama_jenis' => 'PPh Pasal 29 (Badan)',                 'deskripsi' => 'Kekurangan pembayaran pajak penghasilan badan pada akhir tahun pajak.'],
            ['kode' => 'PPH4AYT2', 'nama_jenis' => 'PPh Pasal 4 Ayat 2',                  'deskripsi' => 'Pajak penghasilan yang bersifat final atas penghasilan tertentu.'],
            ['kode' => 'BPHTB',     'nama_jenis' => 'BPHTB',                                'deskripsi' => 'Bea Perolehan Hak atas Tanah dan Bangunan.'],
        ];

        foreach ($jenis as $item) {
            JenisPajak::create(array_merge($item, ['is_aktif' => true]));
        }
    }
}
