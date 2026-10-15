<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( $field_type === 'dimensionsgap' ) {

    $twpgy_field_value = is_array( $field_value ) ? $field_value : array();

    $twpgy_vergap  = isset( $twpgy_field_value['vergap'] )  ? $twpgy_field_value['vergap']  : '';
    $twpgy_horigap = isset( $twpgy_field_value['horigap'] ) ? $twpgy_field_value['horigap'] : '';
    ?>

    <div class="eapgy-gap-wrap">

        <!-- Horizontal Gap -->
        <div class="eapgy-gap-group">
            <div class="eapgy-gap-title"><?php esc_html_e( 'Horizontal Gap', 'themewant-product-gallery' ); ?></div>

            <div class="eapgy-gap-field">
                <span class="eapgy-gap-icon">↔</span>

                <input type="number"
                       name="<?php echo esc_attr( $field_name ); ?>[horigap]"
                       value="<?php echo esc_attr( $twpgy_horigap ); ?>">

                <span class="eapgy-gap-unit">px</span>
            </div>
        </div>

        <!-- Vertical Gap -->
        <div class="eapgy-gap-group">
            <div class="eapgy-gap-title"><?php esc_html_e( 'Vertical Gap', 'themewant-product-gallery' ); ?></div>

            <div class="eapgy-gap-field">
                <span class="eapgy-gap-icon">↕</span>

                <input type="number"
                       name="<?php echo esc_attr( $field_name ); ?>[vergap]"
                       value="<?php echo esc_attr( $twpgy_vergap ); ?>">

                <span class="eapgy-gap-unit">px</span>
            </div>
        </div>

    </div>
<?php
}