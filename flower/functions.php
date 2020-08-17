<?php
function parent_styles()
{
    wp_register_style('fonts', get_stylesheet_directory_uri() . '/css/fontawesome/css/all.min.css');
    wp_enqueue_style('fonts');
    wp_register_style('bootstrap', get_stylesheet_directory_uri() . '/css/bootstrap.min.css');
    wp_enqueue_style('bootstrap');
    wp_register_style('owlcss', get_stylesheet_directory_uri() . '/css/owl.min.css');
    wp_enqueue_style('owlcss');
}

add_action('wp_enqueue_scripts', 'parent_styles');

function my_enqueue_scripts()
{
    wp_register_script('owl', get_stylesheet_directory_uri() . '/js/owl.carousel.min.js', '', '', true);
    wp_enqueue_script('owl');
    wp_register_script('aflowers', get_stylesheet_directory_uri() . '/js/aflowers.min.js', '', '', true);
    wp_enqueue_script('aflowers');
}

add_action('wp_enqueue_scripts', 'my_enqueue_scripts');
/*SKU in order start*/
add_filter('woocommerce_cart_item_name', 'showing_sku_in_cart_items', 99, 3);
function showing_sku_in_cart_items($item_name, $cart_item, $cart_item_key)
{
    $product = $cart_item['data'];
    $sku = $product->get_sku();
    if (empty($sku)) return $item_name;
    // Add the sku
    $item_name .= '<br><small class="product-sku">' . __("SKU: ", "woocommerce") . $sku . '</small>';

    return $item_name;
}

/**
 * Adds SKUs and product images to WooCommerce order emails
 */
function aflower_add_sku_to_wc_emails($args)
{

    $args['show_sku'] = true;
    return $args;
}

add_filter('woocommerce_email_order_items_args', 'aflower_add_sku_to_wc_emails');


/*SKU in order end*/
/**
 * Exclude products from a particular category on the shop page
 */
function custom_pre_get_posts_query($q)
{
    $tax_query = (array)$q->get('tax_query');
    $tax_query[] = array(
        'taxonomy' => 'product_cat',
        'field' => 'slug',
        'terms' => array('suggest'),
        'operator' => 'NOT IN'
    );

    $q->set('tax_query', $tax_query);
}

add_action('woocommerce_product_query', 'custom_pre_get_posts_query');
/*Hide category end*/

/*Sort sku start*/
add_filter('woocommerce_get_catalog_ordering_args', 'am_woocommerce_catalog_orderby');
function am_woocommerce_catalog_orderby($args)
{
    $args['meta_key'] = '_sku';
    $args['orderby'] = 'meta_value';
    $args['order'] = 'desc';
    return $args;
}

/*Sort SKU end*/
/*Alt from title*/
/* Automatically set the image Title, Alt-Text, Caption & Description upon upload
-----------------------------------------------------------------------*/

add_action('add_attachment', 'my_set_image_meta_upon_image_upload');

function my_set_image_meta_upon_image_upload($post_ID)
{
    // Check if uploaded file is an image, else do nothing
    if (wp_attachment_is_image($post_ID)) {
        $my_image_title = get_post($post_ID)->post_title;
        $my_image_title = preg_replace('%\s*[-_\s]+\s*%', ' ',
            $my_image_title);

        $my_image_title = ucwords(strtolower($my_image_title));

        // Set the image Alt-Text
        update_post_meta($post_ID, '_wp_attachment_image_alt',
            $my_image_title);
    }
}

