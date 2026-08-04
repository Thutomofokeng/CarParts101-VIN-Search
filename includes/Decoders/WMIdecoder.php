<?php

if (!defined('ABSPATH')) {
    exit;
}

class WMIDecoder implements DecoderInterface
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
     * Decode the World Manufacturer Identifier (WMI).
     *
     * Characters 1–3 of the VIN determine the manufacturer.
     *
     * @param array $vehicle
     * @return array
     */
    public function decode(array $vehicle): array
    {
        $manufacturers = $this->loader->load('manufacturers');

        $wmi = strtoupper($vehicle['wmi'] ?? '');

        if (
            isset($manufacturers[$wmi]) &&
            isset($manufacturers[$wmi]['manufacturer'])
        ) {

            $vehicle['manufacturer'] =
                $manufacturers[$wmi]['manufacturer'];

        }

        return $vehicle;
    }
}