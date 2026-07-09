<?php
namespace App\Controllers;

use App\Models\RecipeModel;
use App\Models\BookmarkModel;
use App\Models\UserModel;
use App\Models\RecipeUnlockModel;
use App\Models\CoinTransactionModel;

class Recipe extends BaseController
{
    protected RecipeModel       $recipeModel;
    protected BookmarkModel     $bookmarkModel;
    protected UserModel         $userModel;
    protected RecipeUnlockModel $unlockModel;

    public function __construct()
    {
        helper('cookpad');
        $this->recipeModel   = new RecipeModel();
        $this->bookmarkModel = new BookmarkModel();
        $this->userModel     = new UserModel();
        $this->unlockModel   = new RecipeUnlockModel();
    }

    public function index()
    {
        $filters = [];
        $cuisine    = $this->request->getGet('cuisine');
        $category   = $this->request->getGet('category');
        $search     = $this->request->getGet('search');
        $difficulty = $this->request->getGet('difficulty');
        $premium    = $this->request->getGet('premium');

        if (!empty($cuisine))    $filters['cuisine']    = $cuisine;
        if (!empty($category))   $filters['category']   = $category;
        if (!empty($search))     $filters['search']     = $search;
        if (!empty($difficulty)) $filters['difficulty'] = $difficulty;
        if ($premium !== null)   $filters['premium']    = $premium;

        $recipes = $this->recipeModel->getFiltered($filters);

        return view('recipe/index', [
            'recipes' => $recipes,
            'filters' => compact('cuisine','category','search','difficulty','premium'),
            'title'   => 'Semua Resep - Mini Cookpad',
        ]);
    }

    public function detail($slug = null)
    {
        if (empty($slug)) return redirect()->to('/recipes');

        $recipe = $this->recipeModel
            ->select('recipes.*, users.name as chef_name, users.avatar as chef_avatar, users.bio as chef_bio, users.role as chef_role')
            ->join('users', 'users.id = recipes.chef_id')
            ->where('recipes.slug', $slug)
            ->where('recipes.status', 'published')
            ->first();

        if (!$recipe) return redirect()->to('/recipes')->with('error', 'Resep tidak ditemukan');

        $recipeDetails = $this->recipeModel->getWithDetails($recipe['id']);
        if (!$recipeDetails) return redirect()->to('/recipes')->with('error', 'Resep tidak ditemukan');

        $userId         = session()->get('user_id');
        $isLoggedIn     = (bool) session()->get('logged_in');
        $canViewPremium = false;
        $isBookmarked   = false;
        $isUnlocked     = false;
        $userRole       = 'GUEST';
        $userCoins      = 0;

        if ($isLoggedIn) {
            $user     = $this->userModel->find($userId);
            $userRole = $user['role'] ?? session()->get('user_role');
            $userCoins = (int)($user['coin_balance'] ?? 0);
            if ($userRole !== session()->get('user_role')) session()->set('user_role', $userRole);

            // Chef & Admin bisa lihat semua
            $canViewPremium = in_array($userRole, ['CHEF_UNVERIFIED','CHEF_VERIFIED','ADMIN']);

            // User dengan koin yang sudah unlock bisa lihat
            if (!$canViewPremium && !empty($recipeDetails['is_premium'])) {
                $isUnlocked = $this->unlockModel->isUnlocked($userId, $recipe['id']);
                if ($isUnlocked) $canViewPremium = true;
            } elseif (!empty($recipeDetails['is_premium'])) {
                $isUnlocked = true; // chef/admin dianggap "unlock"
            }

            $isBookmarked = $this->bookmarkModel->isBookmarked($userId, $recipe['id']);
        }

        return view('recipe/detail', [
            'recipe'         => $recipeDetails,
            'canViewPremium' => $canViewPremium,
            'isBookmarked'   => $isBookmarked,
            'isUnlocked'     => $isUnlocked,
            'userRole'       => $userRole,
            'userCoins'      => $userCoins,
            'isLoggedIn'     => $isLoggedIn,
            'title'          => $recipeDetails['title'] . ' - Mini Cookpad',
        ]);
    }

