<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'twpgy_allowed_html' ) ) {
    /**
     * Returns allowed HTML tags and attributes for wp_kses.
     *
     * Covers all HTML used in the product gallery output including
     * SVG elements, Swiper markup, form elements, and data attributes.
     *
     * @since 1.0.0
     *
     * @return array Allowed HTML tags with their attributes.
     */
    function twpgy_allowed_html() {

        $global_attributes = array(
            'class'            => true,
            'id'               => true,
            'style'            => true,
            'title'            => true,
            'role'             => true,
            'tabindex'         => true,
            'aria-label'       => true,
            'aria-live'        => true,
            'aria-atomic'      => true,
            'aria-current'     => true,
            'aria-controls'    => true,
            'aria-disabled'    => true,
            'aria-hidden'      => true,
            'aria-expanded'    => true,
            'aria-selected'    => true,
            'aria-describedby' => true,
            'aria-labelledby'  => true,
            'aria-haspopup'    => true,
            'aria-pressed'     => true,
            'aria-checked'     => true,
            'aria-valuenow'    => true,
            'aria-valuemin'    => true,
            'aria-valuemax'    => true,
            'data-*'           => true,
        );

        $allowed_tags = array(

            // Structural
            'div'      => $global_attributes,
            'span'     => $global_attributes,
            'section'  => $global_attributes,
            'article'  => $global_attributes,
            'aside'    => $global_attributes,
            'header'   => $global_attributes,
            'footer'   => $global_attributes,
            'main'     => $global_attributes,
            'figure'   => $global_attributes,
            'figcaption' => $global_attributes,
            'nav'      => $global_attributes,
            'ul'       => $global_attributes,
            'ol'       => array_merge( $global_attributes, array(
                'start'    => true,
                'reversed' => true,
                'type'     => true,
            )),
            'li'       => array_merge( $global_attributes, array(
                'value' => true,
            )),

            // Headings
            'h1'       => $global_attributes,
            'h2'       => $global_attributes,
            'h3'       => $global_attributes,
            'h4'       => $global_attributes,
            'h5'       => $global_attributes,
            'h6'       => $global_attributes,

            // Text / Inline
            'p'        => $global_attributes,
            'a'        => array_merge( $global_attributes, array(
                'href'     => true,
                'target'   => true,
                'rel'      => true,
                'download' => true,
            )),
            'strong'   => $global_attributes,
            'b'        => $global_attributes,
            'em'       => $global_attributes,
            'i'        => $global_attributes,
            'u'        => $global_attributes,
            's'        => $global_attributes,
            'small'    => $global_attributes,
            'sub'      => $global_attributes,
            'sup'      => $global_attributes,
            'br'       => $global_attributes,
            'hr'       => $global_attributes,
            'abbr'     => $global_attributes,
            'cite'     => $global_attributes,
            'code'     => $global_attributes,
            'pre'      => $global_attributes,
            'mark'     => $global_attributes,
            'del'      => array_merge( $global_attributes, array(
                'datetime' => true,
            )),
            'ins'      => array_merge( $global_attributes, array(
                'datetime' => true,
            )),
            'bdi'      => $global_attributes,
            'bdo'      => array_merge( $global_attributes, array(
                'dir' => true,
            )),
            'time'     => array_merge( $global_attributes, array(
                'datetime' => true,
            )),

            // Images
            'img'      => array_merge( $global_attributes, array(
                'src'           => true,
                'srcset'        => true,
                'sizes'         => true,
                'alt'           => true,
                'width'         => true,
                'height'        => true,
                'loading'       => true,
                'decoding'      => true,
                'fetchpriority' => true,
                'crossorigin'   => true,
                'usemap'        => true,
                'ismap'         => true,
            )),
            'picture'  => $global_attributes,
            'source'   => array_merge( $global_attributes, array(
                'src'    => true,
                'srcset' => true,
                'sizes'  => true,
                'media'  => true,
                'type'   => true,
            )),

            // Video / Audio
            'video'    => array_merge( $global_attributes, array(
                'src'         => true,
                'poster'      => true,
                'width'       => true,
                'height'      => true,
                'autoplay'    => true,
                'controls'    => true,
                'loop'        => true,
                'muted'       => true,
                'playsinline' => true,
                'preload'     => true,
            )),
            'audio'    => array_merge( $global_attributes, array(
                'src'      => true,
                'autoplay' => true,
                'controls' => true,
                'loop'     => true,
                'muted'    => true,
                'preload'  => true,
            )),
            'iframe'   => array_merge( $global_attributes, array(
                'src'             => true,
                'width'           => true,
                'height'          => true,
                'frameborder'     => true,
                'allow'           => true,
                'allowfullscreen' => true,
                'loading'         => true,
                'sandbox'         => true,
                'name'            => true,
            )),

            // Form elements
            'form'     => array_merge( $global_attributes, array(
                'action'  => true,
                'method'  => true,
                'enctype' => true,
                'name'    => true,
                'target'  => true,
                'novalidate' => true,
                'autocomplete' => true,
            )),
            'input'    => array_merge( $global_attributes, array(
                'type'         => true,
                'name'         => true,
                'value'        => true,
                'placeholder'  => true,
                'min'          => true,
                'max'          => true,
                'step'         => true,
                'checked'      => true,
                'disabled'     => true,
                'readonly'     => true,
                'required'     => true,
                'autofocus'    => true,
                'autocomplete' => true,
                'inputmode'    => true,
                'pattern'      => true,
                'size'         => true,
                'maxlength'    => true,
                'minlength'    => true,
                'multiple'     => true,
                'accept'       => true,
                'list'         => true,
                'form'         => true,
            )),
            'button'   => array_merge( $global_attributes, array(
                'type'     => true,
                'name'     => true,
                'value'    => true,
                'disabled' => true,
                'form'     => true,
            )),
            'label'    => array_merge( $global_attributes, array(
                'for' => true,
            )),
            'select'   => array_merge( $global_attributes, array(
                'name'         => true,
                'multiple'     => true,
                'disabled'     => true,
                'required'     => true,
                'size'         => true,
                'autocomplete' => true,
                'form'         => true,
            )),
            'option'   => array_merge( $global_attributes, array(
                'value'    => true,
                'selected' => true,
                'disabled' => true,
                'label'    => true,
            )),
            'optgroup' => array_merge( $global_attributes, array(
                'label'    => true,
                'disabled' => true,
            )),
            'textarea' => array_merge( $global_attributes, array(
                'name'         => true,
                'rows'         => true,
                'cols'         => true,
                'placeholder'  => true,
                'disabled'     => true,
                'readonly'     => true,
                'required'     => true,
                'maxlength'    => true,
                'minlength'    => true,
                'wrap'         => true,
                'autocomplete' => true,
                'form'         => true,
            )),
            'fieldset' => array_merge( $global_attributes, array(
                'disabled' => true,
                'form'     => true,
                'name'     => true,
            )),
            'legend'   => $global_attributes,

            // Table
            'table'    => array_merge( $global_attributes, array(
                'border'      => true,
                'cellpadding' => true,
                'cellspacing' => true,
                'width'       => true,
            )),
            'thead'    => $global_attributes,
            'tbody'    => $global_attributes,
            'tfoot'    => $global_attributes,
            'tr'       => $global_attributes,
            'th'       => array_merge( $global_attributes, array(
                'colspan' => true,
                'rowspan' => true,
                'scope'   => true,
                'abbr'    => true,
                'headers' => true,
            )),
            'td'       => array_merge( $global_attributes, array(
                'colspan' => true,
                'rowspan' => true,
                'headers' => true,
            )),
            'caption'  => $global_attributes,
            'colgroup' => array_merge( $global_attributes, array(
                'span' => true,
            )),
            'col'      => array_merge( $global_attributes, array(
                'span' => true,
            )),

            // SVG
            'svg'      => array_merge( $global_attributes, array(
                'xmlns'            => true,
                'viewBox'          => true,
                'viewbox'          => true,
                'width'            => true,
                'height'           => true,
                'fill'             => true,
                'stroke'           => true,
                'stroke-width'     => true,
                'stroke-linecap'   => true,
                'stroke-linejoin'  => true,
                'enable-background' => true,
                'preserveAspectRatio' => true,
                'x'                => true,
                'y'                => true,
                'opacity'          => true,
                'transform'        => true,
                'clip-path'        => true,
                'clip-rule'        => true,
                'mask'             => true,
                'overflow'         => true,
                'version'          => true,
                'xml:space'        => true,
                'xmlns:xlink'      => true,
                'focusable'        => true,
            )),
            'path'     => array_merge( $global_attributes, array(
                'd'              => true,
                'fill'           => true,
                'fill-rule'      => true,
                'fill-opacity'   => true,
                'stroke'         => true,
                'stroke-width'   => true,
                'stroke-linecap' => true,
                'stroke-linejoin' => true,
                'stroke-dasharray' => true,
                'stroke-dashoffset' => true,
                'stroke-opacity' => true,
                'opacity'        => true,
                'transform'      => true,
                'clip-path'      => true,
                'clip-rule'      => true,
            )),
            'circle'   => array_merge( $global_attributes, array(
                'cx'             => true,
                'cy'             => true,
                'r'              => true,
                'fill'           => true,
                'fill-opacity'   => true,
                'stroke'         => true,
                'stroke-width'   => true,
                'opacity'        => true,
                'transform'      => true,
            )),
            'ellipse'  => array_merge( $global_attributes, array(
                'cx'             => true,
                'cy'             => true,
                'rx'             => true,
                'ry'             => true,
                'fill'           => true,
                'fill-opacity'   => true,
                'stroke'         => true,
                'stroke-width'   => true,
                'opacity'        => true,
                'transform'      => true,
            )),
            'rect'     => array_merge( $global_attributes, array(
                'x'              => true,
                'y'              => true,
                'width'          => true,
                'height'         => true,
                'rx'             => true,
                'ry'             => true,
                'fill'           => true,
                'fill-opacity'   => true,
                'stroke'         => true,
                'stroke-width'   => true,
                'opacity'        => true,
                'transform'      => true,
            )),
            'line'     => array_merge( $global_attributes, array(
                'x1'             => true,
                'y1'             => true,
                'x2'             => true,
                'y2'             => true,
                'stroke'         => true,
                'stroke-width'   => true,
                'stroke-linecap' => true,
                'opacity'        => true,
                'transform'      => true,
            )),
            'polyline' => array_merge( $global_attributes, array(
                'points'         => true,
                'fill'           => true,
                'stroke'         => true,
                'stroke-width'   => true,
                'stroke-linecap' => true,
                'stroke-linejoin' => true,
                'opacity'        => true,
                'transform'      => true,
            )),
            'polygon'  => array_merge( $global_attributes, array(
                'points'         => true,
                'fill'           => true,
                'fill-rule'      => true,
                'stroke'         => true,
                'stroke-width'   => true,
                'opacity'        => true,
                'transform'      => true,
            )),
            'g'        => array_merge( $global_attributes, array(
                'fill'           => true,
                'fill-rule'      => true,
                'stroke'         => true,
                'stroke-width'   => true,
                'opacity'        => true,
                'transform'      => true,
                'clip-path'      => true,
            )),
            'defs'     => $global_attributes,
            'symbol'   => array_merge( $global_attributes, array(
                'viewBox'  => true,
                'viewbox'  => true,
                'width'    => true,
                'height'   => true,
                'fill'     => true,
                'overflow' => true,
            )),
            'use'      => array_merge( $global_attributes, array(
                'href'       => true,
                'xlink:href' => true,
                'x'          => true,
                'y'          => true,
                'width'      => true,
                'height'     => true,
                'fill'       => true,
                'stroke'     => true,
            )),
            'clipPath'  => $global_attributes,
            'linearGradient' => array_merge( $global_attributes, array(
                'x1'                => true,
                'y1'                => true,
                'x2'                => true,
                'y2'                => true,
                'gradientUnits'     => true,
                'gradientTransform' => true,
            )),
            'radialGradient' => array_merge( $global_attributes, array(
                'cx'                => true,
                'cy'                => true,
                'r'                 => true,
                'fx'                => true,
                'fy'                => true,
                'gradientUnits'     => true,
                'gradientTransform' => true,
            )),
            'stop'     => array_merge( $global_attributes, array(
                'offset'     => true,
                'stop-color' => true,
                'stop-opacity' => true,
            )),
            'text'     => array_merge( $global_attributes, array(
                'x'           => true,
                'y'           => true,
                'dx'          => true,
                'dy'          => true,
                'text-anchor' => true,
                'font-size'   => true,
                'font-family' => true,
                'font-weight' => true,
                'fill'        => true,
                'opacity'     => true,
                'transform'   => true,
            )),
            'tspan'    => array_merge( $global_attributes, array(
                'x'           => true,
                'y'           => true,
                'dx'          => true,
                'dy'          => true,
                'fill'        => true,
                'font-size'   => true,
                'font-family' => true,
                'font-weight' => true,
            )),
            'mask'     => array_merge( $global_attributes, array(
                'x'      => true,
                'y'      => true,
                'width'  => true,
                'height' => true,
                'maskUnits' => true,
                'maskContentUnits' => true,
            )),
            'title'    => $global_attributes,
            'desc'     => $global_attributes,
        );

        return apply_filters( 'twpgy_allowed_html', $allowed_tags );
    }
}

