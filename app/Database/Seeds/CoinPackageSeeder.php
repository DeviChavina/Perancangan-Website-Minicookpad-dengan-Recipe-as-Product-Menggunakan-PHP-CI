<?php
namespace App\Database\Seeds;

class CoinPackageSeeder extends \CodeIgniter\Database\Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');
        $packages = [
            // Rp 5.000 = 10 koin (Rp 500/koin) — starter, bisa beli 1 resep 10 koin
            ['name' => 'Starter', 'coin_amount' => 10, 'price_idr' => 5000, 'bonus_coin' => 0, 'is_popular' => 0, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            // Rp 20.000 = 50 koin (Rp 400/koin) + 5 bonus
            ['name' => 'Basic',   'coin_amount' => 50, 'price_idr' => 20000, 'bonus_coin' => 5, 'is_popular' => 0, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            // Rp 45.000 = 100 koin (Rp 450/koin) + 20 bonus — MOST POPULAR
            ['name' => 'Popular', 'coin_amount' => 100, 'price_idr' => 45000, 'bonus_coin' => 20, 'is_popular' => 1, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            // Rp 80.000 = 200 koin (Rp 400/koin) + 60 bonus
            ['name' => 'Pro',     'coin_amount' => 200, 'price_idr' => 80000, 'bonus_coin' => 60, 'is_popular' => 0, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            // Rp 150.000 = 500 koin (Rp 300/koin) + 200 bonus — best value
            ['name' => 'Master',  'coin_amount' => 500, 'price_idr' => 150000, 'bonus_coin' => 200, 'is_popular' => 0, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
        ];
        $this->db->table('coin_packages')->insertBatch($packages);
    }
}
