<?php
namespace App\Database\Seeds;

class AdminSeeder extends \CodeIgniter\Database\Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');
        $users = [
            ['email' => 'admin@cookpad.com',        'name' => 'Admin Cookpad',   'password' => password_hash('admin123', PASSWORD_DEFAULT), 'role' => 'ADMIN',           'coin_balance' => 9999, 'bio' => 'Administrator platform Mini Cookpad', 'created_at' => $now, 'updated_at' => $now],
            ['email' => 'chef.rina@cookpad.com',     'name' => 'Chef Rina',       'password' => password_hash('chef123',  PASSWORD_DEFAULT), 'role' => 'CHEF_VERIFIED',   'coin_balance' => 150,  'bio' => 'Chef spesialis masakan Indonesia & Nusantara', 'created_at' => $now, 'updated_at' => $now],
            ['email' => 'chef.takeshi@cookpad.com',  'name' => 'Chef Takeshi',    'password' => password_hash('chef123',  PASSWORD_DEFAULT), 'role' => 'CHEF_VERIFIED',   'coin_balance' => 80,   'bio' => 'Japanese cuisine enthusiast & ramen master', 'created_at' => $now, 'updated_at' => $now],
            ['email' => 'chef.marco@cookpad.com',    'name' => 'Chef Marco',      'password' => password_hash('chef123',  PASSWORD_DEFAULT), 'role' => 'CHEF_UNVERIFIED', 'coin_balance' => 30,   'bio' => 'Italian pasta & pizza specialist', 'created_at' => $now, 'updated_at' => $now],
            ['email' => 'user.andi@cookpad.com',     'name' => 'Andi Pratama',    'password' => password_hash('user123',  PASSWORD_DEFAULT), 'role' => 'USER_FREE',       'coin_balance' => 25,   'bio' => null, 'created_at' => $now, 'updated_at' => $now],
            ['email' => 'user.sari@cookpad.com',     'name' => 'Sari Dewi',       'password' => password_hash('user123',  PASSWORD_DEFAULT), 'role' => 'USER_FREE',       'coin_balance' => 60,   'bio' => null, 'created_at' => $now, 'updated_at' => $now],
        ];
        $this->db->table('users')->insertBatch($users);
    }
}