/*Alt from title end*/
/*VAT text*/
function custom_cart_totals_order_total_html($value)
{
    $value = '<strong>' . WC()->cart->get_total() . '</strong> ';

// If prices are tax inclusive, show taxes here.
    if (wc_tax_enabled() && WC()->cart->display_prices_including_tax()) {
        $tax_string_array = array();
        $cart_tax_totals = WC()->cart->get_tax_totals();
        if (get_option('woocommerce_tax_total_display') === 'itemized') {
            foreach ($cart_tax_totals as $code => $tax) {
                $tax_string_array[] = sprintf('%s %s', $tax->formatted_amount, $tax->label);
            }
        } elseif (!empty($cart_tax_totals)) {
            $tax_string_array[] = sprintf('%s %s', wc_price(WC()->cart->get_taxes_total(true, true)), WC()->countries->tax_or_vat());
        }

        if (!empty($tax_string_array)) {
            $taaflowerble_address = WC()->customer->get_taaflowerble_address();
            $estimated_text = '';
            $value .= '<small class="includes_tax">' . sprintf(__('(incl. VAT)', 'woocommerce'), implode(', ', $tax_string_array) . $estimated_text) . '</small>';
        }
    }
    return $value;
}

add_filter('woocommerce_cart_totals_order_total_html', 'custom_cart_totals_order_total_html', 20, 1);
/*VAT text end*/
/*SKU*/
add_action('woocommerce_before_shop_loop_item_title', 'shop_sku');
function shop_sku()
{
    global $product;
    if (!empty($product->sku)) {
        echo '<h6 itemprop="productID" class="sku"># ' . $product->sku . '</h6>';
    } else {
        echo '<h6 itemprop="productID" class="sku">#</h6>';
    }
}

/*SKU end*/
//Cupone after update
// hide coupon field on cart page
add_action('woocommerce_before_checkout_form', 'remove_checkout_coupon_form', 9);
function remove_checkout_coupon_form()
{
    remove_action('woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10);
}

// Remove sub-total
add_filter('woocommerce_get_order_item_totals', 'remove_subtotal_from_orders_total_lines', 100, 1);
function remove_subtotal_from_orders_total_lines($totals)
{
    unset($totals['cart_subtotal']);
    return $totals;
}

//Mobile menu
add_filter('storefront_handheld_footer_bar_links', 'aflower_remove_handheld_footer_links');
function aflower_remove_handheld_footer_links($links)
{
    unset($links['my-account']);
    unset($links['search']);

    return $links;
}

add_filter('storefront_handheld_footer_bar_links', 'aflower_add_home_link');
function aflower_add_home_link($links)
{
    $new_links = array(
        'home' => array(
            'priority' => 10,
            'callback' => 'aflower_home_link',
        ),
    );

    $links = array_merge($new_links, $links);

    return $links;
}

function aflower_home_link()
{
    echo '<a href="' . esc_url(home_url('/')) . '">' . __('Home') . '</a>';
}

//Remove related products
remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);

// Remove the sorting dropdown from Woocommerce
remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);
// Remove the result count from WooCommerce

add_action('init', 'sorting_delay_remove');
function sorting_delay_remove()
{
    remove_action('woocommerce_after_shop_loop', 'woocommerce_result_count', 20);
    remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
    remove_action('woocommerce_after_shop_loop', 'woocommerce_catalog_ordering', 10);
    remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 10);
}

/*CatID*/
add_action('admin_init', 'admin_area_ID');
function admin_area_ID()
{
    foreach (get_taxonomies() as $taxonomy) {
        add_action("manage_edit-${taxonomy}_columns", 'tax_add_col');
        add_filter("manage_edit-${taxonomy}_sortable_columns", 'tax_add_col');
        add_filter("manage_${taxonomy}_custom_column", 'tax_show_id', 10, 3);
    }
    add_action('admin_print_styles-edit-tags.php', 'tax_id_style');
    function tax_add_col($columns)
    {
        return $columns + array('tax_id' => 'ID');
    }

    function tax_show_id($v, $name, $id)
    {
        return 'tax_id' === $name ? $id : $v;
    }

    function tax_id_style()
    {
        print '<style>#tax_id{width:4em}</style>';
    }

    add_filter('manage_posts_columns', 'posts_add_col', 5);
    add_action('manage_posts_custom_column', 'posts_show_id', 5, 2);
    add_filter('manage_pages_columns', 'posts_add_col', 5);
    add_action('manage_pages_custom_column', 'posts_show_id', 5, 2);
    add_action('admin_print_styles-edit.php', 'posts_id_style');
    function posts_add_col($defaults)
    {
        $defaults['wps_post_id'] = __('ID');
        return $defaults;
    }

    function posts_show_id($column_name, $id)
    {
        if ($column_name === 'wps_post_id') echo $id;
    }

    function posts_id_style()
    {
        print '<style>#wps_post_id{width:4em}</style>';
    }
}

