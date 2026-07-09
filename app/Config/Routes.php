<?php

use CodeIgniter\Router\RouteCollection;

$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(false);

// PUBLIC
$routes->get('/', 'Home::index');
$routes->get('recipes', 'Recipe::index');
$routes->get('recipe/(:segment)', 'Recipe::detail/$1');

// AUTH
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::doLogin');
$routes->get('register', 'Auth::register');
$routes->post('register', 'Auth::doRegister');
$routes->get('logout', 'Auth::logout');

// USER (harus login)
$routes->group('', ['filter' => 'auth'], function ($routes) {
    $routes->get('dashboard', 'User::dashboard');
    $routes->get('bookmarks', 'Bookmark::index');
    $routes->post('bookmark/toggle/(:segment)', 'Bookmark::toggle/$1');

    // Unlock resep premium dengan koin
    $routes->post('recipe/(:num)/unlock', 'Recipe::unlock/$1');

    // Koin: toko, beli, bayar, riwayat
    $routes->get('coin/store', 'Coin::store');
    $routes->post('coin/buy', 'Coin::buy');
    $routes->get('coin/pay/(:num)', 'Coin::pay/$1');
    $routes->post('coin/simulate/(:num)', 'Coin::simulate/$1');
    $routes->get('coin/history', 'Coin::history');
});

// CHEF (harus login; dashboard/resep butuh filter chef)
$routes->group('chef', ['filter' => 'auth'], function ($routes) {
    // Basic verification: USER_FREE → CHEF_UNVERIFIED
    $routes->get('verify', 'Chef::verifyForm');
    $routes->post('verify', 'Chef::submitVerification');
    // Advanced verification: CHEF_UNVERIFIED → CHEF_VERIFIED
    $routes->get('verify-advanced', 'Chef::verifyAdvancedForm');
    $routes->post('verify-advanced', 'Chef::submitVerificationAdvanced');
    $routes->get('status', 'Chef::status');

    // Dashboard & CRUD resep (chef filter)
    $routes->get('dashboard', 'Chef::dashboard', ['filter' => 'chef']);
    $routes->get('recipe/create', 'Chef::createRecipe', ['filter' => 'chef']);
    $routes->post('recipe/store', 'Chef::storeRecipe', ['filter' => 'chef']);
    $routes->get('recipe/(:num)/edit', 'Chef::editRecipe/$1', ['filter' => 'chef']);
    $routes->post('recipe/(:num)/update', 'Chef::updateRecipe/$1', ['filter' => 'chef']);
    $routes->post('recipe/(:num)/delete', 'Chef::deleteRecipe/$1', ['filter' => 'chef']);
    $routes->post('recipe/(:num)/toggle-publish', 'Chef::togglePublish/$1', ['filter' => 'chef']);
    $routes->post('recipe/(:num)/archive', 'Chef::archiveRecipe/$1', ['filter' => 'chef']);
});

// ADMIN
$routes->group('admin', ['filter' => 'admin'], function ($routes) {
    $routes->get('', 'Admin::index');
    $routes->get('verifications', 'Admin::verifications');
    $routes->post('verifications/(:segment)/(:segment)', 'Admin::reviewVerification/$1/$2');
    $routes->get('users', 'Admin::users');
    $routes->post('user/(:num)/update-role', 'Admin::updateUserRole/$1');
    $routes->post('user/(:num)/delete', 'Admin::deleteUser/$1');
    $routes->get('recipes', 'Admin::recipes');
    $routes->post('recipe/(:num)/status/(:segment)', 'Admin::recipeToggleStatus/$1/$2');
    $routes->post('recipe/(:num)/toggle-premium', 'Admin::recipeTogglePremium/$1');
    $routes->post('recipe/(:num)/delete', 'Admin::recipeDelete/$1');
});
