<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;

/**
 * Migrasi v2: Sistem Koin
 *
 * Perubahan utama:
 * 1. users: tambah kolom coin_balance
 * 2. roles: tambah CHEF_UNVERIFIED (antara USER dan CHEF_VERIFIED)
 * 3. chef_verifications: tambah type (basic/advanced), verification_type
 * 4. coin_transactions: log semua transaksi koin (beli, unlock resep, payout chef)
 * 5. coin_packages: paket pembelian koin
 * 6. recipe_unlocks: relasi user <> resep premium yang sudah dibuka
 * 7. recipes: tambah coin_price (harga koin untuk unlock)
 * 8. Hapus tabel lama: plans, subscriptions, payments
 */
class CoinSystem extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        // 1. Tambah coin_balance ke users
        $db->query('ALTER TABLE users ADD COLUMN coin_balance INT UNSIGNED NOT NULL DEFAULT 0 AFTER phone');

        // 2. Ubah ENUM role agar include CHEF_UNVERIFIED
        $db->query("ALTER TABLE users MODIFY COLUMN role ENUM('GUEST','USER_FREE','CHEF_UNVERIFIED','CHEF_PENDING','CHEF_VERIFIED','ADMIN') NOT NULL DEFAULT 'USER_FREE'");

        // 3. Tambah verification_type ke chef_verifications
        //    'basic'    = ajukan KTP → jadi CHEF_UNVERIFIED
        //    'advanced' = upload sertifikat → jadi CHEF_VERIFIED
        $db->query("ALTER TABLE chef_verifications 
            ADD COLUMN verification_type ENUM('basic','advanced') NOT NULL DEFAULT 'basic' AFTER user_id,
            ADD COLUMN target_role ENUM('CHEF_UNVERIFIED','CHEF_VERIFIED') NOT NULL DEFAULT 'CHEF_UNVERIFIED' AFTER verification_type");

        // 4. Tambah coin_price ke recipes
        $db->query('ALTER TABLE recipes ADD COLUMN coin_price TINYINT UNSIGNED NOT NULL DEFAULT 10 AFTER is_premium');

        // 5. Tabel paket koin
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name'         => ['type' => 'VARCHAR', 'constraint' => 100],
            'coin_amount'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'price_idr'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'bonus_coin'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
            'is_popular'   => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'is_active'    => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('coin_packages');

        // 6. Tabel transaksi koin
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'type'        => ['type' => 'ENUM', 'constraint' => ['topup','unlock','earn','refund','admin_adj']],
            'amount'      => ['type' => 'INT', 'constraint' => 11],   // + masuk, - keluar
            'balance_after' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'ref_table'   => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],  // e.g. 'recipes','coin_packages'
            'ref_id'      => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'note'        => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('coin_transactions');

        // 7. Tabel pembelian koin (top-up)
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'package_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'coin_amount'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'price_idr'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'method'         => ['type' => 'ENUM', 'constraint' => ['qris','bca_va','mandiri_va','bri_va']],
            'status'         => ['type' => 'ENUM', 'constraint' => ['pending','paid','expired','failed'], 'default' => 'pending'],
            'payment_code'   => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'expires_at'     => ['type' => 'DATETIME'],
            'paid_at'        => ['type' => 'DATETIME', 'null' => true],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('package_id', 'coin_packages', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('coin_topups');

        // 8. Tabel unlock resep premium
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'recipe_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'coins_paid' => ['type' => 'TINYINT', 'constraint' => 3, 'unsigned' => true],
            'chef_earn'  => ['type' => 'TINYINT', 'constraint' => 3, 'unsigned' => true],
            'platform_earn' => ['type' => 'TINYINT', 'constraint' => 3, 'unsigned' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['user_id', 'recipe_id']);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('recipe_id', 'recipes', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('recipe_unlocks');

        // 9. Drop tabel lama yang tidak terpakai lagi
        // (plans, subscriptions, payments - diganti sistem koin)
        // Hati-hati: disable FK dulu sebelum drop
        $db->query('SET FOREIGN_KEY_CHECKS = 0');
        $this->forge->dropTable('payments', true);
        $this->forge->dropTable('subscriptions', true);
        $this->forge->dropTable('plans', true);
        $db->query('SET FOREIGN_KEY_CHECKS = 1');
    }

    public function down()
    {
        $db = \Config\Database::connect();
        $db->query('SET FOREIGN_KEY_CHECKS = 0');
        $this->forge->dropTable('recipe_unlocks', true);
        $this->forge->dropTable('coin_topups', true);
        $this->forge->dropTable('coin_transactions', true);
        $this->forge->dropTable('coin_packages', true);
        $db->query('ALTER TABLE recipes DROP COLUMN IF EXISTS coin_price');
        $db->query("ALTER TABLE chef_verifications DROP COLUMN IF EXISTS verification_type, DROP COLUMN IF EXISTS target_role");
        $db->query("ALTER TABLE users MODIFY COLUMN role ENUM('GUEST','USER_FREE','CHEF_PENDING','CHEF_VERIFIED','ADMIN') NOT NULL DEFAULT 'USER_FREE'");
        $db->query('ALTER TABLE users DROP COLUMN IF EXISTS coin_balance');
        $db->query('SET FOREIGN_KEY_CHECKS = 1');
    }
}
