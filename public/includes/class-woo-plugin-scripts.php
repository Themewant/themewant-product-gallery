<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Handles frontend script and style enqueueing for Easy product Gallery.
 *
 * Uses a singleton pattern to manage assets and localize gallery settings
 * based on product-specific configurations.
 *
 * @since 1.0.0
 */
final class TWPGY_Enqueue_Scripts {

    /**
     * Singleton instance.
     *
     * @since 1.0.0
     *
     * @var self|null
     */
    private static $instance = null;

    /**
     * Returns the singleton instance of the class.
     *
     * @since 1.0.0
     *
     * @return self
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Registers hooks for enqueueing scripts and modifying script tags.
     *
     * @since 1.0.0
     */
    private function __construct() {
        add_action( 'wp_enqueue_scripts', [ $this, 'twpgy_enqueue_scripts' ],1000 );
        add_filter( 'script_loader_tag', [ $this, 'twpgy_add_defer_attribute' ], 10, 2 );
        add_filter( 'body_class', [ $this, 'twpgy_add_body_class' ] );
    }

    /**
     * Enqueues frontend styles and scripts for the product gallery.
     *
     * Also localizes dynamic gallery options for JavaScript usage.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function twpgy_enqueue_scripts() {

        if ( ! function_exists( 'is_product' ) || ! is_product() ) {
            return;
        }

        wp_enqueue_style( 'twpgy-woo-gallery-style', TWPGY_GALLERY_PL_URL . 'public/assets/css/public.css', [], TWPGY_VERSION );
        wp_enqueue_style( 'twpgy-woo-gallery-slider-style', TWPGY_GALLERY_PL_URL . 'public/assets/lib/swiper-slider/css/swiper-bundle.min.css', [], TWPGY_VERSION );
        wp_enqueue_style( 'twpgy-woo-gallery-fancybox-style', TWPGY_GALLERY_PL_URL . 'public/assets/lib/fancybox/css/jquery.fancybox.min.css', [], TWPGY_VERSION );

        $theme = wp_get_theme();

        if ( $theme->get_template() === 'woodmart' ) {
            wp_enqueue_style(
                'twpgy-easy-woodmart-css',
                TWPGY_GALLERY_PL_URL . 'public/assets/css/woodmart.css',
                [],
                TWPGY_VERSION
            );
        }

        $css = TWPGY_Dynamic_CSS::get_instance()->twpgy_generate_inline_css(); 

        if ( ! empty( $css ) ) {
            // Note: The $css variable contains only CSS property values (colors, sizes, etc.)
            // that have been previously sanitized and escaped using sanitize_hex_color(),
            // absint(), and esc_attr() before being added to the $css string.
            // Therefore, direct output here is safe.
            wp_add_inline_style( 'twpgy-woo-gallery-style', wp_strip_all_tags( $css ) );
        }

        $options  = get_option( 'twpgy_woo_gallery_options', [] );
        $settings = $options['settings'] ?? [];

        if ( ( $settings['enable_gallery'] ?? 'off' ) === 'on' && is_product() ) {
            wp_add_inline_style( 'twpgy-woo-gallery-style', 'body.twpgy-gallery-enabled { overflow-x: visible !important; }' );
        }

        wp_enqueue_script( 'twpgy-woo-gallery-fancybox-script', TWPGY_GALLERY_PL_URL . 'public/assets/lib/fancybox/js/jquery.fancybox.min.js', ['jquery'], TWPGY_VERSION, true );
        wp_enqueue_script( 'twpgy-woo-gallery-slider-script', TWPGY_GALLERY_PL_URL . 'public/assets/lib/swiper-slider/js/swiper-bundle.min.js', ['jquery'], TWPGY_VERSION, true );
        wp_enqueue_script( 'twpgy-woo-gallery-script', TWPGY_GALLERY_PL_URL . 'public/assets/js/public.js', ['jquery'], TWPGY_VERSION, true );

        wp_localize_script( 'twpgy-woo-gallery-script', 'twpgywoglry_vars', $this->twpgy_get_plugin_options() );
    }

    /**
     * Adds defer attribute to the main gallery script.
     *
     * @since 1.0.0
     *
     * @param string $tag    Script tag HTML.
     * @param string $handle Script handle.
     *
     * @return string Modified or original script tag.
     */
    public function twpgy_add_defer_attribute( $tag, $handle ) {
        if ( 'twpgy-woo-gallery-script' === $handle ) {
            return str_replace( ' src', ' defer="defer" src', $tag );
        }

        return $tag;
    }

    /**
     * Adds a body class when the gallery is enabled.
     *
     * @since 1.0.2
     *
     * @param array $classes Existing body classes.
     *
     * @return array Modified body classes.
     */
    public function twpgy_add_body_class( $classes ) {
        $options  = get_option( 'twpgy_woo_gallery_options', [] );
        $settings = $options['settings'] ?? [];

        if ( ( $settings['enable_gallery'] ?? 'off' ) === 'on' && is_product() ) {
            $classes[] = 'twpgy-gallery-enabled';
        }

        return $classes;
    }

