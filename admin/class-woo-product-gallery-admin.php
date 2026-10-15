<?php 
/**
 * Admin Class for Easy Product Gallery
 *
 * Handles all admin-side functionality including menu registration, 
 * settings page rendering, metaboxes, AJAX handlers, and script/style enqueueing.
 *
 * @package Themewant_Product_Gallery
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once TWPGY_GALLERY_PL_PATH . 'admin/class-woo-gallery-setting-fields.php';
require_once TWPGY_GALLERY_PL_PATH . 'admin/class-woocommerce-hooks.php';
require_once TWPGY_GALLERY_PL_PATH . 'admin/settings/posttype/easy-post-type.php';

/**
 * Class TWPGY_Gallery_Admin
 *
 * Handles the admin interface for Easy Woo Gallery including:
 * - Admin menu pages and subpages
 * - Settings UI rendering
 * - Meta boxes for gallery layouts
 * - AJAX requests for assigning products to layouts
 *
 * Implements Singleton pattern.
 */
class TWPGY_Gallery_Admin {

    /**
	 * Plugin options
	 *
	 * @var array
	 * @since 1.0.0
	 */
	private $twpgy_options = array();

	/**
	 * Singleton instance
	 *
	 * @var self|null
	 * @since 1.0.0
	 */
	private static $instance = null;

    /**
	 * Get singleton instance
	 *
	 * @since 1.0.0
	 *
	 * @return self
	 */
    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
	 * Constructor
	 *
	 * Hooks into WordPress admin actions for menu, scripts, meta boxes, and AJAX handlers.
	 *
	 * @since 1.0.0
	 */
    public function __construct(){   

        if ( ! is_admin() ) {
			return;
		}

        add_action( 'admin_menu', [ $this, 'twpgy_woo_gallery_menu_register'] );
        add_action('admin_enqueue_scripts', [ $this, 'twpgy_enqueue_scripts'] );

        add_action( 'add_meta_boxes', array( $this, 'twpgy_add_layout_settings_metabox' ) );
        add_action( 'save_post_twpgy_woo_layouts', [ $this, 'twpgy_save_layout_settings' ] );
    }

