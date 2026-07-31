<?php

if (!defined('ABSPATH')) {
    exit;
}

class PlantDecoder implements DecoderInterface
{
    private DatabaseLoader $loader;

    public function __construct(DatabaseLoader $loader)
    {
        $this->loader = $loader;
    }

    public function decode(array $vehicle): array
    {
        $plants = $this->loader->load('plants');

        $vehicle['plant'] =
            $plants[$vehicle['plant_code']] ?? null;

        return $vehicle;
    }
}