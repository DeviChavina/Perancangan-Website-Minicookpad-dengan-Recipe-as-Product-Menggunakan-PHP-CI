<?= view('layout/header', ['title' => $title]) ?>
<div class="mc-container" style="padding:2rem 1rem">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem">
        <h2 style="margin:0">📋 Riwayat Transaksi Koin</h2>
        <div>Saldo: <strong style="color:var(--mc-orange);font-size:1.25rem"><?= number_format((int)($user['coin_balance']??0)) ?> 🪙</strong></div>
    </div>

    <?php if (empty($transactions)): ?>
    <div style="text-align:center;padding:3rem;color:#9ca3af">Belum ada transaksi.</div>
    <?php else: ?>
    <div style="background:white;border:1px solid var(--mc-border);border-radius:12px;overflow:hidden">
        <?php foreach ($transactions as $i => $t):
            $isPositive = (int)$t['amount'] > 0;
            $typeLabel  = match($t['type']) {
                'topup'     => ['label'=>'Top-up Koin',       'icon'=>'💳', 'bg'=>'#f0fdf4', 'color'=>'#166534'],
                'unlock'    => ['label'=>'Unlock Resep',      'icon'=>'🔓', 'bg'=>'#fef3c7', 'color'=>'#92400e'],
                'earn'      => ['label'=>'Pendapatan Resep',  'icon'=>'💰', 'bg'=>'#f0fdf4', 'color'=>'#166534'],
                'refund'    => ['label'=>'Refund',            'icon'=>'↩️', 'bg'=>'#eff6ff', 'color'=>'#1d4ed8'],
                'admin_adj' => ['label'=>'Penyesuaian Admin', 'icon'=>'⚙️', 'bg'=>'#f9fafb', 'color'=>'#374151'],
                default     => ['label'=>$t['type'],          'icon'=>'🪙', 'bg'=>'#f9fafb', 'color'=>'#374151'],
            };
        ?>
        <div style="display:flex;justify-content:space-between;align-items:center;padding:1rem 1.25rem;<?= $i > 0 ? 'border-top:1px solid var(--mc-border)' : '' ?>;background:<?= $typeLabel['bg'] ?>">
            <div style="display:flex;align-items:center;gap:.75rem">
                <div style="font-size:1.5rem"><?= $typeLabel['icon'] ?></div>
                <div>
                    <div style="font-weight:600;color:<?= $typeLabel['color'] ?>"><?= $typeLabel['label'] ?></div>
                    <div style="font-size:0.8125rem;color:#6b7280"><?= esc($t['note'] ?? '') ?> • <?= date('d M Y H:i', strtotime($t['created_at'])) ?></div>
                </div>
            </div>
            <div style="text-align:right">
                <div style="font-weight:700;font-size:1.1rem;color:<?= $isPositive ? '#10b981' : '#ef4444' ?>">
                    <?= $isPositive ? '+' : '' ?><?= number_format((int)$t['amount']) ?> 🪙
                </div>
                <div style="font-size:0.75rem;color:#9ca3af">Saldo: <?= number_format((int)$t['balance_after']) ?></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div style="margin-top:1.5rem;text-align:center">
        <a href="/coin/store" class="mc-btn mc-btn-primary" style="background:var(--mc-orange);border-color:var(--mc-orange)">🪙 Beli Koin Lagi</a>
    </div>
</div>
<?= view('layout/footer') ?>
