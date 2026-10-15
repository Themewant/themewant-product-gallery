<?php
/**
 * Admin Settings Fields Configuration
 *
 * This file contains the class responsible for defining the admin settings fields
 * for the Easy Product Gallery plugin.
 *
 * @package Themewant_Product_Gallery
 * @since 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Class TWPGY_Admin_Settings_Fields.
 *
 * Handles all the admin settings fields for Easy Product Gallery plugin.
 * This class provides methods to retrieve configuration arrays for various
 * settings sections like general gallery and slider settings.
 *
 * @since 1.0.0
 */
class TWPGY_Admin_Settings_Fields {
    /**
     * Get general gallery settings fields.
     *
     * Defines the configuration for the general settings section, including
     * layout options, width, and visibility toggles.
     *
     * @since 1.0.0
     *
     * @return array Array of general gallery settings fields.
     */
    public function twpgy_woo_gallery_get_settings_fields() {

        $twpgy_woo_gallery_settings_fields = array();

        $layout_style = array(
            'thumbs_bottom' => array(
                'label' => __( 'Thumbs Bottom', 'themewant-product-gallery' ),
                'image' => TWPGY_GALLERY_DIR_URL . 'admin/assets/img/thumb-bottom.png',
                'demo'  => '#',
            ),
            'thumbs_left' => array(
                'label' => __( 'Thumbs Left', 'themewant-product-gallery' ),
                'image' => TWPGY_GALLERY_DIR_URL . 'admin/assets/img/thumbnail-left.png',
                'demo'  => '#',
            ),
            'thumbs_right' => array(
                'label' => __( 'Thumbs Right', 'themewant-product-gallery' ),
                'image' => TWPGY_GALLERY_DIR_URL . 'admin/assets/img/thumbnail-right.png',
                'demo'  => '#',
            ),
            'thumbs_top' => array(
                'label' => __( 'Thumbs Top', 'themewant-product-gallery' ),
                'image' => TWPGY_GALLERY_DIR_URL . 'admin/assets/img/thumbnail-top.png',
                'demo'  => '#',
            ),
            'slider' => array(
                'label' => __( 'Product Slider', 'themewant-product-gallery' ),
                'image' => TWPGY_GALLERY_DIR_URL . 'admin/assets/img/slider-layout.png',
                'demo'  => '#',
            ), 
            'carousel_two' => array(
                'label' => __( 'Carousel 2 Column', 'themewant-product-gallery' ),
                'image' => TWPGY_GALLERY_DIR_URL . 'admin/assets/img/carousel-two-column.png',
                'demo'  => '#',
            ),
        );

        $layout_options = apply_filters( 'twpgy_gallery_layout_options', $layout_style );
       

        $settings_fields = array(
            array(
                'id'    => 'label_sections_start',
                'name'  => 'label_sections_start',
                'type'  => 'section_start',
                'label' => __( 'General Settings', 'themewant-product-gallery' ),
            ),
            array(
                'id'    => 'enable_gallery',
                'name'  => 'enable_gallery',
                'type'  => 'checkbox',
                'label' => __( 'Enable Product Gallery', 'themewant-product-gallery' ),
            ),
            array(
                'id'    => 'gallery_layout',
                'name'  => 'gallery_layout',
                'type'  => 'image_select',
                'label' => __( 'Gallery Layout', 'themewant-product-gallery' ),
                'options' => $layout_options,
            ),
            array(
                'id'    => 'easyel_grid_large_image_size',
                'name'  => 'easyel_grid_large_image_size',
                'type'  => 'select',
                'label' => __( 'Grid Large Thumbnail Size', 'themewant-product-gallery' ),
                'options' => twpgy_get_image_sizes_options( true, true ),
                'condition' => 'eapgy-grid-large-image-size-select'
            ),
            array(
                'id'    => 'grid_preview_large_image_size',
                'name'  => 'grid_preview_large_image_size',
                'type'  => 'dimension',
                'label' => __( 'Grid Large Image Size', 'themewant-product-gallery' ),
                'desc'  => __( 'Enter number width unit. for ex: 400px . You need to <a href="https://wordpress.org/plugins/regenerate-thumbnails/">regenerate</a> image after change sizes.', 'themewant-product-gallery' ),
                'options' => array(
                    'type' => 'text'
                ),
                'condition' => 'eapgy-grid-large-image-size'
            ),
            array(
                'id'    => 'gallery_for_variation',
                'name'  => 'gallery_for_variation',
                'type'  => 'checkbox',
                'label' => __( 'Enable Product Gallery for Variations', 'themewant-product-gallery' ),
            ),
            array(
                'id'    => 'slide_adaptive_height',
                'name'  => 'slide_adaptive_height',
                'type'  => 'checkbox',
                'label' => __( 'Slide Adaptive Height', 'themewant-product-gallery' ),
                'desc'  => __( 'Resize the Gallery Section Height to Match the Image Height', 'themewant-product-gallery' ),
            ),
            array(
                'id'    => 'hide_feature_image',
                'name'  => 'hide_feature_image',
                'type'  => 'checkbox',
                'label' => __( 'Hide Feature Image', 'themewant-product-gallery' ),
            ),
            array(
                'id'    => 'hide_feature_image_vgallery',
                'name'  => 'hide_feature_image_vgallery',
                'type'  => 'checkbox',
                'label' => __( 'Hide Featured Image on Variations', 'themewant-product-gallery' ),
            ),
        );

        $twpgy_woo_gallery_settings_fields = $settings_fields;
        return $twpgy_woo_gallery_settings_fields;

    }

