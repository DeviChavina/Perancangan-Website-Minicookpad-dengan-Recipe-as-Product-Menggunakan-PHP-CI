<?php
namespace App\Models;
use CodeIgniter\Model;

class IngredientModel extends Model
{
    protected $table = 'ingredients';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['recipe_id', 'name', 'amount', 'unit', 'sort_order'];
    protected $useTimestamps = false;

    public function getByRecipe(int $recipeId): array
    {
        return $this->where('recipe_id', $recipeId)
            ->orderBy('sort_order', 'ASC')
            ->findAll();
    }

    public function deleteByRecipe(int $recipeId): bool
    {
        return $this->where('recipe_id', $recipeId)->delete() !== false;
    }
}
