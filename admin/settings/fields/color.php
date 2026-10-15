<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

printf(
    '<input type="text" name="%s" id="twpgy_woo_gallery_general_settings" value="%s" class="color-picker">',
    esc_attr($field_name),
    esc_attr($field_value)
);

