public function decode(array $vehicle): array
{
    $plants = $this->loader->load('plants');

    $plantCode = strtoupper($vehicle['plant_code'] ?? '');

    if (!isset($plants[$plantCode])) {
        return $vehicle;
    }

    if (is_array($plants[$plantCode])) {
        $vehicle['plant'] = $plants[$plantCode]['name'] ?? null;
    } else {
        $vehicle['plant'] = $plants[$plantCode];
    }

    return $vehicle;
}