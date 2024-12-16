<?php

/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! current_user_can( 'wholesale_customer' ) && ! current_user_can( 'administrator' ) ) {
	?>
	<p class="error text-center"><?php _e( "Sorry, only Wholesale customers can access this page.", 'readyseal' ); ?></p>
	<?php
	return;
}

/*
if ( ! class_exists( 'NetsuiteConnection' ) ) {
	require_once WP_PLUGIN_DIR . '/in8sync-customers/inc/netsuiteconnection.php';
	dump( class_exists( 'NetsuiteConnection' ) );

	$user_id = get_current_user_id();

	dump( $user_id );

	$conn = new NetsuiteConnection( $user_id );

	dump( $conn );

	$response = $conn->doRequest( array(), array( 'customerId' => $user ), 'customer', $user_id );

	exit;
}
*/

$total_gallons_in_cart = rs_get_total_gallons_in_cart();

?>
<table id="rs-order-pricing-structure">
	<caption class="text-left"><strong>Pricing Structure</strong></caption>
	<tr>
		<th>Name</th>
		<th>60-179</th>
		<th>180-539</th>
		<th>540+</th>
	</tr>
	<tr>
		<td>1 Gallon</td>
		<td>$24.95</td>
		<td>$22.50</td>
		<td>$18.95</td>
	</tr>
	<tr>
		<td>5 Gallon</td>
		<td>$116.50</td>
		<td>$104.65</td>
		<td>$88.75</td>
	</tr>
</table>
<?php

global $woocommerce, $post;
/**
 * @var WC_Cart $cart
 */
$cart = $woocommerce->cart;

$order = new WC_Order( $post->ID );

$product_ids = array( RS_1_GALLON_PRODUCT_ID, RS_5_GALLON_PRODUCT_ID, );

$products = array();

$cart_quantities = rs_get_cart_variation_quantities();
$cart_prices = rs_get_cart_variation_prices();
$cart_variation_line_totals = rs_get_cart_variation_line_totals();

foreach ( $product_ids as $product_id ) {
	$product = new WC_Product_Variable( $product_id );

	$product_post_data = $product->get_post_data();

	?>
	<p class="text-center rs-order-product-title">
		<strong><?php echo esc_html( $product_post_data->post_title ); ?></strong>
	</p>
	<?php

	if ( $product_id == RS_1_GALLON_PRODUCT_ID ) {
		?>
		<p class="text-center">Must order in multiples of 4.</p>
		<?php
	}

	$variations = $product->get_available_variations();

	/*
	 * Get the swatch image for each variation
	 */
	$variation_attr_pa_color_ids = array();
	foreach ( $variations as $variation ) {
		if ( ! isset( $variation['attributes']['attribute_pa_color'] ) ) {
			continue;
		}
		$color = $variation['attributes']['attribute_pa_color'];
		$variation_attr_pa_color_ids[ $color ] = $variation['variation_id'];
	}
	$variation_swatch_image_ids = array();
	$colored_vars = get_post_meta( $product_id, '_coloredvariables', true );
	if ( isset( $colored_vars['pa_color']['values'] ) ) {
		foreach ( $colored_vars['pa_color']['values'] as $color => $color_data ) {
			if ( ! isset( $color_data[ 'image'] ) ) {
				continue;
			}
			if ( ! isset( $variation_attr_pa_color_ids[ $color ] ) ) {
				continue;
			}
			$id = $variation_attr_pa_color_ids[ $color ];
			$variation_swatch_image_ids[ $id ] = $color_data[ 'image'];
		}
	}

	$product_gallons = rs_get_gallons_of_product_id( $product_id );

	if ( $variations ) : ?>

		<table class="rs-order-table">
			<tr>
				<th><?php _e( 'QTY', 'readyseal' ); ?></th>
				<th><?php _e( 'Item', 'readyseal' ); ?></th>
				<th><?php _e( 'Price/Gal', 'readyseal' ); ?></th>
				<th><?php _e( 'Total', 'readyseal' ); ?></th>
			</tr>
			<?php foreach ( $variations as $variation ) : ?>

				<?php
				$variation_id = $variation['variation_id'];
				$attribute_pa_color = isset( $variation['attributes']['attribute_pa_color'] ) ?
					$variation['attributes']['attribute_pa_color'] : '';
				$variation_title = ucwords( str_replace( '-', ' ', $attribute_pa_color ) );
				$variation_name = $variation_title;
				$variation_element_qty_id = "variation-qty-{$variation_id}";
				$variation_element_price_id = "variation-price-{$variation_id}";
				$variation_element_total_id = "variation-total-{$variation_id}";
				$variation_hidden_product_id_element_id = "variation-product-id-{$variation_id}";
				$variation_qty = isset( $cart_quantities[ $variation_id ] ) ? $cart_quantities[ $variation_id ] : 0;

				$quantity_discount_mapping = rs_get_wholesale_quantity_discount_mapping_for_variation_id( $variation_id );

				$variation_price = null;

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

				$variation_price = '$' . number_format( $variation_price, 2 );

				$variation_total = isset( $cart_variation_line_totals[ $variation_id ] ) ? '$' . $cart_variation_line_totals[ $variation_id ] : '$0.00';

				$variation_swatch_image_url = '';

				if ( isset( $variation_swatch_image_ids[ $variation_id ] ) ) {
					$variation_swatch_image_url = wp_get_attachment_thumb_url( $variation_swatch_image_ids[ $variation_id ] );
				}
				?>
				<tr>
					<td>
						<input type="number" min="0" value="<?php echo esc_attr( $variation_qty ); ?>" id="<?php echo esc_attr( $variation_element_qty_id ); ?>" class="rs-order-qty" title="">
						<input type="hidden" id="<?php echo esc_attr( $variation_hidden_product_id_element_id ); ?>" value="<?php echo esc_attr( $product_id ); ?>">
					</td>
					<td>
						<img class="float-left" src="<?php echo esc_url( $variation_swatch_image_url ); ?>" width="30" height="30">
						<span class="rs-order-variation-item-name"><?php echo esc_html( $variation_name ); ?></span>
					</td>
					<td>
						<input type="text" id="<?php echo esc_attr( $variation_element_price_id ); ?>" value="<?php echo esc_attr( $variation_price ); ?>" title="" disabled="disabled">
					</td>
					<td>
						<input type="text" id="<?php echo esc_attr( $variation_element_total_id ); ?>" value="<?php echo esc_attr( $variation_total ); ?>" title="" disabled="disabled">
					</td>
				</tr>
			<?php endforeach; ?>
		</table>
	<?php endif;
}

