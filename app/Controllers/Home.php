<?php

namespace App\Controllers;

use App\Models\RecipeModel;

class Home extends BaseController
{
    protected $recipeModel;

    public function __construct()
    {
        helper('cookpad');
        $this->recipeModel = new RecipeModel();
    }

    public function index()
    {
        $filters = [];

        $cuisine = $this->request->getGet('cuisine');
        $category = $this->request->getGet('category');
        $search = $this->request->getGet('search');
        $difficulty = $this->request->getGet('difficulty');

        if (!empty($cuisine)) {
            $filters['cuisine'] = $cuisine;
        }

        if (!empty($category)) {
            $filters['category'] = $category;
        }

        if (!empty($search)) {
            $filters['search'] = $search;
        }

        if (!empty($difficulty)) {
            $filters['difficulty'] = $difficulty;
        }

        $recipes = $this->recipeModel->getFiltered($filters);

        $data = [
            'recipes'  => $recipes,
            'filters'  => [
                'cuisine'   => $cuisine ?? '',
                'category'  => $category ?? '',
                'search'    => $search ?? '',
                'difficulty' => $difficulty ?? '',
            ],
            'title'    => 'Mini Cookpad - Temukan Resep Terbaik',
        ];

        return view('home/index', $data);
    }
}
