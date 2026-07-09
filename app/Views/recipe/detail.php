<?= $this->include('layout/header') ?>

<?php
$cuisineEmoji = ['Indonesian'=>'🍛','Japanese'=>'🍣','Italian'=>'🍝','Korean'=>'🥘','Thai'=>'🍜','Mexican'=>'🌮'];
$emoji        = $cuisineEmoji[$recipe['cuisine']] ?? '🍴';
$ingredients  = $recipe['ingredients'] ?? [];
$steps        = $recipe['steps'] ?? [];
$chefName     = $recipe['chef_name'] ?? 'Unknown Chef';
$recipeId     = $recipe['id'];
$slug         = $recipe['slug'];
$recipeImg    = recipe_image_url($recipe['image'] ?? null);
$isPremium    = !empty($recipe['is_premium']);
$coinPrice    = (int)($recipe['coin_price'] ?? 10);
$chefRole     = $recipe['chef_role'] ?? 'CHEF_UNVERIFIED';
$chefEarnPct  = ($chefRole === 'CHEF_VERIFIED') ? 70 : 50;
?>

<!-- Flash messages -->
<?php foreach (['success','error','info'] as $t):
    $msg = session()->getFlashdata($t);
    if (!$msg) continue;
    $bg = $t === 'success' ? '#f0fdf4' : ($t === 'error' ? '#fef2f2' : '#eff6ff');
    $cl = $t === 'success' ? '#166534' : ($t === 'error' ? '#991b1b' : '#1e40af');
    $bd = $t === 'success' ? '#86efac' : ($t === 'error' ? '#fca5a5' : '#93c5fd');
?>
<div class="mc-container" style="margin-top:1rem">
    <div style="background:<?= $bg ?>;color:<?= $cl ?>;border:1px solid <?= $bd ?>;border-radius:8px;padding:.75rem 1rem">
        <?= esc($msg) ?>
    </div>
</div>
<?php endforeach; ?>

