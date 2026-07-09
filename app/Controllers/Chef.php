<?php
namespace App\Controllers;

use App\Models\ChefVerificationModel;
use App\Models\RecipeModel;
use App\Models\IngredientModel;
use App\Models\StepModel;
use App\Models\UserModel;
use App\Models\RecipeUnlockModel;

class Chef extends BaseController
{
    protected ChefVerificationModel $chefVerificationModel;
    protected RecipeModel           $recipeModel;
    protected IngredientModel       $ingredientModel;
    protected StepModel             $stepModel;
    protected UserModel             $userModel;
    protected RecipeUnlockModel     $unlockModel;

    public function __construct()
    {
        helper(['cookpad','form','url']);
        $this->chefVerificationModel = new ChefVerificationModel();
        $this->recipeModel           = new RecipeModel();
        $this->ingredientModel       = new IngredientModel();
        $this->stepModel             = new StepModel();
        $this->userModel             = new UserModel();
        $this->unlockModel           = new RecipeUnlockModel();
    }

    private function ensureUploadDirs(): void
    {
        foreach ([ROOTPATH.'public/uploads', ROOTPATH.'public/uploads/recipes',
                  ROOTPATH.'public/uploads/steps', ROOTPATH.'public/uploads/verifications'] as $d) {
            if (!is_dir($d)) @mkdir($d, 0775, true);
        }
    }

    private function deleteOldImage(?string $path): void
    {
        if (empty($path)) return;
        $full = ROOTPATH . 'public' . $path;
        if (strpos($path, '/uploads/') === 0 && is_file($full)) @unlink($full);
    }

    // ─── VERIFIKASI CHEF BASIC (USER → CHEF_UNVERIFIED) ─────────

    public function verifyForm()
    {
        $userId   = session()->get('user_id');
        $userRole = session()->get('user_role');

        // ADMIN & CHEF_VERIFIED tidak perlu verifikasi basic
        if (in_array($userRole, ['ADMIN', 'CHEF_VERIFIED'], true)) {
            return redirect()->to('/chef/dashboard')->with('info', 'Anda sudah menjadi Chef Terverifikasi.');
        }
        // CHEF_UNVERIFIED bisa langsung ke advanced
        if ($userRole === 'CHEF_UNVERIFIED') {
            return redirect()->to('/chef/verify-advanced')->with('info', 'Anda sudah Chef. Ajukan sertifikasi untuk menjadi Chef Terverifikasi!');
        }

        $existing = $this->chefVerificationModel->getUserVerification($userId, 'basic');
        if ($existing && in_array($existing['status'], ['pending','approved'])) {
            return redirect()->to('/chef/status')->with('info', 'Pengajuan basic Anda sedang diproses.');
        }

        return view('chef/verify', ['title' => 'Daftar Chef - Mini Cookpad', 'type' => 'basic']);
    }

