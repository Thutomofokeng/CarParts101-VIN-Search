<?php
/*
Plugin Name: CarParts101 VIN Search
Description: VIN lookup integrated with WooCommerce.
Version: 0.3.0
Author: CarParts101
*/

if (!defined('ABSPATH')) {
    exit;
}

define('CP101_VIN_PATH', plugin_dir_path(__FILE__));

/*
|--------------------------------------------------------------------------
| Contracts
|--------------------------------------------------------------------------
*/

require_once CP101_VIN_PATH . 'includes/Contracts/DecoderInterface.php';

/*
|--------------------------------------------------------------------------
| Decoders
|--------------------------------------------------------------------------
*/

require_once CP101_VIN_PATH . 'includes/Decoders/WMIDecoder.php';
require_once CP101_VIN_PATH . 'includes/Decoders/YearDecoder.php';
require_once CP101_VIN_PATH . 'includes/Decoders/PlantDecoder.php';
require_once CP101_VIN_PATH . 'includes/Decoders/EngineDecoder.php';
require_once CP101_VIN_PATH . 'includes/Decoders/BodyDecoder.php';

/*
|--------------------------------------------------------------------------
| Manufacturers
|--------------------------------------------------------------------------
*/

require_once CP101_VIN_PATH . 'includes/Manufacturers/MINIDecoder.php';

/*
|--------------------------------------------------------------------------
| Services
|--------------------------------------------------------------------------
*/

require_once CP101_VIN_PATH . 'includes/Services/DatabaseLoader.php';
require_once CP101_VIN_PATH . 'includes/Services/VINValidator.php';
require_once CP101_VIN_PATH . 'includes/Services/VINParser.php';
require_once CP101_VIN_PATH . 'includes/Services/ManufacturerResolver.php';
require_once CP101_VIN_PATH . 'includes/Services/VINDecoder.php';
require_once CP101_VIN_PATH . 'includes/Services/VINDecoderFactory.php';

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