    /**
     * Register admin menu and submenus for Easy Product Gallery.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function twpgy_woo_gallery_menu_register() {

        add_menu_page(
            __( 'Easy Product Gallery', 'themewant-product-gallery' ),
            __( 'Easy Product Gallery', 'themewant-product-gallery' ),
            'manage_options',
            'twpgy-woo-gallery',
            array( $this, 'twpgy_woo_gallery_plugin_page' ),
            'dashicons-admin-generic',
            59
        );

        add_submenu_page(
            'twpgy-woo-gallery',
            __( 'Gallery Layouts', 'themewant-product-gallery' ),
            __( 'Gallery Layouts', 'themewant-product-gallery' ),
            'manage_options',
            'edit.php?post_type=twpgy_woo_layouts'
            // no callback needed because WP handles the CPT page
        );

        add_submenu_page(
            'twpgy-woo-gallery',
            __( 'Upgrade to Pro', 'themewant-product-gallery' ),
            __( 'Upgrade to Pro', 'themewant-product-gallery' ),
            'manage_options',
            'twpgy-overview',
            array( $this, 'twpgy_overview_page' )
        );


    }

    /**
     * Render the Overview / Free vs Pro comparison page.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function twpgy_overview_page() {

        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $is_pro = apply_filters( 'twpgy_is_pro_plugin', false );

        $pro_url = 'https://themewant.com/plugins/demo/easygallery/pricing/';

        $check_icon = '<span class="twpgy-ov-icon twpgy-ov-icon--check dashicons dashicons-yes-alt" aria-hidden="true"></span>';
        $cross_icon = '<span class="twpgy-ov-icon twpgy-ov-icon--cross dashicons dashicons-dismiss" aria-hidden="true"></span>';

        // Pro-only features (shown at top, highlighted)
        $pro_only_features = array(
            __( 'Modern Grid One / Two / Three / Four', 'themewant-product-gallery' ),
            __( 'Layout vertical scroll', 'themewant-product-gallery' ),
            __( 'Number and Progressbar styles', 'themewant-product-gallery' ),
            __( 'Zoom Window, Zoom Lens, Zoom Window Two', 'themewant-product-gallery' ),
            __( 'Lens shape, color, and border customization', 'themewant-product-gallery' ),
            __( 'Zoom window size and distance settings', 'themewant-product-gallery' ),
            __( 'Vimeo, Dailymotion, Facebook video', 'themewant-product-gallery' ),
            __( 'Self-hosted video (MP4 / WebM)', 'themewant-product-gallery' ),
            __( 'Custom aspect ratio and player controls', 'themewant-product-gallery' ),
            __( '360° autoplay and speed configuration', 'themewant-product-gallery' ),
            __( 'Button position (all four corners)', 'themewant-product-gallery' ),
            __( 'Drag to rotate interaction', 'themewant-product-gallery' ),
        );

        // Visual previews of Pro layouts and pagination styles (marketing on
        // the dedicated Upgrade page — not in the functional settings UI).
        $pro_layout_previews = array(
            array(
                'label' => __( 'Carousel 1 Column', 'themewant-product-gallery' ),
                'image' => TWPGY_GALLERY_DIR_URL . 'admin/assets/img/carousel-one-column.png',
            ),
            array(
                'label' => __( 'Modern Grid One', 'themewant-product-gallery' ),
                'image' => TWPGY_GALLERY_DIR_URL . 'admin/assets/img/grid-style-one.png',
            ),
            array(
                'label' => __( 'Modern Grid Two', 'themewant-product-gallery' ),
                'image' => TWPGY_GALLERY_DIR_URL . 'admin/assets/img/modern-grid-two.png',
            ),
            array(
                'label' => __( 'Modern Grid Three', 'themewant-product-gallery' ),
                'image' => TWPGY_GALLERY_DIR_URL . 'admin/assets/img/grid-one-column.png',
            ),
            array(
                'label' => __( 'Modern Grid Four', 'themewant-product-gallery' ),
                'image' => TWPGY_GALLERY_DIR_URL . 'admin/assets/img/grid-style-two.png',
            ),
        );

        $pro_pagination_previews = array(
            array(
                'label' => __( 'Number Style', 'themewant-product-gallery' ),
                'image' => TWPGY_GALLERY_DIR_URL . 'admin/assets/img/dot_style_2.png',
            ),
            array(
                'label' => __( 'Progressbar Style', 'themewant-product-gallery' ),
                'image' => TWPGY_GALLERY_DIR_URL . 'admin/assets/img/progressbar_style.png',
            ),
        );

        // Features available in both Free and Pro
        $free_and_pro_features = array(
            __( 'Documentation', 'themewant-product-gallery' ),
            __( 'Product Updates', 'themewant-product-gallery' ),
            __( 'Thumbnails — bottom, top, left, right', 'themewant-product-gallery' ),
            __( 'Thumbnail carousel two-column', 'themewant-product-gallery' ),
            __( 'Thumbnail vertical scroll', 'themewant-product-gallery' ),
            __( 'Dot, Dynamic Bullet and Fraction styles', 'themewant-product-gallery' ),
            __( 'Slide, Fade, Cube, Flip, Coverflow, Creative effects', 'themewant-product-gallery' ),
            __( 'Standard zoom', 'themewant-product-gallery' ),
            __( 'YouTube video support', 'themewant-product-gallery' ),
            __( 'Inline and Popup playback modes', 'themewant-product-gallery' ),
            __( '360° popup view (basic)', 'themewant-product-gallery' ),
            __( 'Full-screen lightbox popup', 'themewant-product-gallery' ),
            __( 'Per-variation image gallery', 'themewant-product-gallery' ),
            __( 'Autoplay, infinite loop, mouse wheel control', 'themewant-product-gallery' ),
            __( 'RTL language support', 'themewant-product-gallery' ),
            __( 'Fully responsive and mobile-friendly', 'themewant-product-gallery' ),
        );

        ?>
        <div class="wrap twpgy-admin-wrap twpgy-overview-wrap">

            <div class="twpgy-admin-sticky-header">
                <div class="twpgy-admin-header-brand">
                    <h1><?php esc_html_e( 'Easy Product Gallery — Upgrade to Pro', 'themewant-product-gallery' ); ?></h1>
                </div>
                <div class="twpgy-admin-header-actions">
                    <a href="<?php echo esc_url( $pro_url ); ?>" target="_blank" rel="noopener noreferrer" class="button button-primary twpgy-upgrade-btn">
                        <span class="dashicons dashicons-star-filled"></span>
                        <?php esc_html_e( 'Upgrade to Pro', 'themewant-product-gallery' ); ?>
                    </a>
                </div>
            </div>

            <div class="twpgy-ov-container">

                <?php if ( ! $is_pro ) : ?>
                <div class="twpgy-ov-preview-card">
                    <div class="twpgy-ov-section-label twpgy-ov-section-label--pro">
                        <span class="dashicons dashicons-star-filled"></span>
                        <?php esc_html_e( 'Pro Gallery Layouts', 'themewant-product-gallery' ); ?>
                    </div>
                    <p class="twpgy-ov-preview-desc">
                        <?php esc_html_e( 'These additional gallery layouts are available in the Pro version.', 'themewant-product-gallery' ); ?>
                    </p>
                    <div class="twpgy-ov-preview-grid">
                        <?php foreach ( $pro_layout_previews as $preview ) : ?>
                            <div class="twpgy-ov-preview-item">
                                <div class="twpgy-ov-preview-thumb">
                                    <img src="<?php echo esc_url( $preview['image'] ); ?>" alt="<?php echo esc_attr( $preview['label'] ); ?>">
                                    <span class="twpgy-ov-preview-badge">
                                        <span class="dashicons dashicons-star-filled"></span>
                                        <?php esc_html_e( 'Pro', 'themewant-product-gallery' ); ?>
                                    </span>
                                </div>
                                <div class="twpgy-ov-preview-label"><?php echo esc_html( $preview['label'] ); ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="twpgy-ov-section-label twpgy-ov-section-label--pro">
                        <span class="dashicons dashicons-star-filled"></span>
                        <?php esc_html_e( 'Pro Pagination Styles', 'themewant-product-gallery' ); ?>
                    </div>
                    <p class="twpgy-ov-preview-desc">
                        <?php esc_html_e( 'Advanced pagination designs included with the Pro version.', 'themewant-product-gallery' ); ?>
                    </p>
                    <div class="twpgy-ov-preview-grid">
                        <?php foreach ( $pro_pagination_previews as $preview ) : ?>
                            <div class="twpgy-ov-preview-item">
                                <div class="twpgy-ov-preview-thumb">
                                    <img src="<?php echo esc_url( $preview['image'] ); ?>" alt="<?php echo esc_attr( $preview['label'] ); ?>">
                                    <span class="twpgy-ov-preview-badge">
                                        <span class="dashicons dashicons-star-filled"></span>
                                        <?php esc_html_e( 'Pro', 'themewant-product-gallery' ); ?>
                                    </span>
                                </div>
                                <div class="twpgy-ov-preview-label"><?php echo esc_html( $preview['label'] ); ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div><!-- /.twpgy-ov-preview-card -->
                <?php endif; ?>

                <div class="twpgy-ov-card">

                    <!-- Plan header -->
                    <div class="twpgy-ov-plan-header">
                        <div class="twpgy-ov-plan-header__feature">
                            <?php esc_html_e( 'Features', 'themewant-product-gallery' ); ?>
                        </div>
                        <div class="twpgy-ov-plan-header__col twpgy-ov-plan-header__col--free">
                            <?php esc_html_e( 'Free', 'themewant-product-gallery' ); ?>
                        </div>
                        <div class="twpgy-ov-plan-header__col twpgy-ov-plan-header__col--pro">
                            <span class="twpgy-ov-pro-badge">
                                <span class="dashicons dashicons-star-filled"></span>
                                <?php esc_html_e( 'Pro', 'themewant-product-gallery' ); ?>
                            </span>
                            <?php if ( ! $is_pro ) : ?>
                            <a href="<?php echo esc_url( $pro_url ); ?>" target="_blank" class="twpgy-ov-plan-header__cta">
                                <?php esc_html_e( 'Upgrade', 'themewant-product-gallery' ); ?>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Free + Pro section -->
                    <div class="twpgy-ov-section-label">
                        <?php esc_html_e( 'Available in Free &amp; Pro', 'themewant-product-gallery' ); ?>
                    </div>
                    <?php foreach ( $free_and_pro_features as $feature ) : ?>
                    <div class="twpgy-ov-row">
                        <div class="twpgy-ov-row__feature"><?php echo esc_html( $feature ); ?></div>
                        <div class="twpgy-ov-row__cell"><?php echo $check_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- hardcoded dashicon span ?></div>
                        <div class="twpgy-ov-row__cell twpgy-ov-row__cell--pro"><?php echo $check_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- hardcoded dashicon span ?></div>
                    </div>
                    <?php endforeach; ?>

                    <!-- Pro-only section -->
                    <div class="twpgy-ov-section-label twpgy-ov-section-label--pro">
                        <span class="dashicons dashicons-star-filled"></span>
                        <?php esc_html_e( 'Pro Exclusive Features', 'themewant-product-gallery' ); ?>
                    </div>
                    <?php foreach ( $pro_only_features as $feature ) : ?>
                    <div class="twpgy-ov-row twpgy-ov-row--pro">
                        <div class="twpgy-ov-row__feature"><?php echo esc_html( $feature ); ?></div>
                        <div class="twpgy-ov-row__cell"><?php echo $cross_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- hardcoded dashicon span ?></div>
                        <div class="twpgy-ov-row__cell twpgy-ov-row__cell--pro"><?php echo $check_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- hardcoded dashicon span ?></div>
                    </div>
                    <?php endforeach; ?>

                </div><!-- /.twpgy-ov-card -->

                <?php if ( ! $is_pro ) : ?>
                <div class="twpgy-ov-cta-footer">
                    <p><?php esc_html_e( 'Ready to supercharge your product gallery?', 'themewant-product-gallery' ); ?></p>
                    <a href="<?php echo esc_url( $pro_url ); ?>" target="_blank" class="twpgy-ov-cta-footer__btn">
                        <?php esc_html_e( 'Upgrade to Pro', 'themewant-product-gallery' ); ?>
                    </a>
                </div>
                <?php endif; ?>

            </div>
        </div>
        <?php
    }

    /**
     * Add Gallery Settings meta box to Easy Woo Layouts CPT.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function twpgy_add_layout_settings_metabox() {

        add_meta_box(
            'twpgy_layout_settings',
            __( 'Gallery Settings', 'themewant-product-gallery' ),
            array( $this, 'twpgy_render_layout_settings_metabox' ),
            'twpgy_woo_layouts',
            'normal',
            'high'
        );
    }

    /**
     * Enqueue admin scripts and styles.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function twpgy_enqueue_scripts( $hook_suffix = '' ) {

        if ( ! $this->twpgy_is_plugin_screen( $hook_suffix ) ) {
            return;
        }

        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'wp-color-picker' );

        wp_enqueue_style(
            'twpgy-gallery-admin-style',
            TWPGY_GALLERY_PL_URL . 'admin/assets/css/admin-style.css',
            array(),
            TWPGY_VERSION
        );

        wp_enqueue_style(
            'twpgy-jquery-ui',
            TWPGY_GALLERY_PL_URL . 'admin/assets/css/jquery-ui.css',
            array(),
            TWPGY_VERSION
        );

        wp_enqueue_style(
            'twpgy-select2',
            TWPGY_GALLERY_PL_URL . 'admin/assets/css/select2.min.css',
            array(),
            TWPGY_VERSION
        );

        wp_enqueue_script(
            'twpgy-admin-script',
            TWPGY_GALLERY_PL_URL . 'admin/assets/js/admin.js',
            array( 'jquery', 'wp-color-picker' ),
            TWPGY_VERSION,
            true
        );

        wp_enqueue_script(
            'twpgy-select2',
            TWPGY_GALLERY_PL_URL . 'admin/assets/js/select2.min.js',
            array( 'jquery' ),
            TWPGY_VERSION,
            true
        );

        wp_localize_script(
            'twpgy-admin-script',
            'twpgygalleryObj',
            array(
                'ajaxurl' => admin_url( 'admin-ajax.php' ),
                'nonce' => wp_create_nonce( 'twpgy_woo_gallery_nonce' ),
                'assign_nonce' => wp_create_nonce( 'twpgy_assign_products_nonce' ),
                'i18n' => array(
                    'add_more'      => __( 'Add More', 'themewant-product-gallery' ),
                    'reset_confirm' => __( 'Are you sure you want to reset all settings to defaults? This cannot be undone.', 'themewant-product-gallery' ),
                ),
            ),
        );

        wp_enqueue_script( 'jquery-ui-slider' );
    }

    /**
     * Check whether the current admin screen belongs to this plugin.
     *
     * Covers the plugin's own menu pages, the Gallery Layouts post type
     * and the WooCommerce product edit screen (variation / 360 galleries).
     *
     * @since 1.1.2
     *
     * @param string $hook_suffix Current admin page hook suffix.
     * @return bool
     */
    private function twpgy_is_plugin_screen( $hook_suffix ) {

        if ( false !== strpos( (string) $hook_suffix, 'twpgy-' ) ) {
            return true;
        }

        $screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

        if ( ! $screen ) {
            return false;
        }

        if ( false !== strpos( $screen->id, 'twpgy-' ) ) {
            return true;
        }

        return in_array( $screen->post_type, array( 'twpgy_woo_layouts', 'product' ), true );
    }

