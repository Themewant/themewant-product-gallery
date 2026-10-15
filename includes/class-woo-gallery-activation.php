<?php
/**
 * Plugin Activator Class
 *
 * Handles setting up default options when the Easy Product Gallery plugin is activated.
 *
 * @package Themewant_Product_Gallery
 * @since 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * TWPGY_Product_Gallery_Activator
 *
 * Class responsible for plugin activation tasks, including initializing default plugin settings.
 *
 * @since 1.0.0
 */
class TWPGY_Product_Gallery_Activator {

	/**
	 * Run on plugin activation
	 *
	 * Initializes default plugin settings if they do not exist.
	 * If settings already exist, merges them with defaults to ensure all keys are present.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @return void
	 */
	public static function activate() {

		$option_key = 'twpgy_woo_gallery_options';

		$default_options = array(
			'settings' => array(
				'enable_gallery'              => 'on',
				'gallery_layout'              => 'thumbs_bottom',
				'gallery_for_variation'       => 'on',
				'gallery_video'               => 'on',
				'gallery_image_zoom'          => 'on',
				'gallery_lightbox'            => 'on',
				'enable_thumbs_slider'        => 'on',
				'enable_thumbs_nav'           => 'on',
				'enable_main_product_nav'     => 'on',
				'thumbs_slides_per_view'      => 3,
				'thumbs_slides_space_between' => 15,
				'lightbox_caption'            => 'on',
				'lightbox_counter'            => 'on',
				'video_play_mode'             => 'popup',
				'video_play_icon_size'        => 85,
				'video_play_icon_position'    => 'middle',
				'video_preloader'             => 'on',
			),
		);

		$saved_options = get_option( $option_key, array() );

		if ( empty( $saved_options ) ) {
			add_option( $option_key, $default_options );
			return;
		}

		$merged = array_replace_recursive( $default_options, $saved_options );

		update_option( $option_key, $merged );
	}
}