<div class="mc-container" style="margin-top:1.5rem;margin-bottom:2rem">
    <a href="javascript:history.back()" class="mc-btn mc-btn-outline mc-btn-sm" style="margin-bottom:1rem">← Kembali</a>

    <!-- Recipe Hero Image -->
    <div style="width:100%;max-height:24rem;overflow:hidden;border-radius:var(--radius);margin-bottom:1.5rem;background:#f3f4f6">
        <img src="<?= $recipeImg ?>" alt="<?= esc($recipe['title']) ?>"
             style="width:100%;height:100%;object-fit:cover;display:block;max-height:24rem">
    </div>

    <!-- Recipe Header -->
    <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:1rem;margin-bottom:1.5rem">
        <div style="flex:1;min-width:0">
            <div style="display:flex;align-items:center;gap:.5rem;flex-wrap:wrap;margin-bottom:.5rem">
                <?php if ($isPremium): ?>
                <span style="background:#f59e0b;color:white;font-size:0.75rem;font-weight:700;padding:2px 8px;border-radius:999px">
                    ⭐ PREMIUM <?= $coinPrice ?> 🪙
                </span>
                <?php endif; ?>
                <span class="mc-badge mc-badge-<?= strtolower($recipe['difficulty']) ?>">
                    <?= difficulty_label($recipe['difficulty']) ?>
                </span>
                <span style="font-size:0.8125rem;color:#6b7280"><?= $emoji ?> <?= cuisine_label($recipe['cuisine']) ?></span>
            </div>
            <h1 style="font-size:1.75rem;font-weight:800;line-height:1.2;margin:0 0 .5rem"><?= esc($recipe['title']) ?></h1>
            <div style="font-size:0.875rem;color:#6b7280">
                oleh <strong><?= esc($chefName) ?></strong>
                <?php if ($chefRole === 'CHEF_VERIFIED'): ?>
                <span style="background:#d1fae5;color:#065f46;font-size:0.7rem;font-weight:700;padding:1px 6px;border-radius:999px;margin-left:.25rem">✓ VERIFIED</span>
                <?php endif; ?>
                • ⏱ <?= $recipe['cooking_time'] ?> menit • 🍽 <?= $recipe['servings'] ?> porsi
            </div>
        </div>
        <!-- Bookmark -->
        <div>
            <?php if ($isLoggedIn): ?>
            <form action="/bookmark/toggle/<?= $recipeId ?>" method="post" style="display:inline">
                <?= csrf_field() ?>
                <button class="mc-btn <?= $isBookmarked ? 'mc-btn-primary' : 'mc-btn-outline' ?>" style="font-size:1.1rem">
                    <?= $isBookmarked ? '🔖 Tersimpan' : '🔖 Simpan' ?>
                </button>
            </form>
            <?php else: ?>
            <a href="/login" class="mc-btn mc-btn-outline">🔖 Simpan</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Premium Banner / Unlock UI -->
    <?php if ($isPremium && !$canViewPremium): ?>
    <div class="mc-unlock-banner">
        <div style="font-size:3rem;margin-bottom:1rem">🔒</div>
        <h3 style="margin:0 0 .5rem;font-size:1.25rem">Resep Premium — <?= $coinPrice ?> 🪙</h3>
        <p style="color:#92400e;margin:0 0 1.5rem;font-size:0.9375rem">
            Unlock resep ini untuk melihat semua langkah memasak dan tips rahasia dari chef.
        </p>
        <!-- Bagi hasil info -->
        <div class="mc-revenue-split" style="justify-content:center;margin-bottom:1.25rem">
            <span>Chef dapat <strong><?= $chefEarnPct ?>%</strong></span>
            <span style="color:#d1d5db">·</span>
            <span style="color:#6b7280">Platform <?= 100 - $chefEarnPct ?>%</span>
            <span style="color:#d1d5db">·</span>
            <span>dari setiap unlock</span>
        </div>
        <?php if (!$isLoggedIn): ?>
        <a href="/login" class="mc-btn mc-btn-primary" style="background:#f59e0b;border-color:#f59e0b;font-size:1rem;padding:.75rem 2rem">
            🔑 Login untuk Unlock
        </a>
        <?php else: ?>
        <div style="margin-bottom:1rem">
            Saldo koin Anda: <strong style="font-size:1.1rem"><?= number_format($userCoins) ?> 🪙</strong>
            <?php if ($userCoins < $coinPrice): ?>
            <span style="color:#ef4444;font-size:0.875rem"> (kurang <?= $coinPrice - $userCoins ?> koin)</span>
            <?php endif; ?>
        </div>
        <?php if ($userCoins >= $coinPrice): ?>
        <form action="/recipe/<?= $recipeId ?>/unlock" method="post" style="display:inline"
              onsubmit="return confirm('Unlock resep ini dengan <?= $coinPrice ?> 🪙?')">
            <?= csrf_field() ?>
            <button class="mc-btn mc-btn-primary" style="background:#f59e0b;border-color:#f59e0b;font-size:1rem;padding:.75rem 2rem">
                🔓 Unlock dengan <?= $coinPrice ?> 🪙
            </button>
        </form>
        <?php else: ?>
        <a href="/coin/store" class="mc-btn mc-btn-primary" style="background:#f59e0b;border-color:#f59e0b;font-size:1rem;padding:.75rem 2rem">
            🪙 Top-up Koin Dulu
        </a>
        <?php endif; ?>
        <?php endif; ?>
    </div>

    <?php elseif ($isPremium && $canViewPremium): ?>
    <div style="background:linear-gradient(135deg,#10b981,#059669);color:white;border-radius:12px;padding:1rem 1.5rem;margin-bottom:1.5rem;display:flex;align-items:center;gap:1rem">
        <span style="font-size:1.5rem">✨</span>
        <div>
            <strong>Resep Premium — Terbuka!</strong>
            <div style="font-size:0.8125rem;opacity:.9;margin-top:.125rem">
                <?php if ($isUnlocked): ?>
                    Anda sudah unlock resep ini seharga <?= $coinPrice ?> 🪙
                <?php else: ?>
                    Sebagai <?= role_label($userRole) ?>, Anda memiliki akses penuh ke semua resep.
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Description -->
    <?php if (!empty($recipe['description'])): ?>
    <div style="background:white;border:1px solid var(--mc-border);border-radius:var(--radius);padding:1.25rem;margin-bottom:1.5rem;font-size:0.9375rem;line-height:1.7;color:var(--mc-gray)">
        <?= esc($recipe['description']) ?>
    </div>
    <?php endif; ?>

    <!-- Two-Column Layout -->
    <div class="mc-two-col">
        <!-- Ingredients Sidebar -->
        <div>
            <div class="mc-ingredients">
                <h2>🥘 Bahan-bahan</h2>
                <ul>
                    <?php foreach ($ingredients as $ingredient): ?>
                    <li>
                        <span style="flex:1"><?= esc($ingredient['name']) ?></span>
                        <span style="color:var(--mc-gray);white-space:nowrap"><?= esc($ingredient['amount']) ?> <?= esc($ingredient['unit']) ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php if (empty($ingredients)): ?>
                <p style="color:var(--mc-gray);font-size:0.875rem;text-align:center;padding:1rem 0">Belum ada bahan terdaftar.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Steps -->
        <div>
            <h2 style="font-size:1.125rem;font-weight:700;margin-bottom:1rem">📝 Langkah Memasak</h2>
            <?php foreach ($steps as $index => $step):
                $stepNum  = $index + 1;
                $isLocked = $isPremium && !$canViewPremium && $stepNum >= 3;
                $stepImg  = step_image_url($step['image'] ?? null);
            ?>
            <div class="mc-step <?= $isLocked ? 'mc-premium-lock' : '' ?>">
                <?php if ($isLocked): ?>
                <div class="mc-blur" style="flex:1">
                    <div class="mc-step-content">
                        <p style="font-size:0.9375rem;line-height:1.7;color:var(--mc-gray);font-style:italic">
                            Konten langkah ini terkunci.
                        </p>
                    </div>
                </div>
                <div class="mc-lock-overlay">
                    <span style="font-size:2rem">🔒</span>
                    <strong style="color:var(--mc-dark);font-size:0.9375rem">Langkah Premium</strong>
                    <p style="font-size:0.8125rem;color:var(--mc-gray);text-align:center;max-width:200px">
                        Unlock resep ini dengan <?= $coinPrice ?> 🪙 untuk membuka semua langkah
                    </p>
                    <?php if ($isLoggedIn && $userCoins >= $coinPrice): ?>
                    <form action="/recipe/<?= $recipeId ?>/unlock" method="post" style="margin-top:.5rem"
                          onsubmit="return confirm('Unlock dengan <?= $coinPrice ?> 🪙?')">
                        <?= csrf_field() ?>
                        <button class="mc-btn mc-btn-gold mc-btn-sm">🔓 Unlock <?= $coinPrice ?> 🪙</button>
                    </form>
                    <?php elseif ($isLoggedIn): ?>
                    <a href="/coin/store" class="mc-btn mc-btn-gold mc-btn-sm" style="margin-top:.5rem">🪙 Top-up Koin</a>
                    <?php else: ?>
                    <a href="/login" class="mc-btn mc-btn-gold mc-btn-sm" style="margin-top:.5rem">🔑 Login</a>
                    <?php endif; ?>
                </div>
                <?php else: ?>
                <div class="mc-step-num"><?= $stepNum ?></div>
                <div class="mc-step-content">
                    <p style="font-size:0.9375rem;line-height:1.7"><?= esc($step['description']) ?></p>
                    <?php if ($stepImg): ?>
                    <img src="<?= $stepImg ?>" alt="Langkah <?= $stepNum ?>"
                         style="width:100%;max-height:14rem;object-fit:cover;border-radius:8px;margin-top:.75rem">
                    <?php endif; ?>
                    <?php if (!empty($step['tip'])): ?>
                    <div class="mc-step-tip">💡 <span><?= esc($step['tip']) ?></span></div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>

            <?php if (empty($steps)): ?>
            <div style="text-align:center;padding:2rem 1rem;color:var(--mc-gray);background:white;border:1px solid var(--mc-border);border-radius:var(--radius)">
                <div style="font-size:2rem;margin-bottom:.5rem">📝</div>
                <p>Belum ada langkah memasak.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= view('layout/footer') ?>