    public function submitVerification()
    {
        $userId   = session()->get('user_id');
        $userRole = session()->get('user_role');

        if (in_array($userRole, ['ADMIN', 'CHEF_VERIFIED'], true)) {
            return redirect()->to('/chef/dashboard');
        }
        if ($userRole === 'CHEF_UNVERIFIED') {
            return redirect()->to('/chef/verify-advanced');
        }

        $existing = $this->chefVerificationModel->getUserVerification($userId, 'basic');
        if ($existing && in_array($existing['status'], ['pending','approved'])) {
            return redirect()->to('/chef/status');
        }
        if ($existing && $existing['status'] === 'rejected') {
            $this->chefVerificationModel->delete($existing['id']);
        }

        $rules = [
            'id_card_number'   => 'required|min_length[10]|max_length[20]',
            'specialization'   => 'required|min_length[2]|max_length[255]',
            'experience'       => 'required|min_length[10]',
            'id_card_photo'    => 'uploaded[id_card_photo]|is_image[id_card_photo]|max_size[id_card_photo,2048]',
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->ensureUploadDirs();
        $idCard = $this->request->getFile('id_card_photo');
        $idCardName = '';
        if ($idCard && $idCard->isValid() && !$idCard->hasMoved()) {
            $idCardName = $idCard->getRandomName();
            $idCard->move(ROOTPATH . 'public/uploads/verifications', $idCardName);
        }

        $this->chefVerificationModel->insert([
            'user_id'           => $userId,
            'verification_type' => 'basic',
            'target_role'       => 'CHEF_UNVERIFIED',
            'id_card_number'    => $this->request->getPost('id_card_number'),
            'id_card_photo'     => $idCardName,
            'specialization'    => $this->request->getPost('specialization'),
            'experience'        => $this->request->getPost('experience'),
            'portfolio_url'     => $this->request->getPost('portfolio_url'),
            'status'            => 'pending',
        ]);

        $this->userModel->update($userId, ['role' => 'CHEF_PENDING']);
        session()->set('user_role', 'CHEF_PENDING');

        return redirect()->to('/chef/status')->with('success', 'Pengajuan Chef dikirim! Admin akan memverifikasi KTP Anda.');
    }

    // ─── VERIFIKASI CHEF ADVANCED (CHEF_UNVERIFIED → CHEF_VERIFIED) ──

    public function verifyAdvancedForm()
    {
        $userId   = session()->get('user_id');
        $userRole = session()->get('user_role');

        // Hanya CHEF_UNVERIFIED & ADMIN yang bisa ajukan advanced
        if ($userRole === 'CHEF_VERIFIED') {
            return redirect()->to('/chef/dashboard')->with('info', 'Anda sudah Chef Terverifikasi!');
        }
        if (!in_array($userRole, ['CHEF_UNVERIFIED', 'ADMIN'])) {
            return redirect()->to('/chef/verify')->with('info', 'Anda perlu mendaftar sebagai Chef dulu.');
        }

        $existing = $this->chefVerificationModel->getUserVerification($userId, 'advanced');
        if ($existing && in_array($existing['status'], ['pending','approved'])) {
            return redirect()->to('/chef/status')->with('info', 'Pengajuan sertifikasi Anda sedang diproses.');
        }

        return view('chef/verify_advanced', ['title' => 'Sertifikasi Chef - Mini Cookpad', 'existing' => $existing]);
    }

    public function submitVerificationAdvanced()
    {
        $userId   = session()->get('user_id');
        $userRole = session()->get('user_role');

        if ($userRole === 'CHEF_VERIFIED') return redirect()->to('/chef/dashboard');
        if (!in_array($userRole, ['CHEF_UNVERIFIED', 'ADMIN'])) return redirect()->to('/chef/verify');

        $existing = $this->chefVerificationModel->getUserVerification($userId, 'advanced');
        if ($existing && in_array($existing['status'], ['pending','approved'])) return redirect()->to('/chef/status');
        if ($existing && $existing['status'] === 'rejected') $this->chefVerificationModel->delete($existing['id']);

        $rules = [
            'specialization'    => 'required|min_length[2]|max_length[255]',
            'experience'        => 'required|min_length[10]',
            'certificate_photo' => 'uploaded[certificate_photo]|is_image[certificate_photo]|max_size[certificate_photo,2048]',
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->ensureUploadDirs();
        $cert = $this->request->getFile('certificate_photo');
        $certName = '';
        if ($cert && $cert->isValid() && !$cert->hasMoved()) {
            $certName = $cert->getRandomName();
            $cert->move(ROOTPATH . 'public/uploads/verifications', $certName);
        }

        // Ambil nomor KTP dari verifikasi basic yang approved
        $basicVerif = $this->chefVerificationModel->getUserVerification($userId, 'basic');

        $this->chefVerificationModel->insert([
            'user_id'           => $userId,
            'verification_type' => 'advanced',
            'target_role'       => 'CHEF_VERIFIED',
            'id_card_number'    => $basicVerif['id_card_number'] ?? '-',
            'id_card_photo'     => $basicVerif['id_card_photo'] ?? '',
            'certificate_photo' => $certName,
            'specialization'    => $this->request->getPost('specialization'),
            'experience'        => $this->request->getPost('experience'),
            'portfolio_url'     => $this->request->getPost('portfolio_url'),
            'status'            => 'pending',
        ]);

        return redirect()->to('/chef/status')->with('success', 'Pengajuan sertifikasi Chef Terverifikasi berhasil! Admin akan mereview sertifikat Anda.');
    }

    public function status()
    {
        $userId = session()->get('user_id');
        $verifs = $this->chefVerificationModel->getAllByUser($userId);

        $basicVerif    = collect_by($verifs, 'verification_type', 'basic');
        $advancedVerif = collect_by($verifs, 'verification_type', 'advanced');

        return view('chef/status', [
            'basicVerif'    => $basicVerif,
            'advancedVerif' => $advancedVerif,
            'title'         => 'Status Verifikasi - Mini Cookpad',
        ]);
    }

    // ─── DASHBOARD CHEF ──────────────────────────────────────────

    public function dashboard()
    {
        $userId = session()->get('user_id');
        $status = $this->request->getGet('status') ?? 'all';

        $recipes    = $this->recipeModel->getRecipesByChef($userId, $status);
        $allRecipes = $this->recipeModel->getRecipesByChef($userId, 'all');
        $user       = $this->userModel->find($userId);

        $totalRecipes   = count($allRecipes);
        $premiumCount   = count(array_filter($allRecipes, fn($r) => !empty($r['is_premium'])));
        $publishedCount = count(array_filter($allRecipes, fn($r) => ($r['status'] ?? '') === 'published'));
        $draftCount     = count(array_filter($allRecipes, fn($r) => ($r['status'] ?? '') === 'draft'));
        $archivedCount  = count(array_filter($allRecipes, fn($r) => ($r['status'] ?? '') === 'archived'));

        // Total koin yang dihasilkan dari unlock resep
        $totalEarned = $this->unlockModel->totalEarnedByChef($userId);

        // Tambah unlock count per resep
        foreach ($recipes as &$r) {
            $r['unlock_count'] = $this->unlockModel->countByRecipe($r['id']);
        }

        return view('chef/dashboard', [
            'recipes'        => $recipes,
            'user'           => $user,
            'totalRecipes'   => $totalRecipes,
            'premiumCount'   => $premiumCount,
            'publishedCount' => $publishedCount,
            'draftCount'     => $draftCount,
            'archivedCount'  => $archivedCount,
            'totalEarned'    => $totalEarned,
            'currentStatus'  => $status,
            'title'          => 'Dashboard Chef - Mini Cookpad',
        ]);
    }

    // ─── CRUD RESEP ───────────────────────────────────────────────

    public function createRecipe()
    {
        return view('chef/create_recipe', ['title' => 'Buat Resep Baru - Mini Cookpad']);
    }

    public function storeRecipe()
    {
        $rules = [
            'title'        => 'required|min_length[3]|max_length[255]',
            'description'  => 'required|min_length[10]',
            'cuisine'      => 'required|in_list[Indonesian,Japanese,Italian,Korean,Thai,Mexican]',
            'category'     => 'required|in_list[appetizer,main_course,dessert,snack,drink,soup,other]',
            'difficulty'   => 'required|in_list[easy,medium,hard]',
            'cooking_time' => 'required|integer|greater_than[0]|less_than_equal_to[10080]',
            'servings'     => 'required|integer|greater_than[0]|less_than_equal_to[1000]',
            'image'        => 'permit_empty|is_image[image]|max_size[image,2048]',
            'coin_price'   => 'required|integer|greater_than_equal_to[5]|less_than_equal_to[50]',
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $ingredients = $this->request->getPost('ingredients');
        $steps       = $this->request->getPost('steps');
        if (!is_array($ingredients) || count(array_filter($ingredients, fn($i) => !empty($i['name']))) < 1) {
            return redirect()->back()->withInput()->with('error', 'Minimal 1 bahan diperlukan.');
        }
        if (!is_array($steps) || count(array_filter($steps, fn($s) => !empty($s['description']))) < 1) {
            return redirect()->back()->withInput()->with('error', 'Minimal 1 langkah memasak diperlukan.');
        }

        $this->ensureUploadDirs();
        $userId = session()->get('user_id');
        $title  = $this->request->getPost('title');
        $slug   = slugify($title);
        if ($slug === '') $slug = 'recipe-' . time();
        if ($this->recipeModel->where('slug', $slug)->first()) $slug .= '-' . time();

        $imageName = '';
        $image = $this->request->getFile('image');
        if ($image && $image->isValid() && !$image->hasMoved()) {
            $imageName = $image->getRandomName();
            $image->move(ROOTPATH . 'public/uploads/recipes', $imageName);
        }

        $isPremium  = $this->request->getPost('is_premium') ? 1 : 0;
        $coinPrice  = (int) $this->request->getPost('coin_price');
        $rawStatus  = $this->request->getPost('status');
        $status     = in_array($rawStatus, ['draft','published']) ? $rawStatus : 'published';

        $recipeId = $this->recipeModel->insert([
            'chef_id'      => $userId,
            'title'        => $title,
            'slug'         => $slug,
            'description'  => $this->request->getPost('description'),
            'cuisine'      => $this->request->getPost('cuisine'),
            'category'     => $this->request->getPost('category'),
            'difficulty'   => $this->request->getPost('difficulty'),
            'cooking_time' => (int) $this->request->getPost('cooking_time'),
            'servings'     => (int) $this->request->getPost('servings'),
            'image'        => $imageName,
            'is_premium'   => $isPremium,
            'coin_price'   => $isPremium ? $coinPrice : 0,
            'status'       => $status,
        ]);

        if (!$recipeId) return redirect()->back()->withInput()->with('error', 'Gagal membuat resep');

        $this->saveIngredientsAndSteps($recipeId);
        return redirect()->to('/chef/dashboard')->with('success', 'Resep berhasil dibuat!');
    }

    public function editRecipe($id = null)
    {
        $id     = (int) $id;
        $userId = (int) session()->get('user_id');
        $recipe = $this->recipeModel->getForEdit($id);
        if (!$recipe || (int)$recipe['chef_id'] !== $userId) {
            return redirect()->to('/chef/dashboard')->with('error', 'Resep tidak ditemukan');
        }
        return view('chef/edit_recipe', ['recipe' => $recipe, 'title' => 'Edit Resep - Mini Cookpad']);
    }

    public function updateRecipe($id = null)
    {
        $id     = (int) $id;
        $userId = (int) session()->get('user_id');
        $recipe = $this->recipeModel->find($id);
        if (!$recipe || (int)$recipe['chef_id'] !== $userId) {
            return redirect()->to('/chef/dashboard')->with('error', 'Resep tidak ditemukan');
        }

        $rules = [
            'title'        => 'required|min_length[3]|max_length[255]',
            'description'  => 'required|min_length[10]',
            'cuisine'      => 'required|in_list[Indonesian,Japanese,Italian,Korean,Thai,Mexican]',
            'category'     => 'required|in_list[appetizer,main_course,dessert,snack,drink,soup,other]',
            'difficulty'   => 'required|in_list[easy,medium,hard]',
            'cooking_time' => 'required|integer|greater_than[0]|less_than_equal_to[10080]',
            'servings'     => 'required|integer|greater_than[0]|less_than_equal_to[1000]',
            'image'        => 'permit_empty|is_image[image]|max_size[image,2048]',
            'coin_price'   => 'required|integer|greater_than_equal_to[5]|less_than_equal_to[50]',
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $ingredients = $this->request->getPost('ingredients');
        $steps       = $this->request->getPost('steps');
        if (!is_array($ingredients) || count(array_filter($ingredients, fn($i) => !empty($i['name']))) < 1) {
            return redirect()->back()->withInput()->with('error', 'Minimal 1 bahan diperlukan.');
        }
        if (!is_array($steps) || count(array_filter($steps, fn($s) => !empty($s['description']))) < 1) {
            return redirect()->back()->withInput()->with('error', 'Minimal 1 langkah memasak diperlukan.');
        }

        $this->ensureUploadDirs();
        $title = $this->request->getPost('title');
        $slug  = slugify($title);
        if ($slug === '') $slug = 'recipe-' . time();
        if ($this->recipeModel->where('slug', $slug)->where('id !=', $id)->first()) $slug .= '-' . time();

        $imageName = $recipe['image'];
        $newImage  = $this->request->getFile('image');
        if ($newImage && $newImage->isValid() && !$newImage->hasMoved()) {
            if (!empty($recipe['image'])) $this->deleteOldImage('/uploads/recipes/' . $recipe['image']);
            $imageName = $newImage->getRandomName();
            $newImage->move(ROOTPATH . 'public/uploads/recipes', $imageName);
        } elseif ($this->request->getPost('remove_image')) {
            if (!empty($recipe['image'])) $this->deleteOldImage('/uploads/recipes/' . $recipe['image']);
            $imageName = '';
        }

        $isPremium = $this->request->getPost('is_premium') ? 1 : 0;
        $coinPrice = (int) $this->request->getPost('coin_price');
        $rawStatus = $this->request->getPost('status');
        $status    = in_array($rawStatus, ['draft','published','archived']) ? $rawStatus : $recipe['status'];

        $this->recipeModel->update($id, [
            'title'        => $title,
            'slug'         => $slug,
            'description'  => $this->request->getPost('description'),
            'cuisine'      => $this->request->getPost('cuisine'),
            'category'     => $this->request->getPost('category'),
            'difficulty'   => $this->request->getPost('difficulty'),
            'cooking_time' => (int) $this->request->getPost('cooking_time'),
            'servings'     => (int) $this->request->getPost('servings'),
            'image'        => $imageName,
            'is_premium'   => $isPremium,
            'coin_price'   => $isPremium ? $coinPrice : 0,
            'status'       => $status,
        ]);

        $this->ingredientModel->deleteByRecipe($id);
        $this->stepModel->deleteByRecipe($id);
        $this->saveIngredientsAndSteps($id);

        return redirect()->to('/chef/dashboard')->with('success', 'Resep berhasil diperbarui!');
    }

    public function deleteRecipe($id = null)
    {
        $id     = (int) $id;
        $userId = (int) session()->get('user_id');
        $recipe = $this->recipeModel->find($id);
        if (!$recipe || (int)$recipe['chef_id'] !== $userId) {
            return redirect()->to('/chef/dashboard')->with('error', 'Akses ditolak');
        }
        if (!empty($recipe['image'])) $this->deleteOldImage('/uploads/recipes/' . $recipe['image']);
        $steps = $this->stepModel->getByRecipe($id);
        foreach ($steps as $s) if (!empty($s['image'])) $this->deleteOldImage('/uploads/steps/' . $s['image']);
        $this->recipeModel->deleteWithRelations($id);
        return redirect()->to('/chef/dashboard')->with('success', 'Resep berhasil dihapus');
    }

    public function togglePublish($id = null)
    {
        $id     = (int) $id;
        $userId = (int) session()->get('user_id');
        $recipe = $this->recipeModel->find($id);
        if (!$recipe || (int)$recipe['chef_id'] !== $userId) return redirect()->to('/chef/dashboard');
        $new = ($recipe['status'] === 'published') ? 'draft' : 'published';
        $this->recipeModel->update($id, ['status' => $new]);
        return redirect()->to('/chef/dashboard')->with('success', $new === 'published' ? 'Resep dipublikasi' : 'Resep dijadikan draft');
    }

    public function archiveRecipe($id = null)
    {
        $id     = (int) $id;
        $userId = (int) session()->get('user_id');
        $recipe = $this->recipeModel->find($id);
        if (!$recipe || (int)$recipe['chef_id'] !== $userId) return redirect()->to('/chef/dashboard');
        $this->recipeModel->update($id, ['status' => 'archived']);
        return redirect()->to('/chef/dashboard')->with('success', 'Resep diarsipkan');
    }

    private function saveIngredientsAndSteps(int $recipeId): void
    {
        $ingredients = $this->request->getPost('ingredients');
        if (!empty($ingredients) && is_array($ingredients)) {
            $sort = 1;
            foreach ($ingredients as $i) {
                if (!empty($i['name'])) {
                    $this->ingredientModel->insert(['recipe_id' => $recipeId, 'name' => $i['name'], 'amount' => $i['amount'] ?? '', 'unit' => $i['unit'] ?? '', 'sort_order' => $sort++]);
                }
            }
        }
        $steps = $this->request->getPost('steps');
        if (!empty($steps) && is_array($steps)) {
            $sort = 1;
            $stepFiles = $this->request->getFiles();
            foreach ($steps as $idx => $step) {
                if (!empty($step['description'])) {
                    $stepImg = '';
                    if (isset($stepFiles['steps'][$idx]['image'])) {
                        $f = $stepFiles['steps'][$idx]['image'];
                        if ($f && $f->isValid() && !$f->hasMoved() && in_array($f->getMimeType(), ['image/jpeg','image/png','image/webp','image/gif']) && ($f->getSize()/1024) <= 2048) {
                            $stepImg = $f->getRandomName();
                            $f->move(ROOTPATH . 'public/uploads/steps', $stepImg);
                        }
                    }
                    $this->stepModel->insert(['recipe_id' => $recipeId, 'sort_order' => $sort++, 'description' => $step['description'], 'image' => $stepImg, 'tip' => $step['tip'] ?? null]);
                }
            }
        }
    }
}