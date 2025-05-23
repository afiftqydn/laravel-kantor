<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Cabang;
use App\Models\Unit;
use App\Models\SubUnit;

class WilayahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cabang = Cabang::create(['nama' => 'Kalimantan Barat']);

        $units = [
            'Pontianak',
            'Singkawang',
            'Mempawah',
            'Kubu Raya',
            'Bengkayang',
            'Landak',
            'Sambas',
            'Sanggau',
            'Sekadau',
            'Melawi',
            'Ketapang',
            'Kayong Utara',
            'Kapuas Hulu',
            'Sintang'
        ];

        foreach ($units as $unitNama) {
            $unit = $cabang->units()->create(['nama' => $unitNama]);

            // Buat sub unit hanya untuk Kubu Raya
            if ($unitNama === 'Kubu Raya') {
                $unit->subUnits()->create(['nama' => 'Teluk Pakedai']);
            }
        }
    }
}
