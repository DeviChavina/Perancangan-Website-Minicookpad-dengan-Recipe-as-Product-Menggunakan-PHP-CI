<?php
namespace App\Models;
use CodeIgniter\Model;

class CoinTopupModel extends Model
{
    protected $table         = 'coin_topups';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['user_id','package_id','coin_amount','price_idr','method','status','payment_code','expires_at','paid_at','created_at','updated_at'];

    public function getPendingByUser(int $userId): ?array
    {
        return $this->where('user_id', $userId)
            ->where('status', 'pending')
            ->where('expires_at >', date('Y-m-d H:i:s'))
            ->orderBy('created_at', 'DESC')
            ->first();
    }

    public function getWithPackage(int $id): ?array
    {
        $db = \Config\Database::connect();
        return $db->table('coin_topups')
            ->select('coin_topups.*, coin_packages.name as package_name')
            ->join('coin_packages', 'coin_packages.id = coin_topups.package_id')
            ->where('coin_topups.id', $id)
            ->get()->getRowArray() ?: null;
    }
}
