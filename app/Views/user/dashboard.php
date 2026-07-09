<?php
helper('cookpad');
$userRole = $user['role'] ?? 'USER_FREE';
$badgeMap = ['USER_FREE'=>'free','CHEF_UNVERIFIED'=>'chef','CHEF_PENDING'=>'pending','CHEF_VERIFIED'=>'verified','ADMIN'=>'admin'];
$badgeClass = $badgeMap[$userRole] ?? 'free';
?>
<?= view('layout/header', ['title' => $title]) ?>
<div class="mc-container" style="padding:2rem 1rem">

<?php foreach (['success','error'] as $t): $msg = session()->getFlashdata($t); if (!$msg) continue;
    $bg=$t==='success'?'#f0fdf4':'#fef2f2';$cl=$t==='success'?'#166534':'#991b1b';$bd=$t==='success'?'#86efac':'#fca5a5';?>
<div style="background:<?=$bg?>;color:<?=$cl?>;border:1px solid <?=$bd?>;border-radius:8px;padding:.75rem 1rem;margin-bottom:1rem"><?= esc($msg) ?></div>
<?php endforeach; ?>

<div style="display:grid;grid-template-columns:280px 1fr;gap:2rem;align-items:start">
    <!-- Sidebar Profil -->
    <div>
        <div style="background:white;border:1px solid var(--mc-border);border-radius:12px;padding:1.5rem;text-align:center;margin-bottom:1rem">
            <div style="width:80px;height:80px;background:linear-gradient(135deg,var(--mc-orange),var(--mc-yellow));border-radius:50%;margin:0 auto 1rem;display:flex;align-items:center;justify-content:center;font-size:2rem">
                👤
            </div>
            <h3 style="margin:0 0 .25rem"><?= esc($user['name']) ?></h3>
            <div style="color:#6b7280;font-size:0.875rem;margin-bottom:.75rem"><?= esc($user['email']) ?></div>
            <span class="mc-badge mc-badge-<?= $badgeClass ?>"><?= role_label($userRole) ?></span>
        </div>

        <!-- Koin Balance -->
        <div style="background:linear-gradient(135deg,#f59e0b,#d97706);color:white;border-radius:12px;padding:1.25rem;text-align:center;margin-bottom:1rem">
            <div style="font-size:0.875rem;opacity:.85;margin-bottom:.25rem">Saldo Koin</div>
            <div style="font-size:2.5rem;font-weight:900"><?= number_format((int)($user['coin_balance'] ?? 0)) ?> 🪙</div>
            <a href="/coin/store" style="display:inline-block;margin-top:.75rem;background:rgba(255,255,255,.2);color:white;border:1px solid rgba(255,255,255,.5);padding:.375rem .875rem;border-radius:8px;text-decoration:none;font-size:0.875rem">
                + Top-up Koin
            </a>
        </div>

        <!-- Stats -->
        <div style="background:white;border:1px solid var(--mc-border);border-radius:12px;padding:1rem">
            <div style="display:flex;justify-content:space-between;padding:.5rem 0;border-bottom:1px solid #f3f4f6;font-size:0.875rem">
                <span style="color:#6b7280">Resep Unlock</span>
                <strong><?= number_format((int)($user['unlock_count'] ?? 0)) ?></strong>
            </div>
            <div style="display:flex;justify-content:space-between;padding:.5rem 0;border-bottom:1px solid #f3f4f6;font-size:0.875rem">
                <span style="color:#6b7280">Bookmark</span>
                <strong><?= number_format((int)($user['bookmark_count'] ?? 0)) ?></strong>
            </div>
            <?php if (in_array($userRole, ['CHEF_UNVERIFIED','CHEF_VERIFIED','ADMIN'])): ?>
            <div style="display:flex;justify-content:space-between;padding:.5rem 0;border-bottom:1px solid #f3f4f6;font-size:0.875rem">
                <span style="color:#6b7280">Total Resep</span>
                <strong><?= number_format((int)($user['recipe_count'] ?? 0)) ?></strong>
            </div>
            <div style="display:flex;justify-content:space-between;padding:.5rem 0;font-size:0.875rem">
                <span style="color:#6b7280">Total Penghasilan</span>
                <strong style="color:#f59e0b"><?= number_format((int)($user['total_earned'] ?? 0)) ?> 🪙</strong>
            </div>
            <?php endif; ?>
        </div>

        <!-- Nav Links -->
        <div style="margin-top:1rem;display:flex;flex-direction:column;gap:.5rem">
            <a href="/coin/history" class="mc-btn mc-btn-outline" style="width:100%;text-align:center">📋 Riwayat Koin</a>
            <a href="/bookmarks" class="mc-btn mc-btn-outline" style="width:100%;text-align:center">🔖 Bookmark</a>
            <?php if (in_array($userRole, ['CHEF_UNVERIFIED','CHEF_VERIFIED','ADMIN'])): ?>
            <a href="/chef/dashboard" class="mc-btn mc-btn-primary" style="width:100%;text-align:center">👨‍🍳 Dashboard Chef</a>
            <?php elseif ($userRole === 'USER_FREE'): ?>
            <a href="/chef/verify" class="mc-btn mc-btn-outline" style="width:100%;text-align:center">👨‍🍳 Jadi Chef</a>
            <?php elseif ($userRole === 'CHEF_UNVERIFIED'): ?>
            <a href="/chef/verify-advanced" class="mc-btn mc-btn-outline" style="width:100%;text-align:center">⭐ Upgrade Verified</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Main Content -->
    <div>
        <!-- Recent Transactions -->
        <?php if (!empty($recentTransactions)): ?>
        <div style="background:white;border:1px solid var(--mc-border);border-radius:12px;overflow:hidden;margin-bottom:1.5rem">
            <div style="padding:1rem 1.25rem;border-bottom:1px solid var(--mc-border);display:flex;justify-content:space-between;align-items:center">
                <h3 style="margin:0;font-size:1rem">🪙 Transaksi Terakhir</h3>
                <a href="/coin/history" style="font-size:0.8125rem;color:var(--mc-orange)">Lihat semua →</a>
            </div>
            <?php foreach ($recentTransactions as $t):
                $isPos = (int)$t['amount'] > 0;
                $icon  = match($t['type']) {'topup'=>'💳','unlock'=>'🔓','earn'=>'💰','refund'=>'↩️', default=>'🪙'};
            ?>
            <div style="padding:.75rem 1.25rem;border-bottom:1px solid #f9fafb;font-size:0.8125rem;display:flex;justify-content:space-between;align-items:center">
                <div><?= $icon ?> <?= esc($t['note'] ?? $t['type']) ?></div>
                <div style="font-weight:700;color:<?= $isPos?'#10b981':'#ef4444' ?>">
                    <?= $isPos?'+':'' ?><?= number_format((int)$t['amount']) ?> 🪙
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Unlocked Recipes -->
        <div style="background:white;border:1px solid var(--mc-border);border-radius:12px;overflow:hidden">
            <div style="padding:1rem 1.25rem;border-bottom:1px solid var(--mc-border)">
                <h3 style="margin:0;font-size:1rem">🔓 Resep yang Sudah Di-unlock</h3>
            </div>
            <?php if (empty($unlockedRecipes)): ?>
            <div style="padding:2.5rem;text-align:center;color:#9ca3af">
                <div style="font-size:2rem;margin-bottom:.5rem">🔒</div>
                Belum ada resep yang di-unlock.
                <br><a href="/recipes" style="color:var(--mc-orange);margin-top:.5rem;display:inline-block">Jelajahi resep premium →</a>
            </div>
            <?php else: ?>
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:1rem;padding:1.25rem">
                <?php foreach ($unlockedRecipes as $r): ?>
                <a href="/recipe/<?= esc($r['slug']) ?>" style="text-decoration:none;color:inherit">
                    <div style="border:1px solid var(--mc-border);border-radius:8px;overflow:hidden;transition:box-shadow .2s" onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,.1)'" onmouseout="this.style.boxShadow='none'">
                        <div style="height:100px;overflow:hidden;background:#f3f4f6">
                            <img src="<?= recipe_image_url($r['image'] ?? null) ?>" alt="<?= esc($r['title']) ?>" style="width:100%;height:100%;object-fit:cover">
                        </div>
                        <div style="padding:.625rem">
                            <div style="font-weight:600;font-size:0.875rem;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= esc($r['title']) ?></div>
                            <div style="font-size:0.75rem;color:#9ca3af;margin-top:.125rem"><?= cuisine_label($r['cuisine']) ?> · <?= $r['coins_paid'] ?> 🪙</div>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
</div>
<?= view('layout/footer') ?>
