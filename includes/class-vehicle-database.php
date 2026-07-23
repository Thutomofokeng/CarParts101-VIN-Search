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
        // Load VIN database
        $vehicleFile = plugin_dir_path(__FILE__) . 'Databases/vehicles.json';

        if (file_exists($vehicleFile)) {
            $this->vehicles = json_decode(file_get_contents($vehicleFile), true) ?: [];
        }

        // Load WMI database
        $wmiFile = plugin_dir_path(__FILE__) . 'wmi.php';

        if (file_exists($wmiFile)) {
            $this->manufacturers = require $wmiFile;
        } else {
            error_log('CP101: wmi.php not found.');
        }
    }

    /**
     * Load manufacturer model database
     */
    private function load_database($manufacturer)
    {
        $manufacturer = strtolower($manufacturer);

        $file = plugin_dir_path(__FILE__) .
            "Databases/models/{$manufacturer}.json";

        if (!file_exists($file)) {
            error_log("CP101: Missing model database {$file}");
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

        /*
         * Exact VIN database
         */
        if (isset($this->vehicles[$vin])) {
            return $this->vehicles[$vin];
        }

        /*
         * Manufacturer lookup
         */
        $wmi = substr($vin, 0, 3);

        if (!isset($this->manufacturers[$wmi])) {
            error_log("CP101: Unknown WMI {$wmi}");
            return null;
        }

        $manufacturer = strtolower($this->manufacturers[$wmi]);

        /*
         * Load manufacturer database
         */
        $models = $this->load_database($manufacturer);

        if (empty($models)) {
            return null;
        }

        /*
         * VIN model code
         */
        $modelCode = strtoupper(substr($vin, 3, 4));

        if (!isset($models[$modelCode])) {

            echo '<pre>';
            echo "Manufacturer : {$manufacturer}\n";
            echo "VIN          : {$vin}\n";
            echo "WMI          : {$wmi}\n";
            echo "Model Code   : {$modelCode}\n";
            echo "Database     : Databases/models/{$manufacturer}.json\n";
            echo "Available Keys:\n";
            print_r(array_keys($models));
            echo '</pre>';

            return null;
        }

        $model = $models[$modelCode];

        return [

            'make'   => ucfirst($manufacturer),
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