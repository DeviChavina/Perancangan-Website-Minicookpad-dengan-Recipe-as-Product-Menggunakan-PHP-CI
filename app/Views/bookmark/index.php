<?= $this->include('layout/header') ?>

<?php
 $bookmarkCount = count($bookmarks ?? []);

 $cuisineEmojis = [
    'Indonesian' => '🇮🇩', 'Japanese' => '🇯🇵', 'Italian' => '🇮🇹',
    'Korean' => '🇰🇷', 'Thai' => '🇹🇭', 'Mexican' => '🇲🇽',
];

 $cuisineGradient = [
    'Indonesian' => 'linear-gradient(135deg, #FF6B35, #D4A017)',
    'Japanese'   => 'linear-gradient(135deg, #E63946, #FF6B6B)',
    'Italian'    => 'linear-gradient(135deg, #2D6A4F, #52B788)',
    'Korean'     => 'linear-gradient(135deg, #7B2D8E, #C084FC)',
    'Thai'       => 'linear-gradient(135deg, #D4A017, #F59E0B)',
    'Mexican'    => 'linear-gradient(135deg, #059669, #34D399)',
];

 $difficultyClass = fn($d) => match($d) {
    'easy' => 'mc-diff-easy', 'medium' => 'mc-diff-medium', 'hard' => 'mc-diff-hard', default => 'mc-diff-medium'
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

    <!-- Free User Limit Notice -->
    <?php if (!empty($isFree)): ?>
    <div class="mc-alert mc-alert-warning" style="margin-bottom:1rem;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:0.75rem">
        <div>
            <strong>⚠️ Batasan Free User</strong>
            <p style="font-size:0.8125rem;margin-top:0.25rem">
                Anda menggunakan <?= $bookmarkCount ?> dari <?= $freeLimit ?? 3 ?> slot bookmark.
            </p>
        </div>
        <a href="/subscribe" class="mc-btn mc-btn-gold mc-btn-sm">⭐ Upgrade Premium</a>
    </div>
    <?php endif; ?>

    <!-- Page Header -->
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;margin-bottom:1.5rem">
        <div>
            <h1 style="font-size:1.5rem;font-weight:700">🔖 Resep Tersimpan</h1>
            <p style="color:var(--mc-gray);font-size:0.875rem"><?= $bookmarkCount ?> resep disimpan</p>
        </div>
        <a href="/recipes" class="mc-btn mc-btn-outline">📖 Jelajahi Resep</a>
    </div>

    <?php if (empty($bookmarks)): ?>
    <!-- Empty State -->
    <div style="background:white;border:1px solid var(--mc-border);border-radius:var(--radius);padding:3rem;text-align:center">
        <div style="font-size:4rem;margin-bottom:1rem">🔖</div>
        <h2 style="font-size:1.25rem;font-weight:700;margin-bottom:0.5rem">Belum Ada Resep Tersimpan</h2>
        <p style="color:var(--mc-gray);font-size:0.875rem;margin-bottom:1.5rem;max-width:24rem;margin-left:auto;margin-right:auto">
            Simpan resep favorit Anda agar mudah ditemukan kembali nanti.
        </p>
        <a href="/recipes" class="mc-btn mc-btn-primary">📖 Jelajahi Resep</a>
    </div>

    <?php else: ?>
    <!-- Bookmark List -->
    <div style="display:grid;gap:0.75rem">
        <?php foreach ($bookmarks as $bookmark): ?>
        <?php
            $recipe   = $bookmark;
            $recipeId = $recipe['recipe_id'] ?? $recipe['id'] ?? 0;
            $slug     = $recipe['slug'] ?? '';
            $chefName = $recipe['chef_name'] ?? $recipe['name'] ?? 'Chef';
            $emoji    = $cuisineEmojis[$recipe['cuisine']] ?? '🍽️';
            $gradient = $cuisineGradient[$recipe['cuisine']] ?? 'linear-gradient(135deg, #6B7280, #9CA3AF)';
            $imgUrl   = recipe_image_url($recipe['image'] ?? null);
        ?>
        <div class="mc-card" style="display:flex;align-items:center;flex-wrap:wrap;gap:1rem;padding:1rem 1.25rem">
            <!-- Thumbnail -->
            <div style="width:3.5rem;height:3.5rem;background:<?= $gradient ?>;border-radius:10px;overflow:hidden;display:flex;align-items:center;justify-content:center;font-size:1.5rem;flex-shrink:0">
                <?php if ($imgUrl): ?>
                    <img src="<?= $imgUrl ?>" alt="<?= esc($recipe['title']) ?>" style="width:100%;height:100%;object-fit:cover;display:block">
                <?php else: ?>
                    <?= $emoji ?>
                <?php endif; ?>
            </div>

            <!-- Recipe Info -->
            <div style="flex:1;min-width:180px">
                <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.25rem">
                    <h3 style="font-size:1rem;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                        <a href="/recipe/<?= esc($slug) ?>" style="color:var(--mc-dark);text-decoration:none"><?= esc($recipe['title']) ?></a>
                    </h3>
                    <?php if (!empty($recipe['is_premium'])): ?>
                        <span class="mc-badge mc-badge-premium">⭐</span>
                    <?php endif; ?>
                </div>
                <div style="display:flex;align-items:center;gap:0.75rem;font-size:0.8125rem;color:var(--mc-gray);flex-wrap:wrap">
                    <span style="color:var(--mc-green);font-weight:500">👨‍🍳 <?= esc($chefName) ?></span>
                    <span>·</span>
                    <span>⏱️ <?= $recipe['cooking_time'] ?? '-' ?> menit</span>
                    <span>·</span>
                    <span class="mc-diff-badge <?= $difficultyClass($recipe['difficulty'] ?? 'medium') ?>"><?= difficulty_label($recipe['difficulty'] ?? 'medium') ?></span>
                </div>
            </div>

            <!-- Actions -->
            <div style="display:flex;align-items:center;gap:0.5rem;flex-shrink:0">
                <a href="/recipe/<?= esc($slug) ?>" class="mc-btn mc-btn-outline mc-btn-sm">👁️ Lihat</a>
                <form action="/bookmark/toggle/<?= $recipeId ?>" method="post" style="display:inline">
                    <?= csrf_field() ?>
                    <button type="submit" class="mc-btn mc-btn-danger mc-btn-sm"
                            onclick="return confirm('Hapus resep ini dari bookmark?')"
                            title="Hapus dari bookmark">🗑️</button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

</div>

<?= $this->include('layout/footer') ?>