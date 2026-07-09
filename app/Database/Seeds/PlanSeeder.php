<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        $plans = [
            [
                'name'        => 'Basic',
                'price'       => 0,
                'duration'    => 0,
                'description' => 'Paket gratis untuk semua pengguna. Akses resep gratis dan fitur dasar.',
                'features'    => json_encode([
                    'Akses semua resep gratis',
                    'Bookmark hingga 3 resep',
                    'Pencarian resep dasar',
                ]),
                'is_active'   => 1,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'name'        => 'Premium Monthly',
                'price'       => 49000,
                'duration'    => 30,
                'description' => 'Langganan premium bulanan. Akses penuh ke semua resep termasuk konten eksklusif.',
                'features'    => json_encode([
                    'Akses semua resep termasuk premium',
                    'Bookmark tanpa batas',
                    'Pencarian resep lanjutan',
                    'Tips eksklusif dari chef',
                    'Video langkah memasak HD',
                ]),
                'is_active'   => 1,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'name'        => 'Premium Yearly',
                'price'       => 399000,
                'duration'    => 365,
                'description' => 'Langganan premium tahunan. Hemat 32% dibanding langganan bulanan dengan akses penuh.',
                'features'    => json_encode([
                    'Akses semua resep termasuk premium',
                    'Bookmark tanpa batas',
                    'Pencarian resep lanjutan',
                    'Tips eksklusif dari chef',
                    'Video langkah memasak HD',
                    'Prioritas dukungan pelanggan',
                    'Sertifikat memasak digital',
                    'Hemat 32% dari paket bulanan',
                ]),
                'is_active'   => 1,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
        ];

        $this->db->table('plans')->insertBatch($plans);
    }
}
