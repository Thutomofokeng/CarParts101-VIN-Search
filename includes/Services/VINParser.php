<?php

if (!defined('ABSPATH')) {
    exit;
}

class VINParser
{
    /**
     * Parse a VIN into its standard ISO 3779 components.
     *
     * @param string $vin
     * @return array
     */
    public function parse(string $vin): array
    {
        // Normalise the VIN
        $vin = strtoupper(trim($vin));

        return [

            /*
            |--------------------------------------------------------------------------
            | Full VIN
            |--------------------------------------------------------------------------
            */

            'vin' => $vin,

            /*
            |--------------------------------------------------------------------------
            | World Manufacturer Identifier (Characters 1-3)
            |--------------------------------------------------------------------------
            */

            'wmi' => substr($vin, 0, 3),

            /*
            |--------------------------------------------------------------------------
            | Vehicle Descriptor Section (Characters 4-8)
            |--------------------------------------------------------------------------
            */

            'vds' => substr($vin, 3, 5),

            /*
            |--------------------------------------------------------------------------
            | Vehicle Identifier Section (Characters 10-17)
            |--------------------------------------------------------------------------
            */

            'vis' => substr($vin, 9, 8),

            /*
            |--------------------------------------------------------------------------
            | Individual Character Codes
            |--------------------------------------------------------------------------
            */

            'check_digit' => $vin[8],

            'year_code' => $vin[9],

            'plant_code' => $vin[10],

            'serial' => substr($vin, 11, 6),

            /*
            |--------------------------------------------------------------------------
            | Future Convenience Fields
            |--------------------------------------------------------------------------
            */

            'country_code' => $vin[0],

            'manufacturer_code' => substr($vin, 0, 3),

            /*
            |--------------------------------------------------------------------------
            | Reserved For Future Decoders
            |--------------------------------------------------------------------------
            */

            'manufacturer' => null,

            'year' => null,

            'plant' => null,

            'model' => null,

            'series' => null,

            'generation' => null,

            'trim' => null,

            'body' => null,

            'engine' => null,

            'fuel' => null,

            'drive' => null,

            'transmission' => null,

            /*
            |--------------------------------------------------------------------------
            | Metadata
            |--------------------------------------------------------------------------
            */

            'confidence' => 0,

            'source' => 'VIN Parser'

        ];
    }
}