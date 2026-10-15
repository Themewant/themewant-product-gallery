<?php 
/**
 * Admin Class for handling Custom Post Type and Product Assignment.
 *
 * This class registers the 'twpgy_woo_layouts' post type and manages the 
 * association between gallery layouts and WooCommerce products via AJAX and Modals.
 *
 * @package Themewant_Product_Gallery
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class TWPGY_Gallery_Posttype {

    /**
     * Plugin options
     *
     * @var array
     * @since 1.0.0
     */
	private $twpgy_options = array();

	/**
     * Singleton instance of the class.
     *
     * @var self|null
     * @since 1.0.0
     */
	private static $instance = null;

    /**
     * Get the singleton instance.
     *
     * @since 1.0.0
     * @return self
     */
    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor.
     * * Initializes admin hooks, registers CPT, and sets up AJAX handlers.
     * * @since 1.0.0
     */
    private function __construct(){   

        if ( ! is_admin() ) {
			return;
		}

        add_action('init', array( $this,'twpgy_register_twpgy_woo_layouts_cpt') );

        add_filter(
            'post_row_actions',
            [ $this, 'twpgy_add_assign_products_action' ],
            10,
            2
        );

        add_action( 'admin_footer', [ $this, 'twpgy_assign_products_modal' ] );

        add_action(
            'wp_ajax_twpgy_save_assigned_products',
            [ $this, 'twpgy_save_assigned_products' ]
        );

        add_action(
            'wp_ajax_twpgy_get_assigned_products',
            [ $this, 'twpgy_get_assigned_products' ]
        );

        add_filter( 'manage_twpgy_woo_layouts_posts_columns', array( $this, 'twpgy_layout_post_column' ) );
        add_action( 'manage_twpgy_woo_layouts_posts_custom_column', array( $this, 'twpgy_get_assign_product_columns' ),10, 2 );
    }

    /**
     * Register 'twpgy_woo_layouts' Custom Post Type.
     * * @since 1.0.0
     * @return void
     */
    public function twpgy_register_twpgy_woo_layouts_cpt() {
        $labels = array(
            'name'               => _x('Layouts', 'post type general name', 'themewant-product-gallery'),
            'singular_name'      => _x('Layout', 'post type singular name', 'themewant-product-gallery'),
            'menu_name'          => _x('Layouts', 'admin menu', 'themewant-product-gallery'),
            'name_admin_bar'     => _x('Layout', 'add new on admin bar', 'themewant-product-gallery'),
            'add_new'            => _x('Add New', 'layout', 'themewant-product-gallery'),
            'add_new_item'       => __('Add New Layout', 'themewant-product-gallery'),
            'new_item'           => __('New Layout', 'themewant-product-gallery'),
            'edit_item'          => __('Edit Layout', 'themewant-product-gallery'),
            'view_item'          => __('View Layout', 'themewant-product-gallery'),
            'all_items'          => __('All Layouts', 'themewant-product-gallery'),
            'search_items'       => __('Search Layouts', 'themewant-product-gallery'),
            'parent_item_colon'  => __('Parent Layouts:', 'themewant-product-gallery'),
            'not_found'          => __('No layouts found.', 'themewant-product-gallery'),
            'not_found_in_trash' => __('No layouts found in Trash.', 'themewant-product-gallery')
        );

        $args = array(
            'labels'             => $labels,
            'public'             => false,
            'show_ui'            => true,
            'show_in_menu'       => false, 
            'capability_type'    => 'post',
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => array('title', 'editor', 'thumbnail', 'revisions')
        );

        register_post_type( 'twpgy_woo_layouts', $args );
    }

    /**
     * Add settings metabox for the layout post type.
     * * @since 1.0.0
     * @return void
     */
    public function twpgy_add_layout_settings_metabox() {
        add_meta_box(
            'twpgy_layout_settings',
            __( 'Gallery Settings', 'themewant-product-gallery' ),
            'twpgy_render_layout_settings_metabox',
            'twpgy_woo_layouts',
            'normal',
            'high'
        );
    }

    /**
     * Add 'Assigned Products' column to the layout list table.
     * * @since 1.0.0
     * @param array $columns Existing columns.
     * @return array Modified columns.
     */
    public function twpgy_layout_post_column( $columns ) {
        $new_columns = [];

        foreach ( $columns as $key => $value ) {
           
            if ( $key === 'date' ) {
                $new_columns['assigned_products'] = __('Assigned Products', 'themewant-product-gallery');
            }
            $new_columns[$key] = $value;
        }

        return $new_columns;
    }

    /**
     * Render content for the 'Assigned Products' admin column.
     * * @since 1.0.0
     * @param string $column  Column name.
     * @param int    $post_id Post ID.
     * @return void
     */
    public function twpgy_get_assign_product_columns( $column, $post_id ) {
        if ( $column === 'assigned_products' ) {
            $product_ids = get_post_meta( $post_id, 'twpgy_assigned_products', true );

            if ( ! is_array( $product_ids ) || empty( $product_ids ) ) {
                echo '-';
                return;
            }

            $links = array_map( function( $id ) {
                $product = get_post( $id );
                if ( ! $product ) {
                    return '';
                }

                $frontend_url = get_permalink( $id );
                $title_link   = '<a href="' . esc_url( $frontend_url ) . '" target="_blank">' . esc_html( $product->post_title ) . '</a> || ';

                $edit_url = get_edit_post_link( $id );
                $edit_link = '<a href="' . esc_url( $edit_url ) . '" target="_blank" style="margin-left:5px;font-size:11px;">' . esc_html__( '(Edit Product)', 'themewant-product-gallery' ) . '</a>';

                return $title_link . ' ' . $edit_link;
            }, $product_ids );

            //echo implode( '<br>', array_filter( $links ) );
            echo wp_kses_post( implode( '<br>', array_filter( $links ) ) );
        }
    }

    /**
     * Add custom row action for assigning products.
     * * @since 1.0.0
     * @param array   $actions Post actions.
     * @param WP_Post $post    Post object.
     * @return array  Modified actions.
     */
    public function twpgy_add_assign_products_action( $actions, $post ) {

        if ( $post->post_type !== 'twpgy_woo_layouts' ) {
            return $actions;
        }

        $actions['assign_products'] =
            '<a href="#" 
                class="eapgy-assign-products"
                data-layout-id="' . esc_attr( $post->ID ) . '">
                ' . esc_html__( 'Assign Products', 'themewant-product-gallery' ) . '
            </a>';

        return $actions;
    }

    /**
     * AJAX handler to save assigned products for a layout.
     * * @since 1.0.0
     * @return void
     */
    public function twpgy_save_assigned_products() {

        check_ajax_referer( 'twpgy_assign_products_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( 'You do not have permission to do this.', 'themewant-product-gallery' ), 403 );
        }

        $layout_id = isset( $_POST['layout_id'] ) ? absint( wp_unslash( $_POST['layout_id'] ) ) : 0;

        if ( ! $layout_id || 'twpgy_woo_layouts' !== get_post_type( $layout_id ) ) {
            wp_send_json_error( __( 'Invalid layout ID.', 'themewant-product-gallery' ) );
        }

        $products = map_deep( (array) wp_unslash( $_POST['products'] ?? [] ), 'absint' );

       $all_layouts = get_posts(array(
            'post_type'      => 'twpgy_woo_layouts',
            'posts_per_page' => -1,
            'fields'         => 'ids',
        ));

        $other_layouts = array_diff( $all_layouts, array( $layout_id ) );

        $already_assigned = [];
        foreach ( $other_layouts as $other_id ) {
            $assigned = get_post_meta( $other_id, 'twpgy_assigned_products', true );
            if ( is_array( $assigned ) ) {
                $already_assigned = array_merge( $already_assigned, $assigned );
            }
        }

        $products = array_diff( $products, $already_assigned );

        if ( empty( $products ) ) {
            wp_send_json_error( __( 'Selected products are already assigned to other layouts.', 'themewant-product-gallery' ) );
        }

        update_post_meta( $layout_id, 'twpgy_assigned_products', $products );

        wp_send_json_success( __( 'Products saved successfully.', 'themewant-product-gallery' ) );
    }

    /**
     * AJAX handler to retrieve assigned product IDs for a specific layout.
     * * @since 1.0.0
     * @return void
     */
    public function twpgy_get_assigned_products() {

        check_ajax_referer( 'twpgy_assign_products_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( 'You do not have permission to do this.', 'themewant-product-gallery' ), 403 );
        }

        $layout_id = isset( $_POST['layout_id'] ) ? absint( wp_unslash( $_POST['layout_id'] ) ) : 0;

        if ( ! $layout_id || 'twpgy_woo_layouts' !== get_post_type( $layout_id ) ) {
            wp_send_json_error( __( 'Invalid layout ID.', 'themewant-product-gallery' ) );
        }
        
        $assigned_products = get_post_meta( $layout_id, 'twpgy_assigned_products', true );

        if ( !is_array($assigned_products ) ) {
            $assigned_products = [];
        }

        wp_send_json( $assigned_products );
    }

    /**
     * Render the product assignment modal and overlay in the admin footer.
     * * @since 1.0.0
     * @param int $gallery_id Optional gallery ID.
     * @return void
     */
    public function twpgy_assign_products_modal( $gallery_id = 0 ) {

        $screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
        if ( ! $screen || 'edit-twpgy_woo_layouts' !== $screen->id ) {
            return;
        }

        $assigned_products = get_post_meta( $gallery_id, '_twpgy_assigned_products', true );
        if ( ! is_array( $assigned_products ) ) {
            $assigned_products = [];
        }
        ?>
        <div id="eapgy-assign-products-modal-overlay" class="eapgy-assign-products-modal-overlay" style="display:none;"></div>
        <div id="eapgy-assign-products-modal" class="eapgy-assign-products-modal" style="display:none;">
            <div class="eapgy-assign-product-message"></div>
            <span class="eapgy-close-modal">&times;</span>
            <h2><?php esc_html_e( 'Assign Products', 'themewant-product-gallery' ); ?></h2>
            <select id="eapgy-products-select" class="eapgy-products-select" multiple>
                <?php
                $products = get_posts( array(
                    'post_type'      => 'product',
                    'posts_per_page' => -1,
                ) );

                foreach ( $products as $product ) {
                    $selected = in_array( $product->ID, $assigned_products, true ) ? 'selected' : '';
                    echo '<option value="' . esc_attr( $product->ID ) . '" ' . esc_attr( $selected ) . '>' .
                        esc_html( $product->post_title ) .
                        '</option>';
                }
                ?>
            </select>

            <button class="button button-primary eapgy-save-assigned-products" id="eapgy-save-assigned-products">
                <?php esc_html_e( 'Save', 'themewant-product-gallery' ); ?>
            </button>
        </div>
        <?php
    }
}

TWPGY_Gallery_Posttype::get_instance();