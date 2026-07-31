<?php

if (!defined('ABSPATH')) {
    exit;
}

class DatabaseLoader
{
    /**
     * In-memory cache.
     *
     * @var array
     */
    private static array $cache = [];

    /**
     * Database root directory.
     *
     * @var string
     */
    private string $databasePath;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->databasePath = plugin_dir_path(__FILE__) . '../Databases/';
    }

    /**
     * Load a JSON database.
     *
     * Examples:
     *
     * load('manufacturers');
     * load('years');
     * load('plants');
     * load('engines');
     * load('body-types');
     * load('models/bmw');
     * load('manufacturer-maps/mini');
     *
     * @param string $database
     * @return array
     * @throws RuntimeException
     */
    public function load(string $database): array
    {
        $database = trim($database);

        /*
        |--------------------------------------------------------------------------
        | Return from cache
        |--------------------------------------------------------------------------
        */

        if (isset(self::$cache[$database])) {
            return self::$cache[$database];
        }

        /*
        |--------------------------------------------------------------------------
        | Build file path
        |--------------------------------------------------------------------------
        */

        $file = $this->databasePath . $database . '.json';

        /*
        |--------------------------------------------------------------------------
        | File does not exist
        |--------------------------------------------------------------------------
        */

        if (!file_exists($file)) {
            throw new RuntimeException(
                sprintf(
                    'Database "%s" not found (%s)',
                    $database,
                    $file
                )
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Read file
        |--------------------------------------------------------------------------
        */

        $json = file_get_contents($file);

        if ($json === false) {
            throw new RuntimeException(
                sprintf(
                    'Unable to read database "%s"',
                    $database
                )
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Decode JSON
        |--------------------------------------------------------------------------
        */

        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException(
                sprintf(
                    'JSON error in "%s": %s',
                    $database,
                    json_last_error_msg()
                )
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Cache
        |--------------------------------------------------------------------------
        */

        self::$cache[$database] = $data;

        return $data;
    }

    /**
     * Clear one cached database.
     *
     * @param string $database
     */
    public function clear(string $database): void
    {
        unset(self::$cache[$database]);
    }

    /**
     * Clear all cached databases.
     */
    public function clearAll(): void
    {
        self::$cache = [];
    }

    /**
     * Check if a database has already been loaded.
     *
     * @param string $database
     * @return bool
     */
    public function isLoaded(string $database): bool
    {
        return isset(self::$cache[$database]);
    }

    /**
     * Return all currently cached databases.
     *
     * Useful for debugging.
     *
     * @return array
     */
    public function getLoadedDatabases(): array
    {
        return array_keys(self::$cache);
    }
}