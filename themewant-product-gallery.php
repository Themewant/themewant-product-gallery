<?php
/**
 * Plugin Name: Themewant Product Gallery
 * Description: Easy Product Gallery plugin allows you to add extra images for each product variation, so customers can view different images when switching between variations. Instantly upgrade the default WooCommerce product gallery into an attractive thumbnail-based gallery slider on the single product page to help boost sales.
 * Plugin URI:  https://themewant.com/plugins/demo/easygallery/
 * Author:      Themewant
 * Author URI:  http://themewant.com/
 * Requires at least: 5.8
 * Requires PHP:7.4
 * Version:     1.1.2
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: themewant-product-gallery
 * Requires Plugins:  woocommerce
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'TWPGY_VERSION', '1.1.2' );
define( 'TWPGY_GALLERY_PL_ROOT', __FILE__ );
define( 'TWPGY_GALLERY_PL_URL', plugins_url( '/', TWPGY_GALLERY_PL_ROOT ) );
define( 'TWPGY_GALLERY_PL_PATH', plugin_dir_path( TWPGY_GALLERY_PL_ROOT ) );
define( 'TWPGY_GALLERY_DIR_URL', plugin_dir_url( TWPGY_GALLERY_PL_ROOT ) );
define( 'TWPGY_GALLERY_PLUGIN_BASE', plugin_basename( TWPGY_GALLERY_PL_ROOT ) );
define( 'TWPGY_PLUGIN_NAME', 'Themewant Product Gallery' );

require __DIR__ . '/vendor/autoload.php';

/**
 * Declare WooCommerce HPOS compatibility
 */
function twpgy_declare_hpos_compatibility() {
    if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
            'custom_order_tables', // HPOS feature
            TWPGY_GALLERY_PL_ROOT,            
            true  // true = compatible
        );
    }
}

add_action( 'before_woocommerce_init', 'twpgy_declare_hpos_compatibility' );

function twpgy_woo_gallery_activation_func() {
	require_once TWPGY_GALLERY_PL_PATH . 'includes/class-woo-gallery-activation.php';
	TWPGY_Product_Gallery_Activator::activate();
    
}

register_activation_hook(__FILE__, 'twpgy_woo_gallery_activation_func');

require TWPGY_GALLERY_PL_PATH . 'includes/helper-function.php';
require TWPGY_GALLERY_PL_PATH . 'includes/class-easy-woo-gallery.php';


TWPGY_WOO_GALLERY::instance();


/**
 * Initialize the plugin tracker
 *
 * @return void
 */
function twpgy_appsero_init_tracker_themewant_product_gallery() {

    $client = new \Twpgy\Appsero\Client( 'b9b41b91-1430-481a-9777-e4da59d52450', 'Themewant Product Gallery', __FILE__ );

    // Active insights
    $client->insights()->init();

}

twpgy_appsero_init_tracker_themewant_product_gallery();