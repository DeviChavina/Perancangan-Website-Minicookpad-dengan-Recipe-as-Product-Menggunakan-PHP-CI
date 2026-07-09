<?= view('layout/header', ['title' => $title]) ?>
<div class="mc-container" style="padding:2rem 1rem">

<?php if (session()->getFlashdata('success')): ?>
<div class="mc-alert mc-alert-success"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
<div class="mc-alert mc-alert-error"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('info')): ?>
<div class="mc-alert" style="background:#dbeafe;color:#1e40af;border:1px solid #93c5fd;padding:.75rem 1rem;border-radius:8px;margin-bottom:1rem"><?= session()->getFlashdata('info') ?></div>
<?php endif; ?>

<!-- Saldo -->
<div style="background:linear-gradient(135deg,#f59e0b,#d97706);color:white;border-radius:16px;padding:2rem;margin-bottom:2rem;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem">
    <div>
        <div style="font-size:0.875rem;opacity:0.85;margin-bottom:0.25rem">Saldo Koin Anda</div>
        <div style="font-size:3rem;font-weight:900;line-height:1"><?= number_format((int)($user['coin_balance'] ?? 0)) ?> 🪙</div>
        <div style="font-size:0.8125rem;opacity:0.75;margin-top:0.25rem">= Rp <?= number_format((int)($user['coin_balance'] ?? 0) * 500, 0, ',', '.') ?> nilai</div>
    </div>
    <div style="text-align:right">
        <a href="/coin/history" style="background:rgba(255,255,255,0.2);color:white;border:1px solid rgba(255,255,255,0.5);padding:.5rem 1rem;border-radius:8px;text-decoration:none;font-size:0.875rem">📋 Riwayat Transaksi</a>
    </div>
</div>

<!-- Pending topup alert -->
<?php if (!empty($pending)): ?>
<div style="background:#fef3c7;border:2px solid #f59e0b;border-radius:12px;padding:1rem 1.5rem;margin-bottom:1.5rem;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem">
    <div>
        <strong>⏳ Ada pembayaran pending</strong><br>
        <span style="font-size:0.875rem;color:#92400e"><?= number_format((int)$pending['coin_amount']) ?> koin • <?= strtoupper($pending['method']) ?> • Rp <?= number_format((int)$pending['price_idr'],0,',','.') ?></span>
    </div>
    <a href="/coin/pay/<?= $pending['id'] ?>" class="mc-btn mc-btn-secondary" style="background:#f59e0b;color:white;border:none">Selesaikan Bayar →</a>
</div>
<?php endif; ?>

<h2 style="margin-bottom:1.5rem">Pilih Paket Koin</h2>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:1.5rem;margin-bottom:3rem">
<?php foreach ($packages as $pkg):
    $total = (int)$pkg['coin_amount'] + (int)$pkg['bonus_coin'];
    $perCoin = round($pkg['price_idr'] / $total);
?>
<div class="mc-coin-card <?= $pkg['is_popular'] ? 'popular' : '' ?>">
    <div style="font-weight:700;font-size:1.1rem;color:#92400e;margin-bottom:.5rem"><?= esc($pkg['name']) ?></div>
    <div class="mc-coin-amount"><?= number_format((int)$pkg['coin_amount']) ?></div>
    <div style="font-size:0.8125rem;color:#b45309">koin</div>
    <?php if ($pkg['bonus_coin'] > 0): ?>
    <div style="background:#10b981;color:white;font-size:0.75rem;font-weight:700;padding:2px 8px;border-radius:999px;display:inline-block;margin:.5rem 0">+<?= number_format((int)$pkg['bonus_coin']) ?> BONUS</div>
    <?php endif; ?>
    <?php if ($pkg['bonus_coin'] > 0): ?>
    <div style="font-weight:800;font-size:1.25rem;color:#065f46"><?= number_format($total) ?> total 🪙</div>
    <?php endif; ?>
    <div style="color:#92400e;font-weight:700;font-size:1.1rem;margin:.5rem 0"><?= format_rupiah((int)$pkg['price_idr']) ?></div>
    <div style="font-size:0.75rem;color:#b45309;margin-bottom:1rem">≈ Rp <?= number_format($perCoin) ?>/koin</div>

    <?php if (!empty($pending)): ?>
    <button class="mc-btn" style="width:100%;background:#d1d5db;cursor:not-allowed" disabled>Ada transaksi pending</button>
    <?php else: ?>
    <button class="mc-btn mc-btn-primary" style="width:100%;background:#f59e0b;border-color:#f59e0b"
        onclick="document.getElementById('modal-pkg-<?= $pkg['id'] ?>').style.display='flex'">
        Beli Sekarang
    </button>
    <?php endif; ?>
</div>

<!-- Modal pembayaran per paket -->
<div id="modal-pkg-<?= $pkg['id'] ?>" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;align-items:center;justify-content:center">
    <div style="background:white;border-radius:16px;padding:2rem;max-width:420px;width:90%;position:relative">
        <button onclick="document.getElementById('modal-pkg-<?= $pkg['id'] ?>').style.display='none'" style="position:absolute;top:1rem;right:1rem;background:none;border:none;font-size:1.5rem;cursor:pointer">✕</button>
        <h3 style="margin:0 0 1rem">Beli Paket <?= esc($pkg['name']) ?></h3>
        <div style="background:#fef3c7;border-radius:8px;padding:1rem;margin-bottom:1.5rem;text-align:center">
            <div style="font-size:2rem;font-weight:800;color:#92400e"><?= number_format($total) ?> 🪙</div>
            <div style="color:#b45309"><?= format_rupiah((int)$pkg['price_idr']) ?></div>
        </div>
        <form action="/coin/buy" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="package_id" value="<?= $pkg['id'] ?>">
            <label style="display:block;margin-bottom:.5rem;font-weight:600">Metode Pembayaran</label>
            <select name="method" class="mc-input" required>
                <option value="">-- Pilih metode --</option>
                <option value="qris">QRIS</option>
                <option value="bca_va">BCA Virtual Account</option>
                <option value="mandiri_va">Mandiri Virtual Account</option>
                <option value="bri_va">BRI Virtual Account</option>
            </select>
            <button type="submit" class="mc-btn mc-btn-primary" style="width:100%;margin-top:1rem;background:#f59e0b;border-color:#f59e0b">
                Bayar <?= format_rupiah((int)$pkg['price_idr']) ?>
            </button>
        </form>
    </div>
</div>
<?php endforeach; ?>
</div>

</div>
<?= view('layout/footer') ?>
