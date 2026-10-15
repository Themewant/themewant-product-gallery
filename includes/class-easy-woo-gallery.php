<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'TWPGY_WOO_GALLERY' ) ) {
    /**
     * TWPGY_WOO_GALLERY
     *
     * Main plugin class for Easy Woo Product Gallery.
     * Handles initialization, loading of public/admin files, custom image sizes,
     * and plugin settings links.
     *
     * Implements the singleton pattern to ensure only one instance is loaded.
     *
     * @package Themewant_Product_Gallery
     * @since 1.0.0
     */
    class TWPGY_WOO_GALLERY {

        /**
         * Singleton instance
         *
         * @var TWPGY_WOO_GALLERY|null
         */
        private static $_instance = null;


        /**
         * Current product ID (if needed for context)
         *
         * @var int
         */
        private $current_product_id = 0;

        /**
         * Get instance of the class (Singleton)
         *
         * Ensures only one instance of the class is loaded or can be loaded.
         *
         * @since 1.0.0
         * @access public
         * @static
         *
         * @return TWPGY_WOO_GALLERY Singleton instance
         */
        public static function instance() {
            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        /**
         * Constructor
         *
         * Registers plugin hooks for initialization and custom image sizes.
         *
         * @since 1.0.0
         * @access public
         */
        private function __construct() {

            add_action( 'plugins_loaded', [ $this, 'twpgy_init' ] );
            add_action( 'after_setup_theme', [$this, 'twpgy_add_image_sizes'] );
            add_action( 'after_setup_theme', [$this, 'twpgy_grid_add_image_sizes'] );
            add_filter( 'wp_get_attachment_image_src', [ $this, 'twpgy_maybe_resize_image' ], 10, 4 );
            add_action( 'update_option_twpgy_woo_gallery_options', [ $this, 'twpgy_schedule_regeneration' ], 10, 2 );
            add_action( 'twpgy_regenerate_images', [ $this, 'twpgy_regenerate_product_images' ] );

        }

        /**
         * Initialize the plugin
         *
         * Loads required admin/public files and registers plugin action links.
         *
         * @since 1.0.0
         * @access public
         */
        public function twpgy_init() {

            add_filter( 'plugin_action_links_' . TWPGY_GALLERY_PLUGIN_BASE, [ $this, 'twpgy_woo_gallery_plugin_action_links' ], 10, 4 );
            require_once TWPGY_GALLERY_PL_PATH . 'admin/class-woo-product-gallery-admin.php';

            $this->twpgy_load_files();
               
        }

        /**
         * Load admin and public files conditionally
         *
         * @since 1.0.0
         * @access public
         *
         * @param int|null $product_id Optional product ID to load context-specific files.
         */
        public function twpgy_load_files( $product_id = null ) {

            require_once TWPGY_GALLERY_PL_PATH . 'public/class-woo-product-gallery-public.php';
           
        }

        /**
         * Add settings link to plugin action links
         *
         * @since 1.0.0
         * @access public
         *
         * @param array  $plugin_actions Existing plugin action links.
         * @param string $plugin_file    Plugin file path.
         * @param array  $plugin_data    Plugin metadata.
         * @param string $context        Plugin context.
         *
         * @return array Modified plugin action links
         */
        public function twpgy_woo_gallery_plugin_action_links( $plugin_actions, $plugin_file, $plugin_data, $context ) {

            $new_actions = array();

            $new_actions['twpgy_settings'] = sprintf(
                '<a href="%s">%s</a>',
                esc_url( admin_url( 'admin.php?page=twpgy-woo-gallery' ) ),
                esc_html__( 'Settings', 'themewant-product-gallery' )
            );

            return array_merge( $new_actions, $plugin_actions );
        }

        /**
         * Register custom image size for WooCommerce gallery images
         *
         * Default size is 440x500, can be overridden by plugin settings.
         *
         * @since 1.0.0
         * @access public
         */
        public function twpgy_add_image_sizes() {

            $options = get_option( 'twpgy_woo_gallery_options', array() );
            $design  = isset( $options['design'] ) && is_array( $options['design'] )
                ? $options['design']
                : array();

            $image_width  = 440;
            $image_height = 500;

            if ( isset( $design['preview_image_size'] ) && is_array( $design['preview_image_size'] ) ) {

                $image_size = $design['preview_image_size'];

                if ( ! empty( $image_size['width'] ) ) {
                    $image_width = absint( $image_size['width'] );
                }

                if ( ! empty( $image_size['height'] ) ) {
                    $image_height = absint( $image_size['height'] );
                }
            }

            add_image_size(
                'twpgy_woo_gallery_image_size',
                $image_width,
                $image_height,
                true
            );
        }

        /**
         * Register custom image size for WooCommerce gallery images
         *
         * Default size is 440x500, can be overridden by plugin settings.
         *
         * @since 1.0.0
         * @access public
         */
        public function twpgy_grid_add_image_sizes() {

            $options = get_option( 'twpgy_woo_gallery_options', array() );
            $design  = isset( $options['settings'] ) && is_array( $options['settings'] )
                ? $options['settings']
                : array();

            $image_width  = 440;
            $image_height = 500;

            if ( isset( $design['easyel_grid_large_image_size'] ) &&  $design['easyel_grid_large_image_size'] === 'custom')  {

           
                if ( isset( $design['grid_preview_large_image_size'] ) && is_array( $design['grid_preview_large_image_size'] ) ) {

                    $image_size = isset( $design['grid_preview_large_image_size'] ) ? $design['grid_preview_large_image_size'] : '';

                    if ( ! empty( $image_size['width'] ) ) {
                        $image_width = absint( $image_size['width'] );
                    }

                    if ( ! empty( $image_size['height'] ) ) {
                        $image_height = absint( $image_size['height'] );
                    }
                }

                add_image_size(
                    'twpgy_woo_gallery_image_size_grid_large',
                    $image_width,
                    $image_height,
                    true
                );
            }
        }

        /**
         * On-the-fly image resize (like WooCommerce's WC_Regenerate_Images)
         *
         * If the requested image size doesn't exist yet, generate it now.
         * This handles images uploaded BEFORE the custom size was registered.
         *
         * @param array|false  $image         Array of image data (url, width, height, is_intermediate).
         * @param int          $attachment_id Attachment ID.
         * @param string|int[] $size          Image size name or array of width/height.
         * @param bool         $icon          Whether to use icon.
         * @return array|false
         */
        public function twpgy_maybe_resize_image( $image, $attachment_id, $size, $icon ) {
            // Only handle our custom sizes.
            if ( ! in_array( $size, array( 'twpgy_woo_gallery_image_size', 'twpgy_woo_gallery_image_size_grid_large' ), true ) ) {
                return $image;
            }

            // If the image is already an intermediate size, it exists — skip.
            if ( is_array( $image ) && ! empty( $image[3] ) ) {
                return $image;
            }

            // Check if this size exists in attachment metadata.
            $metadata = wp_get_attachment_metadata( $attachment_id );
            if ( ! is_array( $metadata ) || empty( $metadata['file'] ) ) {
                return $image;
            }

            if ( isset( $metadata['sizes'][ $size ] ) ) {
                return $image;
            }

            // Size doesn't exist — generate it on the fly.
            $fullsizepath = get_attached_file( $attachment_id );
            if ( false === $fullsizepath || ! file_exists( $fullsizepath ) ) {
                return $image;
            }

            $global_sizes = wp_get_registered_image_subsizes();
            if ( ! isset( $global_sizes[ $size ] ) ) {
                return $image;
            }

            $target    = $global_sizes[ $size ];
            $editor    = wp_get_image_editor( $fullsizepath );

            if ( is_wp_error( $editor ) ) {
                return $image;
            }

            $resized = $editor->resize( $target['width'], $target['height'], $target['crop'] );
            if ( is_wp_error( $resized ) ) {
                return $image;
            }

            $saved = $editor->save();
            if ( is_wp_error( $saved ) ) {
                return $image;
            }

            // Update attachment metadata with the new size.
            $metadata['sizes'][ $size ] = array(
                'file'      => $saved['file'],
                'width'     => $saved['width'],
                'height'    => $saved['height'],
                'mime-type' => $saved['mime-type'],
            );
            wp_update_attachment_metadata( $attachment_id, $metadata );

            $upload_dir = wp_get_upload_dir();
            $image_url  = trailingslashit( $upload_dir['baseurl'] ) . trailingslashit( dirname( $metadata['file'] ) ) . $saved['file'];

            return array( $image_url, $saved['width'], $saved['height'], true );
        }

        /**
         * Schedule background image regeneration when settings change.
         *
         * @param mixed $old_value Old option value.
         * @param mixed $new_value New option value.
         */
        public function twpgy_schedule_regeneration( $old_value, $new_value ) {
            $old_design = isset( $old_value['design']['preview_image_size'] ) ? $old_value['design']['preview_image_size'] : '';
            $new_design = isset( $new_value['design']['preview_image_size'] ) ? $new_value['design']['preview_image_size'] : '';

            $old_grid = isset( $old_value['settings']['grid_preview_large_image_size'] ) ? $old_value['settings']['grid_preview_large_image_size'] : '';
            $new_grid = isset( $new_value['settings']['grid_preview_large_image_size'] ) ? $new_value['settings']['grid_preview_large_image_size'] : '';

            // If image size settings changed, schedule regeneration.
            if ( $old_design !== $new_design || $old_grid !== $new_grid ) {
                if ( ! wp_next_scheduled( 'twpgy_regenerate_images' ) ) {
                    wp_schedule_single_event( time() + 1, 'twpgy_regenerate_images' );
                }
            }
        }

        /**
         * Regenerate product images for custom sizes (background task).
         */
        public function twpgy_regenerate_product_images() {
            $product_ids = wc_get_products( array(
                'limit'  => -1,
                'return' => 'ids',
                'status' => 'publish',
            ) );

            foreach ( $product_ids as $product_id ) {
                $product       = wc_get_product( $product_id );
                $attachment_ids = array();

                if ( $product->get_image_id() ) {
                    $attachment_ids[] = $product->get_image_id();
                }

                $gallery_ids = $product->get_gallery_image_ids();
                if ( ! empty( $gallery_ids ) ) {
                    $attachment_ids = array_merge( $attachment_ids, $gallery_ids );
                }

                foreach ( array_unique( $attachment_ids ) as $attachment_id ) {
                    $fullsizepath = get_attached_file( $attachment_id );
                    if ( $fullsizepath && file_exists( $fullsizepath ) ) {
                        $metadata = wp_generate_attachment_metadata( $attachment_id, $fullsizepath );
                        wp_update_attachment_metadata( $attachment_id, $metadata );
                    }
                }
            }
        }

    }
}