<?php
namespace Config;
use CodeIgniter\Config\BaseConfig;

class Filters extends BaseConfig
{
    public array $aliases = [
        'csrf'          => \CodeIgniter\Filters\CSRF::class,
        'toolbar'       => \CodeIgniter\Filters\DebugToolbar::class,
        'honeypot'      => \CodeIgniter\Filters\Honeypot::class,
        'invalidchars'  => \CodeIgniter\Filters\InvalidChars::class,
        'secureheaders' => \CodeIgniter\Filters\SecureHeaders::class,
        'auth'          => \App\Filters\AuthFilter::class,
        'chef'          => \App\Filters\ChefFilter::class,
        'admin'         => \App\Filters\AdminFilter::class,
    ];
    public array $required = ['before' => ['honeypot'], 'after' => ['toolbar']];
    public array $globals  = ['before' => ['honeypot','csrf'], 'after' => ['toolbar']];
    public array $methods  = [];
    public array $filters  = [];
}
