<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

if ( $field_type === 'dimension' ) {

    $twpgy_field_label = isset( $field['label'] ) ? $field['label'] : '';
    $twpgy_options     = ( isset( $field['options'] ) && ! empty( $field['options'] ) )
        ? $field['options']
        : array();

    $twpgy_unit = ( isset( $twpgy_options['unit'] ) && ! empty( $twpgy_options['unit'] ) )
        ? $twpgy_options['unit']
        : 'px';

    $twpgy_field_value = isset( $twpgy_options_values[ $field_name ] )
        ? $twpgy_options_values[ $field_name ]
        : $field_value;

    $twpgy_height = ( isset( $twpgy_field_value['height'] ) && ! empty( $twpgy_field_value['height'] ) )
        ? $twpgy_field_value['height']
        : '';

    $twpgy_width = ( isset( $twpgy_field_value['width'] ) && ! empty( $twpgy_field_value['width'] ) )
        ? $twpgy_field_value['width']
        : '';

    if ( ! empty( $twpgy_options ) && is_array( $twpgy_options ) ) {

        echo '<div class="themewant-product-gallery-item-multiple">';

        $twpgy_input_type = ( isset( $twpgy_options['type'] ) && ! empty( $twpgy_options['type'] ) )
            ? $twpgy_options['type']
            : 'text';

        printf(
            '<div class="themewant-product-gallery-settings-inner">
                <label>%1$s</label>
                <input type="%2$s"
                       name="%3$s[height]"
                       id="twpgy_woo_gallery_general_settings_height"
                       value="%4$s" />
            </div>
            <div class="themewant-product-gallery-settings-inner">
                <label>%5$s</label>
                <input type="%2$s"
                       name="%3$s[width]"
                       id="twpgy_woo_gallery_general_settings_width"
                       value="%6$s" />
            </div>',
            esc_html__( 'Height', 'themewant-product-gallery' ),
            esc_attr( $twpgy_input_type ),
            esc_attr( $field_name ),
            esc_attr( $twpgy_height ),
            esc_html__( 'Width', 'themewant-product-gallery' ),
            esc_attr( $twpgy_width )
        );

        echo '</div>';
    }
}