if ( ! function_exists( 'twpgy_kses' ) ) {
    /**
     * Sanitize HTML content using the plugin's allowed tags.
     *
     * @since 1.0.0
     *
     * @param string $content HTML content to sanitize.
     * @return string Sanitized HTML.
     */
    function twpgy_kses( $content ) {
        return wp_kses( $content, twpgy_allowed_html() );
    }
}


if ( ! function_exists( 'twpgy_is_360_enabled' ) ) {
    /**
     * Check whether the 360 degree product gallery feature is enabled.
     *
     * Retrieves plugin options and determines if the 360 degree gallery
     * setting is turned on.
     *
     * @since 1.0.0
     *
     * @return bool True if 360 degree gallery is enabled, false otherwise.
     */
    function twpgy_is_360_enabled( $product_id = null ) {

		$product_id = $product_id ?: get_the_ID();
		$options = twpgy_get_display_settings_for_product( $product_id );

        return isset( $options['enable_360degree_gallery'] ) 
            && $options['enable_360degree_gallery'] === 'on';
    }
}


if ( ! function_exists( 'twpgy_get_360_layout' ) ) {

    /**
     * Get the selected layout type for the 360 degree product view.
     *
     * Returns the saved layout option for the 360 degree view. Falls back
     * to the default layout if the option is not set.
     *
     * @since 1.0.0
     *
     * @return string Layout identifier for the 360 degree view.
     */
    function twpgy_get_360_layout( $product_id = null ) {

		$product_id = $product_id ?: get_the_ID();
		$options = twpgy_get_display_settings_for_product( $product_id );

        return $options['view360degree_layout'] ?? 'nav_view';
    }
}


