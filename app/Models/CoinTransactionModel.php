<?php
namespace App\Models;
use CodeIgniter\Model;

class CoinTransactionModel extends Model
{
    protected $table         = 'coin_transactions';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = ['user_id','type','amount','balance_after','ref_table','ref_id','note','created_at'];

    public function getByUser(int $userId, int $limit = 20): array
    {
        return $this->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Catat transaksi koin + update saldo user secara atomik.
     * Harus dipanggil dalam context DB transaction.
     */
    public static function record(\CodeIgniter\Database\BaseConnection $db, array $data): void
    {
        $db->table('coin_transactions')->insert(array_merge([
            'created_at' => date('Y-m-d H:i:s'),
        ], $data));
    }
}
