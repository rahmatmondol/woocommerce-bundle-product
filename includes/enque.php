<?php 

function bundle_product_scripts() {
    wp_enqueue_script( 'jquery' );
}
add_action( 'wp_enqueue_scripts', 'bundle_product_scripts' );
