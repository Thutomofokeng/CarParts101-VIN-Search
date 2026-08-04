<?php

if (!defined('ABSPATH')) {
    exit;
}

class VINDecoderFactory
{
    /**
     * Build a fully configured VIN decoder.
     *
     * @return VINDecoder
     */
    public static function make(): VINDecoder
    {
        $loader = new DatabaseLoader();

        return new VINDecoder(

            new VINValidator(),

            new VINParser(),

            $loader,

            new ManufacturerResolver($loader),

            new WMIDecoder($loader),

            new YearDecoder($loader),

            new PlantDecoder($loader)

        );
    }
}