<?php
/*
Plugin Name: Bundle product plugin
Description: This plugin will help you to create bundle product. [bundle_product]
Version: 1.0
Author: Rahmat Mondol
Author URI: https://rahmatmondol.com
*/

// files to include
require_once plugin_dir_path(__FILE__) . 'shortcodes/bundle-product.php';
require_once plugin_dir_path(__FILE__) . 'includes/custom-product-type.php';
require_once plugin_dir_path(__FILE__) . 'includes/enque.php';
require_once plugin_dir_path(__FILE__) . 'ajax/get-product.php';
require_once plugin_dir_path(__FILE__) . 'ajax/add-to-cart.php';
