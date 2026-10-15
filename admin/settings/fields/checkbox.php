<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


printf(
    '<input 
        type="%1$s"
        name="%2$s"
        id="%3$s"
        value="on"
        %4$s
    />',
    esc_attr( $field_type ),
    esc_attr( $field_name ),
    esc_attr( 'twpgy_woo_gallery_general_settings' ),
    checked( 'on', $field_value, false ),
);