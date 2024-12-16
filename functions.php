<?php

define( 'RS_THEME_VERSION', '1.2.3' );

// Product Information
define( 'RS_1_GALLON_PRODUCT_ID', 139 );
define( 'RS_5_GALLON_PRODUCT_ID', 155 );

define( 'RS_DEV_MODE', 'staging' ); // live, staging, or local

if ( 'live' === RS_DEV_MODE ) {

	define( 'RS_ORDER_PAGE_ID', 3099 );

} elseif ( 'staging' === RS_DEV_MODE ) {

	define( 'RS_ORDER_PAGE_ID', 3099 );

} else { // local

	define( 'RS_ORDER_PAGE_ID', 3005 );

}

//* Start the engine
include_once( get_template_directory() . '/lib/init.php' );

//* Setup Theme
include_once( get_stylesheet_directory() . '/lib/theme-defaults.php' );

//* Set Localization (do not remove)
load_child_theme_textdomain( 'parallax', apply_filters( 'child_theme_textdomain', get_stylesheet_directory() . '/languages', 'parallax' ) );

//* Add Image upload to WordPress Theme Customizer
add_action( 'customize_register', 'parallax_customizer' );
function parallax_customizer(){
	require_once( get_stylesheet_directory() . '/lib/customize.php' );
}

//* Include Section Image CSS
include_once( get_stylesheet_directory() . '/lib/output.php' );

global $blogurl;
$blogurl = get_stylesheet_directory_uri();

//* Enqueue scripts and styles
add_action( 'wp_enqueue_scripts', 'parallax_enqueue_scripts_styles' );
function parallax_enqueue_scripts_styles() {
	// Styles
	//wp_enqueue_style( 'dashicons' );
	wp_enqueue_style( 'customcss', get_stylesheet_directory_uri() . '/custom.css', array() );
	wp_enqueue_style( 'fontawesomecss', get_stylesheet_directory_uri() . '/fonts/css/font-awesome.min.css', array() );
	

	// Scripts
	wp_enqueue_script( 'scripts', get_stylesheet_directory_uri() . '/js/scripts.min.js', array() );

}

add_action( 'admin_enqueue_scripts', 'rs_admin_enqueue_scripts', 10 );
/*
 * Register and/or enqueue scripts/styles used for the backend.
 *
 * @since 1.2.3
 */
function rs_admin_enqueue_scripts() {
	$stylesheet_dir_uri = get_stylesheet_directory_uri() . '/';
	wp_enqueue_style( 'rs-backend', $stylesheet_dir_uri . 'backend.css', array(), RS_THEME_VERSION );
	wp_enqueue_script( 'rs-backend', $stylesheet_dir_uri . 'js/backend.js', array( 'jquery' ), RS_THEME_VERSION );
}

//* Include Woocommerce Functions
include_once( get_stylesheet_directory() . '/woocommerce-assets/woofunctions.php' );

//* Add HTML5 markup structure
add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );

//* Add viewport meta tag for mobile browsers
add_theme_support( 'genesis-responsive-viewport' );

// Add Search to Primary Nav
//add_filter( 'genesis_header', 'genesis_search_primary_nav_menu', 10 );
function genesis_search_primary_nav_menu( $menu ){
    locate_template( array( 'searchform-header.php' ), true );
}

//* Add support for structural wraps
add_theme_support( 'genesis-structural-wraps', array(
	'header',
	'subnav',
	'footer-widgets',
	'footer',
) );

// Home Post Featured Image
add_image_size( 'home-post-featured', 555, 370, true );

// Add Read More Link to Excerpts
add_filter('excerpt_more', 'get_read_more_link');
add_filter( 'the_content_more_link', 'get_read_more_link' );
function get_read_more_link() {
   return '...&nbsp;<a class="readmore" href="' . get_permalink() . '">Read&nbsp;More &raquo;</a>';
}

//* Add support for 4-column footer widgets
add_theme_support( 'genesis-footer-widgets', 4 );

remove_action( 'genesis_before_footer', 'genesis_footer_widget_areas' );
add_action( 'genesis_footer', 'genesis_footer_widget_areas', 5 );

//* Customize the entry meta in the entry header (requires HTML5 theme support)
add_filter( 'genesis_post_info', 'sp_post_info_filter' );
function sp_post_info_filter($post_info) {
	$post_info = '[post_date] [post_comments] [post_edit]';
	return $post_info;
}

// Unregister unused sidebar
//unregister_sidebar( 'header-right' );

// Previous / Next Post Navigation Filter For Genesis Pagination
add_filter( 'genesis_prev_link_text', 'gt_review_prev_link_text' );
function gt_review_prev_link_text() {
        $prevlink = '&laquo;';
        return $prevlink;
}
add_filter( 'genesis_next_link_text', 'gt_review_next_link_text' );
function gt_review_next_link_text() {
        $nextlink = '&raquo;';
        return $nextlink;
}

//* Remove the entry title (requires HTML5 theme support)

add_action( 'get_header', 'remove_titles_all_single_pages' );
function remove_titles_all_single_pages() {
    if ( is_page() || is_single() || FLBuilderModel::is_builder_enabled() || !is_archive() || !is_home() ) {
        remove_action( 'genesis_entry_header', 'genesis_do_post_title', 4 );
    }
}

// Simply remove anything that looks like an archive title prefix (Examples: "Archive:", "Test Foo:", "Bar:").
add_filter('get_the_archive_title', function ($title) {
    return preg_replace('/\w+.\w+:/', '', $title);
});

