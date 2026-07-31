<?php

if (!defined('ABSPATH')) {
    exit;
}

class WMIDecoder implements DecoderInterface
{
    private DatabaseLoader $loader;

    /**
     * Constructor
     */
    public function __construct(DatabaseLoader $loader)
    {
        $this->loader = $loader;
    }

    /**
     * Decode the WMI into a manufacturer.
     *
     * @param array $vehicle
     * @return array
     */
    public function decode(array $vehicle): array
    {
        $manufacturers = $this->loader->load('manufacturers');

        $vehicle['manufacturer'] =
            $manufacturers[$vehicle['wmi']] ?? null;

        return $vehicle;
    }
}