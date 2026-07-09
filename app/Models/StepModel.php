<?php
namespace App\Models;
use CodeIgniter\Model;

class StepModel extends Model
{
    protected $table = 'steps';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['recipe_id', 'sort_order', 'description', 'image', 'tip'];
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
