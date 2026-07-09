<?php
namespace App\Controllers;

use App\Models\UserModel;
use App\Models\RecipeUnlockModel;
use App\Models\CoinTransactionModel;

class User extends BaseController
{
    public function dashboard()
    {
        helper('cookpad');
        $userId = session()->get('user_id');
        $userModel  = new UserModel();
        $unlockModel = new RecipeUnlockModel();

        $user = $userModel->getWithStats($userId);
        if (!$user) return redirect()->to('/login');

        $unlockedRecipes = $unlockModel->getUnlockedByUser($userId);

        $db = \Config\Database::connect();
        $recentTransactions = $db->table('coin_transactions')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->get()->getResultArray();

        return view('user/dashboard', [
            'user'               => $user,
            'unlockedRecipes'    => $unlockedRecipes,
            'recentTransactions' => $recentTransactions,
            'title'              => 'Dashboard - Mini Cookpad',
        ]);
    }
}
