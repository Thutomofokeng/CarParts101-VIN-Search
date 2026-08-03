<?php

if (!defined('ABSPATH')) {
    exit;
}

class MINI implements DecoderInterface
{
    /**
     * Database loader.
     */
    private DatabaseLoader $loader;

    /**
     * Constructor.
     */
    public function __construct(DatabaseLoader $loader)
    {
        $this->loader = $loader;
    }

    /**
     * Decode MINI-specific VIN information.
     *
     * @param array $vehicle
     * @return array
     */
    public function decode(array $vehicle): array
    {
        // Only process MINI vehicles
        if (($vehicle['manufacturer'] ?? '') !== 'MINI') {
            return $vehicle;
        }

        // Load MINI mapping database
        $map = $this->loader->load('manufacturer-maps/mini');

        // Use the Vehicle Descriptor Section (characters 4-8)
        $vds = $vehicle['vds'];

        if (isset($map['vds'][$vds])) {

            $vehicle = array_merge(
                $vehicle,
                $map['vds'][$vds]
            );
        }

        return $vehicle;
    }
}