    /** POST: Unlock resep premium dengan koin */
    public function unlock($recipeId = null)
    {
        $recipeId = (int) $recipeId;
        if (!session()->get('logged_in')) return redirect()->to('/login');

        $recipe = $this->recipeModel->find($recipeId);
        if (!$recipe || empty($recipe['is_premium'])) {
            return redirect()->back()->with('error', 'Resep tidak valid atau bukan premium');
        }

        $userId   = session()->get('user_id');
        $userRole = session()->get('user_role');

        // Chef & Admin tidak perlu unlock
        if (in_array($userRole, ['CHEF_UNVERIFIED','CHEF_VERIFIED','ADMIN'])) {
            return redirect()->to('/recipe/' . $recipe['slug']);
        }

        // Cek sudah unlock?
        if ($this->unlockModel->isUnlocked($userId, $recipeId)) {
            return redirect()->to('/recipe/' . $recipe['slug'])->with('info', 'Resep sudah pernah di-unlock');
        }

        $coinPrice = (int) $recipe['coin_price'];
        if ($coinPrice <= 0) $coinPrice = 10; // fallback

        // Hitung bagi hasil
        $chefRole = null;
        $chef     = $this->userModel->find($recipe['chef_id']);
        if ($chef) $chefRole = $chef['role'];

        // CHEF_VERIFIED: 70% chef, 30% platform
        // CHEF_UNVERIFIED: 50% chef, 50% platform (dibulatkan ke bawah untuk chef)
        if ($chefRole === 'CHEF_VERIFIED') {
            $chefEarn     = (int) floor($coinPrice * 0.7);
            $platformEarn = $coinPrice - $chefEarn;
        } else {
            $chefEarn     = (int) floor($coinPrice * 0.5);
            $platformEarn = $coinPrice - $chefEarn;
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // 1. Kurangi koin user
            $newBalance = $this->userModel->spendCoins($userId, $coinPrice);
            if ($newBalance === false) {
                $db->transRollback();
                return redirect()->back()->with('error', "Koin tidak cukup! Dibutuhkan {$coinPrice} koin. Silakan top-up koin terlebih dahulu.");
            }

            // 2. Catat unlock
            $this->unlockModel->insert([
                'user_id'       => $userId,
                'recipe_id'     => $recipeId,
                'coins_paid'    => $coinPrice,
                'chef_earn'     => $chefEarn,
                'platform_earn' => $platformEarn,
                'created_at'    => date('Y-m-d H:i:s'),
            ]);

            // 3. Catat transaksi keluar untuk user
            CoinTransactionModel::record($db, [
                'user_id'       => $userId,
                'type'          => 'unlock',
                'amount'        => -$coinPrice,
                'balance_after' => $newBalance,
                'ref_table'     => 'recipes',
                'ref_id'        => $recipeId,
                'note'          => 'Unlock resep: ' . $recipe['title'],
            ]);

            // 4. Tambah koin ke chef (earning)
            if ($chefEarn > 0 && $recipe['chef_id']) {
                $chefNewBal = $this->userModel->addCoins((int)$recipe['chef_id'], $chefEarn);
                CoinTransactionModel::record($db, [
                    'user_id'       => (int)$recipe['chef_id'],
                    'type'          => 'earn',
                    'amount'        => $chefEarn,
                    'balance_after' => $chefNewBal,
                    'ref_table'     => 'recipes',
                    'ref_id'        => $recipeId,
                    'note'          => 'Hasil resep premium: ' . $recipe['title'],
                ]);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->with('error', 'Gagal memproses transaksi');
            }

            // Update session coins
            session()->set('user_coins', $newBalance);

            return redirect()->to('/recipe/' . $recipe['slug'])
                ->with('success', "✅ Resep berhasil di-unlock! -{$coinPrice} koin. Sisa: {$newBalance} koin.");

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Unlock error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat unlock resep.');
        }
    }
}
