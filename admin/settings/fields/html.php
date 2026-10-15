<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

$twpgy_allowed_html = array(
    'br'     => array(),
    'p'      => array(),
    'a'      => array(),
    'span'   => array(),
    'div'    => array(),
    'strong' => array(),
    'h4'     => array(),
    'h5'     => array(),
    'h6'     => array(),
);

echo wp_kses( $field_value, $twpgy_allowed_html );