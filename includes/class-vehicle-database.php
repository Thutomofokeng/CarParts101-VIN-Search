<?php

if (!defined('ABSPATH')) {
    exit;
}

class CP101_Vehicle_Database
{
    /**
     * Database loader.
     *
     * @var DatabaseLoader
     */
    private DatabaseLoader $loader;

    /**
     * WMI database.
     *
     * @var array
     */
    private array $manufacturers = [];

    /**
     * Year database.
     *
     * @var array
     */
    private array $years = [];

    /**
     * Plant database.
     *
     * @var array
     */
    private array $plants = [];

    /**
     * Engine database.
     *
     * @var array
     */
    private array $engines = [];

    public function __construct()
    {
        $this->loader = new DatabaseLoader();

        $this->manufacturers = $this->loader->load('manufacturers');
        $this->years         = $this->loader->load('years');
        $this->plants        = $this->loader->load('plants');
        $this->engines       = $this->loader->load('engines');
    }

    /**
     * Find a vehicle from its VIN.
     */
    public function find_vehicle(string $vin): ?array
    {
        $vin = strtoupper(trim($vin));

        if (strlen($vin) !== 17) {
            return null;
        }

        // WMI
        $wmi = substr($vin, 0, 3);

        if (!isset($this->manufacturers[$wmi])) {
            return null;
        }

        $manufacturer = $this->manufacturers[$wmi]['manufacturer'];

        // Load all model files (R-models, F-models, J-models, etc.)
        $models = $this->loader->loadManufacturerModels($manufacturer);

        // VDS (positions 4-7)
        $modelCode = strtoupper(substr($vin, 3, 4));

        if (!isset($models[$modelCode])) {
            return null;
        }

        $vehicle = $models[$modelCode];

        // Model year (position 10)
        $yearCode = substr($vin, 9, 1);

        $year = $this->years[$yearCode] ?? '';

        // Plant (position 11)
        $plantCode = substr($vin, 10, 1);

        $plant = $this->plants[$plantCode] ?? [
            'name' => ''
        ];

        // Engine
        $engine = [];

        if (
            isset($vehicle['engine']) &&
            isset($this->engines[$vehicle['engine']])
        ) {
            $engine = $this->engines[$vehicle['engine']];
        }

        return [

            'vin' => $vin,

            'manufacturer' => $manufacturer,

            'series' => $vehicle['series'] ?? '',

            'model' => $vehicle['model'] ?? '',

            'trim' => $vehicle['trim'] ?? '',

            'body' => $vehicle['body'] ?? '',

            'drive' => $vehicle['drive'] ?? '',

            'fuel' => $vehicle['fuel'] ?? '',

            'transmission' => $vehicle['transmission'] ?? '',

            'production' => $vehicle['production'] ?? '',

            'year' => $year,

            'plant' => $plant['name'] ?? '',

            'engine' => $engine,

            'engine_code' => $vehicle['engine'] ?? ''

        ];
    }
}