    /**
     * Get gallery slider settings fields.
     *
     * Provides the settings configuration for the product gallery slider, 
     * including effects, orientation, speed, and navigation styling.
     *
     * @since 1.0.0
     *
     * @return array Array of gallery slider settings fields.
     */
    public function twpgy_easy_woo_gallery_slider_fields() {

        $twpgy_woo_gallery_settings_fields = array();

        $twpgy_slider_effect = array(
            'slide'      => __( 'Slide', 'themewant-product-gallery' ),
            'fade'       => __( 'Fade', 'themewant-product-gallery' ),
            'cube'       => __( 'Cube', 'themewant-product-gallery' ),
            'flip'       => __( 'Flip', 'themewant-product-gallery' ),
            'coverflow'  => __( 'Coverflow', 'themewant-product-gallery' ),
            'creative'   => __( 'Creative', 'themewant-product-gallery' ),
        );

        $twpgy_slider_effect = apply_filters( 'twpgy_slider_effect', $twpgy_slider_effect );


        $twpgy_pagination_layout = array(
            'dot_style' => array(
                'label' => __( 'Dot Style', 'themewant-product-gallery' ),
                'image' => TWPGY_GALLERY_DIR_URL . 'admin/assets/img/dot_style_1.png',
                'demo'  => '#',
            ),
            'number_dynamic_bullet' => array(
                'label' => __( 'Dynamic bullet Style', 'themewant-product-gallery' ),
                'image' => TWPGY_GALLERY_DIR_URL . 'admin/assets/img/dynamic_dots.png',
                'demo'  => '#',
            ), 
            'number_fraction' => array(
                'label' => __( 'fraction Style', 'themewant-product-gallery' ),
                'image' => TWPGY_GALLERY_DIR_URL . 'admin/assets/img/dot_fraction.png',
                'demo'  => '#',
            ),
        );

        $twpgy_pagination_layout = apply_filters( 'twpgy_pagination_layout', $twpgy_pagination_layout );

        $twpgy_pagination_position = array(
            'bullet-bottom'      => __( 'Vertical Bottom', 'themewant-product-gallery' ),
            'bullet-right'    => __( 'Horizontal Right', 'themewant-product-gallery' ),
        );

        $twpgy_pagination_position = apply_filters( 'twpgy_pagination_position', $twpgy_pagination_position );

        $settings_fields = array(
            array(
              'id'    => 'slider_sections_start',
              'name'  => 'slider_sections_start',
              'type'  => 'section_start',
              'label' => __( 'Gallery Slider Settings:', 'themewant-product-gallery' ),
            ),
            array(
                'id'    => 'easyel_slider_large_image_size',
                'name'  => 'easyel_slider_large_image_size',
                'type'  => 'select',
                'label' => __( 'Slider Image Size', 'themewant-product-gallery' ),
                'options' => twpgy_get_image_sizes_options( true, true ),
                'condition' => 'eapgy-slider-large-image-size-select'
            ),
            array(
                'id'    => 'preview_image_size',
                'name'  => 'preview_image_size',
                'type'  => 'dimension',
                'label' => __( 'Slider Image Size', 'themewant-product-gallery' ),
                'desc'  => __( 'Enter number width unit. for ex: 400px . You need to <a href="https://wordpress.org/plugins/regenerate-thumbnails/">regenerate</a> image after change sizes.', 'themewant-product-gallery' ),
                'options' => array(
                    'type' => 'text'
                ),
                'condition' => 'eapgy-slider-large-image-size-custom'
            ),
            array(
                'id'    => 'slide_effect',
                'name'  => 'slide_effect',
                'type'  => 'select',
                'label' => __( 'Slider Effect', 'themewant-product-gallery' ),
                'options' => $twpgy_slider_effect,
                
            ),
            array(
                'id'    => 'slider_autoplay',
                'name'  => 'slider_autoplay',
                'type'  => 'checkbox',
                'label' => __( 'Autoplay', 'themewant-product-gallery' ),
            ),
            array(
                'id'    => 'slider_infinite',
                'name'  => 'slider_infinite',
                'type'  => 'checkbox',
                'label' => __( 'Infinite Loop?', 'themewant-product-gallery' ),
            ),
            array(
                'id'    => 'slider_rtl',
                'name'  => 'slider_rtl',
                'type'  => 'checkbox',
                'label' => __( 'RTL Enable?', 'themewant-product-gallery' ),
            ),
            array(
                'id'    => 'slider_free_mode_enable',
                'name'  => 'slider_free_mode_enable',
                'type'  => 'checkbox',
                'label' => __( 'Free Mode Enable?', 'themewant-product-gallery' ),
            ),
            array(
                'id'      => 'twpgy_slider_speed',
                'name'    => 'twpgy_slider_speed',
                'type'    => 'slider',
                'label'   => __( 'Slider Speed (ms)', 'themewant-product-gallery' ),
                'default' => 500,
                'min'     => 0,
                'max'     => 2500,
                'step'    => 1,
                'unit'    => 'ms',
            ),
            array(
                'id'    => 'slider_mouse_wheel',
                'name'  => 'slider_mouse_wheel',
                'type'  => 'checkbox',
                'label' => __( 'Mouse Wheel', 'themewant-product-gallery' ),
            ),
            array(
                'id'    => 'slider_size',
                'name'  => 'slider_size',
                'type'  => 'dimension',
                'label' => __( 'Main Slider Size', 'themewant-product-gallery' ),
                'desc'  => __( 'Enter number width unit. for ex: 400px.', 'themewant-product-gallery' ),
                'options' => array(
                    'type' => 'text'
                ),
                'selectors' => array(
                    '.twpgy-easy-woo-gallery-image-area.layout-thumbs_left .themewant-product-gallery-preview-slider-container,
                    .twpgy-easy-woo-gallery-image-area.layout-thumbs_top .themewant-product-gallery-preview-slider-container,
                    .twpgy-easy-woo-gallery-image-area.layout-thumbs_right .themewant-product-gallery-preview-slider-container,
                    .twpgy-easy-woo-gallery-image-area.layout-thumbs_bottom .themewant-product-gallery-preview-slider-container,
                    .twpgy-easy-woo-gallery-image-area.layout-slider .themewant-product-gallery-preview-slider-container
                    ' => 'width'
                )
            ),
            array(
                'id'    => 'slider_padding',
                'name'  => 'slider_padding',
                'type'  => 'spacing',
                'label' => __( 'Padding', 'themewant-product-gallery' ),
                'desc'  => __( 'Enter number width unit. for ex: 10px.', 'themewant-product-gallery' ),
                'options' => array(
                    'type' => 'text'
                ),
                'selectors' => array(
                    '.twpgy-easy-woo-gallery-image-area .themewant-product-gallery-preview-slider-container' => 'padding'
                )
            ),
            array(
                'id'    => 'slider_margin',
                'name'  => 'slider_margin',
                'type'  => 'spacing',
                'label' => __( 'Margin', 'themewant-product-gallery' ),
                'desc'  => __( 'Enter number width unit. for ex: 10px.', 'themewant-product-gallery' ),
                'options' => array(
                    'type' => 'text'
                ),
                'selectors' => array(
                    '.twpgy-easy-woo-gallery-image-area .themewant-product-gallery-preview-slider-container' => 'margin'
                )
            ),
            array(
                'id'    => 'label_sections_end',
                'name'  => 'label_sections_end',
                'type'  => 'sub_section_end',
            ),
            array(
                'id'    => 'label_sections_start',
                'name'  => 'label_sections_start',
                'type'  => 'sub_section_start',
                'label' => __( 'Main Product Navigation', 'themewant-product-gallery' ),
            ),
            array(
                'id'    => 'enable_main_product_nav',
                'name'  => 'enable_main_product_nav',
                'type'  => 'checkbox',
                'label' => __( 'Enable Main Product Navigation', 'themewant-product-gallery' ),
            ),
            array(
                'id'    => 'main_product_nav_visibility',
                'name'  => 'main_product_nav_visibility',
                'type'  => 'select',
                'label' => __( 'Thumbnail Nav Visibility', 'themewant-product-gallery' ),
                'options' => array(
                'onhover' => __( 'Hover', 'themewant-product-gallery' ),
                  'always' => __( 'Always', 'themewant-product-gallery' ),
                )
            ),
            array(
                'id'    => 'main_gallery_nav_colors',
                'name'  => 'main_gallery_nav_colors',
                'type'  => 'colors',
                'label' => __( 'Thumbnail Nav Color', 'themewant-product-gallery' ),
                'multiple' => true,
                'fields' => array(
                    array( 'name' => 'color', 'label' => __( 'Color', 'themewant-product-gallery' ) ),
                    array( 'name' => 'hover_color', 'label' => __( 'Hover Color', 'themewant-product-gallery' ) ),
                    array( 'name' => 'background_color', 'label' => __( 'Background', 'themewant-product-gallery' ) ),
                    array( 'name' => 'hover_background_color', 'label' => __( 'Hover Background', 'themewant-product-gallery' ) ),
                ),
            ),
            array(
                'id'      => 'main_gallery_nav_icon_size',
                'name'    => 'main_gallery_nav_icon_size',
                'type'    => 'slider',
                'label'   => __( 'Nav Icon Size (px)', 'themewant-product-gallery' ),
                'default' => 18,
                'min'     => 0,
                'max'     => 40,
                'step'    => 1,
                'unit'    => 'px',
            ),
            array(
                'id'    => 'label_sections_end',
                'name'  => 'label_sections_end',
                'type'  => 'sub_section_end',
            ),

            array(
                'id'    => 'label_sections_start',
                'name'  => 'label_sections_start',
                'type'  => 'sub_section_start',
                'label' => __( 'Main Product Pagination', 'themewant-product-gallery' ),
            ),
            array(
                'id'    => 'enable_main_product_pagination',
                'name'  => 'enable_main_product_pagination',
                'type'  => 'checkbox',
                'label' => __( 'Enable Main Product Pagination', 'themewant-product-gallery' ),
            ),
            array(
                'id'    => 'pagination_layout',
                'name'  => 'pagination_layout',
                'type'  => 'image_select',
                'label' => __( 'Pagination Design View', 'themewant-product-gallery' ),
                'options' => $twpgy_pagination_layout,
                'condition' => 'eapgy-pagination-layout-view'
            ),
            array(
                'id'    => 'main_product_pagination_visibility',
                'name'  => 'main_product_pagination_visibility',
                'type'  => 'select',
                'label' => __( 'Pagination Visibility', 'themewant-product-gallery' ),
                'options' => array(
                  'pagination-always' => __( 'Always', 'themewant-product-gallery' ),
                  'pagination-onhover' => __( 'Hover', 'themewant-product-gallery' ),
                )
            ),
            array(
                'id'    => 'main_gallery_pagination_colors',
                'name'  => 'main_gallery_pagination_colors',
                'type'  => 'colors',
                'label' => __( 'Pagination Background Color', 'themewant-product-gallery' ),
                'multiple' => true,
                'fields' => array(
                    array( 'name' => 'color', 'label' => __( 'Background', 'themewant-product-gallery' ) ),
                    array( 'name' => 'active_color', 'label' => __( 'Active Background', 'themewant-product-gallery' ) ),
                ),
                'condition' => 'eapgy-pagination-background-color',
            ),
            array(
                'id'    => 'pagination_fraction_colors',
                'name'  => 'pagination_fraction_colors',
                'type'  => 'colors',
                'label' => __( 'Pagination Fraction Color', 'themewant-product-gallery' ),
                'multiple' => true,
                'fields' => array(
                    array( 'name' => 'color', 'label' => __( 'Color', 'themewant-product-gallery' ) ),
                ),
                'condition' => 'eapgy-pagination-fraction-color',
            ),
            array(
                'id'      => 'twpgy_bullet_position',
                'name'    => 'twpgy_bullet_position',
                'type'    => 'select', 
                'label'   => __( 'Pagination Position', 'themewant-product-gallery' ),
                'default' => '',
                'options' => $twpgy_pagination_position,
            ),
            array(
                'id'    => 'label_sections_end',
                'name'  => 'label_sections_end',
                'type'  => 'sub_section_end',
            ),
        );

        /** This filter is documented in admin/class-woo-gallery-setting-fields.php */
        $twpgy_woo_gallery_settings_fields = apply_filters( 'twpgy_pagination_settings_fields', $settings_fields );
        return $twpgy_woo_gallery_settings_fields;
    }

