<?php
/**
 * Single Product Price, including microdata for SEO
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/price.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

$classes = array();
if($product->is_on_sale()) $classes[] = 'price-on-sale';
if(!$product->is_in_stock()) $classes[] = 'price-not-in-stock'; ?>
<?php  if($product->is_type('simple')){ ?>
	<div class="price-wrapper">
		<p class="price product-page-price <?php echo implode(' ', $classes); ?>">
	  <?php echo $product->get_price_html(); ?></p>
	</div>
<?php } ?>

<?php  if($product->is_type('variable')){
	echo '<div class="row align-top">';
    foreach($product->get_available_variations() as $variation ){      
		
		// Attributes
        $attributes = array();
        foreach( $variation['attributes'] as $key => $value ){
			$toode_val = $value;
            $taxonomy = str_replace('attribute_', '', $key );
            $taxonomy_label = get_taxonomy( $taxonomy )->labels->singular_name;
            $term_name = get_term_by( 'slug', $value, $taxonomy )->name;
            $attributes[] = $taxonomy_label.': '.$term_name;
        }
		// Variation ID		
        echo '<div class="col medium-3 small-6 large-3"><div class="col-inner">';
        $variation_id = $variation['variation_id'];
		echo '<div data-role="controlgroup"><a data-role="button" id="'.$toode_val.'" data-toodename="'.$term_name.'">';
        echo '<div class="product-variation variation-id-'.$variation_id.'">';
		

		// Image
        if ( has_post_thumbnail( $variation_id ) ) {
             echo get_the_post_thumbnail( $variation_id, 'thumbnail' );
        }
        echo '<div class="variation-attributes">'.$term_name.'</div>';
        // Prices
        $active_price = floatval($variation['display_price']); // Active price
        $regular_price = floatval($variation['display_regular_price']); // Regular Price
        if( $active_price != $regular_price ){
            $sale_price = $active_price; // Sale Price
        }
        echo $variation['price_html'].'</div></div></div></a></div>';
    }
    echo '</div>';
}  ?>