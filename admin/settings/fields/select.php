<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

$twpgy_options         = isset( $field['options'] ) && is_array( $field['options'] ) ? $field['options'] : [];
$twpgy_select_field    = $field_name;
$twpgy_saved_value     = $field_value;
$twpgy_is_multiple     = false;

/**
 * Handle multiple select.
 */
if ( isset( $field['multiple'] ) && true === $field['multiple'] ) {

    $twpgy_is_multiple    = true;
    $twpgy_select_field  .= '[]';

    if ( ! is_array( $twpgy_saved_value ) ) {
        $twpgy_saved_value = [ $twpgy_saved_value ];
    }
} else {
    if ( is_array( $twpgy_saved_value ) ) {
        $twpgy_saved_value = reset( $twpgy_saved_value );
    }
}

/**
 * Output select field.
 */
printf(
    '<select name="%s"%s>',
    esc_attr( $twpgy_select_field ),
    $twpgy_is_multiple ? ' multiple="multiple"' : ''
);

/**
 * Loop through options.
 */

foreach ( $twpgy_options as $twpgy_key => $twpgy_option ) {

    // Selected attribute.
    if ( $twpgy_is_multiple ) {
        $twpgy_selected = in_array( (string) $twpgy_key, array_map( 'strval', $twpgy_saved_value ), true );
    } else {
        $twpgy_selected = ( (string) $twpgy_key === (string) $twpgy_saved_value );
    }

    printf(
        '<option value="%s"%s>%s</option>',
        esc_attr( $twpgy_key ),
        selected( $twpgy_selected, true, false ),
        esc_html( $twpgy_option )
    );
}

echo '</select>';