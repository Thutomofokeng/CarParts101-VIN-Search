<?php

if (!defined('ABSPATH')) {
    exit;
}

class VINValidator
{
    /**
     * Characters that are not allowed in VINs.
     */
    private array $illegalCharacters = [
        'I',
        'O',
        'Q'
    ];

    /**
     * Validate VIN.
     */
    public function validate(string $vin): bool
    {
        $vin = strtoupper(trim($vin));

        /*
        |----------------------------------------------------------
        | Must be exactly 17 characters
        |----------------------------------------------------------
        */

        if (strlen($vin) !== 17) {
            return false;
        }

        /*
        |----------------------------------------------------------
        | Illegal characters
        |----------------------------------------------------------
        */

        foreach ($this->illegalCharacters as $character) {

            if (str_contains($vin, $character)) {
                return false;
            }

        }

        /*
        |----------------------------------------------------------
        | Checksum
        |----------------------------------------------------------
        */

        return $this->validateChecksum($vin);
    }

    /**
     * VIN checksum validation.
     */
    private function validateChecksum(string $vin): bool
    {
        $transliteration = [

            'A'=>1,'B'=>2,'C'=>3,'D'=>4,'E'=>5,'F'=>6,'G'=>7,'H'=>8,

            'J'=>1,'K'=>2,'L'=>3,'M'=>4,'N'=>5,

            'P'=>7,

            'R'=>9,

            'S'=>2,'T'=>3,'U'=>4,'V'=>5,'W'=>6,'X'=>7,'Y'=>8,'Z'=>9,

            '0'=>0,'1'=>1,'2'=>2,'3'=>3,'4'=>4,
            '5'=>5,'6'=>6,'7'=>7,'8'=>8,'9'=>9

        ];

        $weights = [

            8,7,6,5,4,3,2,10,0,9,8,7,6,5,4,3,2

        ];

        $sum = 0;

        for ($i = 0; $i < 17; $i++) {

            $char = $vin[$i];

            if (!isset($transliteration[$char])) {
                return false;
            }

            $sum += $transliteration[$char] * $weights[$i];

        }

        $remainder = $sum % 11;

        $expected = $remainder == 10
            ? 'X'
            : (string)$remainder;

        return $vin[8] === $expected;
    }
}