    /**
     * Get general slider settings fields (General inner tab).
     *
     * @since 1.0.0
     * @return array
     */
    public function twpgy_slider_general_fields() {

        $settings_fields = array(
            array(
                'id'    => 'slider_sections_start',
                'name'  => 'slider_sections_start',
                'type'  => 'section_start',
                'label' => __( 'Gallery Slider Settings:', 'themewant-product-gallery' ),
            ),
            array(
                'id'        => 'easyel_slider_large_image_size',
                'name'      => 'easyel_slider_large_image_size',
                'type'      => 'select',
                'label'     => __( 'Slider Image Size', 'themewant-product-gallery' ),
                'options'   => twpgy_get_image_sizes_options( true, true ),
                'condition' => 'eapgy-slider-large-image-size-select',
            ),
            array(
                'id'        => 'preview_image_size',
                'name'      => 'preview_image_size',
                'type'      => 'dimension',
                'label'     => __( 'Slider Image Size', 'themewant-product-gallery' ),
                'desc'      => __( 'Enter number width unit. for ex: 400px . You need to <a href="https://wordpress.org/plugins/regenerate-thumbnails/">regenerate</a> image after change sizes.', 'themewant-product-gallery' ),
                'options'   => array( 'type' => 'text' ),
                'condition' => 'eapgy-slider-large-image-size-custom',
            ),
            array(
                'id'      => 'slide_effect',
                'name'    => 'slide_effect',
                'type'    => 'select',
                'label'   => __( 'Slider Effect', 'themewant-product-gallery' ),
                'options' => apply_filters( 'twpgy_slider_effect', array(
                    'slide'     => __( 'Slide', 'themewant-product-gallery' ),
                    'fade'      => __( 'Fade', 'themewant-product-gallery' ),
                    'cube'      => __( 'Cube', 'themewant-product-gallery' ),
                    'flip'      => __( 'Flip', 'themewant-product-gallery' ),
                    'coverflow' => __( 'Coverflow', 'themewant-product-gallery' ),
                    'creative'  => __( 'Creative', 'themewant-product-gallery' ),
                ) ),
            ),
            array(
                'id'      => 'slider_orientaiton',
                'name'    => 'slider_orientaiton',
                'type'    => 'select',
                'label'   => __( 'Slider Orientation', 'themewant-product-gallery' ),
                'options' => array(
                    'horizontal' => __( 'Horizontal', 'themewant-product-gallery' ),
                    'vertical'   => __( 'Vertical', 'themewant-product-gallery' ),
                ),
            ),
            array(
                'id'    => 'slider_autoplay',
                'name'  => 'slider_autoplay',
                'type'  => 'checkbox',
                'label' => __( 'Autoplay', 'themewant-product-gallery' ),
            ),
            array(
                'id'    => 'slider_infinite',
                'name'  => 'slider_infinite',
                'type'  => 'checkbox',
                'label' => __( 'Infinite Loop?', 'themewant-product-gallery' ),
            ),
            array(
                'id'    => 'slider_rtl',
                'name'  => 'slider_rtl',
                'type'  => 'checkbox',
                'label' => __( 'RTL Enable?', 'themewant-product-gallery' ),
            ),
            array(
                'id'    => 'slider_free_mode_enable',
                'name'  => 'slider_free_mode_enable',
                'type'  => 'checkbox',
                'label' => __( 'Free Mode Enable?', 'themewant-product-gallery' ),
            ),
            array(
                'id'      => 'twpgy_slider_speed',
                'name'    => 'twpgy_slider_speed',
                'type'    => 'slider',
                'label'   => __( 'Slider Speed (ms)', 'themewant-product-gallery' ),
                'default' => 500,
                'min'     => 0,
                'max'     => 2500,
                'step'    => 1,
                'unit'    => 'ms',
            ),
            array(
                'id'    => 'slider_mouse_wheel',
                'name'  => 'slider_mouse_wheel',
                'type'  => 'checkbox',
                'label' => __( 'Mouse Wheel', 'themewant-product-gallery' ),
            ),
            array(
                'id'        => 'slider_padding',
                'name'      => 'slider_padding',
                'type'      => 'spacing',
                'label'     => __( 'Padding', 'themewant-product-gallery' ),
                'desc'      => __( 'Enter number width unit. for ex: 10px.', 'themewant-product-gallery' ),
                'options'   => array( 'type' => 'text' ),
                'selectors' => array(
                    '.twpgy-easy-woo-gallery-image-area .themewant-product-gallery-preview-slider-container' => 'padding',
                ),
            ),
            array(
                'id'        => 'slider_margin',
                'name'      => 'slider_margin',
                'type'      => 'spacing',
                'label'     => __( 'Margin', 'themewant-product-gallery' ),
                'desc'      => __( 'Enter number width unit. for ex: 10px.', 'themewant-product-gallery' ),
                'options'   => array( 'type' => 'text' ),
                'selectors' => array(
                    '.twpgy-easy-woo-gallery-image-area .themewant-product-gallery-preview-slider-container' => 'margin',
                ),
            ),
        );

        return $settings_fields;
    }