    /**
     * Render main plugin settings page.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function twpgy_woo_gallery_plugin_page() {

        if ( ! current_user_can( 'manage_options' ) ) {
           return;
        }

        $notice = '';

        if ( isset( $_POST['twpgy_woo_gallery_nonce_field'] ) ) {
            if ( wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['twpgy_woo_gallery_nonce_field'] ?? '' ) ), 'twpgy_woo_gallery_action' ) ) {

                if ( isset( $_POST['twpgy_reset_settings'] ) && '1' === sanitize_text_field( wp_unslash( $_POST['twpgy_reset_settings'] ) ) ) {
                    delete_option( 'twpgy_woo_gallery_options' );
                    $notice = '<div class="twpgy-toast twpgy-toast-info"><span class="dashicons dashicons-image-rotate"></span>' . esc_html__( 'Settings have been reset to defaults.', 'themewant-product-gallery' ) . '</div>';
                } elseif ( isset( $_POST['twpgy_woo_gallery_options'] ) ) {
                    $twpgy_options = map_deep(
                        wp_unslash( $_POST['twpgy_woo_gallery_options'] ),
                        'sanitize_text_field'
                    );
                    update_option( 'twpgy_woo_gallery_options', $twpgy_options );
                    $notice = '<div class="twpgy-toast twpgy-toast-success"><span class="dashicons dashicons-yes-alt"></span>' . esc_html__( 'Settings saved successfully!', 'themewant-product-gallery' ) . '</div>';
                }
            }
        }


        ?>

        <div class="wrap twpgy-admin-wrap">

            <div class="twpgy-admin-sticky-header">
                <div class="twpgy-admin-header-brand">
                    <h1><?php esc_html_e( 'Easy Product Gallery Settings', 'themewant-product-gallery' ); ?></h1>
                </div>
                <div class="twpgy-admin-header-actions">
                    <button type="button" id="twpgy-reset-btn" class="button twpgy-reset-btn">
                        <?php esc_html_e( 'Reset Default Settings', 'themewant-product-gallery' ); ?>
                    </button>
                    <button type="submit" form="twpgy-woo_gallery-settings" class="button button-primary twpgy-save-btn">
                        <?php esc_html_e( 'Save Changes', 'themewant-product-gallery' ); ?>
                    </button>
                </div>
            </div>

            <div class="twpgy-admin-content-wrap">

                <div class="twpgy-admin-main">
                    <form method="post" action="#" id="twpgy-woo_gallery-settings">
                        <input type="hidden" name="twpgy_reset_settings" id="twpgy_reset_settings" value="0">
                        <?php
                            wp_nonce_field( 'twpgy_woo_gallery_action', 'twpgy_woo_gallery_nonce_field' );
                            $this->twpgy_render_settings_ui( 'settings', false );
                        ?>
                    </form>
                </div>

                <aside class="twpgy-pro-sidebar">
                    <div class="twpgy-quick-access-box">
                        <h4 class="twpgy-quick-access-box__title">
                            <span class="dashicons dashicons-admin-links"></span>
                            <?php esc_html_e( 'Quick Access', 'themewant-product-gallery' ); ?>
                        </h4>
                        <ul class="twpgy-quick-access-box__list">
                            <li>
                                <a href="https://themewant.com/contact/" target="_blank">
                                    <span class="dashicons dashicons-sos"></span>
                                    <?php esc_html_e( 'Help Center', 'themewant-product-gallery' ); ?>
                                </a>
                            </li>
                            <li>
                                <a href="https://documentation.themewant.com/docs/easy-woo-gallery/" target="_blank">
                                    <span class="dashicons dashicons-book"></span>
                                    <?php esc_html_e( 'Documentation', 'themewant-product-gallery' ); ?>
                                </a>
                            </li>
                            <li>
                                <a href="https://wordpress.org/support/plugin/themewant-product-gallery/reviews/" target="_blank">
                                    <span class="dashicons dashicons-star-half"></span>
                                    <?php esc_html_e( 'Rate Us', 'themewant-product-gallery' ); ?>
                                </a>
                            </li>
                        </ul>
                    </div>
                </aside>

            </div><!-- /.twpgy-admin-content-wrap -->

            <?php echo wp_kses_post( $notice ); ?>
        </div>
        <?php
    }

    /**
     * Render settings fields for a given section.
     *
     * @since 1.0.0
     *
     * @param array  $settings_array Array of field definitions.
     * @param string $section_name   Section slug/key.
     *
     * @return void Outputs HTML directly.
     */
    public function twpgy_render_settings_fields( $settings_array, $section_name ) {

        $options_name = 'twpgy_woo_gallery_options';

        if ( $section_name === 'post_meta' ) {
            $twpgy_gallery_options = get_post_meta(
                get_the_ID(),
                'twpgy_layout_settings',
                true
            );

            if ( empty( $twpgy_gallery_options ) ) {
                $twpgy_gallery_options = get_option( 'twpgy_woo_gallery_options' );
            }

        } else {
            // Settings are saved before the page renders, so always read the stored values.
            $twpgy_gallery_options = get_option( $options_name );
        }

        $is_in_sub_section = false; 

        foreach ( $settings_array as $key => $field ) {

            $field_value  = isset( $field['value'] ) ? $field['value'] : '';
            $field_name   = isset( $field['name'] ) ? $field['name'] : '';
            $field_label  = isset( $field['label'] ) ? $field['label'] : '';
            $field_desc   = isset( $field['desc'] ) ? $field['desc'] : '';
            $field_notice = isset( $field['notice'] ) ? $field['notice'] : '';
            $field_type   = isset( $field['type'] ) ? $field['type'] : '';

            $offset_type = $field['offset_type'] ?? '';
            $condition = isset( $field['condition'] ) ? $field['condition'] : '';

            if ( isset( $field['default'] ) ) {
                $field_value = $field['default'];
            }

            $field_name_short = $field_name;
            $field_name       = $options_name . '[' . $section_name . '][' . $field_name . ']';

            
            if ( 'section_start' === $field_type ) {
                include __DIR__ . '/settings/sections/section_start.php';
            } elseif ( 'sub_section_start' === $field_type ) {

                if ( $is_in_sub_section ) {
                    include __DIR__ . '/settings/fields/sub_section_end.php';
                }

                include __DIR__ . '/settings/fields/sub_section_start.php';
                $is_in_sub_section = true;

            } elseif ( 'sub_section_end' === $field_type ) {

                if ( $is_in_sub_section ) {
                    include __DIR__ . '/settings/fields/sub_section_end.php';
                    $is_in_sub_section = false;
                }

            } else {

                $field_value = isset( $twpgy_gallery_options[ $section_name ][ $field_name_short ] )
                    ? $twpgy_gallery_options[ $section_name ][ $field_name_short ]
                    : $field_value;

                $dependency_map = array(
                    'twpgy-lightbox-dependent'        => 'gallery_lightbox',
                    'twpgy-main-nav-dependent'        => 'enable_main_product_nav',
                    'twpgy-main-pagination-dependent' => 'enable_main_product_pagination',
                    'twpgy-thumbs-nav-dependent'      => 'enable_thumbs_nav',
                    'twpgy-zoom-dependent'            => 'gallery_image_zoom',
                    'twpgy-video-dependent'           => 'gallery_video',
                    'twpgy-360degree-dependent'       => 'enable_360degree_gallery',
                );

                $inline_style = '';
                foreach ( $dependency_map as $dep_class => $checkbox_name ) {
                    if ( strpos( $condition, $dep_class ) !== false ) {
                        $checkbox_value = isset( $twpgy_gallery_options[ $section_name ][ $checkbox_name ] )
                            ? $twpgy_gallery_options[ $section_name ][ $checkbox_name ]
                            : '';
                        if ( $checkbox_value !== 'on' ) {
                            $inline_style = 'display:none;';
                        }
                        break;
                    }
                }

                /**
                 * Filters the initial inline style of a settings field row.
                 *
                 * Add-ons use this to hide their conditional fields on load.
                 *
                 * @since 1.1.2
                 *
                 * @param string $inline_style     Inline CSS, e.g. 'display:none;'. Empty to show.
                 * @param string $condition        Field condition classes.
                 * @param array  $section_settings Saved settings for the current section.
                 */
                $inline_style = (string) apply_filters(
                    'twpgy_settings_field_inline_style',
                    $inline_style,
                    $condition,
                    $twpgy_gallery_options[ $section_name ] ?? array()
                );
                ?>
                <div class="twpgy-settings-item <?php echo esc_attr( $condition ); ?>" <?php echo $offset_type ? 'data-offset="' . esc_attr( $offset_type ) . '"' : ''; ?> <?php echo $inline_style ? 'style="' . esc_attr( $inline_style ) . '"' : ''; ?>>
                    <label>
                        <?php 
                            echo esc_html( $field_label ); 
                        ?>
                    </label>
                    <div class="input-fields">
                        <?php
                        // Only load field templates that ship with the plugin.
                        $field_template = __DIR__ . '/settings/fields/' . sanitize_key( $field_type ) . '.php';
                        if ( '' !== $field_type && is_readable( $field_template ) ) {
                            include $field_template;
                        }
                        ?>
                        <?php if ( ! empty( $field_desc ) ) { ?>
                            <p class="desc"><?php echo wp_kses_post( $field_desc ); ?></p>
                        <?php } ?>
                        <?php if ( ! empty( $field_notice ) ) { ?>
                            <div class="notice notice-info"><?php echo wp_kses_post( $field_notice ); ?></div>
                        <?php } ?>
                    </div>
                </div>
                <?php
            }
        }

        if ( $is_in_sub_section ) {
            echo '</div>';
        }
    }

