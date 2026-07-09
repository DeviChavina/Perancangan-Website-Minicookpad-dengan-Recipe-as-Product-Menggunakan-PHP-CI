<?php

namespace App\Controllers;

use App\Models\BookmarkModel;
use App\Models\RecipeModel;
use App\Models\UserModel;

class Bookmark extends BaseController
{
    protected $bookmarkModel;
    protected $recipeModel;
    protected $userModel;

    public function __construct()
    {
        helper('cookpad');
        $this->bookmarkModel = new BookmarkModel();
        $this->recipeModel   = new RecipeModel();
        $this->userModel     = new UserModel();
    }

    /**
     * Show user's bookmarked recipes
     */
    public function index()
    {
        $userId = session()->get('user_id');
        $userRole = session()->get('user_role');

        $bookmarks     = $this->bookmarkModel->getUserBookmarks($userId);
        $bookmarkCount = $this->bookmarkModel->countByUser($userId);
        $freeLimit     = BookmarkModel::FREE_LIMIT;
        $isFree        = ($userRole === 'USER_FREE');

        $data = [
            'bookmarks'     => $bookmarks,
            'bookmarkCount' => $bookmarkCount,
            'freeLimit'     => $freeLimit,
            'isFree'        => $isFree,
            'title'         => 'Bookmark Saya - Mini Cookpad',
        ];

        return view('bookmark/index', $data);
    }

    /**
     * Toggle bookmark (add/remove)
     */
    public function toggle($recipeId = null)
    {
        if (empty($recipeId)) {
            return redirect()->back()->with('error', 'Resep tidak valid');
        }

        // Coerce ke integer (untuk handle kasus slug terlewat dikirim)
        $recipeIdInt = (int) $recipeId;

        // Cari resep by ID dulu, fallback by slug
        $recipe = $this->recipeModel->find($recipeIdInt);
        if (!$recipe) {
            $recipe = $this->recipeModel->where('slug', $recipeId)->first();
        }

        if (!$recipe) {
            return redirect()->back()->with('error', 'Resep tidak ditemukan');
        }

        $userId   = session()->get('user_id');
        $userRole = session()->get('user_role') ?? 'USER_FREE';

        $result = $this->bookmarkModel->toggle($userId, (int) $recipe['id'], $userRole);

        if ($result === 'added') {
            return redirect()->back()->with('success', 'Resep berhasil ditambahkan ke bookmark');
        } elseif ($result === 'limit_exceeded') {
            return redirect()->back()->with('error', 'Batas bookmark free user hanya 3. Upgrade ke Premium untuk bookmark tanpa batas.');
        } else {
            return redirect()->back()->with('success', 'Resep dihapus dari bookmark');
        }
    }
}   