    /**
     * Get product navigation settings fields (Product Navigation inner tab).
     *
     * @since 1.0.0
     * @return array
     */
    public function twpgy_slider_navigation_fields() {

        $settings_fields = array(
            array(
                'id'    => 'slider_nav_section_start',
                'name'  => 'slider_nav_section_start',
                'type'  => 'section_start',
                'label' => __( 'Product Navigation Settings:', 'themewant-product-gallery' ),
            ),
            array(
                'id'    => 'enable_main_product_nav',
                'name'  => 'enable_main_product_nav',
                'type'  => 'checkbox',
                'label' => __( 'Enable Main Product Navigation', 'themewant-product-gallery' ),
            ),
            array(
                'id'      => 'main_product_nav_visibility',
                'name'    => 'main_product_nav_visibility',
                'type'    => 'select',
                'label'   => __( 'Thumbnail Nav Visibility', 'themewant-product-gallery' ),
                'condition' => 'twpgy-main-nav-dependent',
                'options' => array(
                    'onhover' => __( 'Hover', 'themewant-product-gallery' ),
                    'always'  => __( 'Always', 'themewant-product-gallery' ),
                ),
            ),
            array(
                'id'       => 'main_gallery_nav_colors',
                'name'     => 'main_gallery_nav_colors',
                'type'     => 'colors',
                'label'    => __( 'Thumbnail Nav Color', 'themewant-product-gallery' ),
                'condition' => 'twpgy-main-nav-dependent',
                'multiple' => true,
                'fields'   => array(
                    array( 'name' => 'color',                    'label' => __( 'Color', 'themewant-product-gallery' ) ),
                    array( 'name' => 'hover_color',              'label' => __( 'Hover Color', 'themewant-product-gallery' ) ),
                    array( 'name' => 'background_color',         'label' => __( 'Background', 'themewant-product-gallery' ) ),
                    array( 'name' => 'hover_background_color',   'label' => __( 'Hover Background', 'themewant-product-gallery' ) ),
                ),
            ),
            array(
                'id'      => 'main_gallery_nav_icon_size',
                'name'    => 'main_gallery_nav_icon_size',
                'type'    => 'slider',
                'label'   => __( 'Nav Icon Size (px)', 'themewant-product-gallery' ),
                'condition' => 'twpgy-main-nav-dependent',
                'default' => 18,
                'min'     => 0,
                'max'     => 40,
                'step'    => 1,
                'unit'    => 'px',
            ),
        );

        return $settings_fields;
    }

