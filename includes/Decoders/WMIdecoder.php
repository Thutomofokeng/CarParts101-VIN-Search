/**
 * Decode the World Manufacturer Identifier (WMI).
 *
 * Characters 1–3 of the VIN determine the manufacturer.
 *
 * @param array $vehicle
 * @return array
 */
public function decode(array $vehicle): array
{
    $manufacturers = $this->loader->load('manufacturers');

    $wmi = strtoupper($vehicle['wmi'] ?? '');

    if (isset($manufacturers[$wmi])) {
        $vehicle['manufacturer'] = $manufacturers[$wmi];
    }

    return $vehicle;
}