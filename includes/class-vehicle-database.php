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

        return json_decode(file_get_contents($file), true) ?: [];
    }

    public function find_vehicle($vin)
    {
        $vin = strtoupper(trim($vin));

        if (strlen($vin) !== 17) {
            return null;
        }

        // 1. Exact VIN lookup
        if (isset($this->vehicles[$vin])) {
            return $this->vehicles[$vin];
        }

        // 2. Manufacturer lookup
        $wmi = substr($vin, 0, 3);

        if (!isset($this->manufacturers[$wmi])) {
            return null;
        }

        $manufacturer = $this->manufacturers[$wmi];

        // 3. Load manufacturer-specific model database
        $models = $this->load_database('models', $manufacturer);

        // MINI/BMW style model code
        $modelCode = substr($vin, 3, 4);

        $model = $models[$modelCode] ?? [];

        return [
            'make'   => $manufacturer,
            'series' => $model['series'] ?? 'Unknown',
            'model'  => $model['model'] ?? 'Unknown',
            'body'   => $model['body'] ?? 'Unknown',
            'drive'  => $model['drive'] ?? 'Unknown',
            'year'   => 'Unknown',
            'plant'  => 'Unknown',
            'engine' => 'Unknown'
        ];
    }
}