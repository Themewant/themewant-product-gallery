<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$twpgy_min  = $field['min']  ?? 10;
$twpgy_max  = $field['max']  ?? 100;
$twpgy_step = $field['step'] ?? 1;
$twpgy_unit = $field['unit'] ?? '%';

$twpgy_resp_fields = $field['responsive_fields'] ?? array(
    'desktop' => $field['name'],
    'tablet'  => $field['name'] . '_tablet',
    'mobile'  => $field['name'] . '_mobile',
);

$twpgy_devices = array(
    'desktop' => array(
        'icon'  => 'dashicons-desktop',
        'label' => __( 'Desktop', 'themewant-product-gallery' ),
        'key'   => $twpgy_resp_fields['desktop'],
    ),
    'tablet' => array(
        'icon'  => 'dashicons-tablet',
        'label' => __( 'Tablet (≤921px)', 'themewant-product-gallery' ),
        'key'   => $twpgy_resp_fields['tablet'],
    ),
    'mobile' => array(
        'icon'  => 'dashicons-smartphone',
        'label' => __( 'Mobile (≤767px)', 'themewant-product-gallery' ),
        'key'   => $twpgy_resp_fields['mobile'],
    ),
);

foreach ( $twpgy_devices as $twpgy_device_key => &$twpgy_device ) {
    $twpgy_saved = $twpgy_gallery_options[ $section_name ][ $twpgy_device['key'] ] ?? ( $field['default'] ?? $twpgy_min );
    $twpgy_device['value']     = $twpgy_saved;
    $twpgy_device['full_name'] = $options_name . '[' . $section_name . '][' . $twpgy_device['key'] . ']';
}
unset( $twpgy_device );
?>

<div class="twpgy-responsive-control">

    <div class="twpgy-device-tabs">
        <?php foreach ( $twpgy_devices as $twpgy_device_key => $twpgy_device ) : ?>
            <button type="button"
                    class="twpgy-device-tab <?php echo $twpgy_device_key === 'desktop' ? 'active' : ''; ?>"
                    data-device="<?php echo esc_attr( $twpgy_device_key ); ?>"
                    title="<?php echo esc_attr( $twpgy_device['label'] ); ?>">
                <span class="dashicons <?php echo esc_attr( $twpgy_device['icon'] ); ?>"></span>
            </button>
        <?php endforeach; ?>
    </div>

    <?php foreach ( $twpgy_devices as $twpgy_device_key => $twpgy_device ) : ?>
        <div class="twpgy-device-panel"
             data-device="<?php echo esc_attr( $twpgy_device_key ); ?>"
             <?php echo $twpgy_device_key !== 'desktop' ? 'style="display:none;"' : ''; ?>>

            <div class="eapgy-wooproduct-fieldset">
                <div class="eapgy-wooproduct-table">

                    <div class="eapgy-wooproduct-table-cell eapgy-wooproduct-table-expanded">
                        <div class="eapgy-wooproduct-slider-ui"
                             data-target-input="<?php echo esc_attr( $twpgy_device['full_name'] ); ?>"
                             data-min="<?php echo esc_attr( $twpgy_min ); ?>"
                             data-max="<?php echo esc_attr( $twpgy_max ); ?>"
                             data-step="<?php echo esc_attr( $twpgy_step ); ?>"
                             data-value="<?php echo esc_attr( $twpgy_device['value'] ); ?>">
                        </div>
                    </div>

                    <div class="eapgy-wooproduct-table-cell eapgy-wooproduct-nowrap">
                        <input type="number"
                               name="<?php echo esc_attr( $twpgy_device['full_name'] ); ?>"
                               value="<?php echo esc_attr( $twpgy_device['value'] ); ?>"
                               class="eapgy-wooproduct-number"
                               min="<?php echo esc_attr( $twpgy_min ); ?>"
                               max="<?php echo esc_attr( $twpgy_max ); ?>"
                               step="<?php echo esc_attr( $twpgy_step ); ?>"
                               autocomplete="off">
                        <em><?php echo esc_html( $twpgy_unit ); ?></em>
                    </div>

                </div>
            </div>

        </div>
    <?php endforeach; ?>

</div>
