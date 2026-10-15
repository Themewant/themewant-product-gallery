<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Easy Product Gallery Template: Product Images
 *
 * @package Themewant_Product_Gallery
 * @since 1.0.0
 */

$twpgy_product_id = get_the_ID();
$twpgy_product    = wc_get_product( $twpgy_product_id );

if ( ! is_object( $twpgy_product ) ) {
    return;
}

// Get plugin settings
$twpgy_options          = get_option( 'twpgy_woo_gallery_options', [] );
$twpgy_settings         = $twpgy_options['settings'] ?? [];
$twpgy_product_type     = $twpgy_product->get_type();
$twpgy_gallery_view     = TWPGY_Woo_Gallery_View::get_instance();

$twpgy_gallery_html = $twpgy_gallery_view->twpgy_get_product_gallery_html(
    $twpgy_product_id
);

/* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */
printf( '%s', twpgy_kses( $twpgy_gallery_html ?? '' ) );