<?php 
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( $field_type === 'image_select' ) {

    $twpgy_options = $field['options'] ?? array();

    if ( ! empty( $twpgy_options ) && is_array( $twpgy_options ) ) {

        echo '<div class="themewant-product-gallery-image-select">';

        foreach ( $twpgy_options as $twpgy_key => $twpgy_option ) {

            $twpgy_label = $twpgy_option['label'] ?? '';
            $twpgy_image = $twpgy_option['image'] ?? '';
            $twpgy_demo  = $twpgy_option['demo'] ?? '';

            ?>
            <div class="eapgy-sibling eapgy--image">

                <label class="eapgy--image-select">
                    <img src="<?php echo esc_url( $twpgy_image ); ?>" alt="<?php echo esc_attr( $twpgy_label ); ?>">

                    <input type="radio"
                        name="<?php echo esc_attr( $field_name ); ?>"
                        value="<?php echo esc_attr( $twpgy_key ); ?>"
                        <?php checked( $field_value, $twpgy_key ); ?>
                    />
                </label>

                <p class="eapgy-img-title">
                    <b><?php echo esc_html( $twpgy_label ); ?></b>

                    <?php if ( ! empty( $twpgy_demo ) ) { ?>
                        <a href="<?php echo esc_url( $twpgy_demo ); ?>"
                           title="<?php esc_attr_e( 'Demo', 'themewant-product-gallery' ); ?>"
                           class="eapgy-live-demo-icon"
                           target="_blank">
                            <span class="dashicons dashicons-external" aria-hidden="true"></span>
                        </a>
                    <?php } ?>
                </p>

            </div>
            <?php
        }

        echo '</div>';
    }
}