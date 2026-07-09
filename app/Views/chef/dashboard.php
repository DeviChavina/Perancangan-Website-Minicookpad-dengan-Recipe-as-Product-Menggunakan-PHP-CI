<?= view('layout/header', ['title' => $title]) ?>
<div class="mc-container" style="padding:2rem 1rem">

<?php foreach (['success','error','info'] as $t):
    $msg = session()->getFlashdata($t);
    if (!$msg) continue;
    $bg = $t==='success'?'#f0fdf4':($t==='error'?'#fef2f2':'#eff6ff');
    $cl = $t==='success'?'#166534':($t==='error'?'#991b1b':'#1e40af');
    $bd = $t==='success'?'#86efac':($t==='error'?'#fca5a5':'#93c5fd');
?>
<div style="background:<?= $bg ?>;color:<?= $cl ?>;border:1px solid <?= $bd ?>;border-radius:8px;padding:.75rem 1rem;margin-bottom:1rem"><?= esc($msg) ?></div>
<?php endforeach; ?>

<!-- Header -->
<div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:1rem;margin-bottom:2rem">
    <div>
        <h2 style="margin:0 0 .25rem">👨‍🍳 Dashboard Chef</h2>
        <?php
        $role = session()->get('user_role');
        if ($role === 'CHEF_VERIFIED'): ?>
        <span style="background:#d1fae5;color:#065f46;font-size:0.8125rem;font-weight:700;padding:3px 10px;border-radius:999px">⭐ Chef Verified — 70% revenue</span>
        <?php else: ?>
        <span style="background:#fef3c7;color:#92400e;font-size:0.8125rem;font-weight:700;padding:3px 10px;border-radius:999px">🍳 Chef — 50% revenue</span>
        <?php if ($role === 'CHEF_UNVERIFIED'): ?>
        <a href="/chef/verify-advanced" style="font-size:0.8125rem;color:#10b981;margin-left:.75rem;font-weight:600">⬆️ Upgrade ke Chef Verified →</a>
        <?php endif; ?>
        <?php endif; ?>
    </div>
    <a href="/chef/recipe/create" class="mc-btn mc-btn-primary">+ Buat Resep</a>
</div>

<!-- Stats Grid -->
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:1rem;margin-bottom:2rem">
    <div style="background:white;border:1px solid var(--mc-border);border-radius:12px;padding:1.25rem;text-align:center">
        <div style="font-size:2rem;font-weight:800;color:var(--mc-orange)"><?= $totalRecipes ?></div>
        <div style="font-size:0.8125rem;color:#6b7280">Total Resep</div>
    </div>
    <div style="background:white;border:1px solid var(--mc-border);border-radius:12px;padding:1.25rem;text-align:center">
        <div style="font-size:2rem;font-weight:800;color:#f59e0b"><?= $premiumCount ?></div>
        <div style="font-size:0.8125rem;color:#6b7280">Resep Premium</div>
    </div>
    <div style="background:white;border:1px solid var(--mc-border);border-radius:12px;padding:1.25rem;text-align:center">
        <div style="font-size:2rem;font-weight:800;color:#10b981"><?= $publishedCount ?></div>
        <div style="font-size:0.8125rem;color:#6b7280">Dipublikasi</div>
    </div>
    <div style="background:white;border:1px solid var(--mc-border);border-radius:12px;padding:1.25rem;text-align:center">
        <div style="font-size:2rem;font-weight:800;color:#6366f1"><?= $draftCount ?></div>
        <div style="font-size:0.8125rem;color:#6b7280">Draft</div>
    </div>
    <!-- Coin earnings -->
    <div style="background:linear-gradient(135deg,#fef3c7,#fde68a);border:2px solid #f59e0b;border-radius:12px;padding:1.25rem;text-align:center">
        <div style="font-size:2rem;font-weight:800;color:#92400e"><?= number_format($totalEarned) ?> 🪙</div>
        <div style="font-size:0.8125rem;color:#b45309;font-weight:600">Total Penghasilan</div>
    </div>
    <div style="background:white;border:1px solid var(--mc-border);border-radius:12px;padding:1.25rem;text-align:center">
        <div style="font-size:2rem;font-weight:800;color:#f59e0b"><?= number_format((int)($user['coin_balance'] ?? 0)) ?> 🪙</div>
        <div style="font-size:0.8125rem;color:#6b7280">Saldo Koin</div>
    </div>
</div>

<!-- Revenue info box -->
<?php $chefRole = session()->get('user_role'); ?>
<div style="background:#f9fafb;border:1px solid var(--mc-border);border-radius:8px;padding:1rem 1.25rem;margin-bottom:1.5rem;font-size:0.875rem">
    <strong>💰 Bagi Hasil Resep Premium Anda:</strong>
    <?php if ($chefRole === 'CHEF_VERIFIED'): ?>
    Anda mendapat <strong style="color:#10b981">70%</strong> dari setiap koin yang masuk. Contoh: resep 10 🪙 → Anda dapat <strong>7 🪙</strong>, platform 3 🪙.
    <?php else: ?>
    Anda mendapat <strong style="color:#f59e0b">50%</strong> dari setiap koin yang masuk. Contoh: resep 10 🪙 → Anda dapat <strong>5 🪙</strong>, platform 5 🪙.
    <a href="/chef/verify-advanced" style="margin-left:.5rem;color:#10b981;font-weight:600">Upgrade ke Chef Verified untuk dapat 70% →</a>
    <?php endif; ?>