    /**
     * Render settings tabs UI.
     *
     * @since 1.0.0
     *
     * @param string $context Context of settings, e.g., 'settings' or 'post_meta'.
     *
     * @return void
     */
    public function twpgy_render_settings_tabs( $context = 'settings' ) {

        $admin_settings_fields = new TWPGY_Admin_Settings_Fields();
        
        $this->twpgy_render_settings_ui( 'post_meta', false );
       
    }

    /**
     * Render full settings UI with optional submit button.
     *
     * @since 1.0.0
     *
     * @param string  $context        Settings context.
     * @param boolean $submit_button  Whether to show the submit button.
     *
     * @return void
     */
    public function twpgy_render_settings_ui( $context = 'settings', $submit_button = false ) {

        $admin_settings_fields = new TWPGY_Admin_Settings_Fields();
        $nonce = wp_create_nonce( 'twpgy_layout_settings_save' ); 
        $current_tab = 'tab1';
        $twpgy_current_subtab = '';

        if (
            isset(
                $_POST['twpgy_layout_settings_nonce'],
                $_POST['twpgy_current_tab']
            ) &&
            wp_verify_nonce(
                sanitize_text_field(
                    wp_unslash( $_POST['twpgy_layout_settings_nonce'] )
                ),
                'twpgy_layout_settings_save'
            )
        ) {
            $current_tab = sanitize_text_field(
                wp_unslash( $_POST['twpgy_current_tab'] )
            );
        }

        if (
            isset(
                $_POST['twpgy_layout_settings_nonce'],
                $_POST['twpgy_current_subtab']
            ) &&
            wp_verify_nonce(
                sanitize_text_field(
                    wp_unslash( $_POST['twpgy_layout_settings_nonce'] )
                ),
                'twpgy_layout_settings_save'
            )
        ) {
            $twpgy_current_subtab = sanitize_text_field(
                wp_unslash( $_POST['twpgy_current_subtab'] )
            );
        }

        $twpgy_current_slider_subtab = '';
        if (
            isset(
                $_POST['twpgy_layout_settings_nonce'],
                $_POST['twpgy_current_slider_subtab']
            ) &&
            wp_verify_nonce(
                sanitize_text_field(
                    wp_unslash( $_POST['twpgy_layout_settings_nonce'] )
                ),
                'twpgy_layout_settings_save'
            )
        ) {
            $twpgy_current_slider_subtab = sanitize_text_field(
                wp_unslash( $_POST['twpgy_current_slider_subtab'] )
            );
        }

        $twpgy_current_thumb_subtab = '';
        if (
            isset(
                $_POST['twpgy_layout_settings_nonce'],
                $_POST['twpgy_current_thumb_subtab']
            ) &&
            wp_verify_nonce(
                sanitize_text_field(
                    wp_unslash( $_POST['twpgy_layout_settings_nonce'] )
                ),
                'twpgy_layout_settings_save'
            )
        ) {
            $twpgy_current_thumb_subtab = sanitize_text_field(
                wp_unslash( $_POST['twpgy_current_thumb_subtab'] )
            );
        }

        ?>
       
        <div class="tabs twpgy-woo_gallery-settings-tabs twpgy-layout-tabs">
            <input type="hidden" name="twpgy_layout_settings_nonce" value="<?php echo esc_attr( $nonce ); ?>">
            <ul id="twpgy-tabs-nav" class="twpgy-tabs-nav">
                <li class="<?php echo $current_tab === 'tab1' ? 'active' : ''; ?>">
                    <a href="#tab1">
                        <span class="dashicons dashicons-admin-settings"></span>
                        <?php esc_html_e('General', 'themewant-product-gallery'); ?>
                    </a>
                </li>
                <li class="twpgy-has-subtabs <?php echo $current_tab === 'tab2' ? 'active open' : ''; ?>">
                    <a href="#tab2" class="twpgy-tab-parent-link">
                        <div class="twpgy-gallery-parent-tab">
                            <span class="dashicons dashicons-images-alt2"></span>
                            <?php esc_html_e('Slider', 'themewant-product-gallery'); ?>
                        </div>
                        <span class="twpgy-subnav-arrow dashicons dashicons-arrow-down-alt2"></span>
                    </a>
                    <ul class="twpgy-sub-nav" <?php echo $current_tab !== 'tab2' ? 'style="display:none;"' : ''; ?>>
                        <li class="<?php echo ( $twpgy_current_subtab === '' || $twpgy_current_subtab === 'subtab-general' ) ? 'active' : ''; ?>">
                            <a href="#subtab-general"><?php esc_html_e('Main Product Slider', 'themewant-product-gallery'); ?></a>
                        </li>
                        <li class="<?php echo $twpgy_current_subtab === 'subtab-thumbnails' ? 'active' : ''; ?>">
                            <a href="#subtab-thumbnails"><?php esc_html_e('Thumb Slider', 'themewant-product-gallery'); ?></a>
                        </li>
                        <li class="<?php echo $twpgy_current_subtab === 'subtab-productzoom' ? 'active' : ''; ?>">
                            <a href="#subtab-productzoom"><?php esc_html_e('Product Zoom', 'themewant-product-gallery'); ?></a>
                        </li>
                    </ul>
                </li>
                <li class="<?php echo $current_tab === 'tab3' ? 'active' : ''; ?>">
                    <a href="#tab3">
                        <span class="dashicons dashicons-format-image"></span>
                        <?php esc_html_e('Lightbox', 'themewant-product-gallery'); ?>
                    </a>
                </li>
                <li class="<?php echo $current_tab === 'tab4' ? 'active' : ''; ?>">
                    <a href="#tab4">
                        <span class="dashicons dashicons-video-alt3"></span>
                        <?php esc_html_e('Video', 'themewant-product-gallery'); ?>
                    </a>
                </li>
                <li class="<?php echo $current_tab === 'tab5' ? 'active' : ''; ?>">
                    <a href="#tab5">
                        <span class="dashicons dashicons-image-rotate"></span>
                        <?php esc_html_e('360° View', 'themewant-product-gallery'); ?>
                    </a>
                </li>
            </ul>
            <input type="hidden" name="twpgy_current_tab" value="<?php echo esc_attr( $current_tab ); ?>">
            <div class="twpgy-tab-contents-wrapper">

                <div id="tab1" class="twpgy-tab-content" style="<?php echo $current_tab === 'tab1' ? 'display:block;' : 'display:none;'; ?>">
                    <?php
                    $fields = $admin_settings_fields->twpgy_woo_gallery_get_settings_fields();
                    $this->twpgy_render_settings_fields( $fields, $context );
                    ?>
                </div>

                <div id="tab2" class="twpgy-tab-content" style="<?php echo $current_tab === 'tab2' ? 'display:block;' : 'display:none;'; ?>">
                    <input type="hidden" id="twpgy_current_subtab" name="twpgy_current_subtab" value="<?php echo esc_attr( $twpgy_current_subtab ); ?>">

                    <div id="subtab-general" class="twpgy-sub-tab-content" style="<?php echo ( $twpgy_current_subtab === '' || $twpgy_current_subtab === 'subtab-general' ) ? 'display:block;' : 'display:none;'; ?>">
                        <input type="hidden" id="twpgy_current_slider_subtab" name="twpgy_current_slider_subtab" value="<?php echo esc_attr( $twpgy_current_slider_subtab ); ?>">

                        <div class="twpgy-inner-tabs-nav">
                            <a href="#slider-subtab-general" class="twpgy-inner-tab-link <?php echo ( $twpgy_current_slider_subtab === '' || $twpgy_current_slider_subtab === 'slider-subtab-general' ) ? 'active' : ''; ?>">
                                <span class="dashicons dashicons-admin-settings"></span>
                                <?php esc_html_e( 'General', 'themewant-product-gallery' ); ?>
                            </a>
                            <a href="#slider-subtab-navigation" class="twpgy-inner-tab-link <?php echo $twpgy_current_slider_subtab === 'slider-subtab-navigation' ? 'active' : ''; ?>">
                                <span class="dashicons dashicons-arrow-left-alt2"></span>
                                <?php esc_html_e( 'Product Navigation', 'themewant-product-gallery' ); ?>
                            </a>
                            <a href="#slider-subtab-pagination" class="twpgy-inner-tab-link <?php echo $twpgy_current_slider_subtab === 'slider-subtab-pagination' ? 'active' : ''; ?>">
                                <span class="dashicons dashicons-ellipsis"></span>
                                <?php esc_html_e( 'Product Pagination', 'themewant-product-gallery' ); ?>
                            </a>
                        </div>

                        <div id="slider-subtab-general" class="twpgy-inner-tab-content" style="<?php echo ( $twpgy_current_slider_subtab === '' || $twpgy_current_slider_subtab === 'slider-subtab-general' ) ? 'display:block;' : 'display:none;'; ?>">
                            <?php
                            $fields = $admin_settings_fields->twpgy_slider_general_fields();
                            $this->twpgy_render_settings_fields( $fields, $context );
                            ?>
                        </div>

                        <div id="slider-subtab-navigation" class="twpgy-inner-tab-content" style="<?php echo $twpgy_current_slider_subtab === 'slider-subtab-navigation' ? 'display:block;' : 'display:none;'; ?>">
                            <?php
                            $fields = $admin_settings_fields->twpgy_slider_navigation_fields();
                            $this->twpgy_render_settings_fields( $fields, $context );
                            ?>
                        </div>

                        <div id="slider-subtab-pagination" class="twpgy-inner-tab-content" style="<?php echo $twpgy_current_slider_subtab === 'slider-subtab-pagination' ? 'display:block;' : 'display:none;'; ?>">
                            <?php
                            $fields = $admin_settings_fields->twpgy_slider_pagination_fields();
                            $this->twpgy_render_settings_fields( $fields, $context );
                            ?>
                        </div>
                    </div>

                    <div id="subtab-thumbnails" class="twpgy-sub-tab-content" style="<?php echo $twpgy_current_subtab === 'subtab-thumbnails' ? 'display:block;' : 'display:none;'; ?>">
                        <input type="hidden" id="twpgy_current_thumb_subtab" name="twpgy_current_thumb_subtab" value="<?php echo esc_attr( $twpgy_current_thumb_subtab ); ?>">

                        <div class="twpgy-inner-tabs-nav">
                            <a href="#thumb-subtab-general" class="twpgy-inner-tab-link <?php echo ( $twpgy_current_thumb_subtab === '' || $twpgy_current_thumb_subtab === 'thumb-subtab-general' ) ? 'active' : ''; ?>">
                                <span class="dashicons dashicons-admin-settings"></span>
                                <?php esc_html_e( 'General', 'themewant-product-gallery' ); ?>
                            </a>
                            <a href="#thumb-subtab-navigation" class="twpgy-inner-tab-link <?php echo $twpgy_current_thumb_subtab === 'thumb-subtab-navigation' ? 'active' : ''; ?>">
                                <span class="dashicons dashicons-arrow-left-alt2"></span>
                                <?php esc_html_e( 'Thumbnail Navigation', 'themewant-product-gallery' ); ?>
                            </a>
                        </div>

                        <div id="thumb-subtab-general" class="twpgy-inner-tab-content" style="<?php echo ( $twpgy_current_thumb_subtab === '' || $twpgy_current_thumb_subtab === 'thumb-subtab-general' ) ? 'display:block;' : 'display:none;'; ?>">
                            <?php
                            $fields = $admin_settings_fields->twpgy_easy_woo_gallery_thumb_fields();
                            $this->twpgy_render_settings_fields( $fields, $context );
                            ?>
                        </div>

                        <div id="thumb-subtab-navigation" class="twpgy-inner-tab-content" style="<?php echo $twpgy_current_thumb_subtab === 'thumb-subtab-navigation' ? 'display:block;' : 'display:none;'; ?>">
                            <?php
                            $fields = $admin_settings_fields->twpgy_easy_woo_gallery_thumb_nav_fields();
                            $this->twpgy_render_settings_fields( $fields, $context );
                            ?>
                        </div>
                    </div>

                    <div id="subtab-productzoom" class="twpgy-sub-tab-content" style="<?php echo $twpgy_current_subtab === 'subtab-productzoom' ? 'display:block;' : 'display:none;'; ?>">
                        <?php
                        $fields = $admin_settings_fields->twpgy_woo_gallery_zoom_fields();
                        $this->twpgy_render_settings_fields( $fields, $context );
                        ?>
                    </div>
                </div>

                <div id="tab3" class="twpgy-tab-content twpgy-lightbox-settings-tab" style="<?php echo $current_tab === 'tab3' ? 'display:block;' : 'display:none;'; ?>">
                    <?php
                    $fields = $admin_settings_fields->twpgy_woo_gallery_lightbox_fields();
                    $this->twpgy_render_settings_fields( $fields, $context );
                    ?>
                </div>

                <div id="tab4" class="twpgy-tab-content" style="<?php echo $current_tab === 'tab4' ? 'display:block;' : 'display:none;'; ?>">
                    <?php
                    $fields = $admin_settings_fields->twpgy_woo_gallery_get_video_fields();
                    $this->twpgy_render_settings_fields( $fields, $context );
                    ?>
                </div>

                <div id="tab5" class="twpgy-tab-content" style="<?php echo $current_tab === 'tab5' ? 'display:block;' : 'display:none;'; ?>">
                    <?php
                    $fields = $admin_settings_fields->twpgy_woo_gallery_get_360_degree_fields();
                    $this->twpgy_render_settings_fields( $fields, $context );
                    ?>
                </div>

                <?php 
                if( $submit_button ) {
                    submit_button( __( 'Save Settings', 'themewant-product-gallery' ), 'primary', 'twpgy_settings_submit' ); 
                }
                ?>

            </div>
        </div>
        <?php
    }

