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
            <p>Bundle Selected: <span id="bundle-products"></span></p>
            <p>Price: <span id="bundle-price">100</span><?php echo get_woocommerce_currency_symbol(); ?></p>
            <button class="add-to-cart">Add to cart</button>
        </div>

        <div class="pupup-gallery" style="display: none;">
            <div class="gallery-content">
                <div class="gallery-images"></div>
                <div class="gallery-controls">
                    <span class="gallery-close">Close</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        jQuery(document).ready(function($) {

            // show loading animation
            $('<div id="loading-animation">Loading...</div>').appendTo('#products');

            var products = [];

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
                        products = data.products;
                        data.products.forEach(product => {

                            let view_button = '';
                            if (product.gallery.length > 0) {
                                view_button = `<span class="product-view" style="display: none;" data-id="${product.id}">
                                        <svg viewBox="0 0 3 3" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M1.25 0.25c-0.551 0-1 0.449-1 1s0.449 1 1 1a0.994 0.994 0 0 0 0.612-0.211l0.675 0.675a0.125 0.125 0 0 0 0.177 0 0.125 0.125 0 0 0 0-0.177l-0.674-0.675A0.994 0.994 0 0 0 2.25 1.25c0-0.551-0.449-1-1-1m0 0.25c0.416 0 0.75 0.334 0.75 0.75s-0.334 0.75-0.75 0.75-0.75-0.334-0.75-0.75 0.334-0.75 0.75-0.75m0 0.25a0.125 0.125 0 0 0-0.125 0.125v0.25H0.875a0.125 0.125 0 0 0-0.125 0.125 0.125 0.125 0 0 0 0.125 0.125h0.25v0.25a0.125 0.125 0 0 0 0.125 0.125 0.125 0.125 0 0 0 0.125-0.125v-0.25h0.25a0.125 0.125 0 0 0 0.125-0.125 0.125 0.125 0 0 0-0.125-0.125h-0.25V0.875a0.125 0.125 0 0 0-0.125-0.125" style="fill: #c6c6c6;"></path>
                                        </svg>
                                    </span>`;
                            }

                            $('#products').append(`
                                <div class="product-card" data-id="${product.id}" data-price="${product.price}" data-quantity="${product.quantity}">
                                  ${view_button}
                                    <img class="product-image" src="${product.featured_image}" alt="${product.name}">
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



            // show gallery
            $(document).on('click', '.product-card .product-view', function() {
                let product_id = $(this).data('id');
                let product = products.find(product => product.id == product_id);
                if (!product.gallery.length > 0) {
                    return;
                }
                let gallery = product.gallery.map(image => `<img src="${image}" alt="${product.name}">`).join('');
                $('.gallery-images').html(gallery);
                $('.pupup-gallery').fadeIn('slow');
            });

            // close gallery
            $(document).on('click', '.gallery-close', function() {
                $('.pupup-gallery').fadeOut('fast');
            });

            // close gallery on outside click
            $(document).on('click', '.pupup-gallery', function(e) {
                if (e.target !== this) {
                    return;
                }
                $('.pupup-gallery').fadeOut('fast');
            });

            // select product
            $(document).on('click', '.product-card .product-image', function() {
                $(this).parent().toggleClass('active');
                // show quantity controls
                $(this).parent().find('.quantity-controls').toggle();
                $(this).parent().find('.product-view').toggle();
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
        .popup-gallery {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: none;
        }

        .gallery-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            transition: all 0.3s;
        }

        .gallery-images {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            max-height: 500px;
            overflow: auto;
        }

        .gallery-controls {
            display: flex;
            justify-content: flex-end;
        }

        .gallery-close {
            cursor: pointer;
        }


        .pupup-gallery {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #00000096;
            z-index: 99;
        }

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
            position: relative;
        }


        #products .product-card.active {
            box-shadow: 0px 0px 4px 2px #0001;
            opacity: 1;
        }

        #products .product-card .product-image {
            filter: grayscale(100%) brightness(1);
            cursor: pointer;
            max-width: 200px;
            max-height: 200px;
            object-fit: fill;
            object-position: center;
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
            box-shadow: 0px 0px 4px 2px #0001;
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
            transition: all 0.3s;
        }

        .bundle-info .add-to-cart:hover {
            background: #000;
            color: #fff;
        }

        #products .product-card .product-view {
            position: absolute;
            top: 0px;
            right: 0px;
            z-index: 44;
            width: 34px;
            cursor: pointer;
            padding: 6px;
        }
    </style>

    <!-- responsive css -->

    <style>
        @media (max-width: 768px) {
            #products {
                grid-template-columns: repeat(3, 1fr);
            }

            .gallery-content {
                width: 90%;
                max-width: 90%;
            }
        }

        @media (max-width: 480px) {
            #products {
                grid-template-columns: repeat(2, 1fr);
            }

            .gallery-content {
                width: 90%;
                max-width: 90%;
            }
        }
    </style>
<?php
    return ob_get_clean();
}

add_shortcode('bundle_product', 'bundle_product_shortcode');
