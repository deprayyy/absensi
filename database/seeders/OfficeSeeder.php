<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Office;

class OfficeSeeder extends Seeder
{
    public function run(): void
    {
        Office::create([
            'name' => 'Kantor Pusat',
            'latitude' => -6.485654,   // ganti sesuai lokasi kamu
            'longitude' => 106.841987, // ganti sesuai lokasi kamu
        ]);
    }
}
