<?= view('layout/header', ['title' => $title]) ?>
<div class="mc-container" style="padding:2rem 1rem">

<?php foreach (['success','error'] as $t): $msg = session()->getFlashdata($t); if (!$msg) continue;
    $bg=$t==='success'?'#f0fdf4':'#fef2f2';$cl=$t==='success'?'#166534':'#991b1b';$bd=$t==='success'?'#86efac':'#fca5a5';?>
<div style="background:<?=$bg?>;color:<?=$cl?>;border:1px solid <?=$bd?>;border-radius:8px;padding:.75rem 1rem;margin-bottom:1rem"><?= esc($msg) ?></div>
<?php endforeach; ?>

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem">
    <h2 style="margin:0">⏳ Verifikasi Chef</h2>
    <a href="/admin" class="mc-btn mc-btn-outline mc-btn-sm">← Dashboard</a>
</div>

<!-- Filters -->
<form method="get" style="display:flex;gap:.75rem;flex-wrap:wrap;margin-bottom:1.5rem">
    <div>
        <label style="font-size:0.8125rem;color:#6b7280;display:block;margin-bottom:.25rem">Tipe</label>
        <select name="type" class="mc-input" style="min-width:160px" onchange="this.form.submit()">
            <option value="all" <?= $filterType==='all'?'selected':'' ?>>Semua Tipe</option>
            <option value="basic" <?= $filterType==='basic'?'selected':'' ?>>🪪 Basic (KTP → Chef)</option>
            <option value="advanced" <?= $filterType==='advanced'?'selected':'' ?>>📜 Advanced (→ Chef Verified)</option>
        </select>
    </div>
    <div>
        <label style="font-size:0.8125rem;color:#6b7280;display:block;margin-bottom:.25rem">Status</label>
        <select name="status" class="mc-input" style="min-width:160px" onchange="this.form.submit()">
            <option value="pending" <?= $filterStatus==='pending'?'selected':'' ?>>⏳ Pending</option>
            <option value="approved" <?= $filterStatus==='approved'?'selected':'' ?>>✅ Disetujui</option>
            <option value="rejected" <?= $filterStatus==='rejected'?'selected':'' ?>>❌ Ditolak</option>
            <option value="all" <?= $filterStatus==='all'?'selected':'' ?>>Semua</option>
        </select>
    </div>
</form>

<?php if (empty($verifications)): ?>
<div style="text-align:center;padding:3rem;color:#9ca3af;background:white;border:1px solid var(--mc-border);border-radius:12px">
    <div style="font-size:2rem;margin-bottom:.5rem">✅</div>
    Tidak ada verifikasi <?= $filterStatus !== 'all' ? $filterStatus : '' ?>.
</div>
<?php else: ?>
<div style="display:flex;flex-direction:column;gap:1rem">
<?php foreach ($verifications as $v):
    $isBas  = $v['verification_type'] === 'basic';
    $isAdv  = $v['verification_type'] === 'advanced';
    $stIcon = match($v['status']) {'approved'=>'✅','rejected'=>'❌',default=>'⏳'};
    $stColor= match($v['status']) {'approved'=>'#10b981','rejected'=>'#ef4444',default=>'#f59e0b'};
