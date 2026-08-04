<?php

if (!defined('ABSPATH')) {
    exit;
}

class MINIDecoder implements DecoderInterface
{
    /**
     * @var DatabaseLoader
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
     * Decode MINI VIN information.
     */
    public function decode(array $vehicle): array
    {
        if (($vehicle['manufacturer'] ?? '') !== 'MINI') {
            return $vehicle;
        }

        $models = $this->loader->loadManufacturerModels('Mini');

        // Characters 4–7
        $modelCode = strtoupper(substr($vehicle['vin'], 3, 4));

        if (!isset($models[$modelCode])) {
            return $vehicle;
        }

        $model = $models[$modelCode];

        return array_merge($vehicle, [

            'series'       => $model['series'] ?? null,
            'model'        => $model['model'] ?? null,
            'generation'   => $model['generation'] ?? null,
            'trim'         => $model['trim'] ?? null,
            'body'         => $model['body'] ?? null,
            'drive'        => $model['drive'] ?? null,
            'fuel'         => $model['fuel'] ?? null,
            'transmission' => $model['transmission'] ?? null,
            'engine'       => $model['engine'] ?? null,
            'production'   => $model['production'] ?? null,

        ]);
    }
}