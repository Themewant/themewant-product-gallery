<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

$twpgy_htmlentities = htmlentities( $field_value );
echo wp_kses( $twpgy_htmlentities, $allowed_html );