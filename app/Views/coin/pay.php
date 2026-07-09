<?= view('layout/header', ['title' => $title]) ?>
<div class="mc-container" style="padding:2rem 1rem;max-width:500px">
    <h2>💳 Selesaikan Pembayaran</h2>

    <div style="background:#f9fafb;border:1px solid var(--mc-border);border-radius:12px;padding:1.5rem;margin-bottom:2rem">
        <div style="display:flex;justify-content:space-between;margin-bottom:.75rem">
            <span>Paket</span><strong><?= esc($topup['package_name']) ?></strong>
        </div>
        <div style="display:flex;justify-content:space-between;margin-bottom:.75rem">
            <span>Koin</span><strong><?= number_format((int)$topup['coin_amount']) ?> 🪙</strong>
        </div>
        <div style="display:flex;justify-content:space-between;margin-bottom:.75rem">
            <span>Total</span><strong><?= format_rupiah((int)$topup['price_idr']) ?></strong>
        </div>
        <div style="display:flex;justify-content:space-between;margin-bottom:.75rem">
            <span>Metode</span><strong><?= strtoupper(str_replace('_',' ',$topup['method'])) ?></strong>
        </div>
        <hr style="border:none;border-top:1px solid var(--mc-border);margin:.75rem 0">
        <div style="display:flex;justify-content:space-between">
            <span>Kode Pembayaran</span>
            <strong style="font-size:1.25rem;color:var(--mc-orange)"><?= esc($topup['payment_code']) ?></strong>
        </div>
    </div>

    <div style="background:#fef3c7;border:1px solid #fcd34d;border-radius:8px;padding:1rem;margin-bottom:2rem;font-size:0.875rem">
        ⏰ Kadaluarsa: <strong><?= date('d M Y H:i', strtotime($topup['expires_at'])) ?></strong>
    </div>

    <div style="text-align:center;margin-bottom:2rem">
        <div style="background:#f3f4f6;border-radius:8px;padding:1.5rem;margin-bottom:1rem;font-size:0.875rem;color:#6b7280">
            Simulasi environment development: klik tombol di bawah untuk menyelesaikan pembayaran.
        </div>
        <form action="/coin/simulate/<?= $topup['id'] ?>" method="post">
            <?= csrf_field() ?>
            <button class="mc-btn mc-btn-primary" style="background:#10b981;border-color:#10b981;font-size:1.1rem;padding:.875rem 2rem">
                ✅ Simulasi Bayar Sekarang
            </button>
        </form>
    </div>

    <div style="text-align:center">
        <a href="/coin/store" style="color:var(--mc-orange)">← Kembali ke Toko Koin</a>
    </div>
</div>
<?= view('layout/footer') ?>
