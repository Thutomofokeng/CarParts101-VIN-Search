<?php

if (!defined('ABSPATH')) {
    exit;
}

class ManufacturerResolver
{
    /**
     * @var array<string, DecoderInterface>
     */
    private array $manufacturers = [];

    /**
     * Register manufacturer decoders.
     *
     * @param array<string, DecoderInterface> $manufacturers
     */
    public function __construct(array $manufacturers = [])
    {
        $this->manufacturers = $manufacturers;
    }

    /**
     * Resolve manufacturer specific decoding.
     *
     * @param array $vehicle
     * @return array
     */
    public function decode(array $vehicle): array
    {
        $manufacturer = $vehicle['manufacturer'] ?? '';

        if (!isset($this->manufacturers[$manufacturer])) {
            return $vehicle;
        }

        return $this->manufacturers[$manufacturer]->decode($vehicle);
    }

    /**
     * Register a manufacturer decoder.
     *
     * @param string $manufacturer
     * @param DecoderInterface $decoder
     */
    public function register(
        string $manufacturer,
        DecoderInterface $decoder
    ): void {

        $this->manufacturers[$manufacturer] = $decoder;

    }
}