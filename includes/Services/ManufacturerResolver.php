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
     * Constructor.
     */
    public function __construct(DatabaseLoader $loader)
    {
        // Register all manufacturer decoders here

        $this->register(
            'MINI',
            new MINIDecoder($loader)
        );

        // Future manufacturers
        // $this->register('BMW', new BMWDecoder($loader));
        // $this->register('AUDI', new AudiDecoder($loader));
        // $this->register('TOYOTA', new ToyotaDecoder($loader));
    }

    /**
     * Register a manufacturer decoder.
     */
    public function register(
        string $manufacturer,
        DecoderInterface $decoder
    ): void {

        $this->manufacturers[strtoupper($manufacturer)] = $decoder;

    }

    /**
     * Decode manufacturer-specific VIN information.
     */
    public function decode(array $vehicle): array
    {
        $manufacturer = strtoupper(
            $vehicle['manufacturer'] ?? ''
        );

        if (!isset($this->manufacturers[$manufacturer])) {
            return $vehicle;
        }

        return $this->manufacturers[$manufacturer]
            ->decode($vehicle);
    }
}