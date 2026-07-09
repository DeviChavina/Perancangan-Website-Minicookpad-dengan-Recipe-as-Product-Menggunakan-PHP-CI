<?php
namespace App\Models;
use CodeIgniter\Model;

class RecipeUnlockModel extends Model
{
    protected $table         = 'recipe_unlocks';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = ['user_id','recipe_id','coins_paid','chef_earn','platform_earn','created_at'];

    public function isUnlocked(int $userId, int $recipeId): bool
    {
        return (bool) $this->where('user_id', $userId)->where('recipe_id', $recipeId)->first();
    }

    public function getUnlockedByUser(int $userId): array
    {
        $db = \Config\Database::connect();
        return $db->table('recipe_unlocks')
            ->select('recipe_unlocks.*, recipes.title, recipes.slug, recipes.image, recipes.cuisine')
            ->join('recipes', 'recipes.id = recipe_unlocks.recipe_id')
            ->where('recipe_unlocks.user_id', $userId)
            ->orderBy('recipe_unlocks.created_at', 'DESC')
            ->get()->getResultArray();
    }

    /** Total koin yang dihasilkan chef dari resep-resepnya */
    public function totalEarnedByChef(int $chefId): int
    {
        $db = \Config\Database::connect();
        $row = $db->table('recipe_unlocks')
            ->selectSum('chef_earn')
            ->join('recipes', 'recipes.id = recipe_unlocks.recipe_id')
            ->where('recipes.chef_id', $chefId)
            ->get()->getRowArray();
        return (int)($row['chef_earn'] ?? 0);
    }

    /** Jumlah unlock per resep */
    public function countByRecipe(int $recipeId): int
    {
        return $this->where('recipe_id', $recipeId)->countAllResults();
    }
}
