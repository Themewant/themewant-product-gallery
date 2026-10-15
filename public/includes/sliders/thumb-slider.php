<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

$twpgy_vertical_gap = 'eapgy-vertical-' . ( $plugin_settings['gallery_layout'] ?? '' );
?>

<div class="twpgy-thumb-slider-container <?php echo esc_attr( $twpgy_vertical_gap . ' ' . $thumbs_nav_visibility ); ?>">
    <div class="twpgy-swiper">
        <div class="twpgy-swiper-wrapper">
            <?php
            if ( ! empty( $attachment_ids ) && is_array( $attachment_ids ) ) {

                foreach ( $attachment_ids as $twpgy_attachment_id ) {

                    $twpgy_video_url = get_post_meta(
                        $twpgy_attachment_id,
                        'twpgy_eswoglry_video',
                        true
                    );

                    $twpgy_image_caption = (
                        isset( $plugin_settings['lightbox_caption'] ) &&
                        'on' === $plugin_settings['lightbox_caption']
                    )
                        ? wp_get_attachment_caption( $twpgy_attachment_id )
                        : '';

                    if (
                        isset( $plugin_settings['gallery_video'] ) &&
                        'on' === $plugin_settings['gallery_video'] &&
                        ! empty( $twpgy_video_url )
                    ) {
                        $twpgy_parsed_video_url = wp_parse_url( $twpgy_video_url );
                        $twpgy_video_host       = $twpgy_parsed_video_url['host'] ?? '';
                    }

                    $twpgy_thumb_image = wp_get_attachment_image(
                        $twpgy_attachment_id,
                        $thumbnail_image_size,
                        false,
                        array(
                            'data-caption' => esc_attr( $twpgy_image_caption ),
                        )
                    );
                    ?>
                    <div class="twpgy-swiper-slide">
                        <?php
                        echo wp_kses_post( $twpgy_thumb_image );

                        $twpgy_show_play_icon = (
                            ! empty( $twpgy_video_url ) &&
                            isset( $plugin_settings['gallery_video'] ) &&
                            'on' === $plugin_settings['gallery_video']
                        );

                        if ( $twpgy_show_play_icon ) { ?>
                            <span class="play-icon position-<?php echo esc_attr( $plugin_settings['video_play_icon_position'] ?? 'middle' ); ?>"
                                  title="<?php echo esc_attr__( 'Click here to play video.', 'themewant-product-gallery' ); ?>">
                                <svg viewBox="0 0 512 512" width="512" height="512" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="256.5" cy="256" r="256" fill="red"/>
                                    <path d="m383.55 233.228-172.85-99.79a26.3 26.3 0 0 0 -39.44 22.762v199.6a26.039 26.039 0 0 0 13.14 22.77 26.067 26.067 0 0 0 26.3 0l172.85-99.8a26.3 26.3 0 0 0 0-45.54z" fill="#fff"/>
                                </svg>
                            </span>
                        <?php } ?>
                    </div>
                    <?php
                }
            }
            ?>
        </div>
    </div>

    <?php
    if ( count( $attachment_ids ) > 3 && 'on' === $enable_thumbs_nav ) {
        echo wp_kses_post( $slider_nav_btns );
    }
    ?>
</div>