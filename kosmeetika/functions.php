<?php
// Add custom Theme Functions here

// Remove SKU from frontend
function starkup_remove_product_page_skus( $enabled ) {
    if ( ! is_admin() && is_product() ) {
        return false;
    }

    return $enabled;
}
add_filter( 'wc_product_sku_enabled', 'starkup_remove_product_page_skus' );



// Register the script Negative Space
function theme_js() {
    wp_enqueue_script( 'theme_js', get_theme_root_uri() . '/ideaal-kosmeetika/assets/js/variationscript.js', '1.0', true );

$translation_array = array(
    'puhas_string' => esc_html__( 'Clear', 'woocommerce' ),
);
wp_localize_script( 'theme_js', 'object_name', $translation_array );
}
add_action('wp_enqueue_scripts', 'theme_js');