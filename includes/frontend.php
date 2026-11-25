<?php
/**
 * Front-end buttons, shortcode and the cart redirect.
 *
 * @package OOW
 */

defined( 'ABSPATH' ) || exit;

/**
 * Simple chat-bubble icon (generic, not a brand logo).
 *
 * @return string
 */
function oow_icon() {
	return '<svg class="oow-icon" width="18" height="18" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path fill="currentColor" d="M12 3C6.98 3 3 6.58 3 11c0 2.2 1 4.2 2.62 5.65L5 21l4.2-2.1c.9.24 1.83.36 2.8.36 5.02 0 9-3.58 9-8.26S17.02 3 12 3zm-4 9.25a1.25 1.25 0 110-2.5 1.25 1.25 0 010 2.5zm4 0a1.25 1.25 0 110-2.5 1.25 1.25 0 010 2.5zm4 0a1.25 1.25 0 110-2.5 1.25 1.25 0 010 2.5z"/></svg>';
}

/**
 * Button HTML for a product.
 *
 * @param WC_Product $product Product.
 * @param string     $context single | loop | shortcode.
 * @return string
 */
function oow_product_button( $product, $context = 'single' ) {
	if ( ! $product instanceof WC_Product || '' === oow_phone() ) {
		return '';
	}

	if ( ! apply_filters( 'oow_show_product_button', true, $product, $context ) ) {
		return '';
	}

	$live     = 'single' === $context;
	$variable = $live && $product->is_type( 'variable' );
	$message  = oow_product_message( $product, $live );
	$fallback = oow_fill(
		$message,
		array(
			'quantity'  => 1,
			'variation' => '',
			'price'     => oow_plain_price( wc_get_price_to_display( $product ) ),
		)
	);

	$attrs = array(
		'class'         => 'oow-button oow-' . $context . '-button' . ( $live ? ' oow-product-button' : '' ) . ( $variable ? ' oow-disabled' : '' ),
		'href'          => oow_link( $fallback ),
		'target'        => '_blank',
		'rel'           => 'noopener nofollow',
		'data-phone'    => oow_phone(),
		'data-template' => $live ? $message : '',
		'data-price'    => oow_plain_price( wc_get_price_to_display( $product ) ),
		'data-choose'   => __( 'Please choose the product options first.', 'oow' ),
	);

	if ( $variable ) {
		$attrs['aria-disabled'] = 'true';
	}

	$html = '<a';
	foreach ( $attrs as $name => $value ) {
		if ( '' === $value && 'data-template' === $name ) {
			continue;
		}
		$html .= ' ' . $name . '="' . ( 'href' === $name ? esc_url( $value, array( 'https' ) ) : esc_attr( $value ) ) . '"';
	}
	$html .= '>' . oow_icon() . '<span>' . esc_html( oow_option( 'button_text' ) ) . '</span></a>';

	if ( $live ) {
		$html = '<div class="oow-wrap">' . $html . '<p class="oow-note" role="alert" hidden></p></div>';
	}

	return $html;
}

/* -------------------------------------------------------------------------
 * Placement
 * ---------------------------------------------------------------------- */

