<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

$twpgy_fields            = isset( $field['fields'] ) ? $field['fields'] : [];
$twpgy_multiple          = '';
$twpgy_select_field_name = $field_name;

$twpgy_saved_value = $field_value;

if ( ! empty( $field['multiple'] ) && $field['multiple'] === true ) {
    if ( ! is_array( $twpgy_saved_value ) ) {
        $twpgy_saved_value = [ $twpgy_saved_value ];
    }
} else {
    if ( is_array( $twpgy_saved_value ) ) {
        $twpgy_saved_value = reset( $twpgy_saved_value ); 
    }
}

echo '<div class="themewant-product-gallery-item-multiple">';

foreach ( $twpgy_fields as $twpgy_sub_key => $twpgy_sub_field ) {

    $twpgy_label = isset( $twpgy_sub_field['label'] ) ? $twpgy_sub_field['label'] : '';
   
    $twpgy_name  = isset( $twpgy_sub_field['name'] ) ? $field_name . '[' . $twpgy_sub_field['name'] . ']' : $field_name;

    if ( isset( $twpgy_saved_value[ $twpgy_sub_field['name'] ] ) ) {
        $twpgy_value = $twpgy_saved_value[ $twpgy_sub_field['name'] ];
    } else {
        $twpgy_value = isset( $twpgy_sub_field['value'] ) ? $twpgy_sub_field['value'] : '';
    }

    ?>
    <div class="themewant-product-gallery-settings-inner">
        <label for="twpgy_woo_gallery_general_settings"><?php echo esc_html( $twpgy_label ); ?></label>
        <input 
            type="text" 
            name="<?php echo esc_attr( $twpgy_name ); ?>" 
            id="twpgy_woo_gallery_general_settings" 
            value="<?php echo esc_attr( $twpgy_value ); ?>" 
            class="color-picker"
        >
    </div>
    <?php
}

echo '</div>';