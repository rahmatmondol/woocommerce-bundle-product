<?php

add_action('wp_ajax_nopriv_add_to_cart', 'add_to_cart_callback');
add_action('wp_ajax_add_to_cart', 'add_to_cart_callback');

function add_to_cart_callback()
{
    $products = isset($_POST['data']) ? $_POST['data'] : [];

    if (empty($products)) {
        $data = array(
            'status' => 'error',
            'message' => 'No products found'
        );
        echo json_encode($data);
        wp_die();
    }

    //add cart cart
    foreach ($products as $product) {
        WC()->cart->add_to_cart($product['id'], $product['quantity'], 0, []);
    }


    $data = array(
        'status' => 'success',
        'message' => 'Product added to cart'
    );

    echo json_encode($data);
    wp_die();
}
