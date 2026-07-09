<?= $this->include('layout/header') ?>

<?php
 $users = $users ?? [];
 $search = $search ?? '';
 $role = $role ?? '';
?>

<!-- Flash messages -->
<?php if (session()->getFlashdata('success')): ?>
<div class="mc-container" style="margin-top:1rem">
    <div class="mc-alert mc-alert-success">✅ <?= esc(session()->getFlashdata('success')) ?></div>
</div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
<div class="mc-container" style="margin-top:1rem">
    <div class="mc-alert mc-alert-error">❌ <?= esc(session()->getFlashdata('error')) ?></div>
</div>
<?php endif; ?>

<div class="mc-container" style="margin-top:1.5rem;margin-bottom:2rem">

    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;margin-bottom:1.5rem">
        <div>
            <h1 style="font-size:1.5rem;font-weight:700">👥 Manajemen User</h1>
            <p style="color:var(--mc-gray);font-size:0.875rem">Kelola semua pengguna Mini Cookpad — ubah role atau hapus user</p>
        </div>
        <a href="/admin" class="mc-btn mc-btn-outline mc-btn-sm">← Dashboard</a>
    </div>

    <!-- Filter Bar -->
    <div style="background:white;border:1px solid var(--mc-border);border-radius:var(--radius);padding:1rem;margin-bottom:1.5rem">
        <form action="/admin/users" method="get" style="display:flex;gap:0.75rem;flex-wrap:wrap;align-items:flex-end">
            <div class="mc-form-group" style="flex:2;margin-bottom:0">
                <label style="font-size:0.8125rem">Cari</label>
                <input type="text" name="search" class="mc-input" placeholder="Nama atau email..." value="<?= esc($search) ?>">
            </div>
            <div class="mc-form-group" style="flex:1;margin-bottom:0">
                <label style="font-size:0.8125rem">Role</label>
                <select name="role" class="mc-select">
                    <option value="">Semua Role</option>
                    <option value="USER_FREE" <?= $role === 'USER_FREE' ? 'selected' : '' ?>>Free</option>
                    <option value="USER_PREMIUM" <?= $role === 'USER_PREMIUM' ? 'selected' : '' ?>>Premium</option>
                    <option value="CHEF_PENDING" <?= $role === 'CHEF_PENDING' ? 'selected' : '' ?>>Chef Pending</option>
                    <option value="CHEF_VERIFIED" <?= $role === 'CHEF_VERIFIED' ? 'selected' : '' ?>>Chef Verified</option>
                    <option value="ADMIN" <?= $role === 'ADMIN' ? 'selected' : '' ?>>Admin</option>
                </select>
            </div>
            <button type="submit" class="mc-btn mc-btn-primary">🔍 Cari</button>
            <?php if ($search || $role): ?>
            <a href="/admin/users" class="mc-btn mc-btn-outline">Reset</a>
            <?php endif; ?>
        </form>
    </div>

    <?php if (empty($users)): ?>
    <div style="background:white;border:1px solid var(--mc-border);border-radius:var(--radius);padding:3rem;text-align:center">
        <div style="font-size:3rem;margin-bottom:1rem">👥</div>
        <h2 style="font-size:1.125rem;font-weight:700;margin-bottom:0.5rem">Tidak Ada User</h2>
        <p style="color:var(--mc-gray);font-size:0.875rem">Belum ada user yang sesuai filter.</p>
    </div>
    <?php else: ?>
    <div style="background:white;border:1px solid var(--mc-border);border-radius:var(--radius);overflow:hidden">
        <div style="overflow-x:auto">
            <table class="mc-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Bergabung</th>
                        <th style="text-align:center">Resep</th>
                        <th style="text-align:center">🔖</th>
                        <th style="text-align:center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                    <?php
                        $badgeClass = match($u['role']) {
                            'USER_FREE' => 'mc-badge-free',
                            'USER_PREMIUM' => 'mc-badge-premium',
                            'CHEF_PENDING' => 'mc-badge-pending',
                            'CHEF_VERIFIED' => 'mc-badge-chef',
                            'ADMIN' => 'mc-badge-admin',
                            default => 'mc-badge-free',
                        };
                        $initial = strtoupper(mb_substr($u['name'], 0, 1));
                        $joined = !empty($u['created_at']) ? date('d M Y', strtotime($u['created_at'])) : '-';
                        $isSelf = (int) $u['id'] === (int) session()->get('user_id');
                    ?>
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:0.625rem">
                                <div style="width:2rem;height:2rem;background:var(--mc-green);color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:0.75rem;font-weight:700;flex-shrink:0">
                                    <?= $initial ?>
                                </div>
                                <div>
                                    <div style="font-weight:600"><?= esc($u['name']) ?> <?= $isSelf ? '<span style="color:var(--mc-gray);font-weight:400">(Anda)</span>' : '' ?></div>
                                </div>
                            </div>
                        </td>
                        <td style="color:var(--mc-gray);font-size:0.875rem"><?= esc($u['email']) ?></td>
                        <td>
                            <!-- Inline role change form -->
                            <form action="/admin/user/<?= $u['id'] ?>/update-role" method="post" style="display:inline-flex;align-items:center;gap:0.25rem">
            <?= csrf_field() ?>
                                <select name="role" class="mc-select" style="padding:0.25rem 0.5rem;font-size:0.75rem;width:auto" onchange="this.form.submit()">
                                    <option value="USER_FREE" <?= $u['role'] === 'USER_FREE' ? 'selected' : '' ?>>Free</option>
                                    <option value="USER_PREMIUM" <?= $u['role'] === 'USER_PREMIUM' ? 'selected' : '' ?>>Premium</option>
                                    <option value="CHEF_PENDING" <?= $u['role'] === 'CHEF_PENDING' ? 'selected' : '' ?>>Chef Pending</option>
                                    <option value="CHEF_VERIFIED" <?= $u['role'] === 'CHEF_VERIFIED' ? 'selected' : '' ?>>Chef Verified</option>
                                    <option value="ADMIN" <?= $u['role'] === 'ADMIN' ? 'selected' : '' ?>>Admin</option>
                                </select>
                            </form>
                        </td>
                        <td style="font-size:0.8125rem;color:var(--mc-gray)"><?= $joined ?></td>
                        <td style="text-align:center;font-weight:600"><?= $u['recipe_count'] ?? 0 ?></td>
                        <td style="text-align:center;font-weight:600"><?= $u['unlock_count'] ?? 0 ?></td>
                        <td style="text-align:center">
                            <?php if ($isSelf): ?>
                            <span style="color:var(--mc-gray);font-size:0.75rem">—</span>
                            <?php else: ?>
                            <form action="/admin/user/<?= $u['id'] ?>/delete" method="post"
                                  onsubmit="return confirm('Hapus user \'<?= esc($u['name']) ?>\'? Semua resep, bookmark, dan data terkait akan dihapus.')">
                                <?= csrf_field() ?>
                                <button type="submit" class="mc-btn mc-btn-danger mc-btn-sm" title="Hapus user">🗑️</button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div style="text-align:center;margin-top:1rem;font-size:0.8125rem;color:var(--mc-gray)">
        Menampilkan <?= count($users) ?> user
    </div>
    <?php endif; ?>

</div>

<?= $this->include('layout/footer') ?>