<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$twpgy_dynamic_class =
    $slider_orientaiton . ' ' .
    $main_product_nav_visibility . ' ' .
    $mproduct_pagination_visibility . ' ' .
    $pagination_layout . ' ' .
    $twpgy_bullet_position . ' ' .
    $gallery_image_zoom_class;

if ( $product->get_image_id() ) { ?>
    <div class="themewant-product-gallery-preview-slider-container <?php echo esc_attr( $twpgy_dynamic_class ); ?>">

        <?php
        if ( $gallery_layout !== 'carousel_two' ) {
            do_action( 'twpgy_on_product_image' );
        }

        global $post;
        echo wp_kses_post(
            $this->twpgy_woocommerce_get_sale_flash( $html, $post, $product )
        );
        ?>

        <div class="twpgy-swiper twpgy-eapgy-swiper">
            <div class="twpgy-swiper-wrapper">
                <?php
                if ( ! empty( $attachment_ids ) && is_array( $attachment_ids ) ) {
                    foreach ( $attachment_ids as $twpgy_attachment_id ) {

                        $twpgy_image_url = wp_get_attachment_image_url(
                            $twpgy_attachment_id,
                            $preview_image_size
                        );

                        $twpgy_image_caption = (
                            isset( $plugin_settings['lightbox_caption'] ) &&
                            'on' === $plugin_settings['lightbox_caption']
                        )
                            ? wp_get_attachment_caption( $twpgy_attachment_id )
                            : '';

                        $twpgy_video_url     = get_post_meta(
                            $twpgy_attachment_id,
                            'twpgy_eswoglry_video',
                            true
                        );

                        $twpgy_lightbox_url  = $twpgy_image_url;
                        $twpgy_slide_classes = $gallery_image_zoom_class;

                        $twpgy_has_playable_video = false;
                        $twpgy_video_host         = '';

                        if (
                            isset( $plugin_settings['gallery_video'] ) &&
                            'on' === $plugin_settings['gallery_video'] &&
                            ! empty( $twpgy_video_url )
                        ) {
                            $twpgy_parsed_video_url = wp_parse_url( $twpgy_video_url );
                            $twpgy_video_host       = strtolower( $twpgy_parsed_video_url['host'] ?? '' );

                            /**
                             * Filters whether a product video URL can be played by the gallery.
                             *
                             * The gallery plays YouTube videos. Add-ons can return true for
                             * other sources they render via `twpgy_inline_video_html`.
                             *
                             * @since 1.1.2
                             *
                             * @param bool   $is_playable Whether the URL is a YouTube video.
                             * @param string $video_url   Video URL saved on the attachment.
                             * @param string $video_host  Lower-cased host of the video URL.
                             */
                            $twpgy_has_playable_video = (bool) apply_filters(
                                'twpgy_is_playable_video',
                                false !== strpos( $twpgy_video_host, 'youtu' ),
                                $twpgy_video_url,
                                $twpgy_video_host
                            );

                            if ( $twpgy_has_playable_video ) {
                                $twpgy_slide_classes .= ' has-video';
                                $twpgy_lightbox_url   = $twpgy_video_url;
                            }
                        }

                        $twpgy_is_inline_video = (
                            $twpgy_has_playable_video &&
                            isset( $plugin_settings['video_play_mode'] ) &&
                            'inline' === $plugin_settings['video_play_mode']
                        );

                        if ( $twpgy_is_inline_video ) {
                            $twpgy_slide_classes .= ' twpgy-inline-video-slide';
                        }

                        $twpgy_lightbox_start = sprintf(
                            '<a href="%s"   data-fancybox="gallery" data-caption="%s"  class="lightbox-toggle">',
                            esc_url( $twpgy_lightbox_url ),
                            esc_attr( $twpgy_image_caption )
                        );
                        

                        $twpgy_lightbox_end = '</a>';

                        $twpgy_full_size_image = wp_get_attachment_image_src( $twpgy_attachment_id, 'full' );
                        $twpgy_preview_image = wp_get_attachment_image(
                            $twpgy_attachment_id,
                            $preview_image_size,
                            false,
                            array(
                                'data-caption'          => esc_attr( $twpgy_image_caption ),
                                'class'                 => 'preview-image ' . esc_attr( $zoom_style ),
                                'data-src'              => $twpgy_full_size_image ? esc_url( $twpgy_full_size_image[0] ) : '',
                                'data-large_image'      => $twpgy_full_size_image ? esc_url( $twpgy_full_size_image[0] ) : '',
                                'data-large_image_width'  => $twpgy_full_size_image ? esc_attr( $twpgy_full_size_image[1] ) : '',
                                'data-large_image_height' => $twpgy_full_size_image ? esc_attr( $twpgy_full_size_image[2] ) : '',
                            )
                        );
                        ?>
                        <div class="twpgy-swiper-slide product-thumb <?php echo esc_attr( $twpgy_slide_classes ); ?>"
                             <?php if ( ! empty( $gallery_image_zoom_class ) && ! $twpgy_is_inline_video ) : ?>
                             style="background-image: url(<?php echo esc_url( $twpgy_image_url ); ?>);"
                             <?php endif; ?>>

                            <?php
                            if ( $gallery_layout === 'carousel_two' ) {
                               do_action( 'twpgy_on_product_image' );
                            }

                            if ( ! $twpgy_is_inline_video ) {
                                echo wp_kses_post( $twpgy_lightbox_start );
                            }

                            if (
                                ! $twpgy_is_inline_video &&
                                $twpgy_has_playable_video &&
                                isset( $plugin_settings['video_play_mode'] ) &&
                                'popup' === $plugin_settings['video_play_mode']
                            ) { ?>
                                <span class="play-icon position-<?php echo esc_attr( $plugin_settings['video_play_icon_position'] ?? 'middle' ); ?>"
                                      title="<?php echo esc_attr__( 'Click here to play video.', 'themewant-product-gallery' ); ?>">
                                    <svg height="512" viewBox="0 0 512 512" width="512" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false"><circle cx="256" cy="256" r="256" fill="red"/><path d="M200 148v216l176-108z" fill="#fff" stroke="#fff" stroke-width="24" stroke-linejoin="round"/></svg>
                                </span>
                            <?php }

                            if (
                                ! $twpgy_is_inline_video &&
                                isset( $plugin_settings['gallery_lightbox'] ) &&
                                'on' === $plugin_settings['gallery_lightbox']
                            ) { ?>
                                <button class="twpgy-woglry-lightbox-trigger position-<?php echo esc_attr( $plugin_settings['lightbox_icon_position'] ?? 'top_right' ); ?>">
                                    <svg height="512" viewBox="0 0 32 32" width="512" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false"><path fill-rule="evenodd" d="M16 0a16 16 0 1 1 0 32a16 16 0 1 1 0-32zM14.5 8.5a6 6 0 1 0 0 12a6 6 0 1 0 0-12zM14.5 10.4a4.1 4.1 0 1 1 0 8.2a4.1 4.1 0 1 1 0-8.2zM19.45 18.04l4.06 4.05-1.42 1.42-4.05-4.06z"/></svg>
                                </button>
                            <?php }

                            if ( $twpgy_is_inline_video ) {
                                $twpgy_autoplay = ! empty( $plugin_settings['autoplay_video'] ) && 'on' === $plugin_settings['autoplay_video'];
                                $twpgy_loop     = ! empty( $plugin_settings['loop_video'] ) && 'on' === $plugin_settings['loop_video'];
                                $twpgy_preload  = ! empty( $plugin_settings['video_preloader'] ) && 'on' === $plugin_settings['video_preloader'];

                                /**
                                 * Filters whether inline video players show their controls.
                                 *
                                 * @since 1.1.2
                                 *
                                 * @param bool  $show_controls   Default false.
                                 * @param array $plugin_settings Gallery settings for the product.
                                 */
                                $twpgy_controls = (bool) apply_filters( 'twpgy_video_player_controls', false, $plugin_settings );

                                if (
                                    isset( $plugin_settings['gallery_lightbox'] ) &&
                                    'on' === $plugin_settings['gallery_lightbox']
                                ) { ?>
                                    <a href="<?php echo esc_url( $twpgy_lightbox_url ); ?>"
                                       data-fancybox="gallery"
                                       data-caption="<?php echo esc_attr( $twpgy_image_caption ); ?>"
                                       class="lightbox-toggle twpgy-inline-lightbox-anchor">
                                        <button type="button" class="twpgy-woglry-lightbox-trigger position-<?php echo esc_attr( $plugin_settings['lightbox_icon_position'] ?? 'top_right' ); ?>">
                                            <svg height="512" viewBox="0 0 32 32" width="512" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false"><path fill-rule="evenodd" d="M16 0a16 16 0 1 1 0 32a16 16 0 1 1 0-32zM14.5 8.5a6 6 0 1 0 0 12a6 6 0 1 0 0-12zM14.5 10.4a4.1 4.1 0 1 1 0 8.2a4.1 4.1 0 1 1 0-8.2zM19.45 18.04l4.06 4.05-1.42 1.42-4.05-4.06z"/></svg>
                                        </button>
                                    </a>
                                <?php }

                                echo '<div class="twpgy-inline-video-wrap">';
                                ?>
                                <img class="twpgy-inline-video-spacer"
                                     src="<?php echo esc_url( $twpgy_image_url ); ?>"
                                     alt=""
                                     aria-hidden="true" />
                                <?php

                                $twpgy_embed_src = '';

                                if ( false !== strpos( $twpgy_video_host, 'youtu' ) ) {
                                    $twpgy_yt_id = '';
                                    if ( false !== strpos( $twpgy_video_host, 'youtu.be' ) ) {
                                        $twpgy_yt_id = trim( wp_parse_url( $twpgy_video_url, PHP_URL_PATH ), '/' );
                                    } else {
                                        parse_str( (string) wp_parse_url( $twpgy_video_url, PHP_URL_QUERY ), $twpgy_yt_q );
                                        $twpgy_yt_id = $twpgy_yt_q['v'] ?? '';
                                        if ( empty( $twpgy_yt_id ) && false !== strpos( $twpgy_video_url, '/embed/' ) ) {
                                            $twpgy_yt_id = basename( wp_parse_url( $twpgy_video_url, PHP_URL_PATH ) );
                                        }
                                    }
                                    if ( $twpgy_yt_id ) {
                                        $twpgy_embed_src = add_query_arg(
                                            array(
                                                'autoplay' => $twpgy_autoplay ? 1 : 0,
                                                'mute'     => $twpgy_autoplay ? 1 : 0,
                                                'loop'     => $twpgy_loop ? 1 : 0,
                                                'playlist' => $twpgy_loop ? $twpgy_yt_id : false,
                                                'controls' => $twpgy_controls ? 1 : 0,
                                                'rel'      => 0,
                                            ),
                                            'https://www.youtube-nocookie.com/embed/' . rawurlencode( $twpgy_yt_id )
                                        );
                                    }
                                }

                                /**
                                 * Filters the inline player markup for a product video.
                                 *
                                 * Return non-empty HTML to render a video source that the
                                 * gallery does not embed itself.
                                 *
                                 * @since 1.1.2
                                 *
                                 * @param string $html       Default empty string.
                                 * @param string $video_url  Video URL saved on the attachment.
                                 * @param string $video_host Lower-cased host of the video URL.
                                 * @param array  $args       Player options: autoplay, loop, controls, preload, poster.
                                 */
                                $twpgy_inline_video_html = (string) apply_filters(
                                    'twpgy_inline_video_html',
                                    '',
                                    $twpgy_video_url,
                                    $twpgy_video_host,
                                    array(
                                        'autoplay' => $twpgy_autoplay,
                                        'loop'     => $twpgy_loop,
                                        'controls' => $twpgy_controls,
                                        'preload'  => $twpgy_preload,
                                        'poster'   => $twpgy_image_url,
                                    )
                                );

                                if ( '' !== $twpgy_inline_video_html ) {
                                    echo wp_kses( $twpgy_inline_video_html, twpgy_allowed_html() );
                                } elseif ( $twpgy_embed_src ) {
                                    ?>
                                    <iframe class="twpgy-inline-video"
                                            src="<?php echo esc_url( $twpgy_embed_src ); ?>"
                                            frameborder="0"
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                            allowfullscreen
                                            loading="<?php echo $twpgy_preload ? 'eager' : 'lazy'; ?>"></iframe>
                                    <?php
                                } else {
                                    echo wp_kses_post( $twpgy_preview_image );
                                }

                                echo '</div>';
                            } else {
                                echo wp_kses_post( $twpgy_preview_image );
                                echo wp_kses_post( $twpgy_lightbox_end );
                            }
                            ?>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
        </div>
        
            <?php
            if (
                count( $attachment_ids ) > 1 &&
                'on' === $enable_main_product_nav 
                
            ) {
                echo wp_kses_post( $slider_nav_btns );
            }

            if (
                count( $attachment_ids ) > 1 &&
                'on' === $enable_main_product_pagination 
                
            ) {
                echo '<div class="twpgy-swiper-pagination"></div>';
            }
            ?>
        
    </div>
<?php } ?>