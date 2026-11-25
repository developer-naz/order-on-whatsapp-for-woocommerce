<?php
/**
 * Helpers: settings, phone number, message building.
 *
 * @package OOW
 */

defined( 'ABSPATH' ) || exit;

/**
 * Default settings (stored as options named oow_{key}).
 *
 * @return array<string, string>
 */
function oow_defaults() {
	return array(
		'phone'            => '',
		'button_text'      => __( 'Order on WhatsApp', 'oow' ),
		'cart_button_text' => __( 'Order the cart on WhatsApp', 'oow' ),
		'show_product'     => 'yes',
		'show_shop'        => 'no',
		'show_cart'        => 'yes',
		'floating'         => 'no',
		'hide_add_to_cart' => 'no',
		'button_color'     => '#25D366',
		'product_message'  => "Hello! I want to order:\n\n*{product_name}*\n{variation}\nQuantity: {quantity}\nPrice: {price}\n\nLink: {url}",
		'cart_message'     => "Hello! I want to order:\n\n{cart_items}\n\n*Total: {cart_total}*",
		'floating_message' => __( 'Hello! I have a question about your products.', 'oow' ),
	);
}

/**
 * Get a setting. Filter: oow_option_{key}.
 *
 * @param string $key Key without the oow_ prefix.
 * @return string
 */
function oow_option( $key ) {
	$defaults = oow_defaults();
	$value    = get_option( 'oow_' . $key, isset( $defaults[ $key ] ) ? $defaults[ $key ] : '' );

	return (string) apply_filters( 'oow_option_' . $key, $value );
}

/**
 * Whether a yes/no setting is on.
 *
 * @param string $key Key.
 * @return bool
 */
function oow_is_on( $key ) {
	return 'yes' === oow_option( $key );
}

/**
 * WhatsApp number in international format, digits only.
 * Bangladeshi numbers like 01712-345678 become 8801712345678.
 *
 * @param string $phone Phone in any format.
 * @return string
 */
function oow_normalize_phone( $phone ) {
	$digits = preg_replace( '/\D+/', '', (string) $phone );

	if ( preg_match( '/^(?:00)?(?:88)?(01[3-9]\d{8})$/', $digits, $m ) ) {
		return '88' . $m[1];
	}

	return ltrim( $digits, '0' );
}

/**
 * The store's WhatsApp number, or '' if not set.
 *
 * @return string
 */
function oow_phone() {
	return oow_normalize_phone( oow_option( 'phone' ) );
}

/**
 * Build a wa.me link.
 *
 * @param string $message Message text.
 * @return string
 */
function oow_link( $message ) {
	$url = 'https://wa.me/' . oow_phone();

	if ( '' !== $message ) {
		$url .= '?text=' . rawurlencode( $message );
	}

	return $url;
}

/**
 * Price as plain text, e.g. "৳ 500.00".
 *
 * @param float $amount Amount.
 * @return string
 */
function oow_plain_price( $amount ) {
	return trim( html_entity_decode( wp_strip_all_tags( wc_price( $amount ) ), ENT_QUOTES, 'UTF-8' ) );
}

/**
 * Replace {placeholders} in a template and tidy blank lines.
 *
 * @param string $template Template.
 * @param array  $values   Placeholder => value (without braces).
 * @return string
 */
function oow_fill( $template, $values ) {
	foreach ( $values as $key => $value ) {
		$template = str_replace( '{' . $key . '}', (string) $value, $template );
	}

	// Remove lines that became empty and collapse 3+ newlines.
	$template = preg_replace( "/^[ \t]+$/m", '', $template );
	$template = preg_replace( "/\n{3,}/", "\n\n", $template );

	return trim( $template );
}

/**
 * Product message. {quantity} and {variation} are left for the browser
 * to fill in on product pages, where the customer picks them.
 *
 * @param WC_Product $product  Product.
 * @param bool       $for_page True on the single product page (keep live placeholders).
 * @return string
 */
function oow_product_message( $product, $for_page = false ) {
	$values = array(
		'product_name' => $product->get_name(),
		'price'        => $product->is_type( 'variable' ) && $for_page ? '{price}' : oow_plain_price( wc_get_price_to_display( $product ) ),
		'sku'          => $product->get_sku(),
		'url'          => get_permalink( $product->get_id() ),
		'site_name'    => wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ),
	);

	if ( ! $for_page ) {
		$values['quantity']  = 1;
		$values['variation'] = '';
	}

	$message = oow_fill( oow_option( 'product_message' ), $values );

	return (string) apply_filters( 'oow_product_message', $message, $product, $for_page );
}

/**
 * Cart message.
 *
 * @return string
 */
function oow_cart_message() {
	if ( ! WC()->cart || WC()->cart->is_empty() ) {
		return '';
	}

	$lines = array();

	foreach ( WC()->cart->get_cart() as $item ) {
		$product = $item['data'];

		if ( ! $product instanceof WC_Product ) {
			continue;
		}

		$name = $product->get_name();

		if ( $product->is_type( 'variation' ) ) {
			$name = $product->get_title() . ' (' . wc_get_formatted_variation( $product, true, false, false ) . ')';
		}

		$lines[] = sprintf(
			'• %1$s × %2$d = %3$s',
			$name,
			$item['quantity'],
			oow_plain_price( wc_get_price_to_display( $product, array( 'qty' => $item['quantity'] ) ) )
		);
	}

	$message = oow_fill(
		oow_option( 'cart_message' ),
		array(
			'cart_items' => implode( "\n", $lines ),
			'cart_total' => trim( html_entity_decode( wp_strip_all_tags( WC()->cart->get_total() ), ENT_QUOTES, 'UTF-8' ) ),
			'site_name'  => wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ),
		)
	);

	return (string) apply_filters( 'oow_cart_message', $message );
}
