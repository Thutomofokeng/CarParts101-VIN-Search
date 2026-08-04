<?php

if (!defined('ABSPATH')) {
    exit;
}

class YearDecoder implements DecoderInterface
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
     * Decode the model year.
     *
     * Character 10 of the VIN.
     *
     * @param array $vehicle
     * @return array
     */
    public function decode(array $vehicle): array
    {
        $years = $this->loader->load('years');

        $yearCode = strtoupper($vehicle['year_code'] ?? '');

        if (isset($years[$yearCode])) {
            $vehicle['year'] = $years[$yearCode];
        }

        return $vehicle;
    }
}