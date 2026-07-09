<?php
namespace App\Models;
use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['email','name','password','role','avatar','bio','phone','coin_balance','created_at','updated_at'];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $validationRules    = [];
    protected $validationMessages = [];

    public function findByEmail(string $email): ?array
    {
        return $this->where('email', $email)->first();
    }

    /** Tambah koin ke user (atomik) */
    public function addCoins(int $userId, int $amount): int
    {
        $db = \Config\Database::connect();
        $db->query('UPDATE users SET coin_balance = coin_balance + ? WHERE id = ?', [$amount, $userId]);
        $row = $db->query('SELECT coin_balance FROM users WHERE id = ?', [$userId])->getRowArray();
        return (int)$row['coin_balance'];
    }

    /** Kurangi koin dari user, return false jika saldo tidak cukup */
    public function spendCoins(int $userId, int $amount): int|false
    {
        $db = \Config\Database::connect();
        $row = $db->query('SELECT coin_balance FROM users WHERE id = ? FOR UPDATE', [$userId])->getRowArray();
        if (!$row || (int)$row['coin_balance'] < $amount) return false;
        $db->query('UPDATE users SET coin_balance = coin_balance - ? WHERE id = ?', [$amount, $userId]);
        return (int)$row['coin_balance'] - $amount;
    }

    public function getWithStats(int $userId): ?array
    {
        $user = $this->find($userId);
        if (!$user) return null;

        $db = \Config\Database::connect();
        $user['recipe_count']   = $db->table('recipes')->where('chef_id', $userId)->countAllResults();
        $user['unlock_count']   = $db->table('recipe_unlocks')->where('user_id', $userId)->countAllResults();
        $user['bookmark_count'] = $db->table('bookmarks')->where('user_id', $userId)->countAllResults();

        // Koin yang dihasilkan chef dari resep
        $earnRow = $db->table('recipe_unlocks')
            ->selectSum('chef_earn')
            ->join('recipes', 'recipes.id = recipe_unlocks.recipe_id')
            ->where('recipes.chef_id', $userId)
            ->get()->getRowArray();
        $user['total_earned'] = (int)($earnRow['chef_earn'] ?? 0);

        // Verifikasi chef
        $user['verifications'] = $db->table('chef_verifications')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->get()->getResultArray();

        return $user;
    }
}