    /**
     * Get product pagination settings fields (Product Pagination inner tab).
     *
     * @since 1.0.0
     * @return array
     */
    public function twpgy_slider_pagination_fields() {

        $twpgy_pagination_layout = apply_filters( 'twpgy_pagination_layout', array(
            'dot_style' => array(
                'label' => __( 'Dot Style', 'themewant-product-gallery' ),
                'image' => TWPGY_GALLERY_DIR_URL . 'admin/assets/img/dot_style_1.png',
                'demo'  => '#',
            ),
            'number_dynamic_bullet' => array(
                'label' => __( 'Dynamic bullet Style', 'themewant-product-gallery' ),
                'image' => TWPGY_GALLERY_DIR_URL . 'admin/assets/img/dynamic_dots.png',
                'demo'  => '#',
            ),
            'number_fraction' => array(
                'label' => __( 'fraction Style', 'themewant-product-gallery' ),
                'image' => TWPGY_GALLERY_DIR_URL . 'admin/assets/img/dot_fraction.png',
                'demo'  => '#',
            ),
        ) );

        $twpgy_pagination_position = apply_filters( 'twpgy_pagination_position', array(
            'bullet-bottom' => __( 'Vertical Bottom', 'themewant-product-gallery' ),
            'bullet-right'  => __( 'Horizontal Right', 'themewant-product-gallery' ),
        ) );

        $settings_fields = array(
            array(
                'id'    => 'slider_pagination_section_start',
                'name'  => 'slider_pagination_section_start',
                'type'  => 'section_start',
                'label' => __( 'Product Pagination Settings:', 'themewant-product-gallery' ),
            ),
            array(
                'id'    => 'enable_main_product_pagination',
                'name'  => 'enable_main_product_pagination',
                'type'  => 'checkbox',
                'label' => __( 'Enable Main Product Pagination', 'themewant-product-gallery' ),
            ),
            array(
                'id'        => 'pagination_layout',
                'name'      => 'pagination_layout',
                'type'      => 'image_select',
                'label'     => __( 'Pagination Design View', 'themewant-product-gallery' ),
                'options'   => $twpgy_pagination_layout,
                'condition' => 'eapgy-pagination-layout-view twpgy-main-pagination-dependent',
            ),
            array(
                'id'      => 'main_product_pagination_visibility',
                'name'    => 'main_product_pagination_visibility',
                'type'    => 'select',
                'label'   => __( 'Pagination Visibility', 'themewant-product-gallery' ),
                'condition' => 'twpgy-main-pagination-dependent',
                'options' => array(
                    'pagination-always'  => __( 'Always', 'themewant-product-gallery' ),
                    'pagination-onhover' => __( 'Hover', 'themewant-product-gallery' ),
                ),
            ),
            array(
                'id'        => 'main_gallery_pagination_colors',
                'name'      => 'main_gallery_pagination_colors',
                'type'      => 'colors',
                'label'     => __( 'Pagination Background Color', 'themewant-product-gallery' ),
                'multiple'  => true,
                'fields'    => array(
                    array( 'name' => 'color',        'label' => __( 'Background', 'themewant-product-gallery' ) ),
                    array( 'name' => 'active_color', 'label' => __( 'Active Background', 'themewant-product-gallery' ) ),
                ),
                'condition' => 'eapgy-pagination-background-color twpgy-main-pagination-dependent',
            ),
            array(
                'id'        => 'pagination_fraction_colors',
                'name'      => 'pagination_fraction_colors',
                'type'      => 'colors',
                'label'     => __( 'Pagination Fraction Color', 'themewant-product-gallery' ),
                'multiple'  => true,
                'fields'    => array(
                    array( 'name' => 'color', 'label' => __( 'Color', 'themewant-product-gallery' ) ),
                ),
                'condition' => 'eapgy-pagination-fraction-color twpgy-main-pagination-dependent',
            ),
            array(
                'id'      => 'twpgy_bullet_position',
                'name'    => 'twpgy_bullet_position',
                'type'    => 'select',
                'label'   => __( 'Pagination Position', 'themewant-product-gallery' ),
                'condition' => 'twpgy-main-pagination-dependent',
                'default' => '',
                'options' => $twpgy_pagination_position,
            ),
        );

        /**
         * Filters the slider pagination settings fields.
         *
         * @since 1.1.2
         *
         * @param array $settings_fields Pagination field definitions.
         */
        return apply_filters( 'twpgy_pagination_settings_fields', $settings_fields );
    }

    /**
     * Get thumbnail slider settings fields.
     *
     * Defines the configuration for the thumbnail navigation slider, including
     * spacing, image sizes, navigation visibility, and color styling.
     *
     * @since 1.0.0
     *
     * @return array Array of thumbnail slider settings fields.
     */
    public function twpgy_easy_woo_gallery_thumb_fields() {

        $twpgy_woo_gallery_settings_fields = array();

        $settings_fields = array(
            array(
              'id'    => 'slider_sections_start',
              'name'  => 'slider_sections_start',
              'type'  => 'section_start',
              'label' => __( 'Thumb Slider Settings:', 'themewant-product-gallery' ),
            ),
            array(
                'id'    => 'enable_thumbs_slider',
                'name'  => 'enable_thumbs_slider',
                'type'  => 'checkbox',
                'label' => __( 'Enable Slider', 'themewant-product-gallery' ),
            ),
            array(
                'id'    => 'thumbs_slider_mouse_wheel',
                'name'  => 'thumbs_slider_mouse_wheel',
                'type'  => 'checkbox',
                'label' => __( 'Mouse Wheel', 'themewant-product-gallery' ),
            ),
            array(
                'id'    => 'thumbs_slides_per_view',
                'name'  => 'thumbs_slides_per_view',
                'type'  => 'text',
                'label' => __( 'Slides Per View', 'themewant-product-gallery' ),
                'default' => '3'
            ),
            array(
                'id'    => 'eapgy_thumbs_slides_space',
                'name'  => 'eapgy_thumbs_slides_space',
                'type'  => 'dimensionsgap',
                'label' => __( 'Slides Gap', 'themewant-product-gallery' ),
                'desc'  => __( 'Set horizontal and vertical gap between slides.', 'themewant-product-gallery' ),
                'options' => array(
                    'horigap'  => array(
                        'label' => __( 'Horizontal Gap', 'themewant-product-gallery' ),
                        'icon'  => 'fa-arrows-left-right',
                        'unit'  => 'px',
                    ),
                    'vergap' => array(
                        'label' => __( 'Vertical Gap', 'themewant-product-gallery' ),
                        'icon'  => 'fa-arrows-up-down',
                        'unit'  => 'px',
                    ),
                ),
            ),

            array(
                'id'    => 'thumbs_slider_image_size',
                'name'  => 'thumbs_slider_image_size',
                'type'  => 'select',
                'label' => __( 'Thumbnail Size', 'themewant-product-gallery' ),
                'options' => twpgy_get_image_sizes_options_small( true, true ),
            ),
            array(
                'id'    => 'thumbs_slider_padding',
                'name'  => 'thumbs_slider_padding',
                'type'  => 'spacing',
                'label' => __( 'Padding', 'themewant-product-gallery' ),
                'desc'  => __( 'Enter number width unit. for ex: 10px.', 'themewant-product-gallery' ),
                'options' => array(
                    'type' => 'text',
                ),
                'selectors' => array(
                   '.twpgy-easy-woo-gallery-image-area.layout-thumbs_left .twpgy-thumb-slider-container,
                    .twpgy-easy-woo-gallery-image-area.layout-thumbs_right .twpgy-thumb-slider-container,
                    .twpgy-easy-woo-gallery-image-area.layout-thumbs_top .twpgy-thumb-slider-container,
                    .twpgy-easy-woo-gallery-image-area.layout-thumbs_bottom .twpgy-thumb-slider-container' => 'padding',
                )
            ),
            array(
                'id'    => 'thumbs_slider_margin',
                'name'  => 'thumbs_slider_margin',
                'type'  => 'spacing',
                'label' => __( 'Margin', 'themewant-product-gallery' ),
                'desc'  => __( 'Enter number width unit. for ex: 10px.', 'themewant-product-gallery' ),
                'options' => array(
                    'type' => 'text'
                ),
                'selectors' => array(
                    '.twpgy-easy-woo-gallery-image-area.layout-thumbs_left .twpgy-thumb-slider-container,
                    .twpgy-easy-woo-gallery-image-area.layout-thumbs_right .twpgy-thumb-slider-container,
                    .twpgy-easy-woo-gallery-image-area.layout-thumbs_top .twpgy-thumb-slider-container,
                    .twpgy-easy-woo-gallery-image-area.layout-thumbs_bottom .twpgy-thumb-slider-container' => 'margin',
                )
            ),
            array(
                'id'    => 'label_sections_end',
                'name'  => 'label_sections_end',
                'type'  => 'sub_section_end',
            ),
        );

        $twpgy_woo_gallery_settings_fields = $settings_fields;
        return $twpgy_woo_gallery_settings_fields;
    }