?>
<div style="background:white;border:2px solid <?= $isAdv ? '#10b981' : '#e5e7eb' ?>;border-radius:12px;padding:1.5rem">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:1rem;margin-bottom:1rem">
        <div>
            <div style="display:flex;align-items:center;gap:.5rem;flex-wrap:wrap;margin-bottom:.25rem">
                <strong><?= esc($v['user_name']) ?></strong>
                <span style="font-size:0.75rem;color:#9ca3af"><?= esc($v['user_email']) ?></span>
                <span style="background:<?= $stColor ?>20;color:<?= $stColor ?>;font-size:0.75rem;font-weight:700;padding:2px 8px;border-radius:999px">
                    <?= $stIcon ?> <?= ucfirst($v['status']) ?>
                </span>
            </div>
            <div style="display:flex;gap:.5rem;flex-wrap:wrap;font-size:0.8125rem;color:#6b7280">
                <span style="background:<?= $isAdv?'#d1fae5':'#dbeafe' ?>;color:<?= $isAdv?'#065f46':'#1e40af' ?>;padding:1px 8px;border-radius:999px;font-weight:600">
                    <?= $isAdv ? '📜 Sertifikasi (→ Chef Verified)' : '🪪 Basic (→ Chef)' ?>
                </span>
                <span>Target: <strong><?= role_label($v['target_role']) ?></strong></span>
                <span><?= date('d M Y', strtotime($v['created_at'])) ?></span>
            </div>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1rem;margin-bottom:1rem;font-size:0.875rem">
        <div><strong>Spesialisasi:</strong><br><?= esc($v['specialization']) ?></div>
        <?php if (!empty($v['id_card_number'])): ?>
        <div><strong>No. KTP:</strong><br><?= esc($v['id_card_number']) ?></div>
        <?php endif; ?>
        <?php if (!empty($v['portfolio_url'])): ?>
        <div><strong>Portfolio:</strong><br><a href="<?= esc($v['portfolio_url']) ?>" target="_blank" style="color:var(--mc-orange)">Lihat →</a></div>
        <?php endif; ?>
    </div>

    <div style="font-size:0.875rem;margin-bottom:1rem">
        <strong>Pengalaman:</strong><br>
        <span style="color:#4b5563"><?= nl2br(esc($v['experience'])) ?></span>
    </div>

    <!-- Dokumen -->
    <div style="display:flex;gap:.75rem;flex-wrap:wrap;margin-bottom:1rem">
        <?php if (!empty($v['id_card_photo'])): ?>
        <a href="/uploads/verifications/<?= esc($v['id_card_photo']) ?>" target="_blank"
           style="background:#dbeafe;color:#1e40af;padding:.4rem .875rem;border-radius:8px;font-size:0.8125rem;font-weight:600;text-decoration:none">
            🪪 Lihat KTP
        </a>
        <?php endif; ?>
        <?php if (!empty($v['certificate_photo'])): ?>
        <a href="/uploads/verifications/<?= esc($v['certificate_photo']) ?>" target="_blank"
           style="background:#d1fae5;color:#065f46;padding:.4rem .875rem;border-radius:8px;font-size:0.8125rem;font-weight:600;text-decoration:none">
            📜 Lihat Sertifikat
        </a>
        <?php endif; ?>
    </div>

    <?php if (!empty($v['admin_note'])): ?>
    <div style="background:#fef3c7;border-radius:6px;padding:.5rem .75rem;font-size:0.8125rem;margin-bottom:1rem">
        Catatan sebelumnya: <?= esc($v['admin_note']) ?>
    </div>
    <?php endif; ?>

    <!-- Actions (only for pending) -->
    <?php if ($v['status'] === 'pending'): ?>
    <div style="display:flex;gap:.75rem;flex-wrap:wrap;align-items:flex-end">
        <div style="flex:1;min-width:200px">
            <label style="font-size:0.8125rem;color:#6b7280;display:block;margin-bottom:.25rem">Catatan (opsional)</label>
            <input type="text" id="note-<?= $v['id'] ?>" placeholder="Catatan untuk user..." class="mc-input" style="width:100%">
        </div>
        <form action="/admin/verifications/<?= $v['id'] ?>/approve" method="post" style="display:inline"
              onsubmit="document.getElementById('note-<?= $v['id'] ?>-approve').value=document.getElementById('note-<?= $v['id'] ?>').value">
            <?= csrf_field() ?>
            <input type="hidden" name="admin_note" id="note-<?= $v['id'] ?>-approve">
            <button class="mc-btn" style="background:#10b981;color:white;border:none">
                ✅ Setujui → <?= role_label($v['target_role']) ?>
            </button>
        </form>
        <form action="/admin/verifications/<?= $v['id'] ?>/reject" method="post" style="display:inline"
              onsubmit="document.getElementById('note-<?= $v['id'] ?>-reject').value=document.getElementById('note-<?= $v['id'] ?>').value">
            <?= csrf_field() ?>
            <input type="hidden" name="admin_note" id="note-<?= $v['id'] ?>-reject">
            <button class="mc-btn" style="background:#ef4444;color:white;border:none">❌ Tolak</button>
        </form>
    </div>
    <?php endif; ?>
</div>
<?php endforeach; ?>
</div>
<?php endif; ?>
</div>
<?= view('layout/footer') ?>
