<?php

namespace App\Models;

use CodeIgniter\Model;

class RecipeModel extends Model
{
    protected $table          = 'recipes';
    protected $primaryKey     = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields  = [
        'chef_id', 'title', 'slug', 'description', 'cuisine', 'category',
        'difficulty', 'cooking_time', 'servings', 'image', 'is_premium',
        'status', 'created_at', 'updated_at',
    ];
    protected $useTimestamps  = true;
    protected $createdField   = 'created_at';
    protected $updatedField   = 'updated_at';

    public function getWithDetails(int $id): ?array
    {
        $db = \Config\Database::connect();

        $recipe = $db->table('recipes')
            ->select('recipes.*, users.name as chef_name, users.avatar as chef_avatar')
            ->join('users', 'users.id = recipes.chef_id')
            ->where('recipes.id', $id)
            ->get()->getRowArray();

        if (!$recipe) return null;

        $recipe['ingredients'] = $db->table('ingredients')
            ->where('recipe_id', $id)
            ->orderBy('sort_order', 'ASC')
            ->get()->getResultArray();

        $recipe['steps'] = $db->table('steps')
            ->where('recipe_id', $id)
            ->orderBy('sort_order', 'ASC')
            ->get()->getResultArray();

        $recipe['bookmark_count'] = $db->table('bookmarks')
            ->where('recipe_id', $id)
            ->countAllResults();

        return $recipe;
    }

    public function getFiltered(array $filters = []): array
    {
        $db = \Config\Database::connect();

        $builder = $db->table('recipes')
            ->select('recipes.*, users.name as chef_name, users.avatar as chef_avatar')
            ->join('users', 'users.id = recipes.chef_id')
            ->where('recipes.status', 'published');

        if (!empty($filters['cuisine']))   $builder->where('recipes.cuisine', $filters['cuisine']);
        if (!empty($filters['category']))  $builder->where('recipes.category', $filters['category']);
        if (!empty($filters['difficulty'])) $builder->where('recipes.difficulty', $filters['difficulty']);
        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('recipes.title', $filters['search'])
                ->orLike('recipes.description', $filters['search'])
                ->groupEnd();
        }
        if (isset($filters['is_premium']) && $filters['is_premium'] !== '') {
            $builder->where('recipes.is_premium', (int) $filters['is_premium']);
        }

        $builder->orderBy('recipes.created_at', 'DESC');
        $recipes = $builder->get()->getResultArray();

        foreach ($recipes as &$recipe) {
            $recipe['bookmark_count'] = $db->table('bookmarks')
                ->where('recipe_id', $recipe['id'])
                ->countAllResults();
        }

        return $recipes;
    }

    public function getRecipesByChef(int $chefId, ?string $statusFilter = null): array
    {
        $db = \Config\Database::connect();

        $builder = $db->table('recipes')
            ->select('recipes.*, users.name as chef_name, users.avatar as chef_avatar')
            ->join('users', 'users.id = recipes.chef_id')
            ->where('recipes.chef_id', $chefId);

        // Filter by status: 'all' atau null → semua, selain itu filter spesifik
        if ($statusFilter && $statusFilter !== 'all') {
            $builder->where('recipes.status', $statusFilter);
        }

        $builder->orderBy('recipes.created_at', 'DESC');
        $recipes = $builder->get()->getResultArray();

        // Tambah bookmark_count per resep
        foreach ($recipes as &$recipe) {
            $recipe['bookmark_count'] = $db->table('bookmarks')
                ->where('recipe_id', $recipe['id'])
                ->countAllResults();
        }

        return $recipes;
    }

    /**
     * Ambil resep lengkap dengan ingredients & steps untuk form edit.
     */
    public function getForEdit(int $recipeId): ?array
    {
        $recipe = $this->find($recipeId);
        if (!$recipe) return null;

        $db = \Config\Database::connect();

        $recipe['ingredients'] = $db->table('ingredients')
            ->where('recipe_id', $recipeId)
            ->orderBy('sort_order', 'ASC')
            ->get()->getResultArray();

        $recipe['steps'] = $db->table('steps')
            ->where('recipe_id', $recipeId)
            ->orderBy('sort_order', 'ASC')
            ->get()->getResultArray();

        return $recipe;
    }

    /**
     * Hapus resep beserta relasinya (ingredients, steps, bookmarks).
     */
    public function deleteWithRelations(int $recipeId): bool
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $db->table('ingredients')->where('recipe_id', $recipeId)->delete();
            $db->table('steps')->where('recipe_id', $recipeId)->delete();
            $db->table('bookmarks')->where('recipe_id', $recipeId)->delete();
            $db->table('recipes')->where('id', $recipeId)->delete();

            $db->transComplete();
            return $db->transStatus() !== false;
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'deleteWithRelations error: ' . $e->getMessage());
            return false;
        }
    }
}