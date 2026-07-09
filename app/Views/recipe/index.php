<?= $this->include('layout/header') ?>

<?php
// Cuisine emoji and gradient mappings
 $cuisineEmoji = [
    'Indonesian' => '🍛', 'Japanese' => '🍣', 'Italian' => '🍝',
    'Korean' => '🥘', 'Thai' => '🍜', 'Mexican' => '🌮',
];

 $cuisineGradient = [
    'Indonesian' => 'linear-gradient(135deg, #FF6B35, #D4A017)',
    'Japanese'   => 'linear-gradient(135deg, #E63946, #FF6B6B)',
    'Italian'    => 'linear-gradient(135deg, #2D6A4F, #52B788)',
    'Korean'     => 'linear-gradient(135deg, #7B2D8E, #C084FC)',
    'Thai'       => 'linear-gradient(135deg, #D4A017, #F59E0B)',
    'Mexican'    => 'linear-gradient(135deg, #059669, #34D399)',
];

 $cuisineBgColor = [
    'Indonesian' => 'rgba(255,107,53,0.15)',
    'Japanese'   => 'rgba(230,57,70,0.15)',
    'Italian'    => 'rgba(45,106,79,0.15)',
    'Korean'     => 'rgba(123,45,142,0.15)',
    'Thai'       => 'rgba(212,160,23,0.15)',
    'Mexican'    => 'rgba(5,150,105,0.15)',
];

 $cuisineTextColor = [
    'Indonesian' => '#FF6B35',
    'Japanese'   => '#E63946',
    'Italian'    => '#2D6A4F',
    'Korean'     => '#7B2D8E',
    'Thai'       => '#D4A017',
    'Mexican'    => '#059669',
];

 $cuisines = ['Indonesian', 'Japanese', 'Italian', 'Korean', 'Thai', 'Mexican'];
 $currentCuisine = $filters['cuisine'] ?? '';
 $searchQuery = $filters['search'] ?? '';
 $category    = $filters['category'] ?? '';
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

<!-- Page Header -->
<section class="mc-container" style="margin-top:1.5rem">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;margin-bottom:1rem">
        <div>
            <h1 style="font-size:1.5rem;font-weight:700">📖 Semua Resep</h1>
            <p style="color:var(--mc-gray);font-size:0.875rem">Jelajahi koleksi resep dari chef terverifikasi</p>
        </div>
        <!-- Search bar (compact) -->
        <form method="get" action="/recipes" style="display:flex;gap:0.5rem">
            <input type="text" name="search" class="mc-input" placeholder="Cari resep..." value="<?= esc($searchQuery) ?>" style="width:14rem">
            <?php if (!empty($currentCuisine)): ?>
                <input type="hidden" name="cuisine" value="<?= esc($currentCuisine) ?>">
            <?php endif; ?>
            <?php if (!empty($category)): ?>
                <input type="hidden" name="category" value="<?= esc($category) ?>">
            <?php endif; ?>
            <button type="submit" class="mc-btn mc-btn-primary mc-btn-sm">Cari</button>
        </form>
    </div>
</section>

<!-- Cuisine Filter -->
<section class="mc-container">
    <div class="mc-filter">
        <a href="/recipes?<?= http_build_query(array_filter(['search' => $searchQuery, 'category' => $category])) ?>" class="mc-filter-btn <?= empty($currentCuisine) ? 'active' : '' ?>">🍽️ Semua</a>
        <?php foreach ($cuisines as $cuisine): ?>
            <?php
            $params = array_filter(['cuisine' => $cuisine, 'search' => $searchQuery, 'category' => $category]);
            ?>
            <a href="/recipes?<?= http_build_query($params) ?>" class="mc-filter-btn <?= $currentCuisine === $cuisine ? 'active' : '' ?>">
                <?= $cuisineEmoji[$cuisine] ?? '🍴' ?> <?= cuisine_label($cuisine) ?>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<!-- Category Filter -->
<?php
 $categories = [
    ''            => '🍽️ Semua Kategori',
    'appetizer'   => '🥗 Appetizer',
    'main_course' => '🍽️ Main Course',
    'dessert'     => '🍰 Dessert',
    'snack'       => '🍿 Snack',
    'drink'       => '🥤 Minuman',
    'soup'        => '🍲 Sup',
    'other'       => '🍽️ Lainnya',
];
?>
<section class="mc-container">
    <div class="mc-filter">
        <?php foreach ($categories as $key => $label): ?>
            <?php
            $params = array_filter([
                'cuisine' => $currentCuisine,
                'search'  => $searchQuery,
                'category' => $key,
            ]);
            $isActive = ($category === $key) || ($key === '' && empty($category));
            ?>
            <a href="/recipes?<?= http_build_query($params) ?>" class="mc-filter-btn <?= $isActive ? 'active' : '' ?>">
                <?= $label ?>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<!-- Recipe Grid -->
<section class="mc-container" style="margin-bottom:2rem">
    <?php if (!empty($searchQuery)): ?>
        <p style="margin-bottom:1rem;color:var(--mc-gray);font-size:0.875rem">
            Hasil pencarian untuk "<strong><?= esc($searchQuery) ?></strong>" (<?= count($recipes) ?> resep)
        </p>
    <?php endif; ?>

    <?php if (empty($recipes)): ?>
        <div style="text-align:center;padding:3rem 1rem;color:var(--mc-gray)">
            <div style="font-size:3rem;margin-bottom:1rem">🔍</div>
            <h3 style="margin-bottom:0.5rem">Tidak ada resep ditemukan</h3>
            <p>Coba ubah filter atau kata kunci pencarian Anda.</p>
        </div>
    <?php else: ?>
        <div class="mc-grid">
            <?php foreach ($recipes as $recipe): ?>
                <?php
                $gradient  = $cuisineGradient[$recipe['cuisine']] ?? 'linear-gradient(135deg, #6B7280, #9CA3AF)';
                $emoji     = $cuisineEmoji[$recipe['cuisine']] ?? '🍴';
                $textColor = $cuisineTextColor[$recipe['cuisine']] ?? '#6B7280';
                ?>
                <a href="/recipe/<?= esc($recipe['slug']) ?>" class="mc-card" style="text-decoration:none">
                    <div class="mc-card-img" style="background:<?= $gradient ?>">
                        <?= recipe_thumbnail($recipe, $gradient, $emoji) ?>
                        <?php if (!empty($recipe['is_premium'])): ?>
                            <span class="mc-premium-badge">⭐ <?= (int)($recipe['coin_price'] ?? 10) ?> 🪙</span>
                        <?php endif; ?>
                        <span class="mc-cuisine-badge" style="background:rgba(255,255,255,0.9);color:<?= $textColor ?>">
                            <?= $emoji ?> <?= cuisine_label($recipe['cuisine']) ?>
                        </span>
                    </div>
                    <div class="mc-card-body">
                        <div class="mc-card-title"><?= esc($recipe['title']) ?></div>
                        <div class="mc-card-chef">👨‍🍳 <?= esc($recipe['chef_name']) ?></div>
                        <div class="mc-card-desc"><?= esc($recipe['description']) ?></div>
                        <div class="mc-card-meta">
                            <span>⏱️ <?= $recipe['cooking_time'] ?> menit</span>
                            <span>👥 <?= $recipe['servings'] ?> porsi</span>
                            <span class="mc-diff-badge mc-diff-<?= $recipe['difficulty'] ?>"><?= difficulty_label($recipe['difficulty']) ?></span>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?= $this->include('layout/footer') ?>