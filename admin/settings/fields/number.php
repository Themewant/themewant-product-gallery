<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

printf(
    '<input type="%s" name="%s" id="twpgy_woo_gallery_general_settings" value="%s">',
    esc_attr( $field_type ),
    esc_attr( $field_name ),
    esc_attr( $field_value )
);
