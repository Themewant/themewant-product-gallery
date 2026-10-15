<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

$twpgy_field_label = isset( $field_label ) ? $field_label : '';

if ( ! empty( $twpgy_field_label ) ) {
    ?>
    <h3 class="eapgy-section-title">
        <?php echo esc_html( $twpgy_field_label ); ?>
    </h3>
    <?php
}
