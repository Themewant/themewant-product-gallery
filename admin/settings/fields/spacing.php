<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

if ( 'spacing' === $field_type ) {

    $twpgy_field_label = isset( $field['label'] ) ? $field['label'] : '';
    $twpgy_options     = isset( $field['options'] ) && is_array( $field['options'] ) ? $field['options'] : [];
    $twpgy_unit        = isset( $twpgy_options['unit'] ) && ! empty( $twpgy_options['unit'] ) ? $twpgy_options['unit'] : 'px';

    $twpgy_field_value = isset( $twpgy_options[ $field_name ] )
        ? $twpgy_options[ $field_name ]
        : ( is_array( $field_value ) ? $field_value : [] );

    $twpgy_value_top    = isset( $twpgy_field_value['top'] ) ? $twpgy_field_value['top'] : '';
    $twpgy_value_right  = isset( $twpgy_field_value['right'] ) ? $twpgy_field_value['right'] : '';
    $twpgy_value_bottom = isset( $twpgy_field_value['bottom'] ) ? $twpgy_field_value['bottom'] : '';
    $twpgy_value_left   = isset( $twpgy_field_value['left'] ) ? $twpgy_field_value['left'] : '';

    if ( ! empty( $twpgy_options ) ) {

        echo '<div class="themewant-product-gallery-item-multiple fields-group">';

        $twpgy_input_type = isset( $twpgy_options['type'] ) && ! empty( $twpgy_options['type'] )
            ? $twpgy_options['type']
            : 'text';

        printf(
            '<div class="themewant-product-gallery-settings-inner">
                <label>%7$s</label>
                <input type="%1$s" name="%2$s[top]" id="twpgy_woo_gallery_general_settings_top" value="%3$s" />
            </div>
            <div class="themewant-product-gallery-settings-inner">
                <label>%8$s</label>
                <input type="%1$s" name="%2$s[right]" id="twpgy_woo_gallery_general_settings_right" value="%4$s" />
            </div>
            <div class="themewant-product-gallery-settings-inner">
                <label>%9$s</label>
                <input type="%1$s" name="%2$s[bottom]" id="twpgy_woo_gallery_general_settings_bottom" value="%5$s" />
            </div>
            <div class="themewant-product-gallery-settings-inner">
                <label>%10$s</label>
                <input type="%1$s" name="%2$s[left]" id="twpgy_woo_gallery_general_settings_left" value="%6$s" />
            </div>',
            esc_attr( $twpgy_input_type ),
            esc_attr( $field_name ),
            esc_attr( $twpgy_value_top ),
            esc_attr( $twpgy_value_right ),
            esc_attr( $twpgy_value_bottom ),
            esc_attr( $twpgy_value_left ),
            esc_html__( 'Top', 'themewant-product-gallery' ),
            esc_html__( 'Right', 'themewant-product-gallery' ),
            esc_html__( 'Bottom', 'themewant-product-gallery' ),
            esc_html__( 'Left', 'themewant-product-gallery' )
        );

        echo '</div>';
    }
}