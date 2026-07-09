<?php
namespace App\Controllers;

use App\Models\UserModel;
use App\Models\RecipeModel;
use App\Models\ChefVerificationModel;
use App\Models\CoinTopupModel;
use App\Models\RecipeUnlockModel;

class Admin extends BaseController
{
    protected UserModel             $userModel;
    protected RecipeModel           $recipeModel;
    protected ChefVerificationModel $chefVerificationModel;

    public function __construct()
    {
        helper('cookpad');
        $this->userModel             = new UserModel();
        $this->recipeModel           = new RecipeModel();
        $this->chefVerificationModel = new ChefVerificationModel();
    }

    public function index()
    {
        $db = \Config\Database::connect();

        $stats = [
            'total_users'           => $db->table('users')->countAllResults(),
            'total_recipes'         => $db->table('recipes')->where('status','published')->countAllResults(),
            'total_chef_verified'   => $db->table('users')->where('role','CHEF_VERIFIED')->countAllResults(),
            'total_chef_unverified' => $db->table('users')->where('role','CHEF_UNVERIFIED')->countAllResults(),
            'pending_verifications' => $db->table('chef_verifications')->where('status','pending')->countAllResults(),
            'total_unlocks'         => $db->table('recipe_unlocks')->countAllResults(),
            // Platform revenue = semua platform_earn dari unlock
            'platform_coins'        => (int)($db->table('recipe_unlocks')->selectSum('platform_earn')->get()->getRowArray()['platform_earn'] ?? 0),
            // Total koin beredar = sum topup yang paid
            'coins_circulating'     => (int)($db->table('coin_topups')->where('status','paid')->selectSum('coin_amount')->get()->getRowArray()['coin_amount'] ?? 0),
        ];

        $recentUnlocks = $db->table('recipe_unlocks')
            ->select('recipe_unlocks.*, recipes.title as recipe_title, u1.name as buyer_name, u2.name as chef_name')
            ->join('recipes', 'recipes.id = recipe_unlocks.recipe_id')
            ->join('users u1', 'u1.id = recipe_unlocks.user_id')
            ->join('users u2', 'u2.id = recipes.chef_id')
            ->orderBy('recipe_unlocks.created_at', 'DESC')
            ->limit(8)
            ->get()->getResultArray();

        $recentUsers = $this->userModel->orderBy('created_at','DESC')->findAll(5);

        return view('admin/index', [
            'stats'         => $stats,
            'recentUnlocks' => $recentUnlocks,
            'recentUsers'   => $recentUsers,
            'title'         => 'Admin Dashboard - Mini Cookpad',
        ]);
    }

    public function verifications()
    {
        $type   = $this->request->getGet('type') ?? 'all';
        $status = $this->request->getGet('status') ?? 'pending';

        $db = \Config\Database::connect();
        $builder = $db->table('chef_verifications')
            ->select('chef_verifications.*, users.name as user_name, users.email as user_email, users.role as user_role')
            ->join('users', 'users.id = chef_verifications.user_id');

        if ($type !== 'all') $builder->where('chef_verifications.verification_type', $type);
        if ($status !== 'all') $builder->where('chef_verifications.status', $status);

        $verifications = $builder->orderBy('chef_verifications.created_at', 'DESC')->get()->getResultArray();

        return view('admin/verifications', [
            'verifications' => $verifications,
            'filterType'    => $type,
            'filterStatus'  => $status,
            'title'         => 'Verifikasi Chef - Admin',
        ]);
    }

    public function reviewVerification($id = null, $action = null)
    {
        if (!in_array($action, ['approve','reject'])) {
            return redirect()->back()->with('error', 'Aksi tidak valid');
        }

        $verif = $this->chefVerificationModel->find($id);
        if (!$verif) return redirect()->back()->with('error', 'Verifikasi tidak ditemukan');
        if ($verif['status'] !== 'pending') return redirect()->back()->with('error', 'Sudah diproses');

        $db = \Config\Database::connect();
        $db->transStart();

        if ($action === 'approve') {
            $newRole = $verif['target_role']; // CHEF_UNVERIFIED atau CHEF_VERIFIED
            $this->chefVerificationModel->update($id, [
                'status'      => 'approved',
                'admin_note'  => $this->request->getPost('admin_note'),
                'reviewed_at' => date('Y-m-d H:i:s'),
            ]);
            $this->userModel->update($verif['user_id'], ['role' => $newRole]);
            $message = 'Verifikasi disetujui → role menjadi ' . role_label($newRole);
        } else {
            // Jika reject basic, kembalikan ke USER_FREE. Jika reject advanced, tetap CHEF_UNVERIFIED
            $revertRole = ($verif['target_role'] === 'CHEF_UNVERIFIED') ? 'USER_FREE' : 'CHEF_UNVERIFIED';
            $this->chefVerificationModel->update($id, [
                'status'      => 'rejected',
                'admin_note'  => $this->request->getPost('admin_note'),
                'reviewed_at' => date('Y-m-d H:i:s'),
            ]);
            $this->userModel->update($verif['user_id'], ['role' => $revertRole]);
            $message = 'Verifikasi ditolak';
        }

        $db->transComplete();
        return redirect()->to('/admin/verifications')->with('success', $message);
    }

