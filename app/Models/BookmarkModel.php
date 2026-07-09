<?php
namespace App\Models;
use CodeIgniter\Model;

class BookmarkModel extends Model
{
    protected $table          = 'bookmarks';
    protected $primaryKey     = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields  = ['user_id', 'recipe_id', 'created_at'];
    protected $useTimestamps  = true;
    protected $createdField   = 'created_at';
    protected $updatedField   = '';

    /**
     * Limit bookmark untuk user free (premium unlimited)
     */
    const FREE_LIMIT = 3;

    /**
     * Toggle bookmark. Cek limit untuk free user.
     *
     * @return string 'added' | 'removed' | 'limit_exceeded'
     */
    public function toggle(int $userId, int $recipeId, string $userRole = 'USER_FREE'): string
    {
        $existing = $this->where('user_id', $userId)
            ->where('recipe_id', $recipeId)
            ->first();

        if ($existing) {
            $this->delete($existing['id']);
            return 'removed';
        }

        // Cek limit hanya untuk USER_FREE
        if ($userRole === 'USER_FREE') {
            $currentCount = $this->where('user_id', $userId)->countAllResults();
            if ($currentCount >= self::FREE_LIMIT) {
                return 'limit_exceeded';
            }
        }

        $this->insert(['user_id' => $userId, 'recipe_id' => $recipeId]);
        return 'added';
    }

    public function isBookmarked(int $userId, int $recipeId): bool
    {
        return $this->where('user_id', $userId)
            ->where('recipe_id', $recipeId)
            ->countAllResults() > 0;
    }

    public function getUserBookmarks(int $userId): array
    {
        $db = \Config\Database::connect();
        // FIX #9: Hanya tampilkan resep berstatus 'published'.
        // Sebelumnya bookmark resep draft/archived tetap muncul di list
        // tapi mengarah ke 404 / error saat diklik (Recipe::detail filter by status).
        return $db->table('bookmarks')
            ->select('bookmarks.*, recipes.title, recipes.slug, recipes.image, recipes.cuisine, recipes.difficulty, recipes.cooking_time, recipes.is_premium, users.name as chef_name')
            ->join('recipes', 'recipes.id = bookmarks.recipe_id')
            ->join('users', 'users.id = recipes.chef_id')
            ->where('bookmarks.user_id', $userId)
            ->where('recipes.status', 'published')
            ->orderBy('bookmarks.created_at', 'DESC')
            ->get()->getResultArray();
    }

    public function countByUser(int $userId): int
    {
        return $this->where('user_id', $userId)->countAllResults();
    }
}