    /**
     * Get thumbnail navigation settings fields (Thumbnail Navigation inner tab).
     *
     * @since 1.0.0
     * @return array
     */
    public function twpgy_easy_woo_gallery_thumb_nav_fields() {

        $settings_fields = array(
            array(
                'id'    => 'thumb_nav_section_start',
                'name'  => 'thumb_nav_section_start',
                'type'  => 'section_start',
                'label' => __( 'Thumb Navigation Settings:', 'themewant-product-gallery' ),
            ),
            array(
                'id'    => 'enable_thumbs_nav',
                'name'  => 'enable_thumbs_nav',
                'type'  => 'checkbox',
                'label' => __( 'Enable Thumbnails Navigation', 'themewant-product-gallery' ),
            ),
            array(
                'id'    => 'thumbs_nav_visibility',
                'name'  => 'thumbs_nav_visibility',
                'type'  => 'select',
                'label' => __( 'Thumb Nav Visibility', 'themewant-product-gallery' ),
                'condition' => 'twpgy-thumbs-nav-dependent',
                'options' => array(
                  'always' => __( 'Always', 'themewant-product-gallery' ),
                  'onhover' => __( 'Hover', 'themewant-product-gallery' ),
                )
            ),
            array(
                'id'    => 'thumb_nav_colors',
                'name'  => 'thumb_nav_colors',
                'type'  => 'colors',
                'label' => __( 'Thumb Nav Color', 'themewant-product-gallery' ),
                'condition' => 'twpgy-thumbs-nav-dependent',
                'multiple' => true,
                'fields' => array(
                    array( 'name' => 'color', 'label' => __( 'Color', 'themewant-product-gallery' ) ),
                    array( 'name' => 'hover_color', 'label' => __( 'Hover Color', 'themewant-product-gallery' ) ),
                    array( 'name' => 'background_color', 'label' => __( 'Background', 'themewant-product-gallery' ) ),
                    array( 'name' => 'hover_background_color', 'label' => __( 'Hover Background', 'themewant-product-gallery' ) ),
                ),
            ),
            array(
                'id'      => 'thumb_nav_icon_size',
                'name'    => 'thumb_nav_icon_size',
                'type'    => 'slider',
                'label'   => __( 'Nav Icon Size (px)', 'themewant-product-gallery' ),
                'condition' => 'twpgy-thumbs-nav-dependent',
                'default' => 18,
                'min'     => 0,
                'max'     => 40,
                'step'    => 1,
                'unit'    => 'px',
            ),
            array(
                'id'    => 'thumb_nav_section_end',
                'name'  => 'thumb_nav_section_end',
                'type'  => 'sub_section_end',
            ),
        );

        return $settings_fields;
    }

    /**
     * Get lightbox and zoom settings fields.
     *
     * Returns all configuration fields related to the product gallery
     * lightbox and zoom features, including icon position, effects,
     * captions, counters, and close icon colors.
     *
     * @since 1.0.0
     *
     * @return array Array of lightbox and zoom settings fields.
     */
    public function twpgy_woo_gallery_lightbox_fields() {

        $twpgy_woo_gallery_settings_fields = array();

        $settings_fields = array(
            array(
              'id'    => 'slider_sections_start',
              'name'  => 'slider_sections_start',
              'type'  => 'section_start',
              'label' => __( 'Lightbox Settings:', 'themewant-product-gallery' ),
            ),
            array(
                'id'    => 'gallery_lightbox',
                'name'  => 'gallery_lightbox',
                'type'  => 'checkbox',
                'label' => __( 'Enable Lightbox', 'themewant-product-gallery' ),
            ),
            array(
                'id'    => 'lightbox_slide_effect',
                'name'  => 'lightbox_slide_effect',
                'type'  => 'select',
                'label' => __( 'Lightbox Slide Effect', 'themewant-product-gallery' ),
                'condition' => 'twpgy-lightbox-dependent',
                'options' => array(
                  'slide' => __( 'Slide', 'themewant-product-gallery' ),

                ),
            ),
            array(
                'id'    => 'lightbox_icon_position',
                'name'  => 'lightbox_icon_position',
                'type'  => 'select',
                'label' => __( 'Lightbox Icon Display Position', 'themewant-product-gallery' ),
                'condition' => 'twpgy-lightbox-dependent',
                'options' => array(
                  'top_right' => __( 'Top Right', 'themewant-product-gallery' ),
                  'top_left' => __( 'Top Left', 'themewant-product-gallery' ),
                  'bottom_right' => __( 'Bottom Right', 'themewant-product-gallery' ),
                  'bottom_left' => __( 'Bottom Left', 'themewant-product-gallery' ),
                  'middle' => __( 'Middle', 'themewant-product-gallery' ),

                )
            ),
            array(
                'id'    => 'lightbox_caption',
                'name'  => 'lightbox_caption',
                'type'  => 'checkbox',
                'label' => __( 'Enable Lightbox Caption', 'themewant-product-gallery' ),
                'condition' => 'twpgy-lightbox-dependent',
            ),
            array(
                'id'    => 'lightbox_counter',
                'name'  => 'lightbox_counter',
                'type'  => 'checkbox',
                'label' => __( 'Lightbox Image Counter', 'themewant-product-gallery' ),
                'condition' => 'twpgy-lightbox-dependent',
            ),
            array(
                'id'    => 'label_sections_end',
                'name'  => 'label_sections_end',
                'type'  => 'sub_section_end',
            ),
            array(
                'id'    => 'close_icon_colors',
                'name'  => 'close_icon_colors',
                'type'  => 'colors',
                'label' => __( 'Close Icon Color', 'themewant-product-gallery' ),
                'condition' => 'twpgy-lightbox-dependent',
                'multiple' => true,
                'fields' => array(
                    array( 'name' => 'color', 'label' => __( 'Color', 'themewant-product-gallery' ) ),
                    array( 'name' => 'hover_color', 'label' => __( 'Hover Color', 'themewant-product-gallery' ) ),
                    array( 'name' => 'background_color', 'label' => __( 'Background', 'themewant-product-gallery' ) ),
                    array( 'name' => 'hover_background_color', 'label' => __( 'Hover Background', 'themewant-product-gallery' ) ),
                )
            ),
           
            array(
                'id'    => 'slider_sections_end',
                'name'  => 'slider_sections_end',
                'type'  => 'sub_section_end',
            ),
        );

        $twpgy_woo_gallery_settings_fields = $settings_fields;
        return $twpgy_woo_gallery_settings_fields;

    }

