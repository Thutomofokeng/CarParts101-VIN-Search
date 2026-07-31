<?php

if (!defined('ABSPATH')) {
    exit;
}

class VINDecoder
{
    private VINValidator $validator;
    private VINParser $parser;
    private DatabaseLoader $loader;
    private WMIDecoder $wmiDecoder;
    private YearDecoder $yearDecoder;
    private PlantDecoder $plantDecoder;

    /**
     * Constructor
     */
    public function __construct(
        VINValidator $validator,
        VINParser $parser,
        DatabaseLoader $loader,
        WMIDecoder $wmiDecoder,
        YearDecoder $yearDecoder,
        PlantDecoder $plantDecoder
    ) {
        $this->validator    = $validator;
        $this->parser       = $parser;
        $this->loader       = $loader;
        $this->wmiDecoder   = $wmiDecoder;
        $this->yearDecoder  = $yearDecoder;
        $this->plantDecoder = $plantDecoder;
    }

    /**
     * Resolve a VIN.
     */
    public function resolve(string $vin): ?array
    {
        if (!$this->validator->validate($vin)) {
            return null;
        }

        $vehicle = $this->parser->parse($vin);

        $vehicle = $this->wmiDecoder->decode($vehicle);

        $vehicle = $this->yearDecoder->decode($vehicle);

        $vehicle = $this->plantDecoder->decode($vehicle);

        $vehicle['confidence'] = $this->calculateConfidence($vehicle);

        return $vehicle;
    }

    /**
     * Calculate confidence score.
     */
    private function calculateConfidence(array $vehicle): int
    {
        $score = 0;

        if (!empty($vehicle['manufacturer'])) $score += 30;
        if (!empty($vehicle['year']))         $score += 20;
        if (!empty($vehicle['plant']))        $score += 10;
        if (!empty($vehicle['model']))        $score += 20;
        if (!empty($vehicle['engine']))       $score += 10;
        if (!empty($vehicle['body']))         $score += 10;

        return min($score, 100);
    }
}