</div>

<!-- Filter -->
<div style="display:flex;gap:.5rem;flex-wrap:wrap;margin-bottom:1.25rem">
    <?php foreach (['all'=>'Semua','published'=>'Dipublikasi','draft'=>'Draft','archived'=>'Arsip'] as $k=>$label): ?>
    <a href="/chef/dashboard?status=<?= $k ?>" class="mc-btn mc-btn-sm <?= $currentStatus===$k ? 'mc-btn-primary' : 'mc-btn-outline' ?>" style="<?= $currentStatus===$k ? '' : '' ?>"><?= $label ?></a>
    <?php endforeach; ?>
</div>

<!-- Recipes Table -->
<div style="background:white;border:1px solid var(--mc-border);border-radius:12px;overflow:hidden">
    <?php if (empty($recipes)): ?>
    <div style="text-align:center;padding:3rem;color:#9ca3af">
        <div style="font-size:2.5rem;margin-bottom:.5rem">🍳</div>
        <div>Belum ada resep. <a href="/chef/recipe/create" style="color:var(--mc-orange)">Buat sekarang!</a></div>
    </div>
    <?php else: ?>
    <div style="overflow-x:auto">
    <table style="width:100%;border-collapse:collapse;font-size:0.875rem">
        <thead>
            <tr style="background:#f9fafb;border-bottom:1px solid var(--mc-border)">
                <th style="padding:.75rem 1rem;text-align:left">Resep</th>
                <th style="padding:.75rem;text-align:center">Status</th>
                <th style="padding:.75rem;text-align:center">Premium</th>
                <th style="padding:.75rem;text-align:center">Harga</th>
                <th style="padding:.75rem;text-align:center">Unlock</th>
                <th style="padding:.75rem;text-align:center">Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($recipes as $recipe): ?>
        <tr style="border-bottom:1px solid var(--mc-border)">
            <td style="padding:.75rem 1rem">
                <div style="font-weight:600;max-width:220px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                    <?= esc($recipe['title']) ?>
                </div>
                <div style="font-size:0.75rem;color:#9ca3af"><?= cuisine_label($recipe['cuisine']) ?> · <?= difficulty_label($recipe['difficulty']) ?></div>
            </td>
            <td style="padding:.75rem;text-align:center">
                <?php $sc = match($recipe['status']) {'published'=>'#10b981','draft'=>'#f59e0b','archived'=>'#9ca3af',default=>'#9ca3af'}; ?>
                <span style="background:<?= $sc ?>20;color:<?= $sc ?>;font-size:0.75rem;font-weight:700;padding:2px 8px;border-radius:999px">
                    <?= ucfirst($recipe['status']) ?>
                </span>
            </td>
            <td style="padding:.75rem;text-align:center">
                <?= !empty($recipe['is_premium']) ? '<span style="color:#f59e0b;font-weight:700">⭐ Premium</span>' : '<span style="color:#9ca3af">Free</span>' ?>
            </td>
            <td style="padding:.75rem;text-align:center;font-weight:600;color:#92400e">
                <?= !empty($recipe['is_premium']) ? (($recipe['coin_price'] ?? 10) . ' 🪙') : '-' ?>
            </td>
            <td style="padding:.75rem;text-align:center">
                <span style="font-weight:600"><?= number_format((int)($recipe['unlock_count'] ?? 0)) ?></span>
                <?php if (!empty($recipe['is_premium']) && !empty($recipe['unlock_count'])): ?>
                <div style="font-size:0.7rem;color:#9ca3af">
                    +<?= (int)floor(($recipe['coin_price'] ?? 10) * ($chefRole === 'CHEF_VERIFIED' ? 0.7 : 0.5)) * (int)$recipe['unlock_count'] ?> 🪙 earned
                </div>
                <?php endif; ?>
            </td>
            <td style="padding:.75rem;text-align:center">
                <div style="display:flex;gap:.25rem;justify-content:center;flex-wrap:wrap">
                    <a href="/chef/recipe/<?= $recipe['id'] ?>/edit" class="mc-btn mc-btn-sm mc-btn-outline" style="font-size:0.75rem">Edit</a>
                    <form action="/chef/recipe/<?= $recipe['id'] ?>/toggle-publish" method="post" style="display:inline">
                        <?= csrf_field() ?>
                        <button class="mc-btn mc-btn-sm mc-btn-outline" style="font-size:0.75rem">
                            <?= $recipe['status'] === 'published' ? 'Unpublish' : 'Publish' ?>
                        </button>
                    </form>
                    <form action="/chef/recipe/<?= $recipe['id'] ?>/delete" method="post" style="display:inline"
                          onsubmit="return confirm('Hapus resep ini?')">
                        <?= csrf_field() ?>
                        <button class="mc-btn mc-btn-sm" style="background:#ef4444;color:white;border:none;font-size:0.75rem">Hapus</button>
                    </form>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?php endif; ?>
</div>

</div>
<?= view('layout/footer') ?>
