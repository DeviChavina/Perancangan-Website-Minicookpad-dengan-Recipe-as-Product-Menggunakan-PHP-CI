<?= view('layout/header', ['title' => $title]) ?>
<div class="mc-container" style="padding:2rem 1rem;max-width:640px">

<?php if (session()->getFlashdata('errors')): ?>
<div class="mc-alert mc-alert-error" style="background:#fee2e2;border:1px solid #fca5a5;border-radius:8px;padding:.75rem 1rem;margin-bottom:1.5rem">
    <?php foreach (session()->getFlashdata('errors') as $e): ?>
    <div>⚠️ <?= esc($e) ?></div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Banner perbedaan tier -->
<div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:2rem">
    <div class="mc-tier-card unverified" style="opacity:.6">
        <div style="font-size:1.5rem;margin-bottom:.5rem">🍳</div>
        <strong>Chef</strong>
        <div style="font-size:0.8125rem;color:#92400e;margin-top:.25rem">Anda sekarang</div>
        <ul style="font-size:0.8125rem;margin:.75rem 0 0;padding-left:1.25rem;color:#78716c;line-height:1.7">
            <li>Buat & publish resep</li>
            <li>50% dari setiap unlock</li>
        </ul>
    </div>
    <div class="mc-tier-card verified">
        <div style="font-size:1.5rem;margin-bottom:.5rem">⭐</div>
        <strong style="color:#065f46">Chef Verified</strong>
        <div style="font-size:0.8125rem;color:#10b981;margin-top:.25rem">Target setelah disetujui</div>
        <ul style="font-size:0.8125rem;margin:.75rem 0 0;padding-left:1.25rem;color:#166534;line-height:1.7">
            <li>Semua fitur Chef</li>
            <li><strong>70% dari setiap unlock</strong></li>
            <li>Badge Verified di profil</li>
        </ul>
    </div>
</div>

<!-- Contoh revenue -->
<div style="background:#f0fdf4;border:1px solid #86efac;border-radius:8px;padding:1rem;margin-bottom:2rem;font-size:0.875rem">
    <strong>💰 Contoh pendapatan (resep harga 10 🪙):</strong>
    <div style="margin-top:.5rem;display:flex;gap:1rem;flex-wrap:wrap">
        <span>🍳 Chef biasa: <strong>5 🪙</strong></span>
        <span>⭐ Chef Verified: <strong>7 🪙</strong></span>
        <span style="color:#6b7280">Platform: 3 🪙</span>
    </div>
</div>

<h2 style="margin-bottom:1.5rem">📜 Ajukan Sertifikasi Chef Verified</h2>

<?php if ($existing && $existing['status'] === 'rejected'): ?>
<div style="background:#fef3c7;border:1px solid #fcd34d;border-radius:8px;padding:1rem;margin-bottom:1.5rem">
    <strong>⚠️ Pengajuan sebelumnya ditolak</strong><br>
    <span style="font-size:0.875rem">Catatan admin: <?= esc($existing['admin_note'] ?? '-') ?></span><br>
    <span style="font-size:0.875rem;color:#92400e">Silakan ajukan ulang dengan dokumen yang lebih lengkap.</span>
</div>
<?php endif; ?>

<form action="/chef/verify-advanced" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="mc-form-group">
        <label class="mc-label">Spesialisasi Masakan *</label>
        <input type="text" name="specialization" class="mc-input" required placeholder="cth: Masakan Italia & French cuisine"
               value="<?= esc(old('specialization')) ?>">
    </div>

    <div class="mc-form-group">
        <label class="mc-label">Pengalaman Memasak *</label>
        <textarea name="experience" class="mc-input" rows="4" required
                  placeholder="Ceritakan pengalaman memasak Anda, pelatihan yang pernah diikuti, dll."><?= esc(old('experience')) ?></textarea>
    </div>

    <div class="mc-form-group">
        <label class="mc-label">Upload Sertifikat Memasak * <span style="font-size:0.75rem;color:#6b7280">(JPG/PNG, maks 2MB)</span></label>
        <div style="border:2px dashed #d1d5db;border-radius:8px;padding:1.5rem;text-align:center;background:#f9fafb">
            <div style="font-size:2rem;margin-bottom:.5rem">📜</div>
            <div style="color:#6b7280;font-size:0.875rem;margin-bottom:.75rem">Sertifikat memasak dari lembaga/sekolah kuliner</div>
            <input type="file" name="certificate_photo" accept="image/*" required class="mc-input" style="max-width:300px">
        </div>
    </div>

    <div class="mc-form-group">
        <label class="mc-label">Link Portfolio / Instagram <span style="font-size:0.75rem;color:#6b7280">(opsional)</span></label>
        <input type="url" name="portfolio_url" class="mc-input" placeholder="https://instagram.com/namaanda"
               value="<?= esc(old('portfolio_url')) ?>">
    </div>

    <button type="submit" class="mc-btn mc-btn-primary" style="width:100%;background:#10b981;border-color:#10b981">
        📤 Kirim Pengajuan Sertifikasi
    </button>
</form>
<div style="margin-top:1rem;text-align:center">
    <a href="/chef/status" style="color:#6b7280">← Lihat Status Verifikasi</a>
</div>
</div>
<?= view('layout/footer') ?>
