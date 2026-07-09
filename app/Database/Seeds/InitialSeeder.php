<?php
namespace App\Database\Seeds;

class InitialSeeder extends \CodeIgniter\Database\Seeder
{
    public function run()
    {
        $this->call('App\Database\Seeds\AdminSeeder');
        $this->call('App\Database\Seeds\CoinPackageSeeder');
        $this->call('App\Database\Seeds\RecipeSeeder');
    }
}
