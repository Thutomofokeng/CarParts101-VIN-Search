// Try local database first
$db = new CP101_Vehicle_Database();
$vehicle = $db->find_vehicle($vin);

// If not found locally, use the NHTSA decoder
if (!$vehicle) {
    $vehicle = $this->decode_vin($vin);

    if (is_wp_error($vehicle)) {
        echo '<p style="color:red;">' . esc_html($vehicle->get_error_message()) . '</p>';
        return ob_get_clean();
    }

    $displayFields = [
        'Make'         => 'Make',
        'Model'        => 'Model',
        'Model Year'   => 'ModelYear',
        'Series'       => 'Series',
        'Trim'         => 'Trim',
        'Body Class'   => 'BodyClass',
        'Engine'       => 'EngineModel',
        'Fuel Type'    => 'FuelTypePrimary',
        'Drive Type'   => 'DriveType',
        'Transmission' => 'TransmissionStyle'
    ];

} else {

    // Vehicle came from our local JSON database
    $displayFields = [
        'Make'       => 'make',
        'Model'      => 'model',
        'Model Year' => 'year',
        'Plant'      => 'plant',
        'Engine'     => 'engine',
        'Body'       => 'body',
        'Drive'      => 'drive'
    ];
}

echo '<h2>Vehicle Identified</h2><table class="widefat striped">';

foreach ($displayFields as $label => $key) {
    if (!empty($vehicle[$key])) {
        echo '<tr><th>' . esc_html($label) . '</th><td>' . esc_html($vehicle[$key]) . '</td></tr>';
    }
}

echo '</table>';