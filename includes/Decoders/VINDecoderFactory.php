<?php

if (!defined('ABSPATH')) {
    exit;
}

class VINDecoderFactory
{
    /**
     * Build a fully configured VIN decoder.
     */
    public static function make(): VINDecoder
    {
        $loader = new DatabaseLoader();

        $manufacturerResolver = new ManufacturerResolver($loader);

        return new VINDecoder(

            new VINValidator(),

            new VINParser(),

            $manufacturerResolver,

            new WMIDecoder($loader),

            new YearDecoder($loader),

            new PlantDecoder($loader)

        );
    }
}