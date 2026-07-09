<?= view('layout/header', ['title' => $title]) ?>
<div class="mc-container" style="padding:2rem 1rem;max-width:680px">
    <h2>📋 Status Verifikasi Chef</h2>

    <!-- BASIC VERIFICATION STATUS -->
    <div style="border:2px solid var(--mc-border);border-radius:12px;padding:1.5rem;margin-bottom:1.5rem">
        <h3 style="margin:0 0 1rem;display:flex;align-items:center;gap:.5rem">
            🪪 Verifikasi Dasar (KTP) 
            <span style="font-size:0.75rem;font-weight:400;background:#f3f4f6;padding:2px 8px;border-radius:999px">→ Chef</span>
        </h3>
        <?php if (empty($basicVerif)): ?>
        <p style="color:#6b7280;margin:0">Belum pernah mengajukan verifikasi dasar.</p>
        <a href="/chef/verify" class="mc-btn mc-btn-primary" style="margin-top:1rem;display:inline-block">Ajukan Sekarang</a>
        <?php else: ?>
            <?php
            $statusColor = match($basicVerif['status']) { 'approved'=>'#10b981','rejected'=>'#ef4444', default=>'#f59e0b' };
            $statusIcon  = match($basicVerif['status']) { 'approved'=>'✅','rejected'=>'❌', default=>'⏳' };
            $statusLabel = match($basicVerif['status']) { 'approved'=>'Disetujui','rejected'=>'Ditolak', default=>'Menunggu Review' };
            ?>
            <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.75rem">
                <span style="font-size:1.25rem"><?= $statusIcon ?></span>
                <strong style="color:<?= $statusColor ?>"><?= $statusLabel ?></strong>
                <span style="font-size:0.8125rem;color:#9ca3af">· <?= date('d M Y', strtotime($basicVerif['created_at'])) ?></span>
            </div>
            <div style="font-size:0.875rem;color:#4b5563">
                Spesialisasi: <strong><?= esc($basicVerif['specialization']) ?></strong>
            </div>
            <?php if (!empty($basicVerif['admin_note'])): ?>
            <div style="background:#fef3c7;border-radius:6px;padding:.5rem .75rem;font-size:0.875rem;margin-top:.75rem">
                Catatan admin: <?= esc($basicVerif['admin_note']) ?>
            </div>
            <?php endif; ?>
            <?php if ($basicVerif['status'] === 'rejected'): ?>
            <a href="/chef/verify" class="mc-btn" style="margin-top:.75rem;display:inline-block;background:#ef4444;color:white;border:none">Ajukan Ulang</a>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <!-- ADVANCED VERIFICATION STATUS -->
    <?php $canAdvanced = !empty($basicVerif) && $basicVerif['status'] === 'approved'; ?>
    <div style="border:2px solid <?= $canAdvanced ? '#10b981' : '#e5e7eb' ?>;border-radius:12px;padding:1.5rem;margin-bottom:1.5rem;<?= !$canAdvanced ? 'opacity:.6' : '' ?>">
        <h3 style="margin:0 0 1rem;display:flex;align-items:center;gap:.5rem">
            📜 Sertifikasi Chef Verified
            <span style="font-size:0.75rem;font-weight:400;background:#f3f4f6;padding:2px 8px;border-radius:999px">→ Chef Verified</span>
        </h3>
        <?php if (!$canAdvanced): ?>
        <p style="color:#9ca3af;margin:0;font-size:0.875rem">Selesaikan verifikasi dasar (KTP) dulu untuk bisa mengajukan sertifikasi.</p>
        <?php elseif (empty($advancedVerif)): ?>
        <p style="color:#6b7280;margin:0">Belum mengajukan sertifikasi. Upgrade ke Chef Verified untuk mendapatkan 70% pendapatan per resep!</p>
        <a href="/chef/verify-advanced" class="mc-btn mc-btn-primary" style="margin-top:1rem;display:inline-block;background:#10b981;border-color:#10b981">⭐ Ajukan Sertifikasi</a>
        <?php else:
            $statusColor2 = match($advancedVerif['status']) { 'approved'=>'#10b981','rejected'=>'#ef4444', default=>'#f59e0b' };
            $statusIcon2  = match($advancedVerif['status']) { 'approved'=>'✅','rejected'=>'❌', default=>'⏳' };
            $statusLabel2 = match($advancedVerif['status']) { 'approved'=>'Disetujui','rejected'=>'Ditolak', default=>'Menunggu Review' };
        ?>
            <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.75rem">
                <span style="font-size:1.25rem"><?= $statusIcon2 ?></span>
                <strong style="color:<?= $statusColor2 ?>"><?= $statusLabel2 ?></strong>
                <span style="font-size:0.8125rem;color:#9ca3af">· <?= date('d M Y', strtotime($advancedVerif['created_at'])) ?></span>
            </div>
            <?php if (!empty($advancedVerif['admin_note'])): ?>
            <div style="background:#fef3c7;border-radius:6px;padding:.5rem .75rem;font-size:0.875rem;margin-top:.75rem">
                Catatan admin: <?= esc($advancedVerif['admin_note']) ?>
            </div>
            <?php endif; ?>
            <?php if ($advancedVerif['status'] === 'rejected'): ?>
            <a href="/chef/verify-advanced" class="mc-btn" style="margin-top:.75rem;display:inline-block;background:#ef4444;color:white;border:none">Ajukan Ulang</a>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <div style="text-align:center;margin-top:1.5rem">
        <?php if (!empty($basicVerif) && $basicVerif['status'] === 'approved'): ?>
        <a href="/chef/dashboard" class="mc-btn mc-btn-primary" style="margin-right:.5rem">Dashboard Chef →</a>
        <?php endif; ?>
        <a href="/dashboard" style="color:#6b7280">← Kembali ke Profil</a>
    </div>
</div>
<?= view('layout/footer') ?>