    /**
     * Retrieves localized plugin options for the current product.
     *
     * Merges default, global, and product-specific layout settings
     * for frontend JavaScript consumption.
     *
     * @since 1.0.0
     *
     * @param int|null $product_id Optional. Product ID. Defaults to current product.
     *
     * @return array Localized plugin options.
     */
    private function twpgy_get_plugin_options( $product_id = null ) {

        $product_id = $product_id ?: get_the_ID();

        $settings = $this->twpgy_get_final_settings_for_product( $product_id );

        $gap_settings = $settings['eapgy_thumbs_slides_space'] ?? [];
        $h_gap        = isset( $gap_settings['horigap'] ) && $gap_settings['horigap'] !== '' ? intval( $gap_settings['horigap'] ) : 15;
        $v_gap        = isset( $gap_settings['vergap'] )  && $gap_settings['vergap']  !== '' ? intval( $gap_settings['vergap'] )  : $h_gap;

        return [
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'twpgy_woo_gallery_nonce' ),
            'pluginOptions' => [
                'autoplay_video'           => ! empty( $settings['autoplay_video'] ) && $settings['autoplay_video'] === 'on',
                'video_play_mode'          => $settings['video_play_mode'] ?? 'popup',
                'loop_video'               => ! empty( $settings['loop_video'] ) && $settings['loop_video'] === 'on' ? 1 : 0,
                'lightbox_counter'         => $settings['lightbox_counter'] ?? 'off',
                'video_preloader'          => ! empty( $settings['video_preloader'] ) && $settings['video_preloader'] === 'on',
                'gallery_for_variation'    => ! empty( $settings['gallery_for_variation'] ) && $settings['gallery_for_variation'] === 'on' ? 'on' : 'off',
                'thumbs_slider_mouse_wheel'=> ! empty( $settings['thumbs_slider_mouse_wheel'] ) && $settings['thumbs_slider_mouse_wheel'] === 'on',
                'slider_free_mode_enable'  => ! empty( $settings['slider_free_mode_enable'] ) && $settings['slider_free_mode_enable'] === 'on',
                'thumbs_slides_space_between' => $h_gap,
                'thumbs_slides_horigap'    => $h_gap,
                'thumbs_slides_vergap'     => $v_gap,
                'thumbs_slides_per_view'   => $settings['thumbs_slides_per_view'] ?? 4,
                'slider_orientaiton'       => $settings['slider_orientaiton'] ?? 'horizontal',
                'pagination_layout'        => $settings['pagination_layout'] ?? 'dot_style',
                'twpgy_slider_speed'       => $settings['twpgy_slider_speed'] ?? 500,
                'slide_effect'             => $settings['slide_effect'] ?? 'slide',
                'gallery_layout'           => $settings['gallery_layout'] ?? '',
                'view360degree_layout'     => $settings['view360degree_layout'] ?? '',
                'zoom_style'               => $settings['zoom_style'] ?? '',
                'slider_mouse_wheel'       => ! empty( $settings['slider_mouse_wheel'] ) && $settings['slider_mouse_wheel'] === 'on',
                'slider_autoplay'          => ! empty( $settings['slider_autoplay'] ) && $settings['slider_autoplay'] === 'on',
                'slider_infinite'          => ! empty( $settings['slider_infinite'] ) && $settings['slider_infinite'] === 'on',
                'enable_thumbs_slider'     => ! empty( $settings['enable_thumbs_slider'] ) && $settings['enable_thumbs_slider'] === 'on',
                'gallery_image_zoom'       => ! empty( $settings['gallery_image_zoom'] ) && $settings['gallery_image_zoom'] === 'on',
                'slider_rtl'               => ! empty( $settings['slider_rtl'] ) && $settings['slider_rtl'] === 'on',
                'slide_adaptive_height'    => ! empty( $settings['slide_adaptive_height'] ) && $settings['slide_adaptive_height'] === 'on',
            ],
        ];

        
    }

    /**
     * Resolves final gallery settings for a specific product.
     *
     * Combines default settings, global options, and layout-specific
     * overrides when applicable.
     *
     * @since 1.0.0
     *
     * @param int $product_id Product ID.
     *
     * @return array Final gallery settings.
     */
    public function twpgy_get_final_settings_for_product( $product_id ) {

        $default_settings = [
            'autoplay_video'                 => false, 
            'loop_video'                     => 0,
            'lightbox_counter'               => 'off',
            'video_preloader'                => false,
            'gallery_for_variation'          => 'off',
            'thumbs_slider_mouse_wheel'      => false,
            'thumbs_slides_per_view'         => 4,
            'slider_orientaiton'             => 'horizontal',
            'pagination_layout'              => 'dot_style',
            'twpgy_slider_speed'             => 500,
            'slide_effect'                   => 'slide',
            'gallery_layout'                 => 'thumbs_bottom',
            'zoom_style'                     => 'default',
            'slider_mouse_wheel'             => false,
            'slider_autoplay'                => false,
            'slider_infinite'                => false,
            'enable_thumbs_slider'           => false,
            'gallery_image_zoom'             => false,
            'slider_rtl'                     => false,
            'slide_adaptive_height'          => false,
        ];

        $default_settings = apply_filters( 'twpgy_default_gallery_settings', $default_settings );

        $options = get_option( 'twpgy_woo_gallery_options', [] );
        $global_settings = $options['settings'] ?? [];

        $final_settings = array_merge( $default_settings, $global_settings );

        $assign_layouts = get_posts( [
            'post_type'      => 'twpgy_woo_layouts',
            'posts_per_page' => -1,
            'fields'         => 'ids',
        ] );

        foreach ( $assign_layouts as $layout_id ) {
            $assigned_products = get_post_meta( $layout_id, 'twpgy_assigned_products', true );

            if ( is_array( $assigned_products ) && in_array( (int) $product_id, array_map( 'intval', $assigned_products ), true ) ) {
                $layout_meta = get_post_meta( $layout_id, 'twpgy_layout_settings', true );

                if ( is_array( $layout_meta ) && isset( $layout_meta['post_meta'] ) ) {

                    return array_merge( $final_settings, $layout_meta['post_meta'] );

                }
            }
        }

        return $final_settings;
    }
}