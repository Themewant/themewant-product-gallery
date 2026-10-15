<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

$twpgy_value       = $field_value ?? $field['default'] ?? 10;
$twpgy_min         = $field['min'] ?? 0;
$twpgy_max         = $field['max'] ?? 100;
$twpgy_step        = $field['step'] ?? 1;
$twpgy_unit        = $field['unit'] ?? '%';
$twpgy_offset_type = $field['offset_type'] ?? '';
?>

<div class="eapgy-wooproduct-fieldset" <?php echo $twpgy_offset_type ? 'data-offset="' . esc_attr( $twpgy_offset_type ) . '"' : ''; ?>>
    <div class="eapgy-wooproduct-table">

        <div class="eapgy-wooproduct-table-cell eapgy-wooproduct-table-expanded">
            <div class="eapgy-wooproduct-slider-ui"
                 data-target-input="<?php echo esc_attr( $field_name ); ?>"
                 data-min="<?php echo esc_attr( $twpgy_min ); ?>"
                 data-max="<?php echo esc_attr( $twpgy_max ); ?>"
                 data-step="<?php echo esc_attr( $twpgy_step ); ?>"
                 data-value="<?php echo esc_attr( $twpgy_value ); ?>">
            </div>
        </div>

        <div class="eapgy-wooproduct-table-cell eapgy-wooproduct-nowrap">
            <input type="number"
                   name="<?php echo esc_attr( $field_name ); ?>"
                   value="<?php echo esc_attr( $twpgy_value ); ?>"
                   class="eapgy-wooproduct-number"
                   min="<?php echo esc_attr( $twpgy_min ); ?>"
                   max="<?php echo esc_attr( $twpgy_max ); ?>"
                   step="<?php echo esc_attr( $twpgy_step ); ?>"
                   autocomplete="off">
            <em><?php echo esc_html( $twpgy_unit ); ?></em>
        </div>

    </div>
</div>