<?php
// get products
add_action('wp_ajax_nopriv_get_products', 'get_products_callback');
add_action('wp_ajax_get_products', 'get_products_callback');

function get_products_callback()
{
    $products = get_posts(array(
        'post_type' => 'product',
        'posts_per_page' => -1
    ));

    $product_arr = array();
    foreach ($products as $product) {
        $product_obj = wc_get_product($product->ID);
        //if product type is bundle then skip
        if ($product_obj->get_type() == 'bundle') {
            continue;
        }
        $product_arr[] = array(
            'id' => $product->ID,
            'name' => $product->post_title,
            'price' => $product_obj->get_price(),
            'featured_image' => wp_get_attachment_image_src(get_post_thumbnail_id($product->ID), 'thumbnail')[0],
            'gallery' => array_map(function ($id) {
                return wp_get_attachment_image_src($id, 'full')[0];
            }, array_diff((array) $product_obj->get_gallery_image_ids(), array(get_post_thumbnail_id($product->ID)))),
            'quantity' => $product_obj->get_stock_quantity()
        );
    }

    wp_reset_postdata();

    if (empty($product_arr)) {
        $data = array(
            'status' => 'error',
            'message' => 'No products found'
        );
        echo json_encode($data);
        wp_die();
    }

    $data = array(
        'status' => 'success',
        'products' => $product_arr
    );

    echo json_encode($data);
    wp_die();
}