//* Add the entry header markup and entry title before the content on all pages except the front page



	
add_action( 'genesis_before', 'entry_header', 5 );
function entry_header() {
	echo '<span id="waypoint-anchor"></span>';
	if (is_front_page()) {
		return;
	}
	if ( is_page( 22322 ) ) { // https://www.readyseal.com/find-a-store-seo/
		return;
	}
	echo '<section class="subpage-header">';
	genesis_entry_header_markup_open();
		echo '<div class="wrap">';
		echo '<article>';
		   echo '<div class="entry-header">';
			echo '<h1 class="entry-title" itemprop="headline">';
				
				if ( is_front_page() && is_home() ) {
				  // Default homepage
				} elseif ( is_front_page() ) {
				  // static homepage
				} elseif ( is_home() ) {
				  // blog page
					echo "Blog";
				} elseif (is_archive()) {
					the_archive_title();
				}elseif ( is_home() ) {
				  // blog page
					echo "Blog";
				}
				else if ( is_post_type_archive('product') ) {
                    echo 'Search Results For: '; echo the_search_query();
					//echo 'Search Results For:';	
              }
				elseif (is_404()) {
					echo "Error 404";
				}
				else {
					the_title();
				}
			echo '</h1>';
		echo '</header>';
	   echo '</article>';
	   echo '</div>';
	genesis_entry_header_markup_close();
	echo '</section>';
	echo '<div class="breadcrumb-container">';
    echo '<div class="wrap"><div class="five-sixths first">';
    	if ( function_exists('yoast_breadcrumb') ) {yoast_breadcrumb('<p id="breadcrumbs">','</p>');} 
    echo ' </div>';
	echo ' <div class="one-sixth"><a href="/about-ready-seal/contact-us/">CONTACT US</a></div>';
	echo '</div>';
  echo '</div>';
}

add_action( 'genesis_after_header', 'rs_display_breadcrumbs_after_site_header_on_find_a_store_seo_page', 10 );
/**
 * On the https://www.readyseal.com/find-a-store-seo/ page, move the breadcrumb container below the site header in
 * the site container (essentially this will display it below the logo).
 */
function rs_display_breadcrumbs_after_site_header_on_find_a_store_seo_page() {
	if ( ! is_page( 22322 ) ) {
		return;
	}
	?>
	<div class="breadcrumb-container">
		<div class="wrap">
			<div class="five-sixths first">
				<?php if ( function_exists('yoast_breadcrumb') ) :
					yoast_breadcrumb('<p id="breadcrumbs">','</p>');
				endif; ?>
	        </div>
			<div class="one-sixth">
				<a href="/about-ready-seal/contact-us/">CONTACT US</a>
			</div>
		</div>
	</div>
	<?php
}

// Ship specific products for free while some others will have shipping costs
/*add_filter( 'woocommerce_cart_shipping_packages', 'bulky_woocommerce_cart_shipping_packages' );

function bulky_woocommerce_cart_shipping_packages( $packages ) {
    // Reset the packages
    $packages = array();
  
    // Bulky items
    $bulky_items   = array();
    $regular_items = array();
    
    // Sort bulky from regular
   // foreach ( WC()->cart->get_cart() as $item ) {
     //   if ( $item['data']->needs_shipping() ) {
       //     if ( $item['data']->get_shipping_class() == 'free-shipping' ) {
         //       $bulky_items[] = $item;
           // } else {
             //   $regular_items[] = $item;
            //}
        //}
    // }
    
    // Put inside packages
    if ( $bulky_items ) {
        $packages[] = array(
            'ship_via'        => array( 'flat_rate' ),
            'contents'        => $bulky_items,
            'contents_cost'   => array_sum( wp_list_pluck( $bulky_items, 'line_total' ) ),
            'applied_coupons' => WC()->cart->applied_coupons,
            'destination'     => array(
                'country'   => WC()->customer->get_shipping_country(),
                'state'     => WC()->customer->get_shipping_state(),
                'postcode'  => WC()->customer->get_shipping_postcode(),
                'city'      => WC()->customer->get_shipping_city(),
                'address'   => WC()->customer->get_shipping_address(),
                'address_2' => WC()->customer->get_shipping_address_2()
            )
        );
    }
    if ( $regular_items ) {
        $packages[] = array(
			'ship_via'        => array( 'ups' ),
            'contents'        => $regular_items,
            'contents_cost'   => array_sum( wp_list_pluck( $regular_items, 'line_total' ) ),
            'applied_coupons' => WC()->cart->applied_coupons,
            'destination'     => array(
                'country'   => WC()->customer->get_shipping_country(),
                'state'     => WC()->customer->get_shipping_state(),
                'postcode'  => WC()->customer->get_shipping_postcode(),
                'city'      => WC()->customer->get_shipping_city(),
                'address'   => WC()->customer->get_shipping_address(),
                'address_2' => WC()->customer->get_shipping_address_2()
            )
        );
    }    
    
    return $packages;
}
*/

// filter into aim gateway to make sure held orders have status of processing
add_filter('wc_payment_gateway_authorize_net_aim_held_order_status', 'dd_aim_gateway_held_to_processing', 20, 4);
function dd_aim_gateway_held_to_processing( $status, $order, $response, $gateway ) {
	return 'processing';
}

/**
 * Hide shipping rates when ups shipping is available.
 * Updated to support WooCommerce 2.6 Shipping Zones.
 *
 * @param array $rates Array of rates found for the package.
 * @return array
 */