if ( ! function_exists( 'twpgy_can_render_360_view' ) ) {
    /**
     * Determine whether the 360 degree view can be rendered for a product.
     *
     * Checks global plugin settings, validates the product ID, and ensures
     * that at least one 360 degree gallery image is available.
     *
     * @since 1.0.0
     *
     * @param int $product_id Optional. Product ID to check. Defaults to current product ID.
     * @return bool True if the 360 degree view can be rendered, false otherwise.
     */
    function twpgy_can_render_360_view( $product_id = 0 ) {

        if (  ! twpgy_is_360_enabled() ) {
            return false;
        }

        if ( ! $product_id ) {
            $product_id = get_the_ID();
        }

        if ( ! $product_id ) {
            return false;
        }

        if ( ! twpgy_get_360_gallery_attachment_ids( $product_id ) ) {
            return false;
        }

        return true;
    }
}

if ( ! function_exists( 'twpgy_product_360_view_meta' ) ) {

    /**
     * Register the 360 degree product gallery meta box.
     *
     * Adds a sidebar meta box to the WooCommerce product edit screen
     * for managing 360 degree product images.
     *
     * @since 1.0.0
     *
     * @return void
     */
	function twpgy_product_360_view_meta() {

        if (  ! twpgy_is_360_enabled() ) {
            return false;
        }

		add_meta_box( 'woocommerce-product-360-images', esc_html__( 'Product 360 View Gallery (optional)', 'themewant-product-gallery' ), 'twpgy_360_metabox_output', 'product', 'side', 'low' );
	}

	add_action( 'add_meta_boxes', 'twpgy_product_360_view_meta', 50 );
}


