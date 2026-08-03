<?php
/*
Plugin Name: CarParts101 VIN Search
Description: VIN lookup integrated with WooCommerce.
Version: 0.2.0
Author: CarParts101
*/

if (!defined('ABSPATH')) {
    exit;
}

define('CP101_VIN_PATH', plugin_dir_path(__FILE__));

/*
|--------------------------------------------------------------------------
| Core Classes
|--------------------------------------------------------------------------
*/

require_once CP101_VIN_PATH . 'includes/DatabaseLoader.php';

require_once CP101_VIN_PATH . 'includes/VINParser.php';

require_once CP101_VIN_PATH . 'includes/ManufacturerResolver.php';

require_once CP101_VIN_PATH . 'includes/VehicleResolver.php';

require_once CP101_VIN_PATH . 'includes/MINIDecoder.php';

require_once CP101_VIN_PATH . 'includes/class-vehicle-database.php';

/*
|--------------------------------------------------------------------------
| WordPress
|--------------------------------------------------------------------------
*/

require_once CP101_VIN_PATH . 'includes/class-shortcode.php';

require_once CP101_VIN_PATH . 'includes/class-admin.php';

/*
|--------------------------------------------------------------------------
| Boot Plugin
|--------------------------------------------------------------------------
*/

new CP101_VIN_Admin();

new CP101_VIN_Shortcode();