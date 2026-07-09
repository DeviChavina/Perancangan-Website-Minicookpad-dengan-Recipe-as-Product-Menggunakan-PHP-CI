<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMiniCookpad extends Migration
{
    public function up()
    {
        // Users table
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'email'      => ['type' => 'VARCHAR', 'constraint' => 255],
            'name'       => ['type' => 'VARCHAR', 'constraint' => 255],
            'password'   => ['type' => 'VARCHAR', 'constraint' => 255],
            'role'       => ['type' => 'ENUM', 'constraint' => ['GUEST', 'USER_FREE', 'USER_PREMIUM', 'CHEF_PENDING', 'CHEF_VERIFIED', 'ADMIN'], 'default' => 'USER_FREE'],
            'avatar'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'bio'        => ['type' => 'TEXT', 'null' => true],
            'phone'      => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('email');
        $this->forge->createTable('users');

        // Chef Verifications table
        $this->forge->addField([
            'id'               => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'id_card_number'   => ['type' => 'VARCHAR', 'constraint' => 20],
            'id_card_photo'    => ['type' => 'VARCHAR', 'constraint' => 255],
            'certificate_photo' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'portfolio_url'    => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'specialization'   => ['type' => 'VARCHAR', 'constraint' => 255],
            'experience'       => ['type' => 'TEXT'],
            'status'           => ['type' => 'ENUM', 'constraint' => ['pending', 'approved', 'rejected'], 'default' => 'pending'],
            'admin_note'       => ['type' => 'TEXT', 'null' => true],
            'reviewed_at'      => ['type' => 'DATETIME', 'null' => true],
            'created_at'       => ['type' => 'DATETIME', 'null' => true],
            'updated_at'       => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('user_id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('chef_verifications');

        // Recipes table
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'chef_id'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'title'        => ['type' => 'VARCHAR', 'constraint' => 255],
            'slug'         => ['type' => 'VARCHAR', 'constraint' => 255],
            'description'  => ['type' => 'TEXT'],
            'cuisine'      => ['type' => 'VARCHAR', 'constraint' => 50],
            'category'     => ['type' => 'VARCHAR', 'constraint' => 50],
            'difficulty'   => ['type' => 'ENUM', 'constraint' => ['easy', 'medium', 'hard'], 'default' => 'medium'],
            'cooking_time' => ['type' => 'INT', 'constraint' => 11],
            'servings'     => ['type' => 'INT', 'constraint' => 11, 'default' => 2],
            'image'        => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'is_premium'   => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'status'       => ['type' => 'ENUM', 'constraint' => ['draft', 'published', 'archived'], 'default' => 'published'],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('slug');
        $this->forge->addKey('chef_id');
        $this->forge->addKey('cuisine');
        $this->forge->addForeignKey('chef_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('recipes');

        // Ingredients table
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'recipe_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'name'       => ['type' => 'VARCHAR', 'constraint' => 255],
            'amount'     => ['type' => 'VARCHAR', 'constraint' => 50],
            'unit'       => ['type' => 'VARCHAR', 'constraint' => 50],
            'sort_order' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('recipe_id', 'recipes', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('ingredients');

        // Steps table
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'recipe_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'sort_order'  => ['type' => 'INT', 'constraint' => 11],
            'description' => ['type' => 'TEXT'],
            'image'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'tip'         => ['type' => 'TEXT', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('recipe_id', 'recipes', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('steps');

        // Plans table
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name'        => ['type' => 'VARCHAR', 'constraint' => 100],
            'price'       => ['type' => 'INT', 'constraint' => 11],
            'duration'    => ['type' => 'INT', 'constraint' => 11],
            'description' => ['type' => 'TEXT'],
            'features'    => ['type' => 'TEXT'],
            'is_active'   => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('plans');

        // Subscriptions table
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'plan_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'status'     => ['type' => 'ENUM', 'constraint' => ['active', 'expired', 'cancelled'], 'default' => 'active'],
            'start_date' => ['type' => 'DATETIME'],
            'end_date'   => ['type' => 'DATETIME'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('plan_id', 'plans', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('subscriptions');

        // Payments table
        $this->forge->addField([
            'id'              => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'subscription_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'plan_id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'amount'          => ['type' => 'INT', 'constraint' => 11],
            'method'          => ['type' => 'ENUM', 'constraint' => ['qris', 'bca_va', 'mandiri_va', 'bri_va']],
            'status'          => ['type' => 'ENUM', 'constraint' => ['pending', 'paid', 'expired', 'failed'], 'default' => 'pending'],
            'payment_code'    => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'qris_url'        => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'expires_at'      => ['type' => 'DATETIME'],
            'paid_at'         => ['type' => 'DATETIME', 'null' => true],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
            'updated_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('plan_id', 'plans', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('payments');

        // Bookmarks table
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'recipe_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['user_id', 'recipe_id']);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('recipe_id', 'recipes', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('bookmarks');
    }

    public function down()
    {
        $this->forge->dropTable('bookmarks');
        $this->forge->dropTable('payments');
        $this->forge->dropTable('subscriptions');
        $this->forge->dropTable('plans');
        $this->forge->dropTable('steps');
        $this->forge->dropTable('ingredients');
        $this->forge->dropTable('recipes');
        $this->forge->dropTable('chef_verifications');
        $this->forge->dropTable('users');
    }
}