function my_hide_shipping_when_ups_is_available( $rates ) {
	$ups = array();
	foreach ( $rates as $rate_id => $rate ) {
		if ( 'ups' === $rate->method_id ) {
			$ups[ $rate_id ] = $rate;
			break;
		}
	}
	return ! empty( $ups ) ? $ups : $rates;
}
add_filter( 'woocommerce_package_rates', 'my_hide_shipping_when_ups_is_available', 100 );

/* Reset status of new orders from "on hold" to "processing" */
add_action( 'woocommerce_thankyou', 'changeOrderStatus' );
function changeOrderStatus($order_id) {
    global $woocommerce;
     if (!$order_id )
        return;
 $billing_paymethod = get_post_meta($order_id,'_payment_method',true);
  $order = new WC_Order( $order_id );
  /* put here your specific payment method */
 if($billing_paymethod == 'authorize_net_aim')
 {
   /* put here your specific  order status */
       $order->update_status( 'processing' );
    }

}

/* Custom Wholesale Pricing Work */
add_action( 'woocommerce_product_options_general_product_data', 'rs_woocommerce_product_options_general_product_data', 10 );
/**
 * Add custom fields to the WooCommerce General product data tab when creating/editing products.
 *
 * @since 1.2.3
 */
function rs_woocommerce_product_options_general_product_data() {
	global $post_id;
	$uses_gallons = absint( get_post_meta( $post_id, 'uses_gallons', true ) );
	woocommerce_wp_checkbox( array(
		'label' => 'Uses Gallons?',
		'id' => 'uses-gallons',
		'name' => 'uses-gallons',
		'value' => $uses_gallons,
		'cbvalue' => 1,
	) );
	$gallons = absint( get_post_meta( $post_id, 'gallons', true ) );
	woocommerce_wp_text_input( array(
		'label' => 'Gallons',
		'id' => 'gallons',
		'name' => 'gallons',
		'value' => $gallons,
	) );
}

add_action( 'woocommerce_process_product_meta', 'rs_woocommerce_process_product_meta', 10 );
/**
 * When saving a WooCommerce product, save any of our custom fields data.
 *
 * @since 1.2.3
 *
 * @param mixed $post_id
 */
function rs_woocommerce_process_product_meta( $post_id ) {

	$uses_gallons = absint( isset( $_POST['uses-gallons'] ) );
	update_post_meta( $post_id, 'uses_gallons', $uses_gallons );

	if ( isset( $_POST['gallons'] ) ) {
		$gallons = absint( $_POST['gallons'] );
		update_post_meta( $post_id, 'gallons', $gallons );
	}
}

add_action( 'woocommerce_gateway_title', 'rs_woocommerce_gateway_title', 10, 1 );
/**
 * Modify the "Cash on Delivery" payment gateway title on the frontend.
 *
 * @since 1.2.3
 *
 * @param string $title
 *
 * @return string title
 */
function rs_woocommerce_gateway_title( $title ) {
	if ( 'cash on delivery' === strtolower( $title ) && ! is_admin() ) {
		return 'Invoice per terms';
	}
	return $title;
}

/**
 * Add wholesale Pricing Structure below the cart total on wholesale products.
 * @param array
 * @return Specific wholesale product id
 */
add_action( 'woocommerce_after_cart', 'thrive_add_pricing_structure', 12 );
function thrive_add_pricing_structure() {
	
	function woo_in_cart($arr_product_id) {
        global $woocommerce;         
        $cartarray=array();

        foreach($woocommerce->cart->get_cart() as $key => $val ) {
            $_product = $val['data'];
            array_push($cartarray,$_product->id);
        }         
        $result = !empty(array_intersect($cartarray,$arr_product_id));
        return $result;

    }
	
	$is_incart=array(139,155);
	if(woo_in_cart($is_incart)) {
    	echo '<div class="pricingstructure">';
		echo '<strong>Pricing Structure</strong><br/>';
		echo '1-Gal: 60 gal=$24.95ea  &nbsp;&nbsp;&nbsp;180 gal=$22.50ea  &nbsp;&nbsp;540 gal=$18.95ea<br/>';
		echo '5-Gal: 60 gal=$116.50ea  &nbsp;180 gal=$104.65ea  &nbsp;&nbsp;540 gal=$88.75ea';
		echo '</div>';
	}
}

// Below function will disable Checkout button if the minimum wholesale product is less than 60.
// Also the function below executes in wholsale premium plugin.
// /plugins/woocommerce-wholesale-prices-premium/includes file name "class-wwpp-wholesale-prices.php" line no (141 and 142) 
// See Code below incaase loss while updating the plugin.
// remove_action( 'woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout', 20 ); 
// add_action('woocommerce_proceed_to_checkout', 'disable_checkout_btn_wholesale_product_cart_quantity',20);
function disable_checkout_btn_wholesale_product_cart_quantity() {
	?>
       <a href="#" class="checkout-button button alt disabled wc-forward">Proceed to Checkout</a> 
  <?php
}

//Adding a Custom Field that allows the customer to enter their own PO number on checkout page
/**
 * Add the field to the checkout page
 */
add_action( 'woocommerce_after_order_notes', 'some_custom_checkout_field' );
 
function some_custom_checkout_field( $checkout ) {
 
    echo '<div id="customer_checkout_field"><h3>' . __('Customer PO Number') . '</h3>';
 
    woocommerce_form_field( 'customer_po_number', array(
        'type'          => 'text',
        'class'         => array('my-field-class form-row-wide'),
        'label'         => __('PO Number'),
        'placeholder'   => __('Enter Your Own PO#'),
        'required'      => false,
        ), $checkout->get_value( 'customer_po_number' ));
 
    echo '</div>';
}

