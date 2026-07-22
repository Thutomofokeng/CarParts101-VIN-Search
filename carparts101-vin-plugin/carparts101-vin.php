<?php
/*
Plugin Name: CarParts101 VIN Search
Description: VIN lookup integrated with WooCommerce.
Version: 0.1.0
*/

if(!defined('ABSPATH')) exit;

define('CP101_VIN_PATH', plugin_dir_path(__FILE__));

require_once CP101_VIN_PATH.'includes/class-admin.php';
require_once CP101_VIN_PATH.'includes/class-shortcode.php';

new CP101_VIN_Admin();
new CP101_VIN_Shortcode();
