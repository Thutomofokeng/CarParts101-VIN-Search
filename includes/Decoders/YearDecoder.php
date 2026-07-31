<?php

if (!defined('ABSPATH')) {
    exit;
}

class YearDecoder implements DecoderInterface
{
    private DatabaseLoader $loader;

    public function __construct(DatabaseLoader $loader)
    {
        $this->loader = $loader;
    }

    public function decode(array $vehicle): array
    {
        $years = $this->loader->load('years');

        $vehicle['year'] =
            $years[$vehicle['year_code']] ?? null;

        return $vehicle;
    }
}