add_action( 'woocommerce_checkout_update_order_meta', 'some_custom_checkout_field_update_order_meta' );
 
function some_custom_checkout_field_update_order_meta( $order_id ) {
    if ( ! empty( $_POST['customer_po_number'] ) ) {
        update_post_meta( $order_id, 'Customer PO Number', sanitize_text_field( $_POST['customer_po_number'] ) );
    }
}

//Disabling AJAX for Cart Page..
function cart_script_disabled(){
	if( is_cart() ) {
		wp_dequeue_script( 'wc-cart' );
	}
}
add_action( 'wp_enqueue_scripts', 'cart_script_disabled' );


/**
 * Get the variations in the cart with their quantities.
 * @global WooCommerce $woocommerce
 * @return array Array of variation quantities with variation ids as keys and their quantity as values.
 */
function rs_get_cart_variation_quantities() {
	global $woocommerce;

	$quantities = array();

	foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $cart_item ) {

		if ( ! isset( $cart_item['variation_id'] ) ) {
			continue;
		}

		$quantities[ $cart_item['variation_id'] ] = $cart_item['quantity'];
	}

	return $quantities;
}

/**
 * Get the variations in the cart with their prices.
 * @global WooCommerce $woocommerce
 * @return array Array of variation prices with variation ids as keys and their prices as values.
 */
function rs_get_cart_variation_prices() {
	global $woocommerce;

	$prices = array();

	/**
	 * @var WC_Cart $cart
	 */
	$cart = $woocommerce->cart;

	$cart_items = $cart->get_cart();

	foreach ( $cart_items as $cart_item_key => $cart_item ) {

		if ( ! isset( $cart_item['variation_id'] ) ) {
			continue;
		}

		$product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

		if ( isset( $cart->display_cart_ex_tax ) && $cart->display_cart_ex_tax ) {
			$product_price = $product->get_price_excluding_tax();
		} else {
			$product_price = $product->get_price_including_tax();
		}

		$prices[ $cart_item['variation_id'] ] = $product_price;
	}

	return $prices;
}

/**
 * Get the variations in the cart with their line totals.
 * @global WooCommerce $woocommerce
 * @return array Array of variation line totals with variation ids as keys and their line totals as values.
 */
function rs_get_cart_variation_line_totals() {
	global $woocommerce;

	$line_totals = array();

	/**
	 * @var WC_Cart $cart
	 */
	$cart = $woocommerce->cart;

	$cart_items = $cart->get_cart();

	foreach ( $cart_items as $cart_item_key => $cart_item ) {

		if ( ! isset( $cart_item['variation_id'] ) ) {
			continue;
		}

		//$price = apply_filters( 'woocommerce_cart_item_price', $product_price, $cart_item, $cart_item_key );

		$line_totals[ $cart_item['variation_id'] ] = number_format( $cart_item['line_total'], 2 );
	}

	return $line_totals;
}

add_action( 'wp_ajax_rs_update_cart_quantity', 'rs_update_cart_quantity', 10 );
/**
 * @global WooCommerce $woocommerce
 */
function rs_update_cart_quantity() {
	// Ensures the grand total can be retrieved
	if ( ! defined( 'WOOCOMMERCE_CHECKOUT' ) ) {
		define( 'WOOCOMMERCE_CHECKOUT', true );
	}
	if ( ! defined( 'RS_ORDER_AJAX' ) ) {
		define( 'RS_ORDER_AJAX', true );
	}
	if ( ! ctype_digit( $_GET['variation_id'] ) ) {
		wp_send_json_error( array( 'error_message' => 'Invalid format for variation id.' ) );
	}
	if ( ! ctype_digit( $_GET['variation_quantity'] ) ) {
		wp_send_json_error( array( 'error_message' => 'Invalid format for variation quantity.' ) );
	}
	if ( ! ctype_digit( $_GET['product_id'] ) ) {
		wp_send_json_error( array( 'error_message' => 'Invalid format for product id.' ) );
	}
	$variation_id = $_GET['variation_id'];
	$variation_qty = $_GET['variation_quantity'];
	$product_id = $_GET['product_id'];
	global $woocommerce;
	/**
	 * @var WC_Cart $cart
	 */
	$cart = $woocommerce->cart;
	$cart_items = $cart->get_cart();
	$the_cart_item_key = null;
	$the_cart_item = null;
	foreach ( $cart_items as $cart_item_key => $cart_item ) {
		if ( isset( $cart_item['variation_id'] ) && $variation_id === strval( $cart_item['variation_id'] ) ) {
			$the_cart_item_key = $cart_item_key;
			$the_cart_item = $cart_item;
			break;
		}
	}
	if ( $the_cart_item_key && $the_cart_item ) {

		if ( $variation_qty <= 0 ) {
			$cart->remove_cart_item( $the_cart_item_key );
		} else {
			$cart->set_quantity( $the_cart_item_key, $variation_qty );
		}

	} else { // Item not in cart. We need to add it.

		$variation_attrs = wc_get_product_variation_attributes( $variation_id );

		$color = $variation_attrs['attribute_pa_color'];

		$variation_data = array(
			'Color' => ucwords( str_replace( '-', ' ', $color ) ),
		);

		if ( ! $cart->add_to_cart( $product_id, $variation_qty, $variation_id, $variation_data ) ) {
			wp_send_json_error( array( 'error_message' => 'The cart item key and item were not found or valid, and failed adding the variation to the cart.' ) );
		}
	}

	$total_gallons_in_cart = rs_get_total_gallons_in_cart();

	$cart->calculate_totals();

	$tax_total = number_format( $cart->get_taxes_total(), 2 );
	if ( empty( $tax_total ) ) {
		$tax_total = '$0.00';
	} else {
		$tax_total = '$' . $tax_total;
	}

	$cart_contents_count = $cart->get_cart_contents_count();

	$data = array(
		'variation_prices'    => rs_get_variation_wholesale_prices( $total_gallons_in_cart ),
		'variation_totals'    => rs_get_variation_line_totals(),
		'cart_subtotal_text'  => sprintf( __( '<strong>Subtotal:</strong> %s', 'readyseal' ), wc_price( $cart->subtotal_ex_tax ) ),
		'cart_tax_total_text' => sprintf( __( '<strong>Tax:</strong> %s', 'readyseal' ), $tax_total ),
		'cart_total_text'     => sprintf( __( '<strong>Total:</strong> %s', 'readyseal' ), $cart->get_total() ),
		'my_cart_text'        => sprintf( __( '%d Items - %s', 'readyseal' ), $cart_contents_count, wc_price( $cart->subtotal_ex_tax ) ),
		'cart_contents_count' => $cart_contents_count,
		'total_gallons_in_cart' => $total_gallons_in_cart,
	);

	wp_send_json_success( $data );
}

