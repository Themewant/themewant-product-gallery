<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

if ( ! class_exists( 'TWPGY_Woo_Hooks' ) ) {

    /**
     * Handles WooCommerce hooks and integrations for Easy Woo Gallery.
     *
     * Extends gallery view logic and overrides default WooCommerce
     * product gallery behavior when enabled.
     *
     * @since 1.0.0
     */
    class TWPGY_Woo_Hooks extends TWPGY_Woo_Gallery_View {

        /**
         * Singleton instance.
         *
         * @since 1.0.0
         *
         * @var self|null
         */
        private static $_instance = null;

        /**
         * Returns the singleton instance of the class.
         *
         * @since 1.0.0
         *
         * @return self
         */
        public static function get_instance() {
            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self();
            }

            return self::$_instance;
        }

        /**
         * Initializes WooCommerce hooks and filters.
         *
         * @since 1.0.0
         */
        public function __construct() {

            parent::__construct();

            $twpgy_options = get_option( 'twpgy_woo_gallery_options', [] );
            $twpgy_options = $twpgy_options['settings'] ?? [];

            $auto_integration_location = $twpgy_options['auto_integration_location'] ?? '';

            // Filters
            add_filter( 'wc_get_template', [ $this, 'twpgy_gallery_template_part_override' ], 99, 2 );
            add_filter( 'woocommerce_sale_flash', [ $this, 'twpgy_remove_sale_flash_on_single_gallery' ], 11, 3 );

        }

        /**
         * Removes the sale flash badge from the single product gallery.
         *
         * @since 1.0.0
         *
         * @param string     $html    Sale badge HTML.
         * @param WP_Post    $post    Product post object.
         * @param WC_Product $product WooCommerce product object.
         *
         * @return string Modified sale badge HTML.
         */
        public function twpgy_remove_sale_flash_on_single_gallery( $html, $post, $product ) {

            if ( is_product() ) {
                return ''; 
            }

            return $html;
        }

        /**
         * Overrides WooCommerce product image template when gallery is enabled.
         *
         * @since 1.0.0
         *
         * @param string $template       Template file path.
         * @param string $template_name  Template name.
         *
         * @return string Final template path.
         */
        public function twpgy_gallery_template_part_override( $template, $template_name ) {

            if ( 'single-product/product-image.php' !== $template_name ) {
                return $template;
            }
            global $product;

            if ( ! $product instanceof WC_Product ) {
                return $template;
            }

            $settings = twpgy_get_display_settings_for_product( $product->get_id() );

            if ( ($settings['enable_gallery'] ?? 'off') !== 'on' ) {
                return $template;
            }

            $old_template = $template;

            if ( 'single-product/product-image.php' === $template_name && $product && ( has_post_thumbnail( $product->get_id() ) || count( $product->get_gallery_image_ids() ) > 0 ) ) {
                $template = TWPGY_GALLERY_PL_PATH . 'public/templates/product-images.php';
            }
            return $template;
            
        }
        
        /**
         * Iterates over an array using a generator to reduce memory usage.
         *
         * Useful for large datasets where loading the full array into memory
         * is unnecessary.
         *
         * @since 1.0.0
         *
         * @param array $array Input array.
         *
         * @return Generator Yields array values one by one.
         */
        public function twpgy_reduce_processor_use( $array ) {
            $array_length = count( $array );
            for ( $i = 0; $i < $array_length; $i++ ) {
                yield $array[ $i ];
            }
        }
    }

    // Initialize
    TWPGY_Woo_Hooks::get_instance();
}