<?php

namespace App;

/**
 * Application configuration
 *
 * PHP version 7.0
 */
class Config
{

    /**
     * Database host
     * @var string
     */
    const DB_HOST = 'localhost';

    /**
     * Database name
     * @var string
     */
    const DB_NAME = 'videgrenierenligne';

    /**
     * Database user
     * @var string
     */
    const DB_USER = 'webapplication';

    /**
     * Database password
     * @var string
     */
    const DB_PASSWORD = '653rag9T';

    /**
     * Show or hide error messages on screen
     * @var boolean
     */
    const SHOW_ERRORS = true;

    /**
     * Read an environment variable with a fallback to the legacy default.
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    private static function env($name, $default)
    {
        $value = getenv($name);

        if ($value === false || $value === '') {
            return $default;
        }

        return $value;
    }

    /**
     * @return string
     */
    public static function dbHost()
    {
        return self::env('DB_HOST', self::DB_HOST);
    }

    /**
     * @return string
     */
    public static function dbName()
    {
        return self::env('DB_NAME', self::DB_NAME);
    }

    /**
     * @return string
     */
    public static function dbUser()
    {
        return self::env('DB_USER', self::DB_USER);
    }

    /**
     * @return string
     */
    public static function dbPassword()
    {
        return self::env('DB_PASSWORD', self::DB_PASSWORD);
    }

    /**
     * @return bool
     */
    public static function showErrors()
    {
        return filter_var(
            self::env('APP_SHOW_ERRORS', self::SHOW_ERRORS ? '1' : '0'),
            FILTER_VALIDATE_BOOLEAN
        );
    }
}
