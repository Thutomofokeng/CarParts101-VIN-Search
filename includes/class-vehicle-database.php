<?php

class CP101_Vehicle_Database {

    private $vehicles = [];

    public function __construct() {

        $file = plugin_dir_path(__FILE__) . 'Databases/vehicles.json';

        if (file_exists($file)) {
            $json = file_get_contents($file);
            $this->vehicles = json_decode($json, true);
        }
    }

    public function find_vehicle($vin) {

        $vin = strtoupper(trim($vin));

        if (isset($this->vehicles[$vin])) {
            return $this->vehicles[$vin];
        }

        return null;
    }
}