    /**
     * Get gallery zoom settings fields.
     *
     * Defines configuration for the image zoom functionality, including zoom styles
     * (window or lens), lens shapes, colors, borders, and opacity settings.
     *
     * @since 1.0.0
     *
     * @return array Array of gallery zoom settings fields.
     */
    public function twpgy_woo_gallery_zoom_fields() {

        $twpgy_woo_gallery_settings_fields = array();

        $zoom_style = array(
            'default' => __( 'Default', 'themewant-product-gallery' ),
        );

        $twpgy_zoom_style_filter = apply_filters( 'twpgy_zoom_style_filter', $zoom_style );

        $settings_fields = array(
            array(
              'id'    => 'slider_sections_start',
              'name'  => 'slider_sections_start',
              'type'  => 'section_start',
              'label' => __( 'Zoom Settings:', 'themewant-product-gallery' ),
            ),
            array(
                'id'    => 'gallery_image_zoom',
                'name'  => 'gallery_image_zoom',
                'type'  => 'checkbox',
                'label' => __( 'Enable Image Zoom', 'themewant-product-gallery' ),
            ),
            array(
                'id'    => 'zoom_style',
                'name'  => 'zoom_style',
                'type'  => 'select',
                'label' => __( 'Zoom Style', 'themewant-product-gallery' ),
                'condition' => 'twpgy-zoom-dependent',
                'options' => $twpgy_zoom_style_filter,
            ),
            array(
                'id'    => 'zoom_sections_end',
                'name'  => 'zoom_sections_end',
                'type'  => 'sub_section_end',
            ),
        );

        $settings_fields = apply_filters( 'twpgy_zoom_settings_fields', $settings_fields );

        $twpgy_woo_gallery_settings_fields = $settings_fields;
        return $twpgy_woo_gallery_settings_fields;

    }

    /**
     * Get video gallery settings fields.
     *
     * Returns all configuration fields related to product gallery
     * video support, including play mode, icon styling, autoplay,
     * looping, aspect ratio, and playback behavior.
     *
     * @since 1.0.0
     *
     * @return array Array of video settings fields.
     */
    public function twpgy_woo_gallery_get_video_fields() {

        $twpgy_woo_gallery_settings_fields = array();

        $twpgy_icon_position = array(
            'middle'       => __( 'Middle', 'themewant-product-gallery' ),
        );

        $twpgy_icon_position = apply_filters( 'twpgy_video_icon_position', $twpgy_icon_position );

        $settings_fields = array(
            array(
                'id'    => 'label_sections_start',
                'name'  => 'label_sections_start',
                'type'  => 'section_start',
                'label' => __( 'Video Settings', 'themewant-product-gallery' ),
            ),
            array(
                'id'    => 'gallery_video',
                'name'  => 'gallery_video',
                'type'  => 'checkbox',
                'label' => __( 'Enable Product Gallery Video', 'themewant-product-gallery' ),
            ),
            array(
                'id'    => 'video_play_mode',
                'name'  => 'video_play_mode',
                'type'  => 'select',
                'label' => __( 'Video Play Mode', 'themewant-product-gallery' ),
                'options' => array(
                  'inline' => __( 'Inline', 'themewant-product-gallery' ),
                  'popup' => __( 'Popup', 'themewant-product-gallery' ),
                ),
                'condition' => 'twpgy-video-dependent',
            ),
            array(
                'id'    => 'video_play_icon_size',
                'name'  => 'video_play_icon_size',
                'type'  => 'text',
                'label' => __( 'Video Play Icon Size', 'themewant-product-gallery' ),
                'default' => '25px',
                'desc'  => __( 'Enter value with unit. Example: 85px', 'themewant-product-gallery' ),
                'selectors' => array(
                    '.twpgy-easy-woo-gallery-image-area' => '--twpgy-play-icon-size',
                ),
                'condition' => 'twpgy-video-dependent',
            ),
            array(
                'id'    => 'video_play_icon_colors',
                'name'  => 'video_play_icon_colors',
                'type'  => 'colors',
                'label' => __( 'Video Icon Color', 'themewant-product-gallery' ),
                'multiple' => true,
                'fields' => array(
                    array( 'name' => 'color', 'label' => __( 'Color', 'themewant-product-gallery' ) ),
                    array( 'name' => 'hover_color', 'label' => __( 'Hover Color', 'themewant-product-gallery' ) ),
                    array( 'name' => 'background_color', 'label' => __( 'Background', 'themewant-product-gallery' ) ),
                    array( 'name' => 'hover_background_color', 'label' => __( 'Hover Background', 'themewant-product-gallery' ) ),
                ),
                'selectors' => array(
                    '.twpgy-easy-woo-gallery-image-area .play-icon svg circle, .twpgy-easy-woo-gallery-image-area .themewant-product-gallery-preview-slider-container .play-icon svg circle' => array(
                        'fill' => 'background_color'
                    ),
                    '.twpgy-easy-woo-gallery-image-area .play-icon:hover svg circle, .twpgy-easy-woo-gallery-image-area .themewant-product-gallery-preview-slider-container .play-icon:hover svg circle' => array(
                        'fill' => 'hover_background_color'
                    ),
                    '.twpgy-easy-woo-gallery-image-area .play-icon svg path, .twpgy-easy-woo-gallery-image-area .themewant-product-gallery-preview-slider-container .play-icon svg path' => array(
                        'fill' => 'color'
                    ),
                    '.twpgy-easy-woo-gallery-image-area .play-icon:hover svg path, .twpgy-easy-woo-gallery-image-area .themewant-product-gallery-preview-slider-container .play-icon:hover svg path' => array(
                        'fill' => 'hover_color'
                    ),

                ),
                'condition' => 'twpgy-video-dependent',
            ),
            array(
                'id'    => 'video_play_icon_position',
                'name'  => 'video_play_icon_position',
                'type'  => 'select',
                'label' => __( 'Video Icon Position', 'themewant-product-gallery' ),
                'options' => $twpgy_icon_position,
                'default' => 'middle',
                'condition' => 'twpgy-video-dependent',
            ),
            array(
                'id'    => 'autoplay_video',
                'name'  => 'autoplay_video',
                'type'  => 'checkbox',
                'label' => __( 'Autoplay Video', 'themewant-product-gallery' ),
                'condition' => 'twpgy-video-dependent',
            ),
            array(
                'id'    => 'loop_video',
                'name'  => 'loop_video',
                'type'  => 'checkbox',
                'label' => __( 'Video Loop', 'themewant-product-gallery' ),
                'condition' => 'twpgy-video-dependent',
            ),
            array(
                'id'    => 'video_preloader',
                'name'  => 'video_preloader',
                'type'  => 'checkbox',
                'label' => __( 'Video Preloader', 'themewant-product-gallery' ),
                'condition' => 'twpgy-video-dependent',
            ),

        );

        $settings_fields = apply_filters( 'twpgy_video_settings_fields', $settings_fields );

        $twpgy_woo_gallery_settings_fields = $settings_fields;

        return $twpgy_woo_gallery_settings_fields;

    }

