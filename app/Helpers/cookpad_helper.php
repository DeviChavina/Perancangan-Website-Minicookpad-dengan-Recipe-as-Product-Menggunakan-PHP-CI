<?php

if (!function_exists('format_rupiah')) {
    function format_rupiah(int $amount): string {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}

if (!function_exists('format_coin')) {
    function format_coin(int $amount): string {
        return number_format($amount, 0, ',', '.') . ' 🪙';
    }
}

if (!function_exists('slugify')) {
    function slugify(string $text): string {
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        if (function_exists('iconv')) {
            $c = @iconv('utf-8', 'us-ascii//TRANSLIT', $text);
            if ($c !== false) $text = $c;
        }
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);
        $text = strtolower($text);
        return $text === '' ? 'item-' . substr(md5(microtime(true)), 0, 8) : $text;
    }
}

if (!function_exists('difficulty_label')) {
    function difficulty_label(string $difficulty): string {
        return match($difficulty) { 'easy'=>'Mudah','medium'=>'Sedang','hard'=>'Sulit', default=>$difficulty };
    }
}

if (!function_exists('cuisine_label')) {
    function cuisine_label(string $cuisine): string {
        return match($cuisine) { 'Indonesian'=>'Indonesia','Japanese'=>'Jepang','Italian'=>'Italia','Korean'=>'Korea','Thai'=>'Thailand','Mexican'=>'Meksiko', default=>$cuisine };
    }
}

if (!function_exists('role_label')) {
    function role_label(?string $role): string {
        if ($role === null) return 'Guest';
        return match($role) {
            'USER_FREE'      => 'Free',
            'CHEF_UNVERIFIED'=> 'Chef',
            'CHEF_PENDING'   => 'Chef Pending',
            'CHEF_VERIFIED'  => 'Chef Verified',
            'ADMIN'          => 'Admin',
            default          => $role,
        };
    }
}

if (!function_exists('can_view_premium')) {
    function can_view_premium(?array $user): bool {
        if (!$user) return false;
        return in_array($user['role'], ['CHEF_UNVERIFIED','CHEF_VERIFIED','ADMIN']);
    }
}

if (!function_exists('recipe_image_url')) {
    function recipe_image_url(?string $image): string {
        return empty($image) ? '/img/recipe-placeholder.svg' : '/uploads/recipes/' . $image;
    }
}

if (!function_exists('step_image_url')) {
    function step_image_url(?string $image): ?string {
        return empty($image) ? null : '/uploads/steps/' . $image;
    }
}

if (!function_exists('recipe_thumbnail')) {
    function recipe_thumbnail(array $recipe, string $gradient = '', string $emoji = ''): string {
        $imgUrl = recipe_image_url($recipe['image'] ?? null);
        return '<img src="' . $imgUrl . '" alt="' . esc($recipe['title'] ?? '') . '" style="width:100%;height:100%;object-fit:cover;display:block">';
    }
}

/** Helper: ambil item pertama dari array berdasarkan key=value */
if (!function_exists('collect_by')) {
    function collect_by(array $items, string $key, mixed $value): ?array {
        foreach ($items as $item) {
            if (($item[$key] ?? null) === $value) return $item;
        }
        return null;
    }
}

/** Hitung bagi hasil chef dari coin_price berdasarkan role */
if (!function_exists('calc_chef_earn')) {
    function calc_chef_earn(int $coinPrice, string $chefRole): array {
        if ($chefRole === 'CHEF_VERIFIED') {
            $chefEarn = (int) floor($coinPrice * 0.7);
        } else {
            $chefEarn = (int) floor($coinPrice * 0.5);
        }
        return ['chef' => $chefEarn, 'platform' => $coinPrice - $chefEarn];
    }
}
