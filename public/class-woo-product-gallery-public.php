<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class TWPGY_Gallery_Public {

    /**
     * Singleton instance
     *
     * @var TWPGY_Gallery_Public|null
     */
    private static $instance = null;

    /**
     * Get instance
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        $this->load_dependencies();
    }

    /**
     * Load required files
     */
    public function load_dependencies() {

        require_once TWPGY_GALLERY_PL_PATH . 'public/includes/class-woo-plugin-scripts.php';
        require_once TWPGY_GALLERY_PL_PATH . 'public/includes/class-woo-dynamic-css.php';
        require_once TWPGY_GALLERY_PL_PATH . 'public/includes/class-woo-gallery-view.php';
        require_once TWPGY_GALLERY_PL_PATH . 'public/includes/class-woocommerce-hooks.php';

        TWPGY_Dynamic_CSS::get_instance();
        TWPGY_Enqueue_Scripts::get_instance();
        TWPGY_Woo_Gallery_View::get_instance();
    }
}

TWPGY_Gallery_Public::get_instance();