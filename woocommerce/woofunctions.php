<?php

// Declare WooCommerce support for custom themes
add_action( 'after_setup_theme', 'woocommerce_support' );
function woocommerce_support() {
    add_theme_support( 'woocommerce' );
}

//* Enqueue scripts and styles
add_action( 'wp_enqueue_scripts', 'thrive_enqueue_scripts_styles' );
function thrive_enqueue_scripts_styles() {
	// Styles
	wp_enqueue_style( 'thrive-archive-search-styles', get_stylesheet_directory_uri() . '/woocommerce-assets/thrive-archive-search-styles.css', array() );
	wp_enqueue_style( 'thrive-single-product-styles', get_stylesheet_directory_uri() . '/woocommerce-assets/thrive-single-product-styles.css', array() );

	// Scripts
	//wp_enqueue_script( 'owlcarousel', get_stylesheet_directory_uri() . '/js/owl-carousel/owl.carousel.min.js', array('jquery') );
}

// Add the img wrap
add_action( 'woocommerce_before_shop_loop_item_title', function(){ return 'echo "<div class=\"img-wrap\"><span></span>"';}, 5, 2);
add_action( 'woocommerce_before_shop_loop_item_title', function(){ return 'echo "</div>"';}, 12, 2); 

/* Show pagination on the top of shop page */
add_action( 'woocommerce_before_shop_loop', 'woocommerce_pagination', 10 );

//Show Ratings even if Product Hadn't Been Reviewed
add_filter('woocommerce_product_get_rating_html', 'your_get_rating_html', 10, 2);
function your_get_rating_html($rating_html, $rating) {
  if ( $rating > 0 ) {
    $title = sprintf( __( 'Rated %s out of 5', 'woocommerce' ), $rating );
  } else {
    $title = 'Not yet rated';
    $rating = 0;
  }

  $rating_html  = '<div class="star-rating" title="' . $title . '">';
  $rating_html .= '<span style="width:' . ( ( $rating / 5 ) * 100 ) . '%"><strong class="rating">' . $rating . '</strong> ' . __( 'out of 5', 'woocommerce' ) . '</span>';
  $rating_html .= '</div>';

  return $rating_html;
}

// Apply Priority Hooks so the price and rating appear top of the title..
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 20 );

remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_product_title', 15 );
add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_product_title', 20 );

remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 15 );


// WooCommerce - Unhook sidebar on Single Product page
add_action( 'get_header', 'remove_storefront_sidebar' );
function remove_storefront_sidebar() {
	if ( is_product() ) {
		remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10);
	}
}

// Remove Product Title
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );

// Remove the additional information tab
function woo_remove_product_tab($tabs) {
   unset( $tabs['additional_information'] );          
               return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'woo_remove_product_tab', 98);

// Removes tabs from their original loaction and Inserts tabs under Summary right product content 
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_output_product_data_tabs', 60 );

// Reposition Meta i.e. category, sku and tags below product image on single product page
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
add_action( 'woocommerce_product_thumbnails', 'woocommerce_template_single_meta', 50 );

// Reposition Social Sharing buttons under product image on single product page
function sv_add_logo_above_wc_product_thumbs() {
    if ( function_exists( 'ADDTOANY_SHARE_SAVE_KIT' ) ) { ADDTOANY_SHARE_SAVE_KIT(); }
}
add_action( 'woocommerce_product_thumbnails', 'sv_add_logo_above_wc_product_thumbs', 40 );

// Display 24 products per page. Goes in functions.php
add_filter( 'loop_shop_per_page', function($cols){ return 6;}, 20 );