/*Credit*/
add_action('init', 'custom_remove_footer_credit', 10);

function custom_remove_footer_credit()
{
    remove_action('storefront_footer', 'storefront_credit', 20);
}

/**/
function yith_remove_notice($show_license_notice)
{
    return false;
}

add_filter('yith_plugin_fw_show_activate_license_notice', 'yith_remove_notice', 99999999999999999, 1);
/**/

/*additional*/
require 'inc/storefront-template-functions.php';
//Header start

/**
 * Adds a top bar to Storefront, before the header.
 */
function storefront_add_topbar()
{
    ?>
    <div class="nav-top">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-sm-12 text-md-left text-right">
                    <?php $aflower_facebook_header = get_field('aflower_facebook_header', 'option'); ?>
                    <?php if ($aflower_facebook_header) { ?>
                        <a class="topbar-social-item fab fa-facebook-f"
                           href="<?php echo $aflower_facebook_header['url']; ?>"
                           target="<?php echo $aflower_facebook_header['target']; ?>"><?php echo $aflower_facebook_header['title']; ?></a>
                    <?php } ?>
                    <?php $aflower_instagram_header = get_field('aflower_instagram_header', 'option'); ?>
                    <?php if ($aflower_instagram_header) { ?>
                        <a class="topbar-social-item fab fa-instagram"
                           href="<?php echo $aflower_instagram_header['url']; ?>"
                           target="<?php echo $aflower_instagram_header['target']; ?>"><?php echo $aflower_instagram_header['title']; ?></a>
                    <?php } ?>
                </div>
                <div class="col-md-6 col-sm-12 text-md-right text-right text-right-flower">
                    <?php $aflower_telephone_header = get_field('aflower_telephone_header', 'option'); ?>
                    <?php if ($aflower_telephone_header) { ?>
                        <a href="<?php echo $aflower_telephone_header['url']; ?>"
                           target="<?php echo $aflower_telephone_header['target']; ?>"><?php echo $aflower_telephone_header['title']; ?></a>
                    <?php } ?>
                    <?php $aflower_telephone_header_mob = get_field('aflower_telephone_header_mob', 'option'); ?>
                    <?php if ($aflower_telephone_header_mob) { ?>
                        <a href="<?php echo $aflower_telephone_header_mob['url']; ?>"
                           target="<?php echo $aflower_telephone_header_mob['target']; ?>"><?php echo $aflower_telephone_header_mob['title']; ?></a>
                    <?php } ?>
                    <?php $aflower_email_header = get_field('aflower_email_header', 'option'); ?>
                    <?php if ($aflower_email_header) { ?>
                        <a href="<?php echo $aflower_email_header['url']; ?>"
                           target="<?php echo $aflower_email_header['target']; ?>"><?php echo $aflower_email_header['title']; ?></a>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <?php
}

add_action('storefront_before_header', 'storefront_add_topbar');
//End first line

// Testimonials
function storefront_add_testimonials()
{
    ?>
    <!-- about section -->
    <section id="about" class="section-aflower box-shadow-3d">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mt-5">
                    <div class="recent-works">
                        <h3><?php the_field('smiles_header', 'option'); ?></h3>
                        <div id="works" class="owl-carousel">
                            <?php if (have_rows('smiles', 'option')): ?>
                                <?php while (have_rows('smiles', 'option')) : the_row(); ?>
                                    <div class="work-item">
                                        <?php $foto = get_sub_field('client_foto');
                                        if (!empty($foto)): ?>
                                            <img src="<?php echo $foto['url']; ?>" alt="<?php echo $foto['alt']; ?>"/>
                                        <?php endif; ?>
                                    </div>
                                <?php endwhile; ?>
                            <?php else : ?>
                            <? endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-7 col-md-offset-1 mt-5">
                    <div class="welcome-block">
                        <h3><?php the_field('review_header', 'option'); ?></h3>
                        <div class="message-carousel owl-carousel">
                            <?php if (have_rows('testimonials', 'option')): ?>
                                <?php while (have_rows('testimonials', 'option')) : the_row(); ?>
                                    <div class="message-body">
                                        <?php $avatar = get_sub_field('client_avatar');
                                        if (!empty($avatar)): ?>
                                            <img class="pull-left" src="<?php echo $avatar['url']; ?>"
                                                 alt="<?php echo $avatar['alt']; ?>"/>
                                        <?php endif; ?>
                                        <?php the_sub_field('client_text'); ?>
                                    </div>
                                <?php endwhile; ?>
                            <?php else : ?>


                            <? endif; ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- end about section -->
<?php }

add_action('storefront_before_footer', 'storefront_add_testimonials');

// End Testimonials

//cart
/**
 * Ensure cart contents update when products are added to the cart via AJAX
 */
function my_header_add_to_cart_fragment($fragments)
{
    ob_start();
    $count = WC()->cart->cart_contents_count;
    ?><a class="cart-header" href="<?php echo WC()->cart->get_cart_url(); ?>"
         title="<?php _e('View your shopping cart'); ?>">
    <img src="/media/img/bag.png" class="bag" alt="bag-cart"><?php
    if ($count > 0) {
        ?>
        <span class="header-icons-total"><?php echo esc_html($count); ?></span>
        <?php
    } else { ?>
        <span class="header-icons-total">0</span>
    <?php }
    ?>

    </a><?php

    $fragments['.cart-header'] = ob_get_clean();

    return $fragments;
}

add_filter('woocommerce_add_to_cart_fragments', 'my_header_add_to_cart_fragment');


//Header end

add_filter('woocommerce_states', 'aflowers_states_estonia');
function aflowers_states_estonia($states)
{
    $states['EE'] = array(
        'TALLINN' => __('Tallinn', 'woocommerce'),
        'MAARDU' => __('Maardu', 'woocommerce'),
        'LAAGRI' => __('Laagri', 'woocommerce'),
        'JÜRI' => __('Jüri', 'woocommerce'),
        'SAUE' => __('Saue', 'woocommerce'),
        'KEILA' => __('Keila', 'woocommerce'),
        'VIIMSI' => __('Viimsi', 'woocommerce'),
        'HARJUMAA' => __('Harjumaa', 'woocommerce'),

    );

    $fields['shipping']['shipping_state']['type'] = 'select';
    $fields['shipping']['shipping_state']['options'] = $states;
    $fields['shipping']['shipping_state']['class'] = 'update_totals_on_change';

    return $states;
}

/*add_filter( 'woocommerce_checkout_fields' , 'custom_override_checkout_fields' );
function custom_override_checkout_fields( $fields ) {

    $fields['shipping']['shipping_state'] = array(
        'required' => 1,
        'type' => 'select',
        'options' => array(
		 '' => __( 'Select your city' ),
		'HARJUMAA' => 'Harjumaa',
        'TALLINN' => 'Tallinn',
        'MAARDU' => 'Maardu',

	),
        'class' => array ('address-field', 'update_totals_on_change' )
    );

    return $fields;
}*/

function woo_add_my_country($country)
{
    $country["MY-EE"] = 'Estonia';
    return $country;
}

add_filter('woocommerce_countries', 'woo_add_my_country', 10, 1);

/*
Addition of WooCommerce Custom Checkout Field
*/

function aflower_custom_checkout_fields($fields)
{
    $fields['aflower_extra_fields'] = array(
        'aflower_text_field' => array(
            'type' => 'textarea',
            'required' => false,
            'label' => __('Input Text Field'),
            'id' => 'Card',
        )
    );
    return $fields;
}

add_filter('woocommerce_checkout_fields', 'aflower_custom_checkout_fields');
function aflower_extra_checkout_fields()
{
    $checkout = WC()->checkout(); ?>
    <div class="extra-fields">
        <h3><?php _e('Postcard'); ?></h3>

        <?php
        foreach ($checkout->checkout_fields['aflower_extra_fields'] as $key => $field) : ?>
            <?php woocommerce_form_field($key, $field, $checkout->get_value($key)); ?>
        <?php endforeach; ?>
    </div>
<?php }

add_action('woocommerce_checkout_after_customer_details', 'aflower_extra_checkout_fields');

/*
Save the Data of Custom Checkout WooCommerce Fields
*/
function aflower_save_extra_checkout_fields($order_id, $posted)
{
    if (isset($posted['aflower_text_field'])) {
        update_post_meta($order_id, '_aflower_text_field', sanitize_text_field($posted['aflower_text_field']));
    }
}

add_action('woocommerce_checkout_update_order_meta', 'aflower_save_extra_checkout_fields', 10, 2);

/*
Display  the Data of  WooCommerce Custom Fields to User
*/
function aflower_display_order_data($order_id)
{ ?>
    <h2><?php _e('Extra Information'); ?></h2>
    <table class="additional_info">
        <tbody>
        <tr>
            <th><?php _e('Card text:'); ?></th>
            <td><?php echo get_post_meta($order_id, '_aflower_text_field', true); ?></td>
        </tr>
        </tbody>
    </table>
<?php }

add_action('woocommerce_thankyou', 'aflower_display_order_data', 20);
add_action('woocommerce_view_order', 'aflower_display_order_data', 20);

/*
Display WooCommerce Admin Custom Order Fields

This code snippet will function as the shipping and billing address data and reveal inputs when the user clicks the little pencil icon.
*/
function aflower_display_order_data_in_admin($order)
{ ?>
    <div class="order_data_column" style="width:100%;">
        <h4><?php _e('Card:', 'woocommerce'); ?><a href="#" class="edit_address"><?php _e('Edit', 'woocommerce'); ?></a>
        </h4>
        <div class="address" style="width:100%;">
            <?php
            echo '<p><strong>' . __('Text') . ':</strong>' . get_post_meta($order->id, '_aflower_text_field', true) . '</p>'; ?>
        </div>
        <div class="edit_address">
            <?php woocommerce_wp_text_input(array('id' => '_aflower_text_field', 'label' => __('Card text'), 'wrapper_class' => '_billing_company_field')); ?>
        </div>
    </div>
<?php }

add_action('woocommerce_admin_order_data_after_order_details', 'aflower_display_order_data_in_admin');


function aflower_save_extra_details($post_id, $post)
{
    update_post_meta($post_id, '_aflower_text_field', wc_clean($_POST['_aflower_text_field']));
}

add_action('woocommerce_process_shop_order_meta', 'aflower_save_extra_details', 45, 2);


/*Hide price*/
add_filter('woocommerce_get_price_html', "aflower_only_sale_price", 99, 2);

function aflower_only_sale_price($price, $product)
{
    if (!is_cart() && !is_checkout() && !is_ajax()) {
        if ($product->is_type('simple') || $product->is_type('variation')) {
            return regularPriceHTML_for_simple_and_variation_product($price, $product);
        }
    }
    return $price;
}

function regularPriceHTML_for_simple_and_variation_product($price, $product)
{
    return wc_price($product->get_price());
}


// Sale text
add_filter('woocommerce_sale_flash', 'woocommerce_custom_sale_text', 10, 3);
function woocommerce_custom_sale_text($text, $post, $_product)
{
    $hit = __('Hit', 'storefront');
    return '<span class="onsale">' . $hit . '</span>';
}