    /**
     * Get 360-degree view settings fields.
     *
     * This method defines the configuration fields for the 360-degree product gallery.
     * It includes settings for popup triggers, button positioning with custom offsets,
     * layout selection (Nav View vs Drag), autoplay speed, and navigation styling.
     *
     * @since 1.0.0
     *
     * @return array Array of 360-degree gallery settings fields.
     */
    public function twpgy_woo_gallery_get_360_degree_fields() {

        $twpgy_woo_gallery_settings_fields = array();

        $popup_button_position = array(
            'bottom-left'=>__( 'Bottom Left', 'themewant-product-gallery' ),
        );

        $popup_button_position = apply_filters( 'twpgy_popup_button_position_filter', $popup_button_position );

        $popup_360degree_layout = array(
            'nav_view'=>__( 'Nav View', 'themewant-product-gallery' ),
        );

        $popup_360degree_layout = apply_filters( 'twpgy_popup_360degree_layout_filter', $popup_360degree_layout );

        $settings_fields = array(
            array(
                'id'    => 'label_sections_start',
                'name'  => 'label_sections_start',
                'type'  => 'section_start',
                'label' => __( '360 Degree View Settings', 'themewant-product-gallery' ),
            ),
            array(
                'id'    => 'enable_360degree_gallery',
                'name'  => 'enable_360degree_gallery',
                'type'  => 'checkbox',
                'label' => __( 'Enable 360 Degree Popup', 'themewant-product-gallery' ),
            ),
            array(
                'id'    => 'twpgy_popup_button_position',
                'name'  => 'twpgy_popup_button_position',
                'type'  => 'select',
                'label' => __( 'Popup Button Position', 'themewant-product-gallery' ),
                'options' => $popup_button_position,
                'condition' => 'twpgy-360degree-dependent',
            ),
            // Bottom Offset
            array(
                'id'      => 'twpgy_popup_button_offset_bottom',
                'name'    => 'twpgy_popup_button_offset_bottom',
                'type'    => 'slider',
                'label'   => __( 'Bottom Offset (%)', 'themewant-product-gallery' ),
                'default' => 10,
                'min'     => 0,
                'max'     => 100,
                'step'    => 1,
                'unit'    => '%',
                'offset_type' => 'bottom',
                'condition' => 'twpgy-360degree-dependent',
            ),

            // Left Offset
            array(
                'id'      => 'twpgy_popup_button_offset_left',
                'name'    => 'twpgy_popup_button_offset_left',
                'type'    => 'slider',
                'label'   => __( 'Left Offset (%)', 'themewant-product-gallery' ),
                'default' => 10,
                'min'     => 0,
                'max'     => 100,
                'step'    => 1,
                'unit'    => '%',
                'offset_type' => 'left',
                'condition' => 'twpgy-360degree-dependent',
            ),
             array(
                'id'    => 'view360degree_layout',
                'name'  => 'view360degree_layout',
                'type'  => 'select',
                'label' => __( '360Degree Layout', 'themewant-product-gallery' ),
                'options' => $popup_360degree_layout,
                'condition' => 'twpgy-360degree-dependent',
            ),
            array(
                'id'    => 'popup_360degree_width',
                'name'  => 'popup_360degree_width',
                'type'  => 'text',
                'label' => __( 'Popup Content Width', 'themewant-product-gallery' ),
                'desc'  => __( 'Enter number width unit. for ex: 600px.', 'themewant-product-gallery' ),
                'selectors' => array(
                    '.eapgy-360-popup-content' => 'max-width'
                ),
                'condition' => 'twpgy-360degree-dependent',
            ),
            array(
                'id'    => 'popup_360degree_height',
                'name'  => 'popup_360degree_height',
                'type'  => 'text',
                'label' => __( 'Popup Content Height', 'themewant-product-gallery' ),
                'desc'  => __( 'Enter height with unit (ex: 400px, 60%).', 'themewant-product-gallery' ),
                'selectors' => array(
                    '.eapgy-360-popup-content' => 'max-height'
                ),
                'condition' => 'twpgy-360degree-dependent',
            ),
        );

        $settings_fields = apply_filters( 'twpgy_360_degree_settings_field_filter', $settings_fields );

        $twpgy_woo_gallery_settings_fields = $settings_fields;
        return $twpgy_woo_gallery_settings_fields;

    }

    /**
     * Get all settings fields for the gallery.
     * * This is the master method that aggregates all settings fields by merging 
     * various configuration arrays including general settings, slider, lightbox, 
     * video, 360-degree view, thumbnails, and zoom settings.
     *
     * @since 1.0.0
     *
     * @return array Combined array of all available settings fields.
     */
    public function twpgy_get_all_settings_fields(){

        $twpgy_woo_gallery_get_settings_fields     = $this->twpgy_woo_gallery_get_settings_fields();
        $twpgy_easy_woo_gallery_slider_fields      = $this->twpgy_easy_woo_gallery_slider_fields();
        $twpgy_woo_gallery_lightbox_fields         = $this->twpgy_woo_gallery_lightbox_fields();
        $twpgy_woo_gallery_get_video_fields        = $this->twpgy_woo_gallery_get_video_fields();
        $twpgy_woo_gallery_get_360degree_fields    = $this->twpgy_woo_gallery_get_360_degree_fields();
        $twpgy_easy_woo_gallery_thumb_fields       = $this->twpgy_easy_woo_gallery_thumb_fields();
        $twpgy_easy_woo_gallery_thumb_nav_fields   = $this->twpgy_easy_woo_gallery_thumb_nav_fields();
        $twpgy_woo_gallery_zoom_fields             = $this->twpgy_woo_gallery_zoom_fields();

        $all_settings = array_merge(
            $twpgy_woo_gallery_get_settings_fields,
            $twpgy_easy_woo_gallery_slider_fields,
            $twpgy_woo_gallery_lightbox_fields,
            $twpgy_woo_gallery_get_video_fields,
            $twpgy_woo_gallery_get_360degree_fields,
            $twpgy_easy_woo_gallery_thumb_fields,
            $twpgy_easy_woo_gallery_thumb_nav_fields,
            $twpgy_woo_gallery_zoom_fields,
        );

        return $all_settings;
    }
}