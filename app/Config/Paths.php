<?php

namespace Config;

/**
 * --------------------------------------------------------------------
 * Paths Configuration
 * --------------------------------------------------------------------
 * Mini Cookpad - Platform Resep Autentik dari Chef Terverifikasi
 *
 * NOTE: This class does not extend BaseConfig so that it can be loaded
 * early without the framework needing to be bootstrapped.
 */
class Paths
{
    /**
     * ---------------------------------------------------------------
     * System Directory
     * ---------------------------------------------------------------
     *
     * This variable must contain the name of your "system" directory.
     * Set the path if it is not in the same directory as this file.
     */
    public string $systemDirectory = __DIR__ . '/../../vendor/codeigniter4/framework/system';

    /**
     * ---------------------------------------------------------------
     * Application Directory
     * ---------------------------------------------------------------
     *
     * This variable must contain the name of your "application" directory.
     */
    public string $appDirectory = __DIR__ . '/..';

    /**
     * ---------------------------------------------------------------
     * Views Directory
     * ---------------------------------------------------------------
     *
     * This variable must contain the name of the directory that
     * contains your view files.
     */
    public string $viewDirectory = __DIR__ . '/../Views';

    /**
     * ---------------------------------------------------------------
     * Writable Directory
     * ---------------------------------------------------------------
     *
     * This variable must contain the name of your "writable" directory.
     */
    public string $writableDirectory = __DIR__ . '/../../writable';

    /**
     * ---------------------------------------------------------------
     * Tests Directory
     * ---------------------------------------------------------------
     *
     * This variable must contain the name of your "tests" directory.
     */
    public string $testsDirectory = __DIR__ . '/../../tests';

    /**
     * ---------------------------------------------------------------
     * Composer Autoload
     * ---------------------------------------------------------------
     *
     * This variable determines whether Composer's autoload should be used.
     */
    public bool $composerAutoload = true;
}
