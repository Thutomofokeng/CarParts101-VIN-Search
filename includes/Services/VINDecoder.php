<?php

if (!defined('ABSPATH')) {
    exit;
}

class VINDecoder
{
    private VINValidator $validator;
    private VINParser $parser;
    private ManufacturerResolver $manufacturerResolver;
    private WMIDecoder $wmiDecoder;
    private YearDecoder $yearDecoder;
    private PlantDecoder $plantDecoder;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $loader = new DatabaseLoader();

        $this->validator = new VINValidator();
        $this->parser = new VINParser();

        $this->manufacturerResolver = new ManufacturerResolver($loader);

        $this->wmiDecoder = new WMIDecoder($loader);
        $this->yearDecoder = new YearDecoder($loader);
        $this->plantDecoder = new PlantDecoder($loader);
    }

    /**
     * Decode a VIN.
     */
    public function resolve(string $vin): ?array
    {
        if (!$this->validator->validate($vin)) {
            return null;
        }

        // Parse VIN
        $vehicle = $this->parser->parse($vin);

        // Generic decoders
        $vehicle = $this->wmiDecoder->decode($vehicle);
        $vehicle = $this->yearDecoder->decode($vehicle);
        $vehicle = $this->plantDecoder->decode($vehicle);

        // Manufacturer-specific decoder
        $vehicle = $this->manufacturerResolver->decode($vehicle);

        // Confidence score
        $vehicle['confidence'] = $this->calculateConfidence($vehicle);

        return $vehicle;
    }

    /**
     * Calculate confidence.
     */
    private function calculateConfidence(array $vehicle): int
    {
        $score = 0;

        $fields = [
            'manufacturer',
            'model',
            'series',
            'year',
            'plant',
            'engine',
            'body',
            'trim'
        ];

        foreach ($fields as $field) {
            if (!empty($vehicle[$field])) {
                $score += 12;
            }
        }

        return min($score, 100);
    }
}