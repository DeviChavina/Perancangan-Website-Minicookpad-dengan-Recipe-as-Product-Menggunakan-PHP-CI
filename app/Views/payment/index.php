<?= $this->include('layout/header') ?>

<?php
$payment = $payment ?? null;
if (!$payment) {
    echo '<div class="mc-container" style="margin-top:1.5rem"><div class="mc-alert mc-alert-error">❌ Data pembayaran tidak ditemukan</div></div>';
    echo $this->include('layout/footer');
    return;
}

$isExpired = $payment['status'] === 'expired';
$isPaid = $payment['status'] === 'paid';
$isPending = $payment['status'] === 'pending' && !$isExpired;
$isQris = ($payment['method'] ?? '') === 'qris';
$isVa = in_array($payment['method'] ?? '', ['bca_va', 'mandiri_va', 'bri_va']);

$methodLabel = match($payment['method'] ?? '') {
    'qris' => 'QRIS',
    'bca_va' => 'Virtual Account BCA',
    'mandiri_va' => 'Virtual Account Mandiri',
    'bri_va' => 'Virtual Account BRI',
    default => ucfirst($payment['method'] ?? '-'),
};

$planName = $payment['plan_name'] ?? 'Paket Langganan';
$amount = $payment['amount'] ?? 0;
$expiresAt = $payment['expires_at'] ?? '';
$paymentCode = $payment['payment_code'] ?? '';
?>

<!-- Flash messages -->
<?php if (session()->getFlashdata('success')): ?>
<div class="mc-container" style="margin-top:1rem">
    <div class="mc-alert mc-alert-success">✅ <?= esc(session()->getFlashdata('success')) ?></div>
</div>
<?php endif; ?>

<div class="mc-container" style="margin-top:1.5rem;margin-bottom:2rem">
    <div style="max-width:32rem;margin:0 auto">

        <?php if ($isPaid): ?>
        <!-- Already Paid -->
        <div style="background:white;border:1px solid var(--mc-border);border-radius:var(--radius);padding:2.5rem;text-align:center">
            <div style="font-size:4rem;margin-bottom:1rem">✅</div>
            <h1 style="font-size:1.375rem;font-weight:700;margin-bottom:0.5rem;color:var(--mc-green)">Pembayaran Berhasil</h1>
            <p style="color:var(--mc-gray);font-size:0.875rem;margin-bottom:1.5rem">
                Terima kasih! Pembayaran Anda telah berhasil diproses.
            </p>
            <a href="/dashboard" class="mc-btn mc-btn-primary">← Kembali ke Dashboard</a>
        </div>

        <?php elseif ($isExpired): ?>
        <!-- Expired -->
        <div style="background:white;border:1px solid var(--mc-border);border-radius:var(--radius);padding:2.5rem;text-align:center">
            <div style="font-size:4rem;margin-bottom:1rem">⏰</div>
            <h1 style="font-size:1.375rem;font-weight:700;margin-bottom:0.5rem;color:var(--mc-red)">Pembayaran Kadaluarsa</h1>
            <p style="color:var(--mc-gray);font-size:0.875rem;margin-bottom:1.5rem">
                Waktu pembayaran telah habis. Silakan buat transaksi baru.
            </p>
            <a href="/subscribe" class="mc-btn mc-btn-primary">💳 Pilih Paket Lagi</a>
        </div>

        <?php else: ?>
        <!-- Pending Payment -->
        <div style="text-align:center;margin-bottom:1.5rem">
            <h1 style="font-size:1.375rem;font-weight:700;margin-bottom:0.375rem">💳 Pembayaran</h1>
            <p style="color:var(--mc-gray);font-size:0.875rem">Selesaikan pembayaran untuk mengaktifkan langganan</p>
        </div>

        <!-- Countdown Timer -->
        <div class="mc-countdown" id="mcCountdown">
            <p style="font-size:0.8125rem;color:var(--mc-gray);margin-bottom:0.375rem">⏰ Waktu tersisa</p>
            <div class="mc-countdown-time" id="mcCountdownTime">--:--:--</div>
            <p style="font-size:0.75rem;color:var(--mc-gray);margin-top:0.25rem">Selesaikan sebelum batas waktu</p>
        </div>

        <!-- Payment Details Card -->
        <div style="background:white;border:1px solid var(--mc-border);border-radius:var(--radius);padding:1.5rem;margin-bottom:1.5rem">
            <h2 style="font-size:1rem;font-weight:700;margin-bottom:1rem;display:flex;align-items:center;gap:0.5rem">📋 Detail Pembayaran</h2>

            <div style="display:grid;gap:0.75rem;font-size:0.875rem">
                <div style="display:flex;justify-content:space-between;padding-bottom:0.5rem;border-bottom:1px solid var(--mc-border)">
                    <span style="color:var(--mc-gray)">Paket</span>
                    <span style="font-weight:600"><?= esc($planName) ?></span>
                </div>
                <div style="display:flex;justify-content:space-between;padding-bottom:0.5rem;border-bottom:1px solid var(--mc-border)">
                    <span style="color:var(--mc-gray)">Metode</span>
                    <span style="font-weight:600"><?= esc($methodLabel) ?></span>
                </div>
                <div style="display:flex;justify-content:space-between;padding-bottom:0.5rem;border-bottom:1px solid var(--mc-border)">
                    <span style="color:var(--mc-gray)">Jumlah</span>
                    <span style="font-weight:700;font-size:1.125rem;color:var(--mc-orange)"><?= format_rupiah($amount) ?></span>
                </div>
                <div style="display:flex;justify-content:space-between">
                    <span style="color:var(--mc-gray)">Status</span>
                    <span class="mc-badge mc-badge-pending">Menunggu Pembayaran</span>
                </div>
            </div>
        </div>

        <!-- Payment Code Display -->
        <?php if ($isQris): ?>
        <!-- QR Code Display -->
        <div style="background:white;border:1px solid var(--mc-border);border-radius:var(--radius);padding:1.5rem;margin-bottom:1.5rem;text-align:center">
            <h2 style="font-size:1rem;font-weight:700;margin-bottom:1rem;display:flex;align-items:center;justify-content:center;gap:0.5rem">📱 Scan QR Code</h2>
            <div class="mc-qr-code">
                <div style="position:absolute;inset:8px;background:white;border-radius:4px;display:flex;align-items:center;justify-content:center">
                    <div style="width:160px;height:160px;background:repeating-conic-gradient(var(--mc-dark) 0% 25%, white 0% 50%) 50%/12px 12px;display:flex;align-items:center;justify-content:center">
                        <div style="width:40px;height:40px;background:var(--mc-dark);border-radius:4px;display:flex;align-items:center;justify-content:center;color:white;font-size:0.625rem;font-weight:700">MC</div>
                    </div>
                </div>
            </div>
            <p style="font-size:0.8125rem;color:var(--mc-gray)">Scan QR code di atas menggunakan aplikasi e-wallet atau mobile banking Anda</p>
            <?php if ($paymentCode): ?>
            <div style="margin-top:0.75rem;padding:0.5rem;background:var(--mc-muted);border-radius:8px;font-size:0.75rem;color:var(--mc-gray)">
                Kode: <strong style="color:var(--mc-dark)"><?= esc($paymentCode) ?></strong>
            </div>
            <?php endif; ?>
        </div>

        <?php elseif ($isVa): ?>
        <!-- VA Code Display -->
        <div style="background:white;border:1px solid var(--mc-border);border-radius:var(--radius);padding:1.5rem;margin-bottom:1.5rem;text-align:center">
            <h2 style="font-size:1rem;font-weight:700;margin-bottom:1rem;display:flex;align-items:center;justify-content:center;gap:0.5rem">🏦 Kode Virtual Account</h2>
            <div class="mc-va-code" id="mcVaCode">
                <?= esc($paymentCode) ?>
            </div>
            <button type="button" class="mc-btn mc-btn-outline mc-btn-sm" style="margin-top:0.75rem"
                    onclick="mcCopyVaCode()">
                📋 Salin Kode
            </button>
            <p style="font-size:0.75rem;color:var(--mc-gray);margin-top:0.75rem">
                Transfer ke nomor Virtual Account di atas melalui ATM, mobile banking, atau internet banking
            </p>
        </div>
        <?php endif; ?>

        <!-- Simulate Payment -->