?>

<div style="overflow: hidden;">
	<div style="float: left;">
		<p>
			<strong>Total Gallons In Cart:</strong>
			<span id="rs-total-gallons-in-cart"><?php echo esc_html( $total_gallons_in_cart ); ?></span>
		</p>
	</div>

	<div style="float: right;">
		<p id="rs-order-subtotal" class="text-right">
			<strong><?php _e( 'Subtotal:', 'readyseal' ); ?></strong>
			<?php echo $cart->get_cart_subtotal(); ?>
		</p>
		<p id="rs-order-shipping-total" class="text-right">
			<strong><?php _e( 'Shipping:', 'readyseal' ); ?></strong>
			<?php echo '$0.00'; ?>
		</p>
		<?php $taxes_total = '$' . number_format( $cart->get_taxes_total(), 2 ); ?>
		<p id="rs-order-tax-total" class="text-right">
			<strong><?php _e( 'Tax:', 'readyseal' ); ?></strong>
			<?php echo $taxes_total; ?>
		</p>
		<p id="rs-order-total" class="text-right">
			<strong><?php _e( 'Total:', 'readyseal' ); ?></strong>
			<?php echo $cart->get_total(); ?>
		</p>
	</div>
</div>

<?php

//wc_print_notices();

do_action( 'woocommerce_before_checkout_form', $checkout );

?>

<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">

	<div id="rs-order-comments-container">

		<?php woocommerce_form_field( 'customer_po_number', array(
			'type'          => 'text',
			'class'         => array( 'my-field-class form-row-wide' ),
			'label'         => __( 'PO Number' ),
			'placeholder'   => __( 'Enter Your Own PO#' ),
			'required'      => false,
		), $checkout->get_value( 'customer_po_number' ) ); ?>

		<div class="form-row">
			<label>Please Note</label>
			<p id="rs-distributor-order-page-please-note-text">To order additional items not listed, please add to the Order Notes below and we will confirm price via email.</p>
		</div>

		<?php foreach ( $checkout->checkout_fields['order'] as $key => $field ) : ?>

			<?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>

		<?php endforeach; ?>
	</div>

	<?php if ( sizeof( $checkout->checkout_fields ) > 0 ) : ?>

		<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

		<div class="col2-set" id="customer_details">
			<div class="col-1">
				<?php do_action( 'woocommerce_checkout_billing' ); ?>
			</div>

			<div class="col-2">
				<?php do_action( 'woocommerce_checkout_shipping' ); ?>
			</div>
		</div>

		<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

	<?php endif; ?>

	<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

	<div id="order_review" class="woocommerce-checkout-review-order">
		<?php do_action( 'woocommerce_checkout_order_review' ); ?>
	</div>

	<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>

</form>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>