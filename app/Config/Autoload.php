<?php

namespace Config;

use CodeIgniter\Config\AutoloadConfig;

/**
 * AUTOLOADER CONFIGURATION
 * NOTE: This class is required prior to Autoloader instantiation,
 * and does not extend BaseConfig.
 *
 * @immutable
 */
class Autoload extends AutoloadConfig
{
    /**
     * @var array<string, list<string>|string>
     */
    public $psr4 = [
        APP_NAMESPACE => APPPATH,
    ];

    /** @var array<string, string> */
    public $classmap = [];

    /** @var list<string> */
    public $files = [];

    /** @var list<string> */
    public $helpers = ['cookpad'];
}
