<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

printf(
    '<input type="%1$s" name="%2$s" id="twpgy_woo_gallery_general_settings" value="%3$s"  />',
    esc_attr( $field_type ),
    esc_attr( $field_name ),
    esc_attr( $field_value ),
);