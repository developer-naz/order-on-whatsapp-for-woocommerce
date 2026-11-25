<?php
/**
 * Plugin Name:          Order on WhatsApp for WooCommerce
 * Plugin URI:           https://github.com/developer-naz/order-on-whatsapp-for-woocommerce
 * Description:          Adds "Order on WhatsApp" buttons to product pages, the shop and the cart, plus an optional floating chat button. The message includes the product, chosen variation, quantity, price and link.
 * Version:              1.0.0
 * Author:               Md Nazmul Islam (developer-naz)
 * Author URI:           https://www.linkedin.com/in/developernaz/
 * License:              GPL-2.0-or-later
 * License URI:          https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:          oow
 * Requires at least:    6.0
 * Requires PHP:         7.4
 * Requires Plugins:     woocommerce
 * WC requires at least: 8.0
 *
 * @package OOW
 */

defined( 'ABSPATH' ) || exit;

define( 'OOW_VERSION', '1.0.0' );
define( 'OOW_FILE', __FILE__ );
define( 'OOW_PATH', plugin_dir_path( __FILE__ ) );
define( 'OOW_URL', plugin_dir_url( __FILE__ ) );

add_action( 'before_woocommerce_init', function () {
	if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', OOW_FILE, true );
	}
} );

require_once OOW_PATH . 'includes/functions.php';

add_action( 'plugins_loaded', function () {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	require_once OOW_PATH . 'includes/frontend.php';

	if ( is_admin() ) {
		require_once OOW_PATH . 'includes/settings.php';
	}
} );
