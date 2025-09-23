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
            'latitude' => -6.236364774146748,   // ganti sesuai lokasi kamu
            'longitude' => 106.82557096084179, // ganti sesuai lokasi kamu
        ]);
    }
}
