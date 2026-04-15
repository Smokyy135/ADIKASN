<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create default users
        User::create([
            'name' => 'Administrator',
            'nip' => 'admin',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'User Biasa',
            'nip' => 'user',
            'password' => Hash::make('user123'),
            'role' => 'user',
        ]);

        // Create default categories - Satuan Unit Kerja (SKPD)
        $skpdCategories = [
            'Dinas Kesehatan',
            'Dinas Pendidikan',
            'Dinas Pekerjaan Umum',
            'Dinas Keuangan',
            'Dinas Sosial',
            'Badan Perencanaan Pembangunan Daerah',
            'Inspektorat Daerah',
            'Sekretariat Daerah',
        ];

        foreach ($skpdCategories as $name) {
            Category::create([
                'type' => 'skpd',
                'name' => $name,
            ]);
        }

        // Create default categories - Jenis Data
        $jenisCategories = [
            'Berdasarkan Jabatan',
            'Berdasarkan Pangkat',
            'Berdasarkan Pendidikan',
            'Berdasarkan Masa Kerja',
            'Berdasarkan Status Pernikahan',
            'Berdasarkan Lokasi Kerja',
        ];

        foreach ($jenisCategories as $name) {
            Category::create([
                'type' => 'jenis_data',
                'name' => $name,
            ]);
        }

        // Create default categories - Periode
        $periodeCategories = [
            '2023',
            '2024',
            '2025',
            '2026',
            'Januari 2026',
            'Februari 2026',
            'Maret 2026',
            'April 2026',
        ];

        foreach ($periodeCategories as $name) {
            Category::create([
                'type' => 'periode',
                'name' => $name,
            ]);
        }
    }
}
