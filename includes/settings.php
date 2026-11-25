<?php
/**
 * Settings tab: WooCommerce → Settings → WhatsApp.
 *
 * @package OOW
 */

defined( 'ABSPATH' ) || exit;

/**
 * Settings fields.
 *
 * @return array
 */
function oow_settings_fields() {
	$d = oow_defaults();

	$placeholders = __( 'Placeholders: {product_name} {variation} {quantity} {price} {sku} {url} {site_name}', 'oow' );

	return array(
		array(
			'title' => __( 'Order on WhatsApp', 'oow' ),
			'type'  => 'title',
			'desc'  => '' === oow_phone()
				? '<strong style="color:#b32d2e">' . esc_html__( 'Add your WhatsApp number to show the buttons.', 'oow' ) . '</strong>'
				: esc_html__( 'Customers tap the button and WhatsApp opens with the order message ready to send.', 'oow' ),
			'id'    => 'oow_main',
		),
		array(
			'title'       => __( 'WhatsApp number', 'oow' ),
			'desc'        => __( 'With country code, e.g. 8801712345678. A Bangladeshi number like 01712-345678 is converted automatically.', 'oow' ),
			'id'          => 'oow_phone',
			'type'        => 'text',
			'default'     => $d['phone'],
			'placeholder' => '8801712345678',
		),
		array(
			'title'   => __( 'Product page button', 'oow' ),
			'desc'    => __( 'Show on single product pages', 'oow' ),
			'id'      => 'oow_show_product',
			'type'    => 'checkbox',
			'default' => $d['show_product'],
		),
		array(
			'title'   => __( 'Shop page buttons', 'oow' ),
			'desc'    => __( 'Show under each product in the shop and category pages', 'oow' ),
			'id'      => 'oow_show_shop',
			'type'    => 'checkbox',
			'default' => $d['show_shop'],
		),
		array(
			'title'   => __( 'Cart button', 'oow' ),
			'desc'    => __( 'Show "Order the cart on WhatsApp" on the cart page', 'oow' ),
			'id'      => 'oow_show_cart',
			'type'    => 'checkbox',
			'default' => $d['show_cart'],
		),
		array(
			'title'   => __( 'Floating chat button', 'oow' ),
			'desc'    => __( 'Show a round chat button in the bottom-right corner of every page', 'oow' ),
			'id'      => 'oow_floating',
			'type'    => 'checkbox',
			'default' => $d['floating'],
		),
		array(
			'title'   => __( 'Hide "Add to cart"', 'oow' ),
			'desc'    => __( 'WhatsApp-only store: hide the normal Add to cart buttons', 'oow' ),
			'id'      => 'oow_hide_add_to_cart',
			'type'    => 'checkbox',
			'default' => $d['hide_add_to_cart'],
		),
		array(
			'type' => 'sectionend',
			'id'   => 'oow_main',
		),
		array(
			'title' => __( 'Buttons and messages', 'oow' ),
			'type'  => 'title',
			'id'    => 'oow_text',
		),
		array(
			'title'   => __( 'Product button text', 'oow' ),
			'id'      => 'oow_button_text',
			'type'    => 'text',
			'default' => $d['button_text'],
		),
		array(
			'title'   => __( 'Cart button text', 'oow' ),
			'id'      => 'oow_cart_button_text',
			'type'    => 'text',
			'default' => $d['cart_button_text'],
		),
		array(
			'title'   => __( 'Button color', 'oow' ),
			'id'      => 'oow_button_color',
			'type'    => 'color',
			'default' => $d['button_color'],
			'css'     => 'width:6em;',
		),
		array(
			'title'   => __( 'Product message', 'oow' ),
			'desc'    => $placeholders . '. ' . __( 'Use *text* for bold in WhatsApp.', 'oow' ),
			'id'      => 'oow_product_message',
			'type'    => 'textarea',
			'default' => $d['product_message'],
			'css'     => 'width:420px;height:150px;',
		),
		array(
			'title'   => __( 'Cart message', 'oow' ),
			'desc'    => __( 'Placeholders: {cart_items} {cart_total} {site_name}', 'oow' ),
			'id'      => 'oow_cart_message',
			'type'    => 'textarea',
			'default' => $d['cart_message'],
			'css'     => 'width:420px;height:110px;',
		),
		array(
			'title'   => __( 'Floating button message', 'oow' ),
			'id'      => 'oow_floating_message',
			'type'    => 'textarea',
			'default' => $d['floating_message'],
			'css'     => 'width:420px;height:60px;',
		),
		array(
			'type' => 'sectionend',
			'id'   => 'oow_text',
		),
	);
}

add_filter( 'woocommerce_settings_tabs_array', function ( $tabs ) {
	$tabs['oow'] = __( 'WhatsApp', 'oow' );
	return $tabs;
}, 60 );

add_action( 'woocommerce_settings_tabs_oow', function () {
	woocommerce_admin_fields( oow_settings_fields() );
} );

add_action( 'woocommerce_update_options_oow', function () {
	woocommerce_update_options( oow_settings_fields() );
} );

add_filter( 'plugin_action_links_' . plugin_basename( OOW_FILE ), function ( $links ) {
	array_unshift(
		$links,
		'<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=oow' ) ) . '">' . esc_html__( 'Settings', 'oow' ) . '</a>'
	);
	return $links;
} );