/**
 * @param string|int $variation_id
 * @return null|string
 */
function rs_get_wholesale_price_for_variation_id( $variation_id ) {
	$wholesale_price = get_post_meta( $variation_id, 'wholesale_customer_wholesale_price', true );

	return $wholesale_price;
}

/**
 * @param $variation_id
 * @return mixed
 */
function rs_get_wholesale_minimum_order_quantity_for_variation_id( $variation_id ) {
	$min_order_quantity = get_post_meta( $variation_id, 'wholesale_customer_wholesale_minimum_order_quantity', true );

	return $min_order_quantity;
}
/**
 * @param string|int $variation_id
 * @return array
 */
function rs_get_wholesale_quantity_discount_mapping_for_variation_id( $variation_id ) {

	$mapping = array();

	$_mapping = get_post_meta( $variation_id, 'wwpp_post_meta_quantity_discount_rule_mapping', true );

	if ( $_mapping ) {
		foreach ( $_mapping as $_map ) {
			$mapping[] = array(
				'start_qty' => $_map['start_qty'],
				'end_qty' => $_map['end_qty'],
				'wholesale_price' => $_map['wholesale_price'],
			);
		}
	}

	return $mapping;
}

/**
 * @global WooCommerce $woocommerce
 * @return int
 */
function rs_get_total_gallons_in_cart() {
	global $woocommerce;
	/**
	 * @var WC_Cart $cart
	 */
	$cart = $woocommerce->cart;
	$cart_items = $cart->get_cart();
	$total_gallons = 0;
	foreach ( $cart_items as $cart_item ) {
		$product_id = $cart_item['product_id'];
		$quantity = $cart_item['quantity'];
		$total_gallons += $quantity * absint( get_post_meta( $product_id, 'gallons', true ) );
	}
	return $total_gallons;
}

/**
 * @param mixed $product_id
 * @return mixed
 */
function rs_get_gallons_of_product_id( $product_id ) {
	return get_post_meta( $product_id, 'gallons', true );
}

/**
 * Get all products which are using gallons.
 * @return WC_Product_Variable[]
 */
function rs_get_gallon_products() {
	$products = array();
	$product_ids = array( RS_1_GALLON_PRODUCT_ID, RS_5_GALLON_PRODUCT_ID, );
	foreach ( $product_ids as $product_id ) {
		$products[] = new WC_Product_Variable( $product_id );
	}
	return $products;
}

/**
 * Get all variations of all products using gallons.
 * @return array
 */
function rs_get_gallon_product_variations() {
	$variations = array();
	foreach ( rs_get_gallon_products() as $product ) {
		$variations = array_merge( $variations, $product->get_available_variations() );
	}
	return $variations;
}

/**
 * Calculate the wholesale price for all variations of products using gallons.
 *
 * @param int $total_gallons_in_cart Used for calculating the correct quantity when a product uses more than 1 gallon.
 *
 * @return array
 */
function rs_get_variation_wholesale_prices( $total_gallons_in_cart ) {
	$wholesale_prices = array();
	$products = rs_get_gallon_products();
	foreach ( $products as $product ) {

		$product_id = $product->get_id();

		$variations = $product->get_available_variations();

		$product_gallons = rs_get_gallons_of_product_id( $product_id );

		foreach ( $variations as $variation ) {

			$variation_id = $variation['variation_id'];

			$variation_price = null;

			$quantity_discount_mapping = rs_get_wholesale_quantity_discount_mapping_for_variation_id( $variation_id );

			$variation_gallons_quantity = $total_gallons_in_cart / $product_gallons;

			foreach ( $quantity_discount_mapping as $mapping ) {

				if ( isset( $mapping['start_qty'] ) && $variation_gallons_quantity >= $mapping['start_qty'] ) {
					if ( ! empty( $mapping['end_qty'] ) && $variation_gallons_quantity >= $mapping['end_qty'] ) {
						continue;
					}
					$variation_price = $mapping['wholesale_price'];
				}
			}

			if ( ! $variation_price ) {
				$min_order_quantity = rs_get_wholesale_minimum_order_quantity_for_variation_id( $variation_id );
				if ( $variation_gallons_quantity >= $min_order_quantity ) {
					$variation_price = rs_get_wholesale_price_for_variation_id( $variation_id );
				} else {
					$variation_price = $variation['display_price'];
				}
			}

			$wholesale_prices[ $variation_id ] = '$' . number_format( $variation_price, 2 );
		}
	}

	return $wholesale_prices;
}

