<?php

if (!defined('ABSPATH')) {
    exit;
}

class PlantDecoder implements DecoderInterface
{
    /**
     * Database loader.
     *
     * @var DatabaseLoader
     */
    private DatabaseLoader $loader;

    /**
     * Constructor.
     *
     * @param DatabaseLoader $loader
     */
    public function __construct(DatabaseLoader $loader)
    {
        $this->loader = $loader;
    }

    /**
     * Decode the production plant.
     *
     * Character 11 of the VIN.
     *
     * @param array $vehicle
     * @return array
     */
    public function decode(array $vehicle): array
    {
        $plants = $this->loader->load('plants');

        $plantCode = strtoupper($vehicle['plant_code'] ?? '');

        if (
            isset($plants[$plantCode]) &&
            isset($plants[$plantCode]['name'])
        ) {
            $vehicle['plant'] = $plants[$plantCode]['name'];
        }

        return $vehicle;
    }
}