if ( ! function_exists( 'twpgy_360_metabox_output' ) ) {

	/**
	 * Output the 360 degree product gallery meta box.
	 *
	 * Displays and manages the 360 degree image gallery for a product
	 * in the WooCommerce product edit screen.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $post Current post object.
	 * @return void
	 */
	function twpgy_360_metabox_output( $post ) {

		if ( ! twpgy_is_360_enabled() ) {
			return;
		}

		wp_nonce_field( 'twpgy_save_360_gallery', 'twpgy_360_gallery_nonce' );
		?>
		<div id="eapgy-product360-images-container" class="eapgy-product360-images-container">
			<ul class="eapgy-product360-images">
				<?php
				$product_image_gallery = twpgy_get_360_gallery_meta( $post->ID );

				$attachments       = array_filter( explode( ',', $product_image_gallery ) );
				$update_meta         = false;
				$updated_gallery_ids = array();

				if ( ! empty( $attachments ) ) {
					foreach ( $attachments as $attachment_id ) {

						$attachment = wp_get_attachment_image(
							$attachment_id,
							'thumbnail'
						);

						if ( empty( $attachment ) ) {
							$update_meta = true;
							continue;
						}

						echo '<li class="image" data-attachment_id="' . esc_attr( $attachment_id ) . '">';
						echo wp_kses_post( $attachment );
						echo '<ul class="actions">';
						echo '<li><a href="#" rel="nofollow" class="delete tips" data-tip="' .
							esc_attr__( 'Delete image', 'themewant-product-gallery' ) . '">' .
							esc_html__( 'Delete', 'themewant-product-gallery' ) . '</a></li>';
						echo '</ul>';
						echo '</li>';

						$updated_gallery_ids[] = $attachment_id;
					}

					if ( $update_meta ) {
						update_post_meta(
							$post->ID,
							'_twpgy_360_image_gallery',
							implode( ',', $updated_gallery_ids )
						);
					}
				}
				?>
			</ul>

			<input
				type="hidden"
				id="twpgy_360_image_gallery"
				name="twpgy_360_image_gallery"
				value="<?php echo esc_attr( $product_image_gallery ); ?>"
			/>
		</div>

		<p class="eapgy-add-product360-images hide-if-no-js">
			<a
				href="#"
				rel="nofollow"
				data-choose="<?php esc_attr_e( 'Add Images Gallery', 'themewant-product-gallery' ); ?>"
				data-update="<?php esc_attr_e( 'Add to gallery', 'themewant-product-gallery' ); ?>"
				data-delete="<?php esc_attr_e( 'Delete image', 'themewant-product-gallery' ); ?>"
				data-text="<?php esc_attr_e( 'Delete', 'themewant-product-gallery' ); ?>"
			>
				<?php esc_html_e( 'Add 360 view gallery images', 'themewant-product-gallery' ); ?>
			</a>
		</p>
		<?php
	}
}


