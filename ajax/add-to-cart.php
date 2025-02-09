<?php

add_action('wp_ajax_nopriv_add_to_cart', 'add_to_cart_callback');
add_action('wp_ajax_add_to_cart', 'add_to_cart_callback');

function add_to_cart_callback()
{
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    $product = wc_get_product($product_id);
    WC()->cart->add_to_cart($product_id, $quantity);

    $cart_contents = WC()->cart->get_cart_contents();
    $cart_contents_count = WC()->cart->get_cart_contents_count();
    $cart_total = WC()->cart->get_cart_total();

    $data = array(
        'status' => 'success',
        'cart_contents' => $cart_contents,
        'cart_contents_count' => $cart_contents_count,
        'cart_total' => $cart_total
    );

    echo json_encode($data);
    wp_die();
}
