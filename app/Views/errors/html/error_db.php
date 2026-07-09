<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kesalahan Database | Mini Cookpad</title>
    <link rel="stylesheet" href="/css/app.css">
</head>
<body>
    <div style="flex:1;display:flex;align-items:center;justify-content:center;padding:2rem">
        <div style="text-align:center;max-width:28rem">
            <div style="font-size:5rem;margin-bottom:1rem">🗄️</div>
            <h1 style="font-size:2rem;font-weight:700;color:var(--mc-red);margin-bottom:0.5rem">Kesalahan Database</h1>
            <h2 style="font-size:1.125rem;font-weight:600;margin-bottom:0.75rem">Dapur Sedang Dalam Perbaikan</h2>
            <p style="color:var(--mc-gray);margin-bottom:1rem">
                Tidak dapat terhubung ke database. Silakan coba lagi dalam beberapa menit.
            </p>
            <?php if (defined('ENVIRONMENT') && ENVIRONMENT === 'development'): ?>
                <div style="background:white;border:1px solid var(--mc-border);border-radius:var(--radius);padding:1rem;text-align:left;margin-bottom:1.5rem;font-size:0.8125rem;overflow:auto;max-height:16rem">
                    <strong style="color:var(--mc-red)">Database Error:</strong><br>
                    <?php if (isset($message)): ?>
                        <code><?= esc($message) ?></code>
                    <?php else: ?>
                        <code>Detail error tidak tersedia.</code>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <a href="/" class="mc-btn mc-btn-primary">🏠 Kembali ke Beranda</a>
        </div>
    </div>
</body>
</html>
