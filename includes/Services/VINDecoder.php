<?php

if (!defined('ABSPATH')) {
    exit;
}

class VINDecoder
{
    /**
     * Core services.
     */
    private VINValidator $validator;
    private VINParser $parser;

    /**
     * Generic decoders.
     */
    private WMIDecoder $wmiDecoder;
    private YearDecoder $yearDecoder;
    private PlantDecoder $plantDecoder;

    /**
     * Manufacturer resolver.
     */
    private ManufacturerResolver $manufacturerResolver;

    /**
     * Constructor.
     */
    public function __construct(
        VINValidator $validator,
        VINParser $parser,
        ManufacturerResolver $manufacturerResolver,
        WMIDecoder $wmiDecoder,
        YearDecoder $yearDecoder,
        PlantDecoder $plantDecoder
    ) {
        $this->validator = $validator;
        $this->parser = $parser;

        $this->manufacturerResolver = $manufacturerResolver;

        $this->wmiDecoder = $wmiDecoder;
        $this->yearDecoder = $yearDecoder;
        $this->plantDecoder = $plantDecoder;
    }

    /**
     * Decode a VIN.
     */
    public function resolve(string $vin): ?array
    {
        /*
        |--------------------------------------------------------------------------
        | Validate VIN
        |--------------------------------------------------------------------------
        */

        if (!$this->validator->validate($vin)) {
            return null;
        }

        /*
        |--------------------------------------------------------------------------
        | Parse VIN
        |--------------------------------------------------------------------------
        */

        $vehicle = $this->parser->parse($vin);

        /*
        |--------------------------------------------------------------------------
        | Generic VIN decoding
        |--------------------------------------------------------------------------
        */

        $vehicle = $this->wmiDecoder->decode($vehicle);
        $vehicle = $this->yearDecoder->decode($vehicle);
        $vehicle = $this->plantDecoder->decode($vehicle);

        /*
        |--------------------------------------------------------------------------
        | Manufacturer-specific decoding
        |--------------------------------------------------------------------------
        */

        $vehicle = $this->manufacturerResolver->decode($vehicle);

        /*
        |--------------------------------------------------------------------------
        | Confidence Score
        |--------------------------------------------------------------------------
        */

        $vehicle['confidence'] = $this->calculateConfidence($vehicle);

        return $vehicle;
    }

    /**
     * Calculate decoder confidence.
     */
    private function calculateConfidence(array $vehicle): int
    {
        $score = 0;

        $fields = [
            'manufacturer',
            'model',
            'series',
            'generation',
            'trim',
            'year',
            'plant',
            'engine',
            'body',
            'fuel',
            'drive',
            'transmission'
        ];

        foreach ($fields as $field) {
            if (!empty($vehicle[$field])) {
                $score += 8;
            }
        }

        return min($score, 100);
    }
}