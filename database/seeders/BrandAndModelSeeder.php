<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BrandAndModelSeeder extends Seeder
{
    public function run(): void
    {
        // Daftar lengkap merek dan model mobil
        $data = [
            'Toyota' => [
                'Innova Zenix G HV', 'Innova Zenix G', 'Innova Zenix Q', 'New Agya', 'Raize',
                'Alphard', 'Camry', 'Yaris', 'Corolla', 'Vellfire', 'Voxy',
                'Land Cruiser', 'Hilux 4x4', 'Hiace', 'Hiace Premio'
            ],
            'Daihatsu' => [
                'Ayla', 'Xenia', 'Sigra', 'Terios', 'Rocky', 'Sirion', 'Gran Max'
            ],
            'Suzuki' => [
                'Ertiga', 'XL7', 'Jimny', 'Baleno', 'Pickup', 'Blindvan'
            ],
            'Honda' => [
                'Brio', 'Jazz', 'Mobilio', 'HR-V', 'CR-V', 'City', 'Civic'
            ],
            'Mitsubishi' => [
                'Xpander', 'Pajero Sport', 'L300'
            ],
        ];

        // Loop untuk membuat data
        foreach ($data as $brandName => $models) {
            // Buat atau temukan merek
            $brand = Brand::firstOrCreate(['name' => $brandName]);

            // Buat model-model yang berelasi dengan merek tersebut
            foreach ($models as $modelName) {
                // firstOrCreate akan mencegah duplikasi jika seeder dijalankan lagi
                $brand->carModels()->firstOrCreate(['name' => $modelName]);
            }
        }
    }
}
