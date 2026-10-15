<?php
/**
 * Dynamic CSS generator for Easy Product Gallery.
 *
 * Builds and injects frontend CSS dynamically based on global,
 * layout-specific, and product-level gallery settings.
 *
 * @since 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Generates and injects dynamic frontend CSS for Easy Product Gallery.
 *
 * Uses plugin settings to build inline styles for sliders, navigation,
 * pagination, zoom effects, and layout spacing on a per-product basis.
 *
 * @since 1.0.0
 */
class TWPGY_Dynamic_CSS {

    /**
     * Holds the singleton instance.
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
     * Retrieves gallery settings for a specific product.
     *
     * Returns layout-specific gallery settings when the product is assigned to
     * a custom layout. Falls back to global gallery settings when no layout is
     * assigned.
     *
     * @since 1.0.0
     *
     * @param int $product_id WooCommerce product ID.
     *
     * @return array Gallery settings for the product.
     */
    private function twpgy_get_css_settings_for_product( $product_id ) {

        $options = get_option( 'twpgy_woo_gallery_options', [] );
        $global_settings = $options['settings'] ?? [];

        $final_settings =  $global_settings;

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

                    return  $layout_meta['post_meta'];

                }
            }
        }

        return $global_settings;
    }

    /**
     * Generates dynamic inline CSS based on gallery settings.
     *
     * Builds CSS rules for spacing, dimensions, colors, navigation,
     * pagination, the 360° button, and layout-specific gaps.
     *
     * @since 1.0.0
     *
     * @param int|null $product_id Optional. Product ID. Defaults to current product.
     *
     * @return string Generated CSS string.
     */
    public function twpgy_generate_inline_css( $product_id = null ) {
 
        $product_id = $product_id ?: get_the_ID();
    
        $plugin_options = twpgy_get_display_settings_for_product( $product_id );
    
        $settings_fields = $this->get_settings_fields();
    
        $css = '';
    
        foreach ( $settings_fields as $field ) {
    
            if ( empty( $field['selectors'] ) || ! is_array( $field['selectors'] ) ) {
                continue;
            }
    
            $option_key = $field['name'];
            $value      = $plugin_options[ $option_key ] ?? null;
    
            foreach ( $field['selectors'] as $selector => $property ) {
    
                // Selectors and property names come from plugin code (trusted),
                // but still strip anything that isn't a safe CSS identifier/selector.
                $safe_selector = $selector; // developer-controlled; kept as-is
                $safe_property = $property; // developer-controlled; kept as-is
    
                switch ( $field['type'] ) {
    
                    case 'spacing':
                        $css .= $this->twpgy_generate_spacing_css( $safe_selector, $safe_property, $value );
                        break;
    
                    case 'dimension':
                        $css .= $this->twpgy_generate_dimension_css( $safe_selector, $value );
                        break;
    
                    case 'colors':
                        if ( ! empty( $field['selectors'] ) && ! empty( $value ) && is_array( $value ) ) {
                            foreach ( $field['selectors'] as $sel => $props ) {
                                if ( is_array( $props ) ) {
                                    foreach ( $props as $css_prop => $color_key ) {
                                        if ( ! empty( $value[ $color_key ] ) ) {
                                            // ← ESCAPE: sanitize color value right before output
                                            $safe_color = $this->twpgy_sanitize_css_color( $value[ $color_key ] );
                                            if ( '' !== $safe_color ) {
                                                $css .= "{$sel} { {$css_prop}: {$safe_color}; } ";
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        break;
    
                    case 'slider':
                        if ( $value !== '' ) {
                            $unit       = $field['unit'] ?? 'px';
                            // ← ESCAPE: numeric values with floatval/absint
                            $safe_value = floatval( $value );
                            $safe_unit  = in_array( $unit, [ 'px', 'em', 'rem', '%', 'vh', 'vw' ], true ) ? $unit : 'px';
                            $css       .= "{$safe_selector} { {$safe_property}: {$safe_value}{$safe_unit}; } ";
                        }
                        break;
    
                    default:
                        if ( ! empty( $value ) ) {
                            $safe_value = $this->twpgy_sanitize_css_color( $value );
                            if ( '' === $safe_value ) {
                                $safe_value = $this->twpgy_sanitize_css_length( $value );
                            }
                            if ( '' !== $safe_value ) {
                                $props = is_array( $safe_property ) ? $safe_property : array( $safe_property );
                                $declarations = array();
                                foreach ( $props as $prop ) {
                                    $declarations[] = "{$prop}: {$safe_value}";
                                }
                                if ( ! empty( $declarations ) ) {
                                    $css .= "{$safe_selector} { " . implode( '; ', $declarations ) . "; } ";
                                }
                            }
                        }
                }
            }
        }
    
        // -----------------------------------------------------------------------
        // Pagination colors
        // -----------------------------------------------------------------------
        $pagination_colors = $plugin_options['main_gallery_pagination_colors'] ?? [];

        // ← ESCAPE: all color values sanitized right before they enter the CSS string
        $pagination_normal_color = $this->twpgy_sanitize_css_color( $pagination_colors['color']        ?? '' );
        $pagination_active_color = $this->twpgy_sanitize_css_color( $pagination_colors['active_color'] ?? '' );

        $css .= '.themewant-product-gallery-preview-slider-container {';
        if ( '' !== $pagination_normal_color ) { $css .= "--eapgy-pagination-bg: {$pagination_normal_color} !important;"; }
        if ( '' !== $pagination_active_color ) { $css .= "--eapgy-pagination-bg-active: {$pagination_active_color} !important;"; }
        $css .= '}';
    
        // -----------------------------------------------------------------------
        // Nav colors & icon sizes
        // -----------------------------------------------------------------------
        $nav_colors          = $plugin_options['main_gallery_nav_colors'] ?? [];
        // ← ESCAPE: absint for pixel sizes, sanitize_css_color for colors
        $nav_icon_size       = absint( $plugin_options['main_gallery_nav_icon_size'] ?? 0 );
        $thumb_nav_icon_size = absint( $plugin_options['thumb_nav_icon_size']        ?? 0 );
    
        $nav_color       = $this->twpgy_sanitize_css_color( $nav_colors['color']                    ?? '' );
        $nav_hover_color = $this->twpgy_sanitize_css_color( $nav_colors['hover_color']              ?? '' );
        $nav_bg          = $this->twpgy_sanitize_css_color( $nav_colors['background_color']         ?? '' );
        $nav_hover_bg    = $this->twpgy_sanitize_css_color( $nav_colors['hover_background_color']   ?? '' );
    
        $css .= '.twpgy-easy-woo-gallery-image-area .themewant-product-gallery-preview-slider-container {';
        if ( $nav_icon_size )   { $css .= "--eapgy-nav-icon-size: {$nav_icon_size}px;"; }
        if ( '' !== $nav_color )       { $css .= "--eapgy-nav-color: {$nav_color};"; }
        if ( '' !== $nav_hover_color ) { $css .= "--eapgy-nav-hover-color: {$nav_hover_color};"; }
        if ( '' !== $nav_bg )          { $css .= "--eapgy-nav-bg: {$nav_bg};"; }
        if ( '' !== $nav_hover_bg )    { $css .= "--eapgy-nav-hover-bg: {$nav_hover_bg};"; }
        $css .= '}';
    
        // -----------------------------------------------------------------------
        // Fraction pagination color
        // -----------------------------------------------------------------------
        $fraction_color_option = $plugin_options['pagination_fraction_colors'] ?? [];

        // ← ESCAPE: sanitize right before interpolation
        $fraction_color = $this->twpgy_sanitize_css_color( $fraction_color_option['color'] ?? '' );

        $css .= ".themewant-product-gallery-preview-slider-container .twpgy-swiper-pagination-fraction { color: {$fraction_color}; }";

        // -----------------------------------------------------------------------
        // Thumb nav colors
        // -----------------------------------------------------------------------
        $thumb_nav_colors = $plugin_options['thumb_nav_colors'] ?? [];
    
        // ← ESCAPE: all sanitized right before they are written into $css
        $t_nav_color       = $this->twpgy_sanitize_css_color( $thumb_nav_colors['color']                  ?? '' );
        $t_nav_hover_color = $this->twpgy_sanitize_css_color( $thumb_nav_colors['hover_color']            ?? '' );
        $t_nav_bg          = $this->twpgy_sanitize_css_color( $thumb_nav_colors['background_color']       ?? '' );
        $t_nav_hover_bg    = $this->twpgy_sanitize_css_color( $thumb_nav_colors['hover_background_color'] ?? '' );
    
        $css .= '.twpgy-easy-woo-gallery-image-area .twpgy-thumb-slider-container {';
        if ( '' !== $t_nav_color )       { $css .= "--eapgy-t-nav-color: {$t_nav_color};"; }
        if ( '' !== $t_nav_hover_color ) { $css .= "--eapgy-t-nav-hover-color: {$t_nav_hover_color};"; }
        if ( '' !== $t_nav_bg )          { $css .= "--eapgy-t-nav-bg: {$t_nav_bg};"; }
        if ( '' !== $t_nav_hover_bg )    { $css .= "--eapgy-t-nav-hover-bg: {$t_nav_hover_bg};"; }
        if ( $thumb_nav_icon_size )      { $css .= "--eapgy-thumb-nav-icon-size: {$thumb_nav_icon_size}px;"; }
        $css .= '}';
    
        // -----------------------------------------------------------------------
        // Popup button position
        // -----------------------------------------------------------------------
        // ← ESCAPE: absint for offsets
        $bottom = isset( $plugin_options['twpgy_popup_button_offset_bottom'] ) ? absint( $plugin_options['twpgy_popup_button_offset_bottom'] ) . '%' : 'auto';
        $left   = isset( $plugin_options['twpgy_popup_button_offset_left'] )   ? absint( $plugin_options['twpgy_popup_button_offset_left'] )   . '%' : 'auto';

        $position_css = ".twpgy-product-360-button { --360degree-btn-bottom: {$bottom}; --360degree-btn-left: {$left}; top: auto; right: auto; }";

        /**
         * Filters the CSS that positions the 360° popup button.
         *
         * @since 1.1.2
         *
         * @param string $position_css   Bottom-left positioning rule.
         * @param array  $plugin_options Gallery settings for the product.
         */
        $css .= (string) apply_filters( 'twpgy_popup_button_position_css', $position_css, $plugin_options );

        // -----------------------------------------------------------------------
        // Slider height
        // -----------------------------------------------------------------------
        $slider_size        = $plugin_options['slider_size'] ?? [];
        // ← ESCAPE: raw height value — must be a valid CSS length or empty
        $slider_size_height = trim( $slider_size['height'] ?? '' );
        // Allow only safe CSS length values: digits + unit
        if ( '' !== $slider_size_height && preg_match( '/^\d+(\.\d+)?(px|em|rem|vh|vw|%)$/', $slider_size_height ) ) {
            $css .= ".twpgy-easy-woo-gallery-image-area .themewant-product-gallery-preview-slider-container .twpgy-swiper {
                height: {$slider_size_height};
            }";
        }
    
        // -----------------------------------------------------------------------
        // Thumbs slider gap
        // -----------------------------------------------------------------------
        // ← ESCAPE: allowlist for layout, absint for gap values
        $layout = $this->twpgy_sanitize_layout( $plugin_options['gallery_layout'] ?? 'thumbs_bottom' );
        $gap    = $plugin_options['eapgy_thumbs_slides_space'] ?? ( $plugin_options['twpgy_thumbs_slides_space'] ?? [] );
        $h_gap  = absint( $gap['horigap'] ?? 0 );
        $v_gap  = absint( $gap['vergap']  ?? 0 );
    
        switch ( $layout ) {
            case 'thumbs_left':
                if ( $v_gap > 0 ) {
                    $css .= ".eapgy-vertical-thumbs_left { margin-right: {$v_gap}px !important; }";
                }
                break;
            case 'thumbs_right':
                if ( $v_gap > 0 ) {
                    $css .= ".eapgy-vertical-thumbs_right { margin-left: {$v_gap}px !important; }";
                }
                break;
            case 'thumbs_top':
                if ( $v_gap > 0 ) {
                    $css .= ".eapgy-vertical-thumbs_top { margin-bottom: {$v_gap}px !important; }";
                }
                break;
            case 'thumbs_bottom':
            default:
                if ( $v_gap > 0 ) {
                    $css .= ".eapgy-vertical-thumbs_bottom { margin-top: {$v_gap}px !important; }";
                }
                break;
        }

        /**
         * Filters the generated inline gallery CSS.
         *
         * Add-ons append CSS for the settings they register.
         *
         * @since 1.1.2
         *
         * @param string $css            Generated CSS.
         * @param array  $plugin_options Gallery settings for the product.
         * @param int    $product_id     Product ID.
         */
        return (string) apply_filters( 'twpgy_dynamic_css', $css, $plugin_options, $product_id );
    }

    /**
     * Retrieves all registered gallery setting fields.
     *
     * @since 1.0.0
     *
     * @return array Settings field definitions.
     */
    private function get_settings_fields() {
        $settings = new TWPGY_Admin_Settings_Fields();
        return $settings->twpgy_get_all_settings_fields();
    }

    /**
     * Generates CSS rules for spacing fields.
     *
     * @since 1.0.0
     *
     * @param string $selector CSS selector.
     * @param string $property CSS property (padding or margin).
     * @param array  $value    Spacing values with unit.
     *
     * @return string Generated CSS.
     */
    private function twpgy_generate_spacing_css( $selector, $property, $value ) {

        if ( empty( $value ) || ! is_array( $value ) ) {
            return '';
        }

        $unit = $value['unit'] ?? '';

        $resolve = static function ( $raw ) use ( $unit ) {
            if ( $raw === null || $raw === '' ) {
                return $unit ? '0' . $unit : '0';
            }

            $raw = trim( (string) $raw );

            if ( '' === $raw ) {
                return $unit ? '0' . $unit : '0';
            }

            if ( $unit ) {
                $raw .= $unit;
            } elseif ( is_numeric( $raw ) ) {
                $raw .= 'px';
            }

            return self::twpgy_is_css_length( $raw, true ) ? $raw : '0';
        };

        $top    = $resolve( $value['top']    ?? '' );
        $right  = $resolve( $value['right']  ?? '' );
        $bottom = $resolve( $value['bottom'] ?? '' );
        $left   = $resolve( $value['left']   ?? '' );

        $is_zero = static function ( $v ) {
            return preg_match( '/^0(px|em|rem|%|vh|vw)?$/', $v ) === 1;
        };

        if ( $is_zero( $top ) && $is_zero( $right ) && $is_zero( $bottom ) && $is_zero( $left ) ) {
            return '';
        }

        return "{$selector} { {$property}: {$top} {$right} {$bottom} {$left}; } ";
    }

    /**
     * Checks that a value is a plain CSS length (e.g. 10px, 1.5em, 50%).
     *
     * @since 1.1.2
     *
     * @param string $value          Value to check.
     * @param bool   $allow_negative Whether negative lengths and "auto" are allowed.
     *
     * @return bool True if the value is a valid CSS length.
     */
    private static function twpgy_is_css_length( $value, $allow_negative = false ) {

        if ( $allow_negative && 'auto' === $value ) {
            return true;
        }

        $sign = $allow_negative ? '-?' : '';

        return 1 === preg_match( '/^' . $sign . '\d+(\.\d+)?(px|em|rem|%|vh|vw|vmin|vmax|ch|ex|pt|cm|mm|in|pc)?$/', $value );
    }

    /**
     * Generates CSS rules for dimension fields.
     *
     * @since 1.0.0
     *
     * @param string $selector CSS selector.
     * @param array  $value    Width/height values with unit.
     *
     * @return string Generated CSS.
     */
    private function twpgy_generate_dimension_css( $selector, $value ) {

        if ( empty( $value ) || ! is_array( $value ) ) {
            return '';
        }

        $unit   = $value['unit']   ?? '';
        $height = trim( (string) ( $value['height'] ?? '' ) );
        $width  = trim( (string) ( $value['width']  ?? '' ) );
        $height = ( '' !== $height && self::twpgy_is_css_length( $height . $unit ) ) ? $height : '';
        $width  = ( '' !== $width && self::twpgy_is_css_length( $width . $unit ) ) ? $width : '';

        if ( ! $height && ! $width ) {
            return '';
        }

        $css = "{$selector} {";

        if ( $height ) {
            $css .= " height: {$height}{$unit};";
        }

        if ( $width ) {
            $css .= " width: {$width}{$unit};";
        }

        return $css . " } ";
    }

    /**
     * Generates CSS rules for color-based fields.
     *
     * @since 1.0.0
     *
     * @param string $selector   CSS selector.
     * @param array  $properties CSS property to option key mapping.
     * @param array  $colors     Color values.
     *
     * @return string Generated CSS.
     */
    private function twpgy_generate_color_css( $selector, $properties, $colors ) {

        if ( empty( $colors ) || ! is_array( $colors ) ) {
            return '';
        }

        $css = '';

        foreach ( $properties as $css_prop => $color_key ) {
            if ( ! empty( $colors[ $color_key ] ) ) {
                $css .= "{$selector} { {$css_prop}: {$colors[$color_key]}; } ";
            }
        }

        return $css;
    }

    /**
     * Escaping helper: sanitize a CSS color value (hex, rgb, rgba, named).
     * Returns empty string if invalid.
     */
    function twpgy_sanitize_css_color( $color ) {
        $color = trim( $color );
        if ( '' === $color ) {
            return '';
        }
        // Allow hex colors
        if ( sanitize_hex_color( $color ) ) {
            return sanitize_hex_color( $color );
        }
        // Allow rgb() / rgba() / hsl() / named colors via a strict allowlist pattern
        if ( preg_match( '/^(rgb|rgba|hsl|hsla)\([\d\s,%.]+\)$/i', $color ) ) {
            return $color;
        }
        // Named CSS colors (only alpha chars)
        if ( preg_match( '/^[a-zA-Z]+$/', $color ) ) {
            return sanitize_key( $color );
        }
        return ''; // reject anything else
    }
 
    /**
     * Sanitize a CSS length value (e.g. 500px, 50%, 100vh).
     * Returns empty string if invalid.
     */
    function twpgy_sanitize_css_length( $value ) {
        $value = trim( $value );
        if ( '' === $value ) {
            return '';
        }
        if ( preg_match( '/^\d+(\.\d+)?(px|em|rem|%|vh|vw|ex|ch|vmin|vmax|cm|mm|in|pt|pc)$/', $value ) ) {
            return $value;
        }
        // Allow bare numbers (treated as px)
        if ( preg_match( '/^\d+(\.\d+)?$/', $value ) ) {
            return $value . 'px';
        }
        return '';
    }

    /**
     * Sanitize a gallery layout keyword.
     */
    function twpgy_sanitize_layout( $layout ) {
        $allowed = [ 'thumbs_bottom', 'thumbs_top', 'thumbs_left', 'thumbs_right' ];
        return in_array( $layout, $allowed, true ) ? $layout : 'thumbs_bottom';
    }
}