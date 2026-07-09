<?php
namespace App\Models;
use CodeIgniter\Model;

class CoinPackageModel extends Model
{
    protected $table         = 'coin_packages';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['name','coin_amount','price_idr','bonus_coin','is_popular','is_active','created_at','updated_at'];
    protected $useTimestamps = true;

    public function getActive(): array
    {
        return $this->where('is_active', 1)->orderBy('price_idr', 'ASC')->findAll();
    }

    /** Total koin yang didapat (base + bonus) */
    public static function totalCoins(array $pkg): int
    {
        return (int)$pkg['coin_amount'] + (int)$pkg['bonus_coin'];
    }
}
