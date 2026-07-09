<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Error | Mini Cookpad</title>
    <style>
        body { font-family: monospace; background: #1a1a2e; color: #e2e8f0; padding: 2rem; }
        .box { background: #16213e; border: 1px solid #e63946; border-radius: 8px; padding: 1.5rem; max-width: 900px; margin: 0 auto; }
        h1 { color: #e63946; font-size: 1.25rem; margin: 0 0 1rem; }
        .msg { background: #0f3460; padding: 1rem; border-radius: 4px; margin-bottom: 1rem; color: #ff6b6b; font-size: 1rem; }
        .meta { font-size: 0.85rem; color: #94a3b8; margin-bottom: 0.5rem; }
        .trace { background: #0d0d0d; padding: 1rem; border-radius: 4px; font-size: 0.75rem; color: #a0aec0; white-space: pre-wrap; overflow: auto; max-height: 400px; }
        a { color: #ff6b35; }
    </style>
</head>
<body>
<div class="box">
    <h1>🔥 Mini Cookpad — Debug Error</h1>

    <?php if (isset($exception)): ?>
        <div class="msg"><?= esc(get_class($exception)) ?>: <?= esc($exception->getMessage()) ?></div>
        <div class="meta">📄 File: <strong><?= esc($exception->getFile()) ?></strong> &nbsp; Line: <strong><?= $exception->getLine() ?></strong></div>
        <div class="trace"><?= esc($exception->getTraceAsString()) ?></div>
    <?php elseif (isset($message)): ?>
        <div class="msg"><?= esc($message) ?></div>
    <?php endif; ?>

    <p style="margin-top:1rem"><a href="/">🏠 Kembali ke Beranda</a></p>
</div>
</body>
</html>