/**
 * Save product 360 gallery meta.
 *
 * @since 1.0.0
 *
 * @param int $post_id Product ID.
 */
function twpgy_save_360_gallery_meta( $post_id ) {

	if ( ! isset( $_POST['twpgy_360_gallery_nonce'] ) ||
         ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['twpgy_360_gallery_nonce'] ) ), 'twpgy_save_360_gallery' ) ) {
        return;
    }

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( wp_is_post_revision( $post_id ) ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( ! isset( $_POST['twpgy_360_image_gallery'] ) ) {
		return;
	}

	$gallery_ids = array_filter(
		array_map(
			'absint',
			explode( ',', sanitize_text_field( wp_unslash( $_POST['twpgy_360_image_gallery'] ) ) )
		)
	);

    $gallery = implode( ',', $gallery_ids );

    update_post_meta( $post_id, '_twpgy_360_image_gallery', $gallery );
    delete_post_meta( $post_id, '_product_360_image_gallery' );

}

add_action( 'save_post_product', 'twpgy_save_360_gallery_meta' );

/**
 * Get the raw 360 gallery meta value (comma separated attachment IDs).
 *
 * Falls back to the pre-1.1.2 unprefixed meta key so existing
 * galleries keep working until the product is saved again.
 *
 * @since 1.1.2
 *
 * @param int $product_id Product ID.
 * @return string
 */
function twpgy_get_360_gallery_meta( $product_id ) {

	$product_id = absint( $product_id );

	if ( metadata_exists( 'post', $product_id, '_twpgy_360_image_gallery' ) ) {
		return (string) get_post_meta( $product_id, '_twpgy_360_image_gallery', true );
	}

	return (string) get_post_meta( $product_id, '_product_360_image_gallery', true );
}


if ( ! function_exists( 'twpgy_get_360_gallery_attachment_ids' ) ) {
    /**
     * Get product 360 gallery attachment IDs.
     *
     * Returns only valid image attachment IDs.
     *
     * @since 1.0.0
     *
     * @param int $product_id Optional. Product ID. Default 0.
     * @return array Array of image attachment IDs.
     */
    function twpgy_get_360_gallery_attachment_ids( $product_id = 0 ) {

        $product_id = absint( $product_id );

        if ( ! $product_id ) {
            $product_id = absint( get_the_ID() );
        }

        if ( ! $product_id ) {
            return array();
        }

        $product_image_gallery = twpgy_get_360_gallery_meta( $product_id );

        if ( empty( $product_image_gallery ) ) {
            return array();
        }

        $attachment_ids = array_map(
            'absint',
            explode( ',', $product_image_gallery )
        );

        $attachment_ids = array_filter(
            $attachment_ids,
            'wp_attachment_is_image'
        );

        return $attachment_ids;
    }
}

if ( ! function_exists( 'twpgy_product_360_button' ) ) {
    function twpgy_product_360_button() {
        $options  = get_option( 'twpgy_woo_gallery_options', array() );
        $settings = isset( $options['settings'] ) ? (array) $options['settings'] : array();

        $pos_class = ! empty( $settings['twpgy_popup_button_position'] )
            ? sanitize_html_class( $settings['twpgy_popup_button_position'] )
            : 'bottom-left';
        ?>
        <div class="twpgy-product-360-button <?php echo esc_attr( $pos_class ); ?>">
            <a href="#" class="twpgy-open-360-popup" rel="nofollow">
                <span class="eapgy-360-icon">
                    <svg fill="#ffffff" height="25px" width="25px" viewBox="0 0 480 480" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
                        <text x="240" y="250" text-anchor="middle" font-family="Arial, Helvetica, sans-serif" font-size="170" font-weight="700">360&#176;</text>
                        <path d="M405 238c38 14 55 32 55 50 0 42-96 76-220 80M75 238c-38 14-55 32-55 50 0 36 70 66 170 77" fill="none" stroke="#ffffff" stroke-width="22" stroke-linecap="round"/>
                        <path d="M178 335l32 30-32 30" fill="none" stroke="#ffffff" stroke-width="22" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                <span class="eapgy-360-tooltip">
                    <?php esc_html_e( '360 product view', 'themewant-product-gallery' ); ?>
                </span>
            </a>
        </div>
        <?php
    }
}

