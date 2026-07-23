<?php

class CP101_Vehicle_Database {

    private $vehicles = [];
    private $manufacturers = [];

    public function __construct() {

        // Load known vehicles
        $vehicle_file = plugin_dir_path(__FILE__) . 'Databases/vehicles.json';

        if (file_exists($vehicle_file)) {
            $json = file_get_contents($vehicle_file);
            $this->vehicles = json_decode($json, true);
        }

        // Load manufacturer database
        $manufacturer_file = plugin_dir_path(__FILE__) . 'Databases/manufacturers.json';

        if (file_exists($manufacturer_file)) {
            $json = file_get_contents($manufacturer_file);
            $this->manufacturers = json_decode($json, true);
        }
    }

    public function find_vehicle($vin) {

        $vin = strtoupper(trim($vin));

        // 1. Exact VIN match
        if (isset($this->vehicles[$vin])) {
            return $this->vehicles[$vin];
        }

        // 2. Manufacturer lookup using WMI
        $wmi = substr($vin, 0, 3);

        if (isset($this->manufacturers[$wmi])) {

            return [
                'make'   => $this->manufacturers[$wmi],
                'model'  => 'Unknown',
                'year'   => '',
                'engine' => '',
                'body'   => '',
                'drive'  => ''
            ];
        }

        // Nothing found
        return null;
    }

}