<?= $this->include('layout/header') ?>

<?php
 $recipes = $recipes ?? [];
 $chefs   = $chefs   ?? [];
 $search  = $search  ?? '';
 $status  = $status  ?? '';
 $chefId  = $chef    ?? '';

 $cuisineEmojis = [
    'Indonesian' => '🇮🇩', 'Japanese' => '🇯🇵', 'Italian' => '🇮🇹',
    'Korean' => '🇰🇷', 'Thai' => '🇹🇭', 'Mexican' => '🇲🇽',
];

 $statusBadge = fn($s) => match($s) {
    'published' => '<span class="mc-badge mc-badge-chef">Dipublikasi</span>',
    'draft'     => '<span class="mc-badge mc-badge-free">Draft</span>',
    'archived'  => '<span class="mc-badge mc-badge-pending">Diarsipkan</span>',
    default     => '<span class="mc-badge mc-badge-free">' . esc($s) . '</span>',
};
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

    <!-- Page Header -->
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;margin-bottom:1.5rem">
        <div>
            <h1 style="font-size:1.5rem;font-weight:700">📖 Manajemen Resep</h1>
            <p style="color:var(--mc-gray);font-size:0.875rem">Kelola semua resep di platform Mini Cookpad</p>
        </div>
        <a href="/admin" class="mc-btn mc-btn-outline mc-btn-sm">← Dashboard</a>
    </div>

    <!-- Filter Bar -->
    <div style="background:white;border:1px solid var(--mc-border);border-radius:var(--radius);padding:1rem;margin-bottom:1.5rem">
        <form action="/admin/recipes" method="get" style="display:flex;gap:0.75rem;flex-wrap:wrap;align-items:flex-end">
            <div class="mc-form-group" style="flex:2;margin-bottom:0">
                <label style="font-size:0.8125rem">Cari</label>
                <input type="text" name="search" class="mc-input" placeholder="Judul atau deskripsi..." value="<?= esc($search) ?>">
            </div>
            <div class="mc-form-group" style="flex:1;margin-bottom:0">
                <label style="font-size:0.8125rem">Status</label>
                <select name="status" class="mc-select">
                    <option value="">Semua</option>
                    <option value="published" <?= $status === 'published' ? 'selected' : '' ?>>Dipublikasi</option>
                    <option value="draft" <?= $status === 'draft' ? 'selected' : '' ?>>Draft</option>
                    <option value="archived" <?= $status === 'archived' ? 'selected' : '' ?>>Diarsipkan</option>
                </select>
            </div>
            <div class="mc-form-group" style="flex:1;margin-bottom:0">
                <label style="font-size:0.8125rem">Chef</label>
                <select name="chef" class="mc-select">
                    <option value="">Semua Chef</option>
                    <?php foreach ($chefs as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= $chefId === (string) $c['id'] ? 'selected' : '' ?>><?= esc($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="mc-btn mc-btn-primary">🔍 Cari</button>
            <?php if ($search || $status || $chefId): ?>
            <a href="/admin/recipes" class="mc-btn mc-btn-outline">Reset</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Recipes Table -->
    <?php if (empty($recipes)): ?>
    <div style="background:white;border:1px solid var(--mc-border);border-radius:var(--radius);padding:3rem;text-align:center">
        <div style="font-size:3rem;margin-bottom:1rem">📖</div>
        <h2 style="font-size:1.125rem;font-weight:700;margin-bottom:0.5rem">Tidak Ada Resep</h2>
        <p style="color:var(--mc-gray);font-size:0.875rem">Belum ada resep yang sesuai filter.</p>
    </div>
    <?php else: ?>
    <div style="background:white;border:1px solid var(--mc-border);border-radius:var(--radius);overflow:hidden">
        <div style="overflow-x:auto">
            <table class="mc-table">
                <thead>
                    <tr>
                        <th>Resep</th>
                        <th>Chef</th>
                        <th>Status</th>
                        <th style="text-align:center">⭐</th>
                        <th style="text-align:center">🔖</th>
                        <th>Tanggal</th>
                        <th style="text-align:center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recipes as $r): ?>
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:0.5rem">
                                <span style="font-size:1.25rem"><?= $cuisineEmojis[$r['cuisine']] ?? '🍽️' ?></span>
                                <div>
                                    <div style="font-weight:600"><?= esc($r['title']) ?></div>
                                    <div style="font-size:0.75rem;color:var(--mc-gray)"><?= cuisine_label($r['cuisine']) ?> · ⏱️ <?= $r['cooking_time'] ?>m</div>
                                </div>
                            </div>
                        </td>
                        <td style="font-size:0.875rem"><?= esc($r['chef_name']) ?></td>
                        <td><?= $statusBadge($r['status']) ?></td>
                        <td style="text-align:center">
                            <?php if (!empty($r['is_premium'])): ?>
                            <span style="color:var(--mc-gold);font-size:1.125rem">⭐</span>
                            <?php else: ?>—<?php endif; ?>
                        </td>
                        <td style="text-align:center;font-weight:600"><?= $r['bookmark_count'] ?? 0 ?></td>
                        <td style="font-size:0.75rem;color:var(--mc-gray)"><?= !empty($r['created_at']) ? date('d M Y', strtotime($r['created_at'])) : '-' ?></td>
                        <td>
                            <div style="display:flex;gap:0.25rem;justify-content:center;flex-wrap:wrap">
                                <!-- View -->
                                <a href="/recipe/<?= esc($r['slug']) ?>" target="_blank" class="mc-btn mc-btn-outline mc-btn-sm" title="Lihat">👁️</a>

                                <!-- Toggle Status -->
                                <form action="/admin/recipe/<?= $r['id'] ?>/status/<?= $r['status'] === 'published' ? 'draft' : 'published' ?>" method="post" style="display:inline">
            <?= csrf_field() ?>
                                    <button type="submit" class="mc-btn mc-btn-sm <?= $r['status'] === 'published' ? 'mc-btn-outline' : 'mc-btn-primary' ?>"
                                            title="<?= $r['status'] === 'published' ? 'Jadikan Draft' : 'Publikasikan' ?>">
                                        <?= $r['status'] === 'published' ? '📝' : '📤' ?>
                                    </button>
                                </form>

                                <!-- Archive -->
                                <form action="/admin/recipe/<?= $r['id'] ?>/status/archived" method="post" style="display:inline">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="mc-btn mc-btn-outline mc-btn-sm" title="Arsipkan"
                                            onclick="return confirm('Arsipkan resep ini?')">📦</button>
                                </form>

                                <!-- Toggle Premium -->
                                <form action="/admin/recipe/<?= $r['id'] ?>/toggle-premium" method="post" style="display:inline">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="mc-btn mc-btn-sm <?= !empty($r['is_premium']) ? 'mc-btn-gold' : 'mc-btn-outline' ?>"
                                            title="<?= !empty($r['is_premium']) ? 'Jadikan Free' : 'Jadikan Premium' ?>">⭐</button>
                                </form>

                                <!-- Delete -->
                                <form action="/admin/recipe/<?= $r['id'] ?>/delete" method="post" style="display:inline"
                                      onsubmit="return confirm('Hapus permanen resep \'<?= esc($r['title']) ?>\'?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="mc-btn mc-btn-danger mc-btn-sm" title="Hapus permanen">🗑️</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div style="text-align:center;margin-top:1rem;font-size:0.8125rem;color:var(--mc-gray)">
        Menampilkan <?= count($recipes) ?> resep
    </div>
    <?php endif; ?>

</div>

<?= $this->include('layout/footer') ?>