if ( ! function_exists( 'twpgy_product_360_view_footer' ) ) {
    function twpgy_product_360_view_footer() {

        global $product;

        if ( ! $product || ! is_a( $product, 'WC_Product' ) ) {
            return;
        }

        $product_id = absint( $product->get_id() );

        if ( ! twpgy_is_360_enabled( $product_id ) ) {
            return;
        }

        $images = twpgy_get_360_gallery_attachment_ids( $product_id );
        if ( empty( $images ) ) {
            return;
        }

        $unique_id = wp_rand( 100, 999 );

        $image_data = wp_get_attachment_image_src( $images[0], 'full' );
        if ( empty( $image_data ) ) {
            return;
        }

        $args = array(
            'frames_count' => count( $images ),
            'images'       => array_map(
                function ( $id ) {
                    return wp_get_attachment_image_url( $id, 'full' );
                },
                $images
            ),
            'width'  => absint( $image_data[1] ),
            'height' => absint( $image_data[2] ),
        );

        $layout   = twpgy_get_360_layout();
       
        ?>

        <div id="twpgy-product-360-view" class="eapgy-360-popup">
            <div class="twpgy-360-overlay"></div>
            <div class="eapgy-360-popup-content">
                <span class="twpgy-360-close">×</span>

                <div class="twpgy-threed-view eapgy-product-threed-popup eapgy-threed-id-<?php echo esc_attr( $unique_id ); ?>"
                    data-eapgyargs="<?php echo esc_attr( wp_json_encode( $args ) ); ?>">

                    <?php if ( 'nav_view' === $layout ) { ?>
                        <div class="twpgy-nav-bar">
                            <a href="#" class="twpgy-nav-bar-previous"><?php esc_html_e( 'Previous', 'themewant-product-gallery' ); ?></a>
                            <a href="#" class="twpgy-nav-bar-play"><?php esc_html_e( 'Play', 'themewant-product-gallery' ); ?></a>
                            <a href="#" class="twpgy-nav-bar-next"><?php esc_html_e( 'Next', 'themewant-product-gallery' ); ?></a>
                        </div>

                        <ul class="twpgy-threed-view-images"></ul>
                        <div class="twpgy-spinner spinner"><span>0%</span></div>
                    <?php } else { ?>
                        <?php
						do_action(
							'twpgy_pro_360_layout',
							$product_id,
							$unique_id,
							$args
						);
						?>
                    <?php } ?>

                </div>
            </div>
        </div>

        <?php
    }
}

add_action( 'wp_footer', 'twpgy_product_360_view_footer', 10 );



/**
 * Render 360 product view button on product image area.
 *
 * @since 1.0.0
 *
 * @return void
 */
function twpgy_render_product_360_view() {

	if ( ! is_product() ) {
		return;
	}

	global $product;

	if ( ! $product || ! is_a( $product, 'WC_Product' ) ) {
		return;
	}

	$product_id = absint( $product->get_id() );

	if ( ! twpgy_can_render_360_view( $product_id ) ) {
		return;
	}

	$images = twpgy_get_360_gallery_attachment_ids( $product_id );
	
	if ( empty( $images ) ) {
		return;
	}

	twpgy_product_360_button();
}

add_action( 'twpgy_on_product_image', 'twpgy_render_product_360_view', 40 );


/**
 * Get the display settings for a specific product.
 *
 * This function retrieves global gallery settings and overrides them
 * with any product-specific settings from assigned layouts.
 *
 * @since 1.0.0
 *
 * @param int $product_id The product ID.
 * @return array The final gallery settings for the product.
 */
function twpgy_get_display_settings_for_product( $product_id ) {

	$product_id = absint( $product_id );

	// Default settings
	$default_settings = [
		'enable_gallery' => 'off',
	];

	$options          = get_option( 'twpgy_woo_gallery_options', [] );
	$global_settings  = isset( $options['settings'] ) && is_array( $options['settings'] ) ? $options['settings'] : [];
	$final_settings   = wp_parse_args( $global_settings, $default_settings );

	$assign_layouts = get_posts( [
		'post_type'      => 'twpgy_woo_layouts',
		'posts_per_page' => -1,
		'fields'         => 'ids',
		'post_status'    => 'publish',
	] );

	if ( empty( $assign_layouts ) ) {
		return $final_settings;
	}

	foreach ( $assign_layouts as $layout_id ) {
		$assigned_products = get_post_meta( $layout_id, 'twpgy_assigned_products', true );
		$assigned_ids      = is_array( $assigned_products ) ? array_map( 'absint', $assigned_products ) : [];

		if ( in_array( $product_id, $assigned_ids, true ) ) {
			$layout_meta = get_post_meta( $layout_id, 'twpgy_layout_settings', true );

			if ( is_array( $layout_meta ) && isset( $layout_meta['post_meta'] ) && is_array( $layout_meta['post_meta'] ) ) {
				$layout_settings = $layout_meta['post_meta'];

				if ( ! isset( $layout_settings['enable_gallery'] ) || empty( $layout_settings['enable_gallery'] ) ) {
					$layout_settings['enable_gallery'] = 'off';
				}

				return $layout_settings;
			}
		}
	}

	return $final_settings;
}


