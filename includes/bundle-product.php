<?php

// bundle product shortcode
function bundle_product_shortcode()
{

    // get all products

    ob_start();
?>

    <div class="bundle-conteiner">
        <div class="products-section">
            <div class="products" id="products"></div>
        </div>
        <div class="bundle-info" style="display: none;">
            <h3>Bundle Info</h3>
            <p>Bundle Products: <span id="bundle-products"></span></p>
            <p>Price: <span id="bundle-price">100</span><?php echo get_woocommerce_currency_symbol(); ?></p>
            <button class="add-to-cart">Add to cart</button>
        </div>
    </div>

    <script>
        jQuery(document).ready(function($) {

            // show loading animation
            $('<div id="loading-animation">Loading...</div>').appendTo('#products');

            // get products
            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'get',
                data: {
                    action: 'get_products'
                },
                success: function(response) {
                    data = JSON.parse(response);
                    console.log(data);
                    if (data.status == 'success') {
                        $('#loading-animation').remove();
                        if (data.products.length == 0) {
                            $('#products').append('<p>No products found</p>');
                            return;
                        }
                        data.products.forEach(product => {
                            $('#products').append(`
                                <div class="product-card" data-id="${product.id}" data-price="${product.price}" data-quantity="${product.quantity}">
                                    <img class="product-image" src="${product.image}" alt="${product.name}">
                                    <span class="product-name">${product.name}</span>
                                     <div class="quantity-controls" style="display: none;">
                                        <span class="quantity-minus quntity-btn">-</span>
                                        <span class="product-quantity">1</span>
                                        <span class="quantity-plus quntity-btn">+</span>
                                    </div>
                                </div>
                            `);
                        });

                    } else {
                        $('#loading-animation').remove();

                        console.log(data.message);
                    }
                }
            })



            // select product
            $(document).on('click', '.product-card .product-image', function() {
                $(this).parent().toggleClass('active');
                // show quantity controls
                $(this).parent().find('.quantity-controls').toggle();
                calculatePrice();
            });

            // calculate price
            function calculatePrice() {
                let price = 0;
                let products = [];

                $('.product-card.active').each(function() {
                    price += parseFloat($(this).data('price')) * parseInt($(this).find('.product-quantity').text());
                    products.push({
                        id: $(this).data('id'),
                        quantity: parseInt($(this).find('.product-quantity').text()),
                    });
                });
                $('#bundle-price').text(price);
                if (products.length > 0) {
                    $('.bundle-info').show();
                } else {
                    $('.bundle-info').hide();
                }
                $('#bundle-products').text(products.length);
            }

            // add to cart function
            $(document).on('click', '.add-to-cart', function() {
                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    type: 'post',
                    data: {
                        action: 'add_to_cart',
                        data: $('.product-card.active').map(function() {
                            return {
                                id: $(this).data('id'),
                                quantity: parseInt($(this).find('.product-quantity').text()),
                            };
                        }).get()
                    },
                    success: function(response) {
                        console.log(response);
                    }
                })
            })

            // quantity controls
            $(document).on('click', '.quntity-btn', function() {
                let quantity = parseInt($(this).parent().find('.product-quantity').text());
                if ($(this).hasClass('quantity-plus')) {
                    quantity++;
                } else {
                    quantity = quantity > 1 ? quantity - 1 : 0;
                }

                //if quantity is 0 then remove product
                if (quantity == 0) {
                    $(this).parent().parent().removeClass('active');
                    $(this).parent().hide();
                }

                $(this).parent().find('.product-quantity').text(quantity);
                calculatePrice();
            })


        });
    </script>

    <style>
        .bundle-conteiner h1,
        .bundle-conteiner h2,
        .bundle-conteiner h3,
        .bundle-conteiner p,
        .bundle-conteiner button,
        .bundle-conteiner span {
            margin: 0;
            padding: 0;
        }

        #products {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 10px;
        }

        #products .product-card {
            display: flex;
            flex-flow: column;
            text-align: center;
            padding: 10px;
            border: 1px solid #e3e3e3;
            border-radius: 4px;
            opacity: 0.5;
            transition: all 0.3s;
            gap: 5px;
            min-height: 248px;
            justify-content: space-around;
        }


        #products .product-card.active {
            border: 1px solid #000;
            box-shadow: 3px 2px 15px 0px #0000000d;
            opacity: 1;
        }

        #products .product-card .product-image {
            filter: grayscale(100%) brightness(1);
            cursor: pointer;
        }

        #products .product-card.active .product-image {
            filter: grayscale(0%) brightness(1);
        }

        #products .product-card .quantity-controls {
            border: 1px solid;
            border-radius: 3px;
            display: flex;
            justify-content: space-between;
        }

        #products .product-card .quantity-controls .quntity-btn {
            padding: 0px 10px;
            font-size: 18px;
            color: #000;
            cursor: pointer;
        }

        .bundle-info {
            border: 1px solid #f2f2f2;
            margin-top: 25px;
            position: sticky;
            bottom: 5px;
            display: flex;
            flex-flow: column;
            gap: 10px;
            padding: 20px;
            max-width: 400px;
            border-radius: 10px;
            background: #fff;
            box-shadow: 3px 3px 10px 0px #0001;
        }

        .bundle-info h3 {
            font-size: 18px;
            font-weight: 600;
            text-align: center;
        }

        .bundle-info .add-to-cart {
            background: transparent;
            border: 1px solid #000;
            padding: 5px 40px;
            border-radius: 20px;
            color: #000;
        }
    </style>

<?php
    return ob_get_clean();
}

add_shortcode('bundle_product', 'bundle_product_shortcode');
