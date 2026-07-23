<?php

if (!defined('ABSPATH')) {
    exit;
}

class CP101_Vehicle_Database
{
    private $vehicles = [];
    private $manufacturers = [];

    public function __construct()
    {
        // Load known VIN database
        $vehicle_file = plugin_dir_path(__FILE__) . 'Databases/vehicles.json';

        if (file_exists($vehicle_file)) {
            $this->vehicles = json_decode(file_get_contents($vehicle_file), true) ?: [];
        }

        // Load manufacturer database
        $manufacturer_file = plugin_dir_path(__FILE__) . 'Databases/manufacturers.json';

        if (file_exists($manufacturer_file)) {
            $this->manufacturers = json_decode(file_get_contents($manufacturer_file), true) ?: [];
        }
    }

    /**
     * Load a manufacturer-specific JSON database.
     */
    private function load_database($folder, $manufacturer)
    {
        $manufacturer = strtolower($manufacturer);

        $file = plugin_dir_path(__FILE__) .
            "Databases/{$folder}/{$manufacturer}.json";

        if (!file_exists($file)) {
            return [];
        }

        $json = file_get_contents($file);
        $data = json_decode($json, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
         error_log('CP101 JSON Error: ' . json_last_error_msg());
        return [];
}

return $data;
    }

    public function find_vehicle($vin)
{
    $vin = strtoupper(trim($vin));

    if (strlen($vin) !== 17) {
        return null;
    }

    // Exact VIN lookup
    if (isset($this->vehicles[$vin])) {
        return $this->vehicles[$vin];
    }

    // Manufacturer lookup
    $wmi = substr($vin, 0, 3);

    if (!isset($this->manufacturers[$wmi])) {
        return null;
    }

    $manufacturer = strtolower($this->manufacturers[$wmi]);

    // Load manufacturer database
    $models = $this->load_database('models', $manufacturer);

    if (empty($models)) {
        error_log("CP101: No model database loaded for {$manufacturer}");
        return null;
    }

    // BMW/MINI type code
    $modelCode = strtoupper(substr($vin, 3, 4));

    if (!array_key_exists($modelCode, $models)) {
    die(
        '<pre>' .
        'Manufacturer: ' . $manufacturer . PHP_EOL .
        'Model Code: ' . $modelCode . PHP_EOL .
        'JSON File: ' . plugin_dir_path(__FILE__) . "Databases/models/{$manufacturer}.json" . PHP_EOL .
        'Keys Loaded: ' . count($models) .
        '</pre>'
    );
}

    $model = $models[$modelCode];

    return [
        'make'   => strtoupper($manufacturer),
        'series' => $model['series'] ?? '',
        'model'  => $model['model'] ?? '',
        'body'   => $model['body'] ?? '',
        'drive'  => $model['drive'] ?? '',
        'year'   => $model['production'] ?? '',
        'plant'  => $model['plant'] ?? '',
        'engine' => $model['engine'] ?? ''
    ];
    }
}