/**
 * Wrap the content of a core/column block containing post title inside a custom wrapper.
 *
 * This filter wraps the inner content of a Gutenberg column block that contains the
 * post title with a div having class 'eapgy-inner-wrapper-summary'. Only applies
 * on single product pages in the frontend.
 *
 * @since 1.0.0
 *
 * @param string $block_content The HTML content of the block.
 * @param array  $block         The block parsed array from render_block.
 * @return string Modified block content with wrapper.
 */
function twpgy_wrap_inside_wp_block_column_dom( $block_content, $block ) {

	
	if ( is_admin() || ! is_singular( 'product' ) ) {
		return $block_content;
	}

	if ( empty( $block['blockName'] ) || $block['blockName'] !== 'core/column' ) {
		return $block_content;
	}

	if ( strpos( $block_content, 'wp-block-post-title' ) === false ) {
		return $block_content;
	}

	// Wrap the column's inner content without re-parsing or filtering it,
	// so markup from other blocks (forms, iframes, scripts) stays intact.
	if ( ! preg_match( '/^\s*<div\b[^>]*\bclass="[^"]*wp-block-column[^"]*"[^>]*>/i', $block_content, $match ) ) {
		return $block_content;
	}

	$open_end    = strlen( $match[0] );
	$close_start = strrpos( $block_content, '</div>' );

	if ( false === $close_start || $close_start < $open_end ) {
		return $block_content;
	}

	return substr( $block_content, 0, $open_end )
		. '<div class="eapgy-inner-wrapper-summary">'
		. substr( $block_content, $open_end, $close_start - $open_end )
		. '</div>'
		. substr( $block_content, $close_start );
}

add_filter( 'render_block', 'twpgy_wrap_inside_wp_block_column_dom', 10, 2 );



/**
 * Open a wrapper div for the classic product summary on single product pages.
 *
 * This div can be used to wrap the summary content for styling or layout purposes.
 *
 * @since 1.0.0
 */
function twpgy_open_classic_summary_wrapper() {
	echo '<div class="eapgy-inner-wrapper-summary">';
}

add_action( 'woocommerce_single_product_summary', 'twpgy_open_classic_summary_wrapper', 1 );


/**
 * Close the wrapper div for the classic product summary on single product pages.
 *
 * @since 1.0.0
 */
function twpgy_close_classic_summary_wrapper() {
	echo '</div>';
}

add_action( 'woocommerce_single_product_summary', 'twpgy_close_classic_summary_wrapper', 100 );



/**
 * Open the main wrapper for the product gallery on single product pages.
 *
 * Adds a wrapper div around the gallery, with layout-specific classes applied.
 * Skips output for block-based themes.
 *
 * @since 1.0.0
 */
function twpgy_open_gallery_wrapper() {

	$product_id = get_the_ID();

	$plugin_options = twpgy_get_display_settings_for_product( $product_id );
	$gallery_layout = $plugin_options['gallery_layout'] ?? '';

	/**
	 * Filters the CSS class added to the gallery main wrapper.
	 *
	 * @since 1.1.2
	 *
	 * @param string $class          Default empty.
	 * @param string $gallery_layout Selected gallery layout.
	 */
	$twpgy_layout_class = (string) apply_filters( 'twpgy_main_wrapper_class', '', $gallery_layout );

	if ( function_exists( 'wp_is_block_theme' ) && wp_is_block_theme() ) {
		return;
	}

	echo '<div class="twpgy-woo-gallery-main-wrapper ' . esc_attr( $twpgy_layout_class ) . '">';
}

add_action( 'woocommerce_before_single_product_summary', 'twpgy_open_gallery_wrapper', 5 );


/**
 * Close the main wrapper for the product gallery on single product pages.
 *
 * Closes the div opened by twpgy_open_gallery_wrapper().
 * Skips output for block-based themes.
 *
 * @since 1.0.0
 */
function twpgy_close_gallery_wrapper() {

	if ( function_exists( 'wp_is_block_theme' ) && wp_is_block_theme() ) {
		return;
	}

	echo '</div>';
}

add_action( 'woocommerce_after_single_product_summary', 'twpgy_close_gallery_wrapper', 5 );

/**
 * Keep the plugin's own admin screens free of other plugins' notices.
 *
 * Runs late on `in_admin_header`, which fires just before WordPress prints
 * notices. Only this plugin's notices, registered on the
 * `twpgy_admin_notices` action, are shown on these screens.
 *
 * @since 1.1.2
 */
add_action( 'in_admin_header', 'twpgy_disable_admin_notices_on_gallery_page', PHP_INT_MAX );

