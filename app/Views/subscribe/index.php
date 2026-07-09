<?= $this->include('layout/header') ?>

<div class="mc-container" style="margin-top:1.5rem;margin-bottom:2rem">

    <!-- Flash messages -->
    <?php if (session()->getFlashdata('error')): ?>
        <div class="mc-alert mc-alert-error" style="margin-bottom:1rem">
            ❌ <?= esc(session()->getFlashdata('error')) ?>
        </div>
    <?php endif; ?>

    <!-- FIX #21: Banner untuk user yang sudah premium -->
    <?php if (!empty($alreadyPremium)): ?>
        <div class="mc-alert mc-alert-info" style="margin-bottom:1rem;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:0.75rem">
            <span>✨ Anda sudah memiliki akses Premium. Tidak perlu berlangganan lagi.</span>
            <a href="/dashboard" class="mc-btn mc-btn-outline mc-btn-sm">← Kembali ke Dashboard</a>
        </div>
    <?php endif; ?>

    <!-- Page Header -->
    <div style="text-align:center;margin-bottom:2rem">
        <h1 style="font-size:1.75rem;font-weight:700;margin-bottom:0.5rem">💳 Pilih Paket Langganan</h1>
        <p style="color:var(--mc-gray);font-size:0.9375rem;max-width:28rem;margin:0 auto">
            Dapatkan akses ke semua resep premium dan fitur eksklusif lainnya
        </p>
    </div>

    <!-- Plan Cards -->
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:1.5rem;margin-bottom:3rem">

        <?php foreach ($plans as $plan): ?>
        <?php
            $isPopular = (stripos($plan['name'], 'monthly') !== false || stripos($plan['name'], 'bulan') !== false) && $plan['price'] > 0;
            $planIcon = $plan['price'] == 0 ? '🆓' : ($isPopular ? '⭐' : '🏆');
            $priceLabel = $plan['price'] == 0 ? 'Gratis' : format_rupiah($plan['price']);
            $periodLabel = $plan['price'] == 0 ? '' : '/' . ($plan['duration'] >= 365 ? 'tahun' : 'bulan');
            $features = !empty($plan['features']) ? json_decode($plan['features'], true) : [];
            if (!is_array($features)) $features = [];
        ?>

        <div class="mc-plan-card <?= $isPopular ? 'popular' : '' ?>">
            <?php if ($isPopular): ?>
                <div class="mc-plan-popular">🔥 Populer</div>
            <?php endif; ?>

            <div style="font-size:2.5rem;margin-bottom:0.75rem"><?= $planIcon ?></div>
            <h3 style="font-size:1.25rem;font-weight:700;margin-bottom:0.5rem"><?= esc($plan['name']) ?></h3>
            <div class="mc-plan-price" style="color:<?= $plan['price'] == 0 ? 'var(--mc-dark)' : 'var(--mc-orange)' ?>">
                <?= $priceLabel ?>
            </div>
            <?php if ($periodLabel): ?>
                <p style="font-size:0.8125rem;color:var(--mc-gray)"><?= $periodLabel ?></p>
            <?php endif; ?>

            <p style="font-size:0.8125rem;color:var(--mc-gray);margin:0.75rem 0"><?= esc($plan['description']) ?></p>

            <?php if (!empty($features)): ?>
            <ul class="mc-plan-features">
                <?php foreach ($features as $feature): ?>
                <li><?= esc($feature) ?></li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>

            <?php
                // FIX #19: Tentukan apakah ini "paket saat ini" berdasarkan role user.
                // Sebelumnya hardcode hanya untuk paket Basic (price=0), yang menyesatkan
                // bagi USER_PREMIUM yang sedang melihat paket berbayar.
                $isCurrentPlan = false;
                if ($plan['price'] == 0 && in_array(session()->get('user_role'), ['USER_FREE', null], true)) {
                    $isCurrentPlan = true;
                }
                if (!empty($alreadyPremium) && $plan['price'] > 0) {
                    $isCurrentPlan = true; // premium user sedang lihat paket berbayar = sudah aktif
                }
            ?>
            <?php if ($isCurrentPlan): ?>
                <button class="mc-btn mc-btn-outline mc-btn-block" disabled style="opacity:0.6">Paket Saat Ini</button>
            <?php else: ?>
                <form action="/subscribe/process" method="post" style="margin-top:0.5rem">
                    <?= csrf_field() ?>
                    <input type="hidden" name="plan_id" value="<?= $plan['id'] ?>">
                    <div class="mc-form-group" style="margin-bottom:0.5rem">
                        <select name="method" class="mc-select" required>
                            <option value="">Pilih metode bayar</option>
                            <option value="qris">QRIS</option>
                            <option value="bca_va">Virtual Account BCA</option>
                            <option value="mandiri_va">Virtual Account Mandiri</option>
                            <option value="bri_va">Virtual Account BRI</option>
                        </select>
                    </div>
                    <button type="submit" class="mc-btn <?= $isPopular ? 'mc-btn-primary' : 'mc-btn-gold' ?> mc-btn-block">
                        Berlangganan
                    </button>
                </form>
            <?php endif; ?>
        </div>

        <?php endforeach; ?>

    </div>

    <!-- Why Upgrade Section -->
    <div style="margin-bottom:2rem">
        <div style="text-align:center;margin-bottom:1.5rem">
            <h2 style="font-size:1.375rem;font-weight:700;margin-bottom:0.375rem">🌟 Mengapa Upgrade ke Premium?</h2>
            <p style="color:var(--mc-gray);font-size:0.875rem">Nikmati pengalaman memasak yang lebih lengkap</p>
        </div>

        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:1.25rem">
            <!-- Benefit 1 -->
            <div style="background:white;border:1px solid var(--mc-border);border-radius:var(--radius);padding:1.5rem;text-align:center">
                <div style="font-size:2.5rem;margin-bottom:0.75rem">📖</div>
                <h3 style="font-size:1rem;font-weight:700;margin-bottom:0.375rem">Akses Semua Resep</h3>
                <p style="font-size:0.8125rem;color:var(--mc-gray)">
                    Buka kunci semua resep premium dari chef terverifikasi tanpa batasan.
                </p>
            </div>

            <!-- Benefit 2 -->
            <div style="background:white;border:1px solid var(--mc-border);border-radius:var(--radius);padding:1.5rem;text-align:center">
                <div style="font-size:2.5rem;margin-bottom:0.75rem">👨‍🍳</div>
                <h3 style="font-size:1rem;font-weight:700;margin-bottom:0.375rem">Tips Eksklusif Chef</h3>
                <p style="font-size:0.8125rem;color:var(--mc-gray)">
                    Dapatkan tips dan trik rahasia dari chef profesional di setiap resep premium.
                </p>
            </div>

            <!-- Benefit 3 -->
            <div style="background:white;border:1px solid var(--mc-border);border-radius:var(--radius);padding:1.5rem;text-align:center">
                <div style="font-size:2.5rem;margin-bottom:0.75rem">🔖</div>
                <h3 style="font-size:1rem;font-weight:700;margin-bottom:0.375rem">Bookmark Tanpa Batas</h3>
                <p style="font-size:0.8125rem;color:var(--mc-gray)">
                    Simpan resep favorit Anda sebanyak mungkin tanpa batasan jumlah bookmark.
                </p>
            </div>
        </div>
    </div>

</div>

<?= $this->include('layout/footer') ?>
