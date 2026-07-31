<?php

interface DecoderInterface
{
    /**
     * Enrich the vehicle array with decoded information.
     *
     * @param array $vehicle
     * @return array
     */
    public function decode(array $vehicle): array;
}