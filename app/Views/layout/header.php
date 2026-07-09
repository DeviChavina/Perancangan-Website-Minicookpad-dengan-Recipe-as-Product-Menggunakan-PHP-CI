<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Mini Cookpad') ?></title>
    <link rel="stylesheet" href="/css/app.css">
</head>
<body>
<?php
$loggedIn    = session()->get('logged_in');
$userRole    = session()->get('user_role');
$userName    = session()->get('user_name');
$currentPath = current_url(true)->getPath();

$badgeMap = [
    'USER_FREE'       => 'free',
    'CHEF_UNVERIFIED' => 'chef',
    'CHEF_PENDING'    => 'pending',
    'CHEF_VERIFIED'   => 'verified',
    'ADMIN'           => 'admin',
];
$badgeClass = $badgeMap[$userRole] ?? 'free';

// Ambil coin balance fresh dari DB (bukan hanya session)
$userCoins = 0;
if ($loggedIn) {
    $db = \Config\Database::connect();
    $row = $db->table('users')->select('coin_balance')->where('id', session()->get('user_id'))->get()->getRowArray();
    $userCoins = (int)($row['coin_balance'] ?? 0);
}
?>
<header class="mc-header">
    <div class="mc-container mc-header-inner">
        <a href="/" class="mc-logo">
            <div class="mc-logo-icon">🍳</div>
            Mini<span>Cookpad</span>
        </a>
        <nav class="mc-nav">
            <a href="/" class="<?= $currentPath === '/' ? 'active' : '' ?>">🏠 Beranda</a>
            <a href="/recipes">📖 Resep</a>
            <?php if (!$loggedIn): ?>
                <a href="/login">🔑 Masuk</a>
            <?php else: ?>
                <a href="/coin/store" style="font-weight:600">🪙 <?= number_format($userCoins) ?></a>
                <a href="/bookmarks">🔖 Simpan</a>
                <?php if ($userRole === 'USER_FREE'): ?>
                    <a href="/chef/verify">👨‍🍳 Jadi Chef</a>
                <?php endif; ?>
                <?php if ($userRole === 'CHEF_PENDING'): ?>
                    <a href="/chef/status">⏳ Status</a>
                <?php endif; ?>
                <?php if ($userRole === 'CHEF_UNVERIFIED'): ?>
                    <a href="/chef/dashboard" class="<?= strpos($currentPath,'/chef')===0 ? 'active':'' ?>">🍳 Dashboard</a>
                    <a href="/chef/verify-advanced">⭐ Upgrade Chef</a>
                <?php endif; ?>
                <?php if ($userRole === 'CHEF_VERIFIED'): ?>
                    <a href="/chef/dashboard" class="<?= strpos($currentPath,'/chef')===0 ? 'active':'' ?>">👨‍🍳 Dashboard</a>
                <?php endif; ?>
                <?php if ($userRole === 'ADMIN'): ?>
                    <a href="/admin" class="<?= strpos($currentPath,'/admin')===0 ? 'active':'' ?>">🛡️ Admin</a>
                    <a href="/chef/dashboard">👨‍🍳 Chef</a>
                <?php endif; ?>
                <a href="/dashboard">👤 Profil</a>
                <a href="/logout">🚪 Keluar</a>
            <?php endif; ?>
        </nav>
        <?php if ($loggedIn): ?>
        <div class="mc-user-info">
            <span class="mc-badge mc-badge-<?= $badgeClass ?>"><?= role_label($userRole) ?></span>
            <span><?= esc($userName) ?></span>
        </div>
        <?php endif; ?>
    </div>
</header>
<main style="flex:1">
