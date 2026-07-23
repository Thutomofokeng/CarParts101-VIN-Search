<?php

if (!defined('ABSPATH')) {
    exit;
}

class CP101_Vehicle_Database {

    private $vehicles = [];
    private $manufacturers = [];
    private $years = [];
    private $plants = [];
    private $engines = [];
    private $bodyTypes = [];

    public function __construct() {

        $this->vehicles      = $this->load_json('vehicles.json');
        $this->manufacturers = $this->load_json('manufacturers.json');
        $this->years         = $this->load_json('years.json');
        $this->plants        = $this->load_json('plants.json');
        $this->engines       = $this->load_json('engines.json');
        $this->bodyTypes     = $this->load_json('body-types.json');
    }

    /**
     * Load a JSON file from the Databases folder.
     */
    private function load_json($file)
    {
        $path = plugin_dir_path(__FILE__) . 'Databases/' . $file;

        if (!file_exists($path)) {
            return [];
        }

        $json = json_decode(file_get_contents($path), true);

        return is_array($json) ? $json : [];
    }

    /**
     * Find a vehicle by VIN.
     */
    public function find_vehicle($vin)
    {
        $vin = strtoupper(trim($vin));

        if (strlen($vin) != 17) {
            return null;
        }

        /*
         * 1. Exact VIN lookup
         */
        if (isset($this->vehicles[$vin])) {
            return $this->vehicles[$vin];
        }

        /*
         * 2. Decode standard VIN fields
         */
        $wmi       = substr($vin, 0, 3);
        $yearCode  = substr($vin, 9, 1);
        $plantCode = substr($vin, 10, 1);

        /*
         * NOTE:
         * Engine/body positions differ by manufacturer.
         * These can be refined later.
         */
        $engineCode = substr($vin, 4, 2);
        $bodyCode   = substr($vin, 3, 2);

        return [

            'make' => $this->manufacturers[$wmi] ?? 'Unknown',

            'model' => 'Unknown',

            'year' => $this->years[$yearCode] ?? 'Unknown',

            'plant' => $this->plants[$plantCode] ?? 'Unknown',

            'engine' => $this->engines[$engineCode] ?? 'Unknown',

            'body' => $this->bodyTypes[$bodyCode] ?? 'Unknown',

            'drive' => ''

        ];
    }
}