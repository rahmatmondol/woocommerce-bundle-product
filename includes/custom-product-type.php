<?php
add_action('init', 'register_bundle_product_type');

function register_bundle_product_type() {
    class WC_Product_Bundle extends WC_Product {
        public function __construct($product) {
            $this->product_type = 'bundle';
            parent::__construct($product);
        }

        // Add any custom methods or override existing ones here
    }
}

add_filter('product_type_selector', 'add_bundle_product_type');

function add_bundle_product_type($types) {
    $types['bundle'] = __('Bundle', 'woocommerce');
    return $types;
}