function twpgy_disable_admin_notices_on_gallery_page() {

    $page   = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    $screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

    $is_gallery_screen = 0 === strpos( $page, 'twpgy-' )
        || ( $screen && 'twpgy_woo_layouts' === $screen->post_type );

    if ( ! $is_gallery_screen ) {
        return;
    }

    remove_all_actions( 'admin_notices' );
    remove_all_actions( 'all_admin_notices' );
    remove_all_actions( 'user_admin_notices' );
    remove_all_actions( 'network_admin_notices' );

    add_action( 'admin_notices', function () {
        /**
         * Fires in place of `admin_notices` on the plugin's own admin screens.
         *
         * @since 1.1.2
         */
        do_action( 'twpgy_admin_notices' );
    } );
}


/**
 * Get all registered image sizes with dimensions
 *
 * @param bool $include_intermediate Include default WP sizes
 * @param bool $include_pro Include "Pro" custom option
 * @return array
 */
function twpgy_get_image_sizes_options( $include_intermediate = true, $include_full = true, $include_custom = true ) {

    $twpgy_additional_image_sizes = wp_get_additional_image_sizes();

    $sizes = array();

    if ( $include_full ) {
        $sizes['full'] = __( 'Full Size (Original Image)', 'themewant-product-gallery' );
    }

    if ( $include_intermediate ) {

        $default_sizes = array( 'thumbnail', 'medium', 'medium_large', 'large' );

        foreach ( $default_sizes as $size ) {

            $width  = get_option( "{$size}_size_w" );
            $height = get_option( "{$size}_size_h" );
            $crop   = get_option( "{$size}_crop" );

            if ( $width && $height ) {
                $sizes[ $size ] = ucfirst($size) . ' - ' . ( $crop ? __( 'hard', 'themewant-product-gallery' ) : __( 'soft', 'themewant-product-gallery' ) ) . ": {$width}x{$height}";
            }
        }
    }

    if ( ! empty( $twpgy_additional_image_sizes ) ) {

        foreach ( $twpgy_additional_image_sizes as $size => $data ) {

            $width  = $data['width'];
            $height = $data['height'];
            $crop   = $data['crop'];

            $sizes[ $size ] = ucfirst(str_replace('_',' ',$size)) . ' - ' . ( $crop ? __( 'hard', 'themewant-product-gallery' ) : __( 'soft', 'themewant-product-gallery' ) ) . ": {$width}x{$height}";
        }
    }

    if ( $include_custom ) {
        $sizes['custom'] = __( 'Custom Size', 'themewant-product-gallery' );
    }

    return $sizes;
}


function twpgy_get_image_sizes_options_small( $include_intermediate = true, $include_full = true, $include_custom = true ) {
	$twpgy_additional_image_sizes = wp_get_additional_image_sizes();

    $sizes = array();

    if ( $include_full ) {
        $sizes['full'] = __( 'Full Size (Original Image)', 'themewant-product-gallery' );
    }

    if ( $include_intermediate ) {

        $default_sizes = array( 'thumbnail', 'medium', 'medium_large', 'large' );

        foreach ( $default_sizes as $size ) {

            $width  = get_option( "{$size}_size_w" );
            $height = get_option( "{$size}_size_h" );
            $crop   = get_option( "{$size}_crop" );

            if ( $width && $height ) {
                $sizes[ $size ] = ucfirst($size) . ' - ' . ( $crop ? __( 'hard', 'themewant-product-gallery' ) : __( 'soft', 'themewant-product-gallery' ) ) . ": {$width}x{$height}";
            }
        }
    }

    if ( ! empty( $twpgy_additional_image_sizes ) ) {

        foreach ( $twpgy_additional_image_sizes as $size => $data ) {

            $width  = $data['width'];
            $height = $data['height'];
            $crop   = $data['crop'];

            $sizes[ $size ] = ucfirst(str_replace('_',' ',$size)) . ' - ' . ( $crop ? __( 'hard', 'themewant-product-gallery' ) : __( 'soft', 'themewant-product-gallery' ) ) . ": {$width}x{$height}";
        }
    }

    return $sizes;
}
if ( ! function_exists( 'twpgy_is_valid_template_path' ) ) {

    /**
     * Check that a filtered template path is a readable PHP file inside the plugins directory.
     *
     * @since 1.1.2
     *
     * @param mixed $path Template path returned by a filter.
     *
     * @return bool True if the path is safe to include.
     */
    function twpgy_is_valid_template_path( $path ) {

        if ( ! is_string( $path ) || '' === $path || '.php' !== strtolower( substr( $path, -4 ) ) ) {
            return false;
        }

        $real_path   = realpath( $path );
        $plugins_dir = realpath( WP_PLUGIN_DIR );

        if ( ! $real_path || ! $plugins_dir || ! is_readable( $real_path ) ) {
            return false;
        }

        $real_path   = wp_normalize_path( $real_path );
        $plugins_dir = trailingslashit( wp_normalize_path( $plugins_dir ) );

        return 0 === strpos( $real_path, $plugins_dir );
    }
}