/**
 * @return array
 */
function rs_get_variation_line_totals() {
	global $woocommerce;

	$variation_totals = array();

	// Get totals of variations in cart
	foreach ( $woocommerce->cart->get_cart() as $cart_item ) {
		if ( ! isset( $cart_item['variation_id'] ) ) {
			continue;
		}
		$variation_id = $cart_item['variation_id'];
		$variation_totals[ $variation_id ] = '$'. number_format( $cart_item['line_total'], 2 );
	}

	// Get totals of variations not in cart
	$variations = rs_get_gallon_product_variations();
	foreach ( $variations as $variation ) {
		$variation_id = $variation['variation_id'];
		if ( ! isset( $variation_totals[ $variation_id ] ) ) {
			$variation_totals[ $variation_id ] = '$0.00';
		}
	}

	return $variation_totals;
}

/**
 * Updates the customers terms.
 * @param mixed $customer_id
 * @param string $customer_data JSON encoded data.
 */
function rs_update_customer_terms( $customer_id, $customer_data ) {
	if ( is_string( $customer_data ) ) {
		$result = json_decode( $customer_data );
		if ( json_last_error() === JSON_ERROR_NONE ) {
			$customer_data = $result;
		} else {
			return;
		}
	}
	if ( ! is_array( $customer_data ) ) {
		return;
	}
	if ( isset( $customer_data['data']['terms']['name'] ) ) {
		update_user_meta( $customer_id, 'terms', $customer_data['data']['terms']['name'] );
	} elseif ( isset( $customer_data['data'][0]['terms']['name'] ) ) {
		update_user_meta( $customer_id, 'terms', $customer_data['data'][0]['terms']['name'] );
	}
}

add_filter( 'woocommerce_get_order_item_totals', 'rs_woocommerce_get_order_item_totals', 10, 2 );
/**
 * Adds terms row to the order item totals for the customer, if they exist.
 * @param array $total_rows
 * @param WC_Abstract_Order $order
 * @return array
 */
function rs_woocommerce_get_order_item_totals( $total_rows, WC_Abstract_Order $order ) {

	$_total_rows = array();

	foreach ( $total_rows as $key => $val ) {
		$_total_rows[ $key ] = $val;
		if ( 'payment_method' !== $key ) {
			continue;
		}
		$terms = get_user_meta( $order->get_user_id(), 'terms', true );
		if ( ! $terms ) {
			continue;
		}
		$_total_rows[ 'terms'] = array(
			'label' => 'Terms:',
			'value' => esc_html( $terms ),
		);
	}

	// Remove Payment method from emails.
	unset( $_total_rows['payment_method'] );

	return $_total_rows;
}

add_action( 'edit_user_profile', 'rs_admin_edit_user_add_customer_internal_id', 10 );
/**
 * Adds the customer internal id field to the edit user form.
 * @param WP_User $user
 */
function rs_admin_edit_user_add_customer_internal_id( WP_User $user ) {
	rs_admin_display_customer_internal_id_field( $user );
}

add_action( 'user_register', 'rs_admin_save_user_customer_internal_id', 10 );
add_action( 'edit_user_profile_update', 'rs_admin_save_user_customer_internal_id', 10 );
/**
 * Saves the customer internal id when creating a new user or editing a user in the backend.
 * @param int $user_id
 */
function rs_admin_save_user_customer_internal_id( $user_id ) {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	if ( ! ( isset( $_POST['customer_internal_id'] ) && ctype_digit( $_POST['customer_internal_id'] ) ) ) {
		return;
	}
	$customer_internal_id = $_POST['customer_internal_id'];
	update_user_meta( $user_id, 'customerinternalid', $customer_internal_id );
}

add_action( 'user_new_form', 'rs_admin_add_customer_internal_id_field_new_user_form', 10 );
/**
 * Adds the Netsuite customer id field to the add user admin screen.
 * @param string $type ‘add-existing-user’ (Multisite) or ‘add-new-user’ (single site and network admin).
 */
function rs_admin_add_customer_internal_id_field_new_user_form( $type ) {
	if ( 'add-new-user' !== $type ) {
		return;
	}
	rs_admin_display_customer_internal_id_field();
}

/**
 * Displays the customer internal id inside a form table.
 * @param WP_User|null $user If WP_User, we want to display their customer internal id (if they have one), otherwise
 *                           we just want to be able to let them enter one themselves (if they are an admin).
 */
function rs_admin_display_customer_internal_id_field( WP_User $user = null ) {
	$customer_internal_id = '';
	if ( $user ) {
		$customer_internal_id = get_user_meta( $user->ID, 'customerinternalid', true );
		if ( ! ctype_digit( $customer_internal_id ) ) {
			$customer_internal_id = '';
		}
	}
	?>
	<h3><?php esc_html_e( 'Netsuite Customer Details', 'readyseal' ); ?></h3>
	<p><?php esc_html_e( "Here you can enter/change the customer's Netsuite customer ID.", 'readyseal' ); ?></p>
	<table class="form-table">
		<tr class="form-field">
			<th scope="row">
				<label for="customer-internal-id"><?php _e( 'Netsuite Customer ID' ); ?></label>
			</th>
			<td>
				<input type="text" id="customer-internal-id" name="customer_internal_id" value="<?php echo esc_attr( $customer_internal_id ); ?>">
			</td>
		</tr>
	</table>
	<?php
}

add_filter( 'wp_nav_menu', 'rs_add_search_bar_after_main_navigation', 10, 2 );
/**
 * Add the search bar to the end of the main menu navigation.
 * @param string $nav_menu_html
 * @param array $args
 * @return string html
 */