<div style="background:white;border:1px solid var(--mc-border);border-radius:var(--radius);padding:1.5rem;margin-bottom:1.5rem">
    <div class="mc-alert mc-alert-warning" style="margin-bottom:1rem">
        ⚠️ Ini adalah simulasi pembayaran untuk keperluan demo. Pada lingkungan produksi, pembayaran akan diverifikasi secara otomatis.
    </div>
    <form action="/payment/simulate/<?= $payment['id'] ?>" method="post"
          onsubmit="return confirm('Simulasikan pembayaran berhasil?')">
        <?= csrf_field() ?>
        <button type="submit" class="mc-btn mc-btn-gold mc-btn-block">
            ✅ Simulasikan Pembayaran Berhasil
        </button>
    </form>
</div>

        <!-- Help -->
        <div style="text-align:center;font-size:0.8125rem;color:var(--mc-gray)">
            <p>Butuh bantuan? Hubungi <a href="#">support@minicookpad.com</a></p>
            <a href="/subscribe" style="font-size:0.8125rem">← Kembali ke halaman langganan</a>
        </div>

        <?php endif; ?>

    </div>
</div>

<?php if ($isPending && !empty($expiresAt)): ?>
<script>
// Countdown Timer
(function() {
    const expiresAt = new Date('<?= $expiresAt ?>').getTime();

    function updateCountdown() {
        const now = new Date().getTime();
        const distance = expiresAt - now;

        if (distance <= 0) {
            document.getElementById('mcCountdownTime').textContent = '00:00:00';
            document.getElementById('mcCountdown').style.borderColor = 'var(--mc-red)';
            location.reload();
            return;
        }

        const hours = Math.floor(distance / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        document.getElementById('mcCountdownTime').textContent =
            String(hours).padStart(2, '0') + ':' +
            String(minutes).padStart(2, '0') + ':' +
            String(seconds).padStart(2, '0');

        // Warning when under 1 hour
        if (distance < 3600000) {
            document.getElementById('mcCountdown').style.borderColor = 'var(--mc-red)';
            document.getElementById('mcCountdownTime').style.color = 'var(--mc-red)';
        }
    }

    updateCountdown();
    setInterval(updateCountdown, 1000);
})();

function mcCopyVaCode() {
    const codeEl = document.getElementById('mcVaCode');
    const code = codeEl ? codeEl.textContent.trim() : '';
    if (code && navigator.clipboard) {
        navigator.clipboard.writeText(code).then(() => {
            alert('Kode VA berhasil disalin!');
        });
    }
}
</script>
<?php endif; ?>

<?= $this->include('layout/footer') ?>
