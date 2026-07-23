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

    public function find_vehicle($vin)
    {
        $vin = strtoupper(trim($vin));

        // VIN must be 17 characters
        if (strlen($vin) !== 17) {
            return null;
        }

        // Exact VIN match
        if (isset($this->vehicles[$vin])) {
            return $this->vehicles[$vin];
        }

        // Manufacturer lookup
        $wmi = substr($vin, 0, 3);

        if (isset($this->manufacturers[$wmi])) {
            return [
                'make'   => $this->manufacturers[$wmi],
                'model'  => 'Unknown',
                'year'   => 'Unknown',
                'plant'  => 'Unknown',
                'engine' => 'Unknown',
                'body'   => 'Unknown',
                'drive'  => ''
            ];
        }

        // Nothing found locally
        return null;
    }
}