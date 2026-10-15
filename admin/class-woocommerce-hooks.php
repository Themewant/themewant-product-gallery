<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

if ( ! class_exists( 'TWPGY_Woo_Hooks_Admin' ) ) {

    /**
     * Class TWPGY_Woo_Hooks_Admin
     *
     * Handles WooCommerce admin hooks and integration for Easy Woo Gallery.
     * Includes custom media fields for video URLs, gallery integration for product variations,
     * and transient clearing for product variation updates.
     *
     * @package Themewant_Product_Gallery
     * @since 1.0.0
     */
    class TWPGY_Woo_Hooks_Admin  {

        /**
         * Singleton instance
         *
         * @var self|null
         * @since 1.0.0
         */
        private static $_instance = null;

        /**
         * Get singleton instance
         *
         * Ensures only one instance of the class exists.
         *
         * @since 1.0.0
         *
         * @return self
         */
        public static function get_instance() {
            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self();
            }

            return self::$_instance;
        }

        /**
         * Constructor
         *
         * Hooks all WooCommerce admin actions and filters required for gallery functionality.
         *
         * @since 1.0.0
         */
        public function __construct() {

            $twpgy_options = get_option( 'twpgy_woo_gallery_options', [] );
            $twpgy_options = $twpgy_options['settings'] ?? [];

            $auto_integration_location = $twpgy_options['auto_integration_location'] ?? '';

            // Filters
            add_filter( 'edit_attachment', [ $this, 'twpgy_add_media_custom_field_save' ] );
            add_filter( 'attachment_fields_to_edit', [ $this, 'twpgy_add_media_custom_field' ], 99, 2 );

            // Actions
            add_action( 'woocommerce_variation_options', [ $this, 'twpgy_woocommerce_add_gallery_product_variation' ], 10, 3 );
            add_action( 'woocommerce_save_product_variation', [ $this, 'twpgy_woocommerce_save_gallery_product_variation' ], 10, 2 );
        }

        
        /**
         * Save custom media field for video URL.
         *
         * @since 1.0.0
         *
         * @param int $attachment_id The attachment (media) ID.
         */
        public function twpgy_add_media_custom_field_save( $attachment_id ) {

            if ( ! current_user_can( 'edit_post', $attachment_id ) ) {
                return;
            }

            if ( ! isset( $_REQUEST['attachments'][ $attachment_id ]['twpgy_woglry_nonce'] )
                || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['attachments'][ $attachment_id ]['twpgy_woglry_nonce'] ) ), 'twpgy_woo_gallery_nonce' ) ) {
                return;
            }

            if ( isset( $_REQUEST['attachments'][ $attachment_id ]['twpgy_eswoglry_video'] ) ) {
                $video = esc_url_raw( wp_unslash( $_REQUEST['attachments'][ $attachment_id ]['twpgy_eswoglry_video'] ) );
                update_post_meta( $attachment_id, 'twpgy_eswoglry_video', $video );
            }
        }

        /**
         * Add custom media field for video link in the attachment edit screen.
         *
         * @since 1.0.0
         *
         * @param array   $form_fields Existing form fields.
         * @param WP_Post $post        Attachment post object.
         *
         * @return array Modified form fields including the video link field.
         */
        public function twpgy_add_media_custom_field( $form_fields, $post ) {

            $twpgy_eswoglry_video = get_post_meta( $post->ID, 'twpgy_eswoglry_video', true );

            $form_fields['twpgy_eswoglry_video'] = array(
                'value' => $twpgy_eswoglry_video ? $twpgy_eswoglry_video : '',
                'label' => esc_html__( 'Video Link', 'themewant-product-gallery' ),
                'input' => 'text',
                /**
                 * Filters the help text shown under the attachment "Video Link" field.
                 *
                 * @since 1.1.2
                 *
                 * @param string $help Help text.
                 */
                'helps' => esc_html( apply_filters( 'twpgy_video_link_help', __( 'Add a YouTube video link.', 'themewant-product-gallery' ) ) ),
            );

            $form_fields['twpgy_woglry_nonce'] = array(
                'value' => wp_create_nonce( 'twpgy_woo_gallery_nonce' ),
                'input' => 'hidden',
            );

            return $form_fields;
        }

        /**
         * Reduce processor usage by yielding array values one by one.
         *
         * Uses a generator to avoid loading large arrays into memory at once.
         *
         * @since 1.0.0
         *
         * @param array $array The input array to iterate over.
         *
         * @return Generator Yields each value from the array.
         */
        public function twpgy_reduce_processor_use( $array ) {
            $array_length = count( $array );
            for ( $i = 0; $i < $array_length; $i++ ) {
                yield $array[ $i ];
            }
        }

        /**
         * Add gallery fields to WooCommerce product variation.
         *
         * @since 1.0.0
         *
         * @param int     $loop           Variation loop index.
         * @param array   $variation_data Variation data.
         * @param WP_Post $variation      Variation post object.
         */
        public function twpgy_woocommerce_add_gallery_product_variation( $loop, $variation_data, $variation ) {

            $twpgy_gallery_options       = get_option( 'twpgy_woo_gallery_options' );
            $twpgy_gallery_options       = isset( $twpgy_gallery_options['settings'] ) ? $twpgy_gallery_options['settings'] : [];
            $gallery_for_variation  = isset( $twpgy_gallery_options['gallery_for_variation'] ) && $twpgy_gallery_options['gallery_for_variation'] === 'on' ? 'on' : 'off';

            if ( $gallery_for_variation === 'off' ) {
                return;
            }
            ?>

            <div class="twpgy-variation-gallery form-row form-row-full">
                <h4><?php esc_html_e( 'Image Gallery', 'themewant-product-gallery' ); ?></h4>
                <div class="twpgy-easy-gallery-items twpgy-easy-gallery-items<?php echo esc_attr( $variation->ID ); ?>" id="<?php echo esc_attr( $variation->ID ); ?>">

                <?php

                $variation_gallery     = get_post_meta( $variation->ID, 'twpgy_woo_gallery_slider', true );
                $variation_gallery_arr = substr( $variation_gallery, 1, -1 );

                $yield_image_ids = [];
                if ( ! empty( $variation_gallery_arr ) ) {

                    $image_ids       = explode( ',', $variation_gallery_arr );
                   
                    $yield_image_ids = iterator_to_array(
                        $this->twpgy_reduce_processor_use( $image_ids ),
                        false
                    );
                    $count           = 1;

                    foreach ( $yield_image_ids as $image_id ) {

                        $video_url       = get_post_meta( $image_id, 'twpgy_eswoglry_video', true );

                        if ( $count <= 3 ) :
                            ?>
                            <div class="twpgy-easy-image twpgy-easy-edit-gallery <?php echo $video_url ? 'eapgy-woglry-video' : ''; ?>" data-attachmentid="<?php echo esc_attr( $image_id ); ?>" data-variation-id="<?php echo esc_attr( $variation->ID ); ?>">
                                <?php echo wp_kses_post( wp_get_attachment_image( $image_id ) ); ?>
                                <div class="twpgy-easy-image-remover"><span class="dashicons dashicons-no"></span></div>

                                <?php if ( $video_url ) : ?>
                                    <div class="twpgy-gallery-video-icons"><i class="dashicons dashicons-video-alt3"></i></div>
                                <?php endif; ?>
                            </div>
                            <?php
                        endif;

                        ++$count;
                    }
                }

                $variation_gallery_attr    = empty( $variation_gallery_arr ) ? 'hidden' : '';
                $variation_upload_img_attr = ! empty( $variation_gallery_arr ) ? 'hidden' : '';
               
                ?>

                <div class="twpgy-easy-image twpgy-woglry-upload-btn-wrapper">

                    <?php if ( count( $yield_image_ids ) > 0 ) { ?>
                        <button type="button" class="twpgy-woglry-upload-btn twpgy-easy-upload-more-image button twpgywoglry-upload-more-image <?php echo esc_attr( $variation_gallery_attr ); ?>" data-variation-id="<?php echo esc_attr( $variation->ID ); ?>">
                            <span class="dashicons dashicons-plus-alt"></span><?php esc_html_e( 'Add More', 'themewant-product-gallery' ); ?>
                        </button>
                    <?php } else { ?>
                        <button type="button" class="twpgy-woglry-upload-btn twpgywoglry-upload-image button <?php echo esc_attr( $variation_upload_img_attr ); ?>" id="<?php echo 'eswoglry-upload-' . esc_attr( $variation->ID ); ?>" data-variation-id="<?php echo esc_attr( $variation->ID ); ?>">
                            <span class="dashicons dashicons-plus-alt"></span><?php esc_html_e( 'Add Gallery', 'themewant-product-gallery' ); ?>
                        </button>
                    <?php } ?>

                </div>
            </div>

            <p>
                <button type="button" class="twpgy-easy-edit button button-primary <?php echo esc_attr( $variation_gallery_attr ); ?>">
                    <?php esc_html_e( 'Edit Gallery', 'themewant-product-gallery' ); ?>
                </button>
                <button type="button" class="twpgy-easy-remove-all-images button <?php echo esc_attr( $variation_gallery_attr ); ?>">
                    <?php esc_html_e( 'Remove all', 'themewant-product-gallery' ); ?>
                </button>
            </p>

            <div class="hidden">
                <?php
                woocommerce_wp_text_input(
                    array(
                        'id'    => 'twpgy_woo_gallery_slider[' . $loop . ']',
                        'class' => 'twpgy-easy-gallery',
                        'label' => '',
                        'value' => get_post_meta( $variation->ID, 'twpgy_woo_gallery_slider', true ),
                    )
                );
                ?>
            </div>
            <?php
        }

        /**
         * Save gallery product variation.
         *
         * @since 1.0.0
         *
         * @param int $variation_id The variation ID.
         * @param int $index        The index of the variation.
         *
         * @return void
         */
        public function twpgy_woocommerce_save_gallery_product_variation( $variation_id, $index ) {

            if ( ! current_user_can( 'edit_post', $variation_id ) ) {
                return;
            }

            check_ajax_referer( 'save-variations', 'security' );

            if ( isset( $_POST['twpgy_woo_gallery_slider'][ $index ] ) ) {
                $custom_field = map_deep(
                    wp_unslash( $_POST['twpgy_woo_gallery_slider'][ $index ] ),
                    'sanitize_text_field'
                );
                update_post_meta( $variation_id, 'twpgy_woo_gallery_slider', $custom_field );
            }
        }
    }

    // Initialize
    TWPGY_Woo_Hooks_Admin::get_instance();
}