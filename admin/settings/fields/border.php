<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$twpgy_value = is_array( $field_value ) ? $field_value : array();

$twpgy_width = $twpgy_value['width'] ?? 1;
$twpgy_style = $twpgy_value['style'] ?? 'solid';
$twpgy_color = $twpgy_value['color'] ?? '';
?>

<div class="themewant-product-gallery-item-multiple eapgy-lengs-border-control">

    <!-- Width -->
    <div class="themewant-product-gallery-settings-inner">
        <label><?php esc_html_e( 'Width', 'themewant-product-gallery' ); ?></label>
        <input type="number"
               min="0"
               name="<?php echo esc_attr( $field_name ); ?>[width]"
               value="<?php echo esc_attr( $twpgy_width ); ?>" />
        <span class="unit">px</span>
    </div>

    <!-- Style -->
    <div class="themewant-product-gallery-settings-inner">
        <label><?php esc_html_e( 'Style', 'themewant-product-gallery' ); ?></label>
        <select name="<?php echo esc_attr( $field_name ); ?>[style]">
            <?php foreach ( $field['fields']['style']['options'] as $twpgy_key => $twpgy_label ) : ?>
                <option value="<?php echo esc_attr( $twpgy_key ); ?>"
                    <?php selected( $twpgy_style, $twpgy_key ); ?>>
                    <?php echo esc_html( $twpgy_label ); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Color -->
    <div class="themewant-product-gallery-settings-inner">
        <label><?php esc_html_e( 'Color', 'themewant-product-gallery' ); ?></label>
        <input type="text"
               class="eapgy-lengs-color-picker color-picker"
               name="<?php echo esc_attr( $field_name ); ?>[color]"
               value="<?php echo esc_attr( $twpgy_color ); ?>" />
    </div>

</div>