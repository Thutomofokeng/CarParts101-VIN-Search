<?php

if (!defined('ABSPATH')) {
    exit;
}

class DatabaseLoader
{
    /**
     * Loaded database cache
     *
     * @var array
     */
    private array $cache = [];

    /**
     * Base database directory
     *
     * @var string
     */
    private string $databasePath;

    public function __construct()
    {
        $this->databasePath = plugin_dir_path(dirname(__FILE__)) . 'Databases/';
    }

    /**
     * Load any JSON database.
     *
     * Examples:
     *
     * manufacturers
     * years
     * Manufacturers-maps/Mini/engines
     */
    public function load(string $database): array
    {
        if (isset($this->cache[$database])) {
            return $this->cache[$database];
        }

        $file = $this->databasePath . $database . '.json';

        if (!file_exists($file)) {
            throw new Exception("Database '{$database}' not found.");
        }

        $json = file_get_contents($file);

        if ($json === false) {
            throw new Exception("Unable to read '{$database}'.");
        }

        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception(
                "Invalid JSON in '{$database}': " .
                json_last_error_msg()
            );
        }

        $this->cache[$database] = $data;

        return $data;
    }

    /**
     * Automatically load every model file
     * inside a manufacturer folder.
     *
     * Example:
     *
     * Mini/
     *   R-models.json
     *   F-models.json
     *   J-models.json
     *
     */
    public function loadManufacturerModels(string $manufacturer): array
    {
        $cacheKey = "models_{$manufacturer}";

        if (isset($this->cache[$cacheKey])) {
            return $this->cache[$cacheKey];
        }

        $directory = $this->databasePath .
            'Manufacturers-maps/' .
            $manufacturer;

        if (!is_dir($directory)) {
            throw new Exception(
                "Manufacturer '{$manufacturer}' not found."
            );
        }

        $files = glob($directory . '/*-models.json');

        if (!$files) {
            return [];
        }

        sort($files);

        $models = [];

        foreach ($files as $file) {

            $json = file_get_contents($file);

            if ($json === false) {
                continue;
            }

            $data = json_decode($json, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception(
                    "Invalid JSON in " . basename($file)
                );
            }

            $models = array_merge($models, $data);
        }

        $this->cache[$cacheKey] = $models;

        return $models;
    }

    /**
     * Load every JSON file
     * from a manufacturer folder.
     *
     * Returns:
     *
     * [
     *     'engines' => [...],
     *     'transmissions' => [...],
     *     'body-types' => [...]
     * ]
     *
     */
    public function loadManufacturerData(string $manufacturer): array
    {
        $cacheKey = "manufacturer_{$manufacturer}";

        if (isset($this->cache[$cacheKey])) {
            return $this->cache[$cacheKey];
        }

        $directory = $this->databasePath .
            'Manufacturers-maps/' .
            $manufacturer;

        if (!is_dir($directory)) {
            throw new Exception(
                "Manufacturer '{$manufacturer}' not found."
            );
        }

        $files = glob($directory . '/*.json');

        $data = [];

        foreach ($files as $file) {

            $name = basename($file, '.json');

            $json = file_get_contents($file);

            if ($json === false) {
                continue;
            }

            $decoded = json_decode($json, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception(
                    "Invalid JSON in {$name}.json"
                );
            }

            $data[$name] = $decoded;
        }

        $this->cache[$cacheKey] = $data;

        return $data;
    }

    /**
     * Check if a database exists.
     */
    public function exists(string $database): bool
    {
        return file_exists(
            $this->databasePath .
            $database .
            '.json'
        );
    }

    /**
     * Clear all cached databases.
     */
    public function clearCache(): void
    {
        $this->cache = [];
    }

    /**
     * Return loaded cache.
     */
    public function getCache(): array
    {
        return $this->cache;
    }
}