add_action( 'woocommerce_single_product_summary', function () {
	global $product;

	if ( oow_is_on( 'show_product' ) ) {
		echo oow_product_button( $product, 'single' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in oow_product_button().
	}
}, 35 );

add_action( 'woocommerce_after_shop_loop_item', function () {
	global $product;

	if ( oow_is_on( 'show_shop' ) ) {
		echo oow_product_button( $product, 'loop' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in oow_product_button().
	}
}, 15 );

/**
 * Cart button. It points to our own URL, which builds the message from the
 * current cart and redirects to WhatsApp, so it is always up to date
 * (also with the Cart block, where the cart changes without a page reload).
 *
 * @return string
 */
function oow_cart_button() {
	if ( '' === oow_phone() || ! WC()->cart || WC()->cart->is_empty() ) {
		return '';
	}

	return sprintf(
		'<a class="oow-button oow-cart-button" href="%1$s" target="_blank" rel="noopener nofollow">%2$s<span>%3$s</span></a>',
		esc_url( add_query_arg( 'oow-cart', '1', home_url( '/' ) ) ),
		oow_icon(),
		esc_html( oow_option( 'cart_button_text' ) )
	);
}

add_action( 'woocommerce_proceed_to_checkout', function () {
	if ( oow_is_on( 'show_cart' ) ) {
		echo oow_cart_button(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in oow_cart_button().
	}
}, 25 );

// Cart block: add the button after the block.
add_filter( 'render_block_woocommerce/cart', function ( $content ) {
	if ( oow_is_on( 'show_cart' ) ) {
		$content .= '<div class="oow-cart-block-wrap">' . oow_cart_button() . '</div>';
	}
	return $content;
} );

add_action( 'template_redirect', function () {
	if ( empty( $_GET['oow-cart'] ) || '' === oow_phone() ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only redirect.
		return;
	}

	$message = oow_cart_message();

	if ( '' === $message ) {
		wp_safe_redirect( wc_get_cart_url() );
		exit;
	}

	nocache_headers();
	wp_redirect( oow_link( $message ) ); // phpcs:ignore WordPress.Security.SafeRedirect.wp_redirect_wp_redirect -- always a wa.me URL we built.
	exit;
} );

add_action( 'wp_footer', function () {
	if ( ! oow_is_on( 'floating' ) || '' === oow_phone() ) {
		return;
	}

	printf(
		'<a class="oow-floating" href="%1$s" target="_blank" rel="noopener nofollow" aria-label="%2$s">%3$s</a>',
		esc_url( oow_link( oow_option( 'floating_message' ) ), array( 'https' ) ),
		esc_attr__( 'Chat with us on WhatsApp', 'oow' ),
		oow_icon() // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG.
	);
} );

/**
 * Shortcode: [order_on_whatsapp] or [order_on_whatsapp id="123"].
 * On a product page it uses the current product. [order_on_whatsapp cart="yes"] shows the cart button.
 */
add_shortcode( 'order_on_whatsapp', function ( $atts ) {
	$atts = shortcode_atts(
		array(
			'id'   => 0,
			'cart' => 'no',
		),
		$atts,
		'order_on_whatsapp'
	);

	if ( 'yes' === $atts['cart'] ) {
		return oow_cart_button();
	}

	$product = $atts['id'] ? wc_get_product( absint( $atts['id'] ) ) : wc_get_product( get_the_ID() );

	return $product ? oow_product_button( $product, is_product() ? 'single' : 'shortcode' ) : '';
} );

/* -------------------------------------------------------------------------
 * Assets
 * ---------------------------------------------------------------------- */

add_action( 'wp_enqueue_scripts', function () {
	if ( '' === oow_phone() ) {
		return;
	}

	$color = sanitize_hex_color( oow_option( 'button_color' ) );
	$color = $color ? $color : '#25D366';

	$css = '.oow-button{display:inline-flex;align-items:center;gap:.5em;margin-top:.75em;padding:.75em 1.25em;border-radius:4px;background:' . $color . ';color:#fff!important;font-weight:600;line-height:1.2;text-decoration:none!important;cursor:pointer}'
		. '.oow-button:hover,.oow-button:focus{filter:brightness(.92);color:#fff}'
		. '.oow-button.oow-disabled{opacity:.55}'
		. '.oow-loop-button{font-size:.875em;padding:.55em .9em}'
		. '.oow-cart-button{display:flex;justify-content:center;width:100%;box-sizing:border-box}'
		. '.oow-note{margin:.5em 0 0;font-size:.875em;color:#b32d2e}'
		. '.oow-floating{position:fixed;right:20px;bottom:20px;z-index:9999;display:flex;align-items:center;justify-content:center;width:56px;height:56px;border-radius:50%;background:' . $color . ';color:#fff;box-shadow:0 4px 12px rgba(0,0,0,.25)}'
		. '.oow-floating .oow-icon{width:28px;height:28px}';

	if ( oow_is_on( 'hide_add_to_cart' ) ) {
		$css .= '.single_add_to_cart_button,.add_to_cart_button,.wc-block-components-product-button__button{display:none!important}';
	}

	wp_register_style( 'oow', false, array(), OOW_VERSION );
	wp_enqueue_style( 'oow' );
	wp_add_inline_style( 'oow', $css );

	if ( is_product() ) {
		wp_enqueue_script( 'oow', OOW_URL . 'assets/js/order-on-whatsapp.js', array( 'jquery' ), OOW_VERSION, true );
	}
} );