function rs_add_search_bar_after_main_navigation( $nav_menu_html, $args ) {

	if ( ! ( isset( $args->menu->slug ) && 'main-menu' === $args->menu->slug ) ) {
		return $nav_menu_html;
	}

	ob_start();

	?>

	<div class="menusearch-container">
		<a href="#" class="search menusearch">SEARCH</a>
		<?php locate_template( array( 'searchform-header.php' ), true ); ?>
	</div>

	<?php

	$contents = ob_get_clean();

	return $nav_menu_html . $contents;
}

/**add_filter( 'wp_nav_menu_main-menu_items', 'rs_add_distributors_item_to_main_menu', 10 );
/**
 * Add a "Distributors" item to the main menu for admins and wholesale customers only.
 * @param string $items
 * @return string

function rs_add_distributors_item_to_main_menu( $items ) {

	// Only admins or wholesale customers should see this menu item.
	if ( ! ( current_user_can( 'wholesale_customer' ) || current_user_can( 'manage_options' ) ) ) {
		return $items;
	}

	$order_page_url = site_url( 'order' );
	$new_item = '<li class="menu-item menu-item-type-post_type menu-item-object-page distributors-menu-item">
					<a href="' . esc_url( $order_page_url ) . '" itemprop="url"><span itemprop="name">' . esc_html__( 'Distributor Order Page', 'readyseal' ) . '</span></a>
				</li>';

	// Add new item between the 1st & second item.
	$first_li_closing_tag_pos = stripos( $items, '</li>' );

	$start = substr( $items, 0, $first_li_closing_tag_pos );

	$rest = substr( $items, $first_li_closing_tag_pos + 5 );

	$items = $start . $new_item . $rest;

	return $items;
}

add_filter( 'body_class', 'rs_show_distributors_body_class', 10 );

 * Add a new class to the body if the user can see the new Distributors menu item.
 * @param array $classes
 * @return array

function rs_show_distributors_body_class( $classes ) {

	if ( ! ( current_user_can( 'wholesale_customer' ) || current_user_can( 'manage_options' ) ) ) {
		return $classes;
	}

	$classes[] = 'can-see-distributors-link';

	if ( ! current_user_can( 'manage_options' ) ) {
		$classes[] = 'wholesale-can-see-distributors-link';
	}

	return $classes;
}
*/

add_action( 'wp_footer', 'rs_update_user_viewing_distributor_order_page_meta', 10 );
/**
 * Update whether the current user (if wholesale customer) is viewing the distributor order page.
 */
function rs_update_user_viewing_distributor_order_page_meta() {
	if ( ! is_user_logged_in() ) {
		return;
	}
	if ( ! current_user_can( 'wholesale_customer' ) ) {
		return;
	}
	if ( is_ajax() ) {
		return;
	}
	$value = is_page( RS_ORDER_PAGE_ID ) ? 'true' : 'false';
	update_user_meta( get_current_user_id(), 'viewing_distributor_order_page', $value );
}

add_filter( 'woocommerce_get_price_html', 'rs_woocommerce_get_price_html', 10, 1 );
/**
 * Modify the price HTML to replace $0.00 with "Free!", for the products & product page.
 * @param string $price html
 * @return mixed html
 */
function rs_woocommerce_get_price_html( $price ) {

	$price = str_replace(
		'<span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">&#36;</span>0.00</span>',
		'<span class="woocommerce-Price-amount amount">Free!</span>',
		$price
	);

	return $price;
}

add_filter( 'woocommerce_checkout_fields', 'rs_woocommerce_checkout_fields', 10 );
/**
 * Modify any WooCommerce checkout fields.
 * @param array $fields
 * @return array
 */
function rs_woocommerce_checkout_fields( $fields ) {

	$fields['order']['order_comments']['placeholder'] = esc_attr__( 'Notes about your order, e.g. additional items, special note for delivery, etc.', 'woocommerce' );

	return $fields;
}

/**
 * Sell only in specific states
 * Remove the states not eligible to purchase
 * Final list is of states that CAN purchase
 */