    /**
     * Render the Gallery Settings meta box content.
     *
     * @since 1.0.0
     *
     * @param WP_Post $post Post object.
     *
     * @return void
     */
    public function twpgy_render_layout_settings_metabox( $post ) {

        wp_nonce_field( 'twpgy_layout_settings_save', 'twpgy_layout_settings_nonce' );

        echo '<div class="twpgy-layout-settings-wrapper">';

        $this->twpgy_render_settings_tabs( 'post_meta' );

        echo '</div>';
    }

    /**
     * Save layout settings for Easy Woo Layouts CPT.
     *
     * @since 1.0.0
     *
     * @param int $post_id Post ID.
     *
     * @return void
     */
    public function twpgy_save_layout_settings( $post_id ) {

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( ! isset( $_POST['twpgy_layout_settings_nonce'] ) || 
            ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['twpgy_layout_settings_nonce'] ) ), 'twpgy_layout_settings_save' ) ) {
            return;
        }

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        if ( ! isset( $_POST['twpgy_woo_gallery_options'] ) ) {
            return;
        }

        $layout_settings = map_deep(
            wp_unslash( $_POST['twpgy_woo_gallery_options'] ),
            'sanitize_text_field'
        );

        update_post_meta( absint( $post_id ), 'twpgy_layout_settings', $layout_settings );

    }
}

// Initialize singleton instance
TWPGY_Gallery_Admin::get_instance();