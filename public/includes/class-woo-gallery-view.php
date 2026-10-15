<?php
/**
 * WooCommerce gallery view handler for Easy Product Gallery.
 *
 * Responsible for rendering product gallery HTML, handling AJAX requests,
 * variation galleries, thumbnails, sliders, zoom, video, and lightbox output.
 *
 * @since 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Handles frontend rendering of WooCommerce product galleries.
 *
 * Manages gallery output, variation-specific images, layout-based settings,
 * and AJAX-powered gallery refresh for Easy Product Gallery.
 *
 * @since 1.0.0
 */
class TWPGY_Woo_Gallery_View {

    /**
     * Holds the singleton instance.
     *
     * @since 1.0.0
     *
     * @var self|null
     */
    private static $_instance = null;

    /**
     * Returns the singleton instance of the gallery view.
     *
     * Ensures only one instance of the class is loaded.
     *
     * @since 1.0.0
     *
     * @return self
     */
    public static function get_instance() {
        if ( null === self::$_instance ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Constructor
     *
     * Registers AJAX handlers for fetching product gallery HTML.
     *
     * @since 1.0.0
     */
    public function __construct() {

        add_action( 'wp_ajax_twpgy_get_product_gallery_html', [ $this, 'twpgy_ajax_get_product_gallery_html' ] );
        add_action( 'wp_ajax_nopriv_twpgy_get_product_gallery_html', [ $this, 'twpgy_ajax_get_product_gallery_html' ] );
    }

    /**
     * AJAX handler that returns the gallery HTML for a product variation.
     *
     * Verifies the nonce, only serves products the current visitor is allowed
     * to view, and ensures the variation belongs to the requested product.
     *
     * @since 1.1.2
     *
     * @return void
     */
    public function twpgy_ajax_get_product_gallery_html() {

        check_ajax_referer( 'twpgy_woo_gallery_nonce', 'nonce' );

        $product_id   = isset( $_POST['product_id'] ) ? absint( wp_unslash( $_POST['product_id'] ) ) : 0;
        $variation_id = isset( $_POST['variation_id'] ) ? absint( wp_unslash( $_POST['variation_id'] ) ) : 0;

        $product = $product_id ? wc_get_product( $product_id ) : false;

        if (
            ! $product
            || $product->is_type( 'variation' )
            || ( 'publish' !== $product->get_status() && ! current_user_can( 'read_post', $product_id ) )
            || post_password_required( $product_id )
        ) {
            wp_die( '', '', [ 'response' => 403 ] );
        }

        if ( $variation_id ) {
            $variation = wc_get_product( $variation_id );

            if ( ! $variation || ! $variation->is_type( 'variation' ) || $variation->get_parent_id() !== $product_id ) {
                wp_die( '', '', [ 'response' => 403 ] );
            }
        }

        $this->twpgy_get_product_gallery_html( $product_id, $variation_id ? $variation_id : null );

        wp_die();
    }

    /**
     * Filters WooCommerce sale flash markup.
     *
     * @since 1.0.0
     *
     * @param string     $html    Sale flash HTML.
     * @param WP_Post    $post    Product post object.
     * @param WC_Product $product WooCommerce product object.
     *
     * @return string Filtered sale flash HTML.
     */
    public function twpgy_woocommerce_get_sale_flash( $html, $post, $product ) {
        if ( $product->is_on_sale() ) {
            $html = '<span class="onsale">' . esc_html__( 'Sale!', 'themewant-product-gallery' ) . '</span>';
        }

        return $html;
    }

    /**
     * Outputs WooCommerce product gallery HTML.
     *
     * Handles gallery rendering for simple and variable products,
     * including variation-specific images, sliders, thumbnails,
     * zoom, video support, and layout-based configurations.
     *
     * Called directly from the product template, and by the AJAX handler
     * after the request has been validated.
     *
     * @since 1.0.0
     * @since 1.1.2 Added the `$variation_id` parameter; no longer reads `$_POST`.
     *
     * @param int|null $product_id   Optional. Product ID. Defaults to current product.
     * @param int|null $variation_id Optional. Variation ID whose gallery should be shown.
     *
     * @return void|string Outputs HTML or returns error message.
     */
    public function twpgy_get_product_gallery_html( $product_id = null, $variation_id = null ) {

        $product = wc_get_product( $product_id );
        if ( ! $product ) {
            return '<p>' . esc_html__( 'Product not found', 'themewant-product-gallery' ) . '</p>';
        }

        $attachment_ids = $product->get_gallery_image_ids();
        $html = '';
        
        $default_values = [
            'enable_gallery'                 => 'off',
            'gallery_layout'                 => 'thumbs_bottom',
            'gallery_video'                  => 'on',
            'video_play_mode'                => 'popup',
            'gallery_image_zoom'             => 'on',
            'gallery_lightbox'               => 'on',
            'hide_feature_image'             => 'off',
            'enable_thumbs_slider'           => 'on',
            'thumbs_slider_image_size'       => 'thumbnail',
            'lightbox_icon_position'         => 'top_right',
            'lightbox_caption'               => 'off',
            'video_play_icon_position'       => 'middle',
            'enable_thumbs_nav'              => 'on',
            'enable_main_product_nav'        => 'on',
            'enable_main_product_pagination' => 'on',
            'hide_feature_image_vgallery' => 'off',
            'slider_orientaiton'             => 'horizontal',
            'thumbs_nav_visibility'          => 'hover',
            'main_product_nav_visibility'    => 'hover',
            'pagination_layout'              => 'dot_style',
            'zoom_style'                     => 'default',
            'main_product_pagination_visibility' => 'pagination-always',
        ];


        $twpgy_options = get_option( 'twpgy_woo_gallery_options' );
        $global_settings = $twpgy_options['settings'] ?? [];

        $plugin_settings = array_merge( $default_values, $global_settings );

        $assign_product_args = array(
            'post_type'      => 'twpgy_woo_layouts',
            'posts_per_page' => -1,
            'fields'         => 'ids',
        );

        $assign_layouts = get_posts( $assign_product_args );

        $current_search_id = ! empty( $product_id ) ? $product_id : get_the_ID();

        $plugin_settings = array_merge( $default_values, ( $twpgy_options['settings'] ?? [] ) );

        $attachment_ids_check = [];

        if ( $variation_id !== null ) {

            $variation_gallery = get_post_meta( $variation_id, 'twpgy_woo_gallery_slider', true );
            $variation_gallery_ids = json_decode( $variation_gallery, true );

            if ( ! is_array( $variation_gallery_ids ) ) {
                $variation_gallery_ids = [];
            }

            $parent_product_id = $product->get_parent_id()
                ? $product->get_parent_id()
                : $product->get_id();

            $main_feature_image_id      = get_post_thumbnail_id( $parent_product_id );
            $default_variation_image_id = get_post_thumbnail_id( $variation_id );

            // Remove the featured / variation image if they are already inside the
            // custom gallery so we can position them explicitly without duplicates.
            $variation_gallery_ids = array_values( array_diff(
                $variation_gallery_ids,
                array_filter([
                    $main_feature_image_id,
                    $default_variation_image_id
                ])
            ) );

            // The variation's own default image is always positioned first.
            if ( $default_variation_image_id ) {
                array_unshift( $variation_gallery_ids, $default_variation_image_id );
            }

            // Add the parent product featured image at the very last position,
            // and only when the hide toggle is OFF.
            if ( $plugin_settings['hide_feature_image_vgallery'] === 'off' && $main_feature_image_id ) {
                $variation_gallery_ids[] = $main_feature_image_id;
            }

            $attachment_ids = array_values( $variation_gallery_ids );
            $attachment_ids_check = array_values( $variation_gallery_ids );
        }

        $current_search_id = ! empty( $product_id ) ? $product_id : get_the_ID();

        $toggle_keys = [
            'gallery_video',
            'gallery_image_zoom',
            'gallery_lightbox',
            'enable_thumbs_slider',
            'enable_thumbs_nav',
            'enable_main_product_nav',
            'enable_main_product_pagination',
            'enable_gallery',
            'hide_feature_image',
            'hide_feature_image_vgallery',
        ];

        $apply_settings = function ( array $source ) use ( &$plugin_settings, $toggle_keys ) {

            foreach ( $plugin_settings as $key => $default_val ) {

                if ( array_key_exists( $key, $source ) ) {
                    $plugin_settings[ $key ] = $source[ $key ];
                }
                elseif ( in_array( $key, $toggle_keys, true ) ) {
                    $plugin_settings[ $key ] = 'off';
                }
            }
        };

        $layout_applied = false;

        foreach ( $assign_layouts as $layout_id ) {

            $assigned_products = get_post_meta( $layout_id, 'twpgy_assigned_products', true );

            if (
                ! is_array( $assigned_products ) ||
                ! in_array( (int) $current_search_id, array_map( 'intval', $assigned_products ), true )
            ) {
                continue;
            }

            $layout_meta = get_post_meta( $layout_id, 'twpgy_layout_settings', true );

            if ( isset( $layout_meta['post_meta'] ) && is_array( $layout_meta['post_meta'] ) ) {
                $apply_settings( $layout_meta['post_meta'] );
                $layout_applied = true;
            }

            break;
        }

        if ( ! $layout_applied ) {
            $apply_settings( $global_settings );
        }


        $grid_image_large_size          = isset( $plugin_settings['easyel_grid_large_image_size'] ) ? $plugin_settings['easyel_grid_large_image_size'] : '';
        $easyel_grid_small_image_size   = isset( $plugin_settings['easyel_grid_small_image_size'] ) ? $plugin_settings['easyel_grid_small_image_size'] : '';
        $easyel_slider_large_image_size = isset( $plugin_settings['easyel_slider_large_image_size'] ) ? $plugin_settings['easyel_slider_large_image_size'] : '';
        $thumbs_slider_image_size       = isset( $plugin_settings['thumbs_slider_image_size'] ) ? $plugin_settings['thumbs_slider_image_size'] : '';

        $preview_image_size_large = $grid_image_large_size;

        if( $grid_image_large_size === 'custom' ) {
            $preview_image_size_large       = 'twpgy_woo_gallery_image_size_grid_large';
        }

        $preview_image_size = $easyel_slider_large_image_size;

        if( $easyel_slider_large_image_size === 'custom' ) {
            $preview_image_size             = 'twpgy_woo_gallery_image_size';
        }

        $thumbnail_image_size           = $thumbs_slider_image_size;
        $gallery_image_zoom_class       = ( $plugin_settings['gallery_image_zoom'] === 'on' ) ? 'zoom-on-mousemove ' : '';
        $enable_thumbs_nav              = $plugin_settings['enable_thumbs_nav'] ?? 'off';
        $enable_main_product_nav        = $plugin_settings['enable_main_product_nav'] ?? 'off';
        $enable_main_product_pagination = $plugin_settings['enable_main_product_pagination'] ?? 'off';
        $slider_orientaiton             = $plugin_settings['slider_orientaiton'] ?? 'horizontal';
        $thumbs_nav_visibility          = $plugin_settings['thumbs_nav_visibility'] ?? 'always';
        $main_product_nav_visibility    = $plugin_settings['main_product_nav_visibility'] ?? 'always';
        $pagination_layout              = $plugin_settings['pagination_layout'] ?? 'dot_style';
        $mproduct_pagination_visibility = $plugin_settings['main_product_pagination_visibility'] ?? 'pagination-always';
        $zoom_style                     = $plugin_settings['zoom_style'] ?? 'default';
        $twpgy_thumbs_slides_space      = $plugin_settings['eapgy_thumbs_slides_space'] ?? ( $plugin_settings['twpgy_thumbs_slides_space'] ?? '' );
        $twpgy_bullet_position          = $plugin_settings['twpgy_bullet_position'] ?? '';


        $thumbnail_id   = $product->get_image_id();
        $slider_nav_btns = '<div class="twpgy-swiper-button-prev twpgy-swiper-nav-button"></div><div class="twpgy-swiper-button-next twpgy-swiper-nav-button"></div>';

        if ( $plugin_settings['hide_feature_image'] === 'off' && ( $variation_id === null || empty( $variation_id ) ) ) {
            $attachment_ids = array_values( array_diff( $attachment_ids, [ $thumbnail_id ] ) );
            array_unshift( $attachment_ids, $thumbnail_id );
        }

        $twpgy_gallery_exist = 'eapgy-not-found-gallery';

        if( ! empty( $product->get_gallery_image_ids() && $plugin_settings['enable_thumbs_slider'] === 'on' ) ) {
            $twpgy_gallery_exist = '';
        }

        $variation_gallery = get_post_meta( $variation_id, 'twpgy_woo_gallery_slider', true );

        $gallery_layout = $plugin_settings['gallery_layout'];

        if( $plugin_settings['enable_gallery'] === 'on') {

            /**
             * Filters extra CSS classes added to the gallery wrapper.
             *
             * @since 1.1.2
             *
             * @param string $classes         Space-separated class names. Default empty.
             * @param array  $plugin_settings Gallery settings for the product.
             */
            $twpgy_wrapper_classes = (string) apply_filters( 'twpgy_gallery_wrapper_classes', '', $plugin_settings );
        ?>

            <div class="twpgy-easy-woo-gallery-image-area woocommerce-product-gallery images <?php echo esc_attr( $twpgy_gallery_exist ); ?> layout-<?php echo esc_attr( $plugin_settings['gallery_layout'] ); ?> <?php echo esc_attr( $twpgy_wrapper_classes ); ?>" data-original-layout="layout-<?php echo esc_attr( $gallery_layout ); ?>">

                <?php
                $pro_template = apply_filters( 'twpgy_pro_grid_template', '', $gallery_layout, $attachment_ids );

                if ( twpgy_is_valid_template_path( $pro_template ) ) {
                    include $pro_template;

                } else {
                    if ( ! empty( $attachment_ids ) ) {
                        include 'sliders/preview-slider.php';
                    }

                    $gallery_image_ids = $product->get_gallery_image_ids();

                    if ( ( $plugin_settings['enable_thumbs_slider'] === 'on' && count( $attachment_ids ) > 0 && $plugin_settings['gallery_layout'] !== 'slider' ) && ( ! empty( $gallery_image_ids ) || ( isset( $variation_gallery ) && ! empty( $variation_gallery ) ) || ( is_array( $attachment_ids_check ) && count( $attachment_ids_check ) > 1 ) ) ) {
                        include 'sliders/thumb-slider.php';
                    }
                }
               
                ?>
            </div>
            <?php
        }
    }
}