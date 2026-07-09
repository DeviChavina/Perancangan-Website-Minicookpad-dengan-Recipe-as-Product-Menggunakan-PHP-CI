<?= view('layout/header', ['title' => $title]) ?>
<div class="mc-container" style="padding:2rem 1rem">

<?php foreach (['success','error'] as $t):
    $msg = session()->getFlashdata($t);
    if (!$msg) continue;
    $bg = $t==='success'?'#f0fdf4':'#fef2f2'; $cl = $t==='success'?'#166534':'#991b1b'; $bd = $t==='success'?'#86efac':'#fca5a5';
?>
<div style="background:<?= $bg ?>;color:<?= $cl ?>;border:1px solid <?= $bd ?>;border-radius:8px;padding:.75rem 1rem;margin-bottom:1rem"><?= esc($msg) ?></div>
<?php endforeach; ?>

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:2rem;flex-wrap:wrap;gap:1rem">
    <h2 style="margin:0">🛡️ Admin Dashboard</h2>
    <div style="display:flex;gap:.5rem;flex-wrap:wrap">
        <a href="/admin/verifications?status=pending" class="mc-btn mc-btn-primary mc-btn-sm">
            ⏳ Review Verifikasi <?= $stats['pending_verifications'] > 0 ? "({$stats['pending_verifications']})" : '' ?>
        </a>
        <a href="/admin/users" class="mc-btn mc-btn-outline mc-btn-sm">👥 Users</a>
        <a href="/admin/recipes" class="mc-btn mc-btn-outline mc-btn-sm">🍳 Resep</a>
    </div>
</div>

<!-- Stats Grid -->
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:1rem;margin-bottom:2rem">
    <?php
    $statItems = [
        ['icon'=>'👥','value'=>number_format($stats['total_users']),'label'=>'Total User','color'=>'#6366f1'],
        ['icon'=>'🍳','value'=>number_format($stats['total_recipes']),'label'=>'Resep Published','color'=>'var(--mc-orange)'],
        ['icon'=>'⭐','value'=>number_format($stats['total_chef_verified']),'label'=>'Chef Verified','color'=>'#10b981'],
        ['icon'=>'🍴','value'=>number_format($stats['total_chef_unverified']),'label'=>'Chef (Unverified)','color'=>'#f59e0b'],
        ['icon'=>'⏳','value'=>number_format($stats['pending_verifications']),'label'=>'Verifikasi Pending','color'=>'#ef4444'],
        ['icon'=>'🔓','value'=>number_format($stats['total_unlocks']),'label'=>'Total Unlock','color'=>'#6366f1'],
        ['icon'=>'🪙','value'=>number_format($stats['platform_coins']).' 🪙','label'=>'Koin Platform','color'=>'#f59e0b'],
        ['icon'=>'💰','value'=>number_format($stats['coins_circulating']).' 🪙','label'=>'Koin Beredar','color'=>'#10b981'],
    ];
    foreach ($statItems as $s): ?>
    <div style="background:white;border:1px solid var(--mc-border);border-radius:12px;padding:1.25rem;text-align:center">
        <div style="font-size:1.5rem;margin-bottom:.25rem"><?= $s['icon'] ?></div>
        <div style="font-size:1.5rem;font-weight:800;color:<?= $s['color'] ?>"><?= $s['value'] ?></div>
        <div style="font-size:0.75rem;color:#6b7280"><?= $s['label'] ?></div>
    </div>
    <?php endforeach; ?>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:2rem">
    <!-- Recent Unlocks -->
    <div style="background:white;border:1px solid var(--mc-border);border-radius:12px;overflow:hidden">
        <div style="padding:1rem 1.25rem;border-bottom:1px solid var(--mc-border);font-weight:700">🔓 Unlock Terbaru</div>
        <?php if (empty($recentUnlocks)): ?>
        <div style="padding:2rem;text-align:center;color:#9ca3af;font-size:0.875rem">Belum ada unlock</div>
        <?php else: foreach ($recentUnlocks as $u): ?>
        <div style="padding:.75rem 1.25rem;border-bottom:1px solid #f9fafb;font-size:0.8125rem">
            <div style="font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?= esc($u['recipe_title']) ?></div>
            <div style="color:#6b7280">
                <?= esc($u['buyer_name']) ?> → Chef <?= esc($u['chef_name']) ?>
                · <span style="color:#92400e;font-weight:600"><?= $u['coins_paid'] ?> 🪙</span>
                (chef +<?= $u['chef_earn'] ?>, platform +<?= $u['platform_earn'] ?>)
            </div>
        </div>
        <?php endforeach; endif; ?>
    </div>

    <!-- Recent Users -->
    <div style="background:white;border:1px solid var(--mc-border);border-radius:12px;overflow:hidden">
        <div style="padding:1rem 1.25rem;border-bottom:1px solid var(--mc-border);font-weight:700">👥 User Terbaru</div>
        <?php foreach ($recentUsers as $u): ?>
        <div style="padding:.75rem 1.25rem;border-bottom:1px solid #f9fafb;font-size:0.8125rem;display:flex;justify-content:space-between;align-items:center">
            <div>
                <div style="font-weight:600"><?= esc($u['name']) ?></div>
                <div style="color:#9ca3af"><?= esc($u['email']) ?></div>
            </div>
            <div style="text-align:right">
                <span style="font-size:0.75rem"><?= role_label($u['role']) ?></span>
                <div style="color:#f59e0b;font-size:0.75rem"><?= number_format((int)$u['coin_balance']) ?> 🪙</div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

</div>
<?= view('layout/footer') ?>