add_filter( 'woocommerce_states', 'thrive_wc_sell_only_states' );
function thrive_wc_sell_only_states( $states ) {

	$states['US'] = array(
		'AL' => __( 'Alabama', 'woocommerce' ),
		'AK' => __( 'Alaska', 'woocommerce' ),
		'AZ' => __( 'Arizona', 'woocommerce' ),
		'AR' => __( 'Arkansas', 'woocommerce' ),
		// 'CA' => __( 'California', 'woocommerce' ),
		'CO' => __( 'Colorado', 'woocommerce' ),
		'CT' => __( 'Connecticut', 'woocommerce' ),
		'DE' => __( 'Delaware', 'woocommerce' ),
		'DC' => __( 'District Of Columbia', 'woocommerce' ),
		'FL' => __( 'Florida', 'woocommerce' ),
		'GA' => _x( 'Georgia', 'US state of Georgia', 'woocommerce' ),
		'HI' => __( 'Hawaii', 'woocommerce' ),
		'ID' => __( 'Idaho', 'woocommerce' ),
		'IL' => __( 'Illinois', 'woocommerce' ),
		'IN' => __( 'Indiana', 'woocommerce' ),
		'IA' => __( 'Iowa', 'woocommerce' ),
		'KS' => __( 'Kansas', 'woocommerce' ),
		'KY' => __( 'Kentucky', 'woocommerce' ),
		'LA' => __( 'Louisiana', 'woocommerce' ),
		'ME' => __( 'Maine', 'woocommerce' ),
		'MD' => __( 'Maryland', 'woocommerce' ),
		'MA' => __( 'Massachusetts', 'woocommerce' ),
		'MI' => __( 'Michigan', 'woocommerce' ),
		'MN' => __( 'Minnesota', 'woocommerce' ),
		'MS' => __( 'Mississippi', 'woocommerce' ),
		'MO' => __( 'Missouri', 'woocommerce' ),
		'MT' => __( 'Montana', 'woocommerce' ),
		'NE' => __( 'Nebraska', 'woocommerce' ),
		'NV' => __( 'Nevada', 'woocommerce' ),
		'NH' => __( 'New Hampshire', 'woocommerce' ),
		'NJ' => __( 'New Jersey', 'woocommerce' ),
		'NM' => __( 'New Mexico', 'woocommerce' ),
		'NY' => __( 'New York', 'woocommerce' ),
		'NC' => __( 'North Carolina', 'woocommerce' ),
		'ND' => __( 'North Dakota', 'woocommerce' ),
		'OH' => __( 'Ohio', 'woocommerce' ),
		'OK' => __( 'Oklahoma', 'woocommerce' ),
		'OR' => __( 'Oregon', 'woocommerce' ),
		'PA' => __( 'Pennsylvania', 'woocommerce' ),
		'RI' => __( 'Rhode Island', 'woocommerce' ),
		'SC' => __( 'South Carolina', 'woocommerce' ),
		'SD' => __( 'South Dakota', 'woocommerce' ),
		'TN' => __( 'Tennessee', 'woocommerce' ),
		'TX' => __( 'Texas', 'woocommerce' ),
		'UT' => __( 'Utah', 'woocommerce' ),
		'VT' => __( 'Vermont', 'woocommerce' ),
		'VA' => __( 'Virginia', 'woocommerce' ),
		'WA' => __( 'Washington', 'woocommerce' ),
		'WV' => __( 'West Virginia', 'woocommerce' ),
		'WI' => __( 'Wisconsin', 'woocommerce' ),
		'WY' => __( 'Wyoming', 'woocommerce' ),
		'AA' => __( 'Armed Forces (AA)', 'woocommerce' ),
		'AE' => __( 'Armed Forces (AE)', 'woocommerce' ),
		'AP' => __( 'Armed Forces (AP)', 'woocommerce' )
	);

	return $states;

}

function thrive_custom_viewport( $content ) {
	return 'width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0';
}
add_filter( 'genesis_viewport_value', 'thrive_custom_viewport' );

// Used for testing WooCommerce orders
// PLEASE TEST AS A GUEST FIRST TO ENSURE CHEQUE CAN ONLY BE SELECTED BY THE BELOW USER.
add_filter( 'woocommerce_available_payment_gateways', 'readyseal_enable_cheque_gateway_for_dev_only' );
/**
 * @param array $available_gateways
 * @return array
 */
function readyseal_enable_cheque_gateway_for_dev_only( $available_gateways ) {
	if ( isset( $available_gateways['cheque'] ) ) {
		$user = wp_get_current_user();
		if ( ! ( isset( $user->user_login ) && 'jaythrive' === $user->user_login ) ) {
			unset( $available_gateways['cheque'] );
		}
	}
	return $available_gateways;
}

// Adding custom fonts for new landing page to beaver builder
function my_bb_custom_fonts ( $system_fonts ) {

  $system_fonts[ 'Wicked Grit Regular' ] = array(
    'fallback' => 'Verdana, Arial, sans-serif',
    'weights' => array(
      '400',
    ),
  );

return $system_fonts;

}
//Add to Beaver Builder Theme Customizer
add_filter( 'fl_theme_system_fonts', 'my_bb_custom_fonts' );

//Add to Beaver Builder modules
add_filter( 'fl_builder_font_families_system', 'my_bb_custom_fonts' );

add_shortcode( 'readyseal_header_top_bar_links', 'readyseal_header_top_bar_links_shortcode' );
/**
 * Display the Header Top Bar links.
 * Ported from Widget as PHP was being used / supported by no longer maintained plugin.
 * @return string
 */
function readyseal_header_top_bar_links_shortcode() {
	ob_start();
	?>
	<a class="callme" href="tel:1.888.782.4648" onClick="ga('send', 'event', { eventCategory: 'Phone', eventAction: 'CTC', eventLabel: 'Header'});">1-888-STAIN-4U</a>
<!-- 	<a href="/my-account/" class="myaccount">My Account</a> -->
	<a href="/my-account/" class="myaccount">My Account</a>
	<?php if ( is_user_logged_in() ) {
		echo '<a class="mylogin" href="'; echo wp_logout_url('$index.php'); echo'">Sign Out</a>';
	}
	?>
	<a class="cart-contents mycart" href="<?php echo WC()->cart->get_cart_url(); ?>" title="<?php _e( 'View your shopping cart' ); ?>">
		<?php echo sprintf (_n( '%d ITEM - ', '%d Items - ', WC()->cart->cart_contents_count ), WC()->cart->cart_contents_count ); echo WC()->cart->get_cart_total(); ?>
	</a>
	<?php
	return ob_get_clean();
}

// Override post count for search page
function custom_search_results_per_page( $query ) {
    if ( ! is_admin() && $query->is_main_query() ) {
        // Set posts per page for search results
        if ( $query->is_search() ) {
            $query->set( 'posts_per_page', 10 ); // Search results: 10 posts per page
        }
    }
}
add_action( 'pre_get_posts', 'custom_search_results_per_page' );