    public function users()
    {
        $search = $this->request->getGet('search');
        $role   = $this->request->getGet('role');
        $db     = \Config\Database::connect();
        $builder = $db->table('users');
        if (!empty($search)) $builder->groupStart()->like('name', $search)->orLike('email', $search)->groupEnd();
        if (!empty($role)) $builder->where('role', $role);
        $users = $builder->orderBy('created_at','DESC')->get()->getResultArray();

        foreach ($users as &$u) {
            $u['recipe_count']  = $db->table('recipes')->where('chef_id', $u['id'])->countAllResults();
            $u['unlock_count']  = $db->table('recipe_unlocks')->where('user_id', $u['id'])->countAllResults();
        }

        return view('admin/users', ['users' => $users, 'search' => $search ?? '', 'role' => $role ?? '', 'title' => 'Manajemen User - Admin']);
    }

    public function updateUserRole($id = null)
    {
        $id = (int) $id;
        $newRole = $this->request->getPost('role');
        $allowed = ['USER_FREE','CHEF_UNVERIFIED','CHEF_PENDING','CHEF_VERIFIED','ADMIN'];
        if (!in_array($newRole, $allowed)) return redirect()->back()->with('error', 'Role tidak valid');
        $target = $this->userModel->find($id);
        if (!$target) return redirect()->back()->with('error', 'User tidak ditemukan');
        if ((int)$target['id'] === (int)session()->get('user_id') && $newRole !== 'ADMIN') {
            return redirect()->back()->with('error', 'Tidak bisa menurunkan role sendiri');
        }
        $this->userModel->update($id, ['role' => $newRole]);
        return redirect()->back()->with('success', "Role '{$target['name']}' → " . role_label($newRole));
    }

    public function deleteUser($id = null)
    {
        $id = (int) $id;
        $target = $this->userModel->find($id);
        if (!$target) return redirect()->back()->with('error', 'User tidak ditemukan');
        if ((int)$target['id'] === (int)session()->get('user_id')) {
            return redirect()->back()->with('error', 'Tidak bisa hapus akun sendiri');
        }
        $db = \Config\Database::connect();
        $verifs = $db->table('chef_verifications')->where('user_id', $id)->get()->getResultArray();
        foreach ($verifs as $v) {
            if (!empty($v['id_card_photo'])) @unlink(ROOTPATH.'public/uploads/verifications/'.$v['id_card_photo']);
            if (!empty($v['certificate_photo'])) @unlink(ROOTPATH.'public/uploads/verifications/'.$v['certificate_photo']);
        }
        $recipes = $db->table('recipes')->where('chef_id', $id)->get()->getResultArray();
        foreach ($recipes as $r) {
            if (!empty($r['image'])) @unlink(ROOTPATH.'public/uploads/recipes/'.$r['image']);
        }
        $this->userModel->delete($id);
        return redirect()->back()->with('success', "User '{$target['name']}' dihapus");
    }

    public function recipes()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('recipes')
            ->select('recipes.*, users.name as chef_name, users.role as chef_role')
            ->join('users', 'users.id = recipes.chef_id');
        $search = $this->request->getGet('search');
        $status = $this->request->getGet('status');
        if (!empty($search)) $builder->groupStart()->like('recipes.title', $search)->orLike('users.name', $search)->groupEnd();
        if (!empty($status)) $builder->where('recipes.status', $status);
        $recipes = $builder->orderBy('recipes.created_at','DESC')->get()->getResultArray();
        foreach ($recipes as &$r) {
            $r['unlock_count'] = $db->table('recipe_unlocks')->where('recipe_id', $r['id'])->countAllResults();
        }
        $chefs = $db->table('users')->whereIn('role',['CHEF_VERIFIED','CHEF_UNVERIFIED'])->orderBy('name','ASC')->get()->getResultArray();
        return view('admin/recipes', ['recipes' => $recipes, 'chefs' => $chefs, 'search' => $search ?? '', 'status' => $status ?? '', 'title' => 'Manajemen Resep - Admin']);
    }

    public function recipeToggleStatus($id = null, $newStatus = null)
    {
        $id = (int) $id;
        if (!in_array($newStatus, ['published','draft','archived'])) return redirect()->back()->with('error', 'Status tidak valid');
        $recipe = $this->recipeModel->find($id);
        if (!$recipe) return redirect()->back()->with('error', 'Resep tidak ditemukan');
        $this->recipeModel->update($id, ['status' => $newStatus]);
        return redirect()->back()->with('success', "Resep '{$recipe['title']}' diperbarui");
    }

    public function recipeTogglePremium($id = null)
    {
        $id = (int) $id;
        $recipe = $this->recipeModel->find($id);
        if (!$recipe) return redirect()->back()->with('error', 'Resep tidak ditemukan');
        $new = !empty($recipe['is_premium']) ? 0 : 1;
        $this->recipeModel->update($id, ['is_premium' => $new]);
        return redirect()->back()->with('success', "Resep " . ($new ? 'dijadikan Premium' : 'dijadikan Free'));
    }

    public function recipeDelete($id = null)
    {
        $id = (int) $id;
        $recipe = $this->recipeModel->find($id);
        if (!$recipe) return redirect()->back()->with('error', 'Resep tidak ditemukan');
        if (!empty($recipe['image'])) @unlink(ROOTPATH.'public/uploads/recipes/'.$recipe['image']);
        $db = \Config\Database::connect();
        $steps = $db->table('steps')->where('recipe_id', $id)->get()->getResultArray();
        foreach ($steps as $s) if (!empty($s['image'])) @unlink(ROOTPATH.'public/uploads/steps/'.$s['image']);
        $this->recipeModel->deleteWithRelations($id);
        return redirect()->back()->with('success', "Resep '{$recipe['title']}' dihapus");
    }
}
