<?php
/**
 * Plugin Name: UXD Elementor Extension
 * Description: Custom Elementor widgets for WooCommerce product carousel and grid with advanced customization options. Gallery widget is free, other widgets require a license.
 * Version: 5.2.1
 * Author: UX Design Experts
 * Author URI: https://uxdesignexperts.com/
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Requires Plugins: woocommerce, elementor
 * Text Domain: uxd-elementor-extension
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.3
 * Requires PHP: 7.4
 * WC requires at least: 4.0
 * WC tested up to: 8.0
 * Elementor tested up to: 3.15
 * Elementor Pro tested up to: 3.15
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Define plugin constants
define('UXD_EE_PLUGIN_URL', plugin_dir_url(__FILE__));
define('UXD_EE_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('UXD_EE_VERSION', '5.2.1');
define('UXD_EE_MINIMUM_ELEMENTOR_VERSION', '3.0.0');
define('UXD_EE_MINIMUM_PHP_VERSION', '7.4');
define('UXD_EE_PLUGIN_FILE', __FILE__);
define('UXD_EE_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Initialize Freemius SDK
 */
if (!function_exists('uxd_ee_fs')) {
    function uxd_ee_fs() {
        global $uxd_ee_fs;

        if (!isset($uxd_ee_fs)) {
            require_once dirname(__FILE__) . '/includes/freemius-config.php';
            $uxd_ee_fs = uxd_ee_freemius();
        }

        return $uxd_ee_fs;
    }

    uxd_ee_fs();
    do_action('uxd_ee_fs_loaded');
}

/**
 * Main Plugin Class
 */
final class UXD_Elementor_Extension {
    
    private static $instance = null;
    private $fs;
    
    /**
     * Get plugin instance.
     */
    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor.
     */
    public function __construct() {
        $this->fs = uxd_ee_fs();
        
        add_action('before_woocommerce_init', [$this, 'declare_woocommerce_compatibility']);
        add_action('plugins_loaded', [$this, 'on_plugins_loaded']);
        
        $this->load_includes();
    }
    
    /**
     * Load plugin includes
     */
    private function load_includes() {
        // Core includes
        require_once UXD_EE_PLUGIN_PATH . 'includes/class-uxd-helper.php';
        require_once UXD_EE_PLUGIN_PATH . 'includes/class-uxd-assets.php';
        
        // Wishlist functionality
        require_once UXD_EE_PLUGIN_PATH . 'includes/wishlist/class-uxd-wishlist.php';
        
        // Admin functionality
        require_once UXD_EE_PLUGIN_PATH . 'includes/admin/class-uxd-admin.php';
        
        // Initialize components
        new UXD_Assets();
        new UXD_Wishlist();
        new UXD_Admin();
    }
    
    /**
     * Get Freemius instance
     */
    public function get_freemius() {
        return $this->fs;
    }
    
    /**
     * Check if premium features are available
     */
    public function is_premium_available() {
        return uxd_ee_can_use_plugin();
    }
    
    /**
     * Load Textdomain
     */
    public function i18n() {
        load_plugin_textdomain('uxd-elementor-extension', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    }
    
    /**
     * On Plugins Loaded
     */
    public function on_plugins_loaded() {
        
        if (!did_action('elementor/loaded')) {
            add_action('admin_notices', [$this, 'admin_notice_missing_main_plugin']);
            return;
        }
        
        if (!version_compare(ELEMENTOR_VERSION, UXD_EE_MINIMUM_ELEMENTOR_VERSION, '>=')) {
            add_action('admin_notices', [$this, 'admin_notice_minimum_elementor_version']);
            return;
        }
        
        if (version_compare(PHP_VERSION, UXD_EE_MINIMUM_PHP_VERSION, '<')) {
            add_action('admin_notices', [$this, 'admin_notice_minimum_php_version']);
            return;
        }
        
        // WooCommerce notice only for premium widgets
        if (!class_exists('WooCommerce') && $this->is_premium_available()) {
            add_action('admin_notices', [$this, 'admin_notice_missing_woocommerce']);
        }
        
        $this->init();
    }
    
    /**
     * Initialize the plugin
     */
    public function init() {
        add_action('init', [$this, 'i18n']);
        add_action('elementor/widgets/register', [$this, 'register_widgets']);
        add_action('elementor/elements/categories_registered', [$this, 'add_elementor_widget_categories']);
        add_filter('plugin_action_links_' . UXD_EE_PLUGIN_BASENAME, [$this, 'plugin_action_links']);
    }
    
    /**
     * Declare WooCommerce compatibility
     */
    public function declare_woocommerce_compatibility() {
        if (class_exists(\Automattic\WooCommerce\Utilities\FeaturesUtil::class)) {
            \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, true);
            \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('cart_checkout_blocks', __FILE__, true);
        }
    }
    
    /**
     * Add Elementor widget categories
     */
    public function add_elementor_widget_categories($elements_manager) {
        // Free category (Gallery widget)
        $elements_manager->add_category(
            'uxd-free',
            [
                'title' => esc_html__('UXD Free', 'uxd-elementor-extension'),
                'icon' => 'fa fa-gift',
            ]
        );
        
        // Premium categories (only if licensed)
        if ($this->is_premium_available()) {
            $elements_manager->add_category(
                'uxd-woocommerce',
                [
                    'title' => esc_html__('UXD WooCommerce Pro', 'uxd-elementor-extension'),
                    'icon' => 'fa fa-shopping-cart',
                ]
            );

            $elements_manager->add_category(
                'uxd-general',
                [
                    'title' => esc_html__('UXD General Pro', 'uxd-elementor-extension'),
                    'icon' => 'fa fa-plug',
                ]
            );
            
            $elements_manager->add_category(
                'uxd-advanced',
                [
                    'title' => esc_html__('UXD Advanced Pro', 'uxd-elementor-extension'),
                    'icon' => 'fa fa-star',
                ]
            );
        }
    }
    
    /**
     * Register widgets
     */
    public function register_widgets($widgets_manager) {
        
        // ALWAYS register FREE widget (Gallery Grid)
        if (file_exists(UXD_EE_PLUGIN_PATH . 'widgets/gallery-grid.php')) {
            require_once UXD_EE_PLUGIN_PATH . 'widgets/gallery-grid.php';
            $widgets_manager->register(new \UXD_Gallery_Grid_Widget());
        }
        
        // Only register PREMIUM widgets if licensed
        if ($this->is_premium_available()) {
            $this->register_premium_widgets($widgets_manager);
        } else {
            // Register premium widgets as locked/demo versions
            $this->register_locked_widgets($widgets_manager);
        }
    }
    
    /**
     * Register premium widgets (full functionality)
     */
    private function register_premium_widgets($widgets_manager) {
        $premium_widgets = [
            'product-carousel.php' => 'UXD_Product_Carousel_Widget',
            'product-grid.php' => 'UXD_Product_Grid_Widget',
            'taxonomy-accordion.php' => 'UXD_Taxonomy_Accordion_Widget',
            'post-grid.php' => 'UXD_Post_Widget',
        ];
        
        foreach ($premium_widgets as $file => $class) {
            if (file_exists(UXD_EE_PLUGIN_PATH . 'widgets/' . $file)) {
                require_once UXD_EE_PLUGIN_PATH . 'widgets/' . $file;
                if (class_exists($class)) {
                    $widgets_manager->register(new $class());
                }
            }
        }
        
        // Advanced premium widgets
        $this->register_advanced_widgets($widgets_manager);
    }
    
    /**
     * Register advanced premium widgets
     */
    private function register_advanced_widgets($widgets_manager) {
        $advanced_widgets_path = UXD_EE_PLUGIN_PATH . 'widgets/premium/';
        
        $advanced_widgets = [
            'advanced-product-filter.php' => 'UXD_Advanced_Product_Filter_Widget',
            'custom-query-builder.php' => 'UXD_Custom_Query_Builder_Widget',
            'mega-menu.php' => 'UXD_Mega_Menu_Widget',
        ];
        
        foreach ($advanced_widgets as $file => $class) {
            if (file_exists($advanced_widgets_path . $file)) {
                require_once $advanced_widgets_path . $file;
                if (class_exists($class)) {
                    $widgets_manager->register(new $class());
                }
            }
        }
    }
    
    /**
     * Register locked widgets (preview/upgrade prompts)
     */
    private function register_locked_widgets($widgets_manager) {
        // Load locked widget base class
        require_once UXD_EE_PLUGIN_PATH . 'includes/class-uxd-locked-widget.php';
        
        $locked_widgets = [
            'product-carousel' => [
                'title' => 'Product Carousel Pro',
                'icon' => 'eicon-media-carousel',
                'description' => 'Beautiful product carousel with advanced customization options.'
            ],
            'product-grid' => [
                'title' => 'Product Grid Pro', 
                'icon' => 'eicon-products',
                'description' => 'Responsive product grid with filtering and sorting.'
            ],
            'taxonomy-accordion' => [
                'title' => 'Taxonomy Accordion Pro',
                'icon' => 'eicon-accordion',
                'description' => 'Collapsible taxonomy display with smooth animations.'
            ],
            'post-grid' => [
                'title' => 'Post Grid Pro',
                'icon' => 'eicon-posts-grid', 
                'description' => 'Flexible post grid with multiple layout options.'
            ],
        ];
        
        foreach ($locked_widgets as $slug => $widget_data) {
            $locked_widget = new UXD_Locked_Widget($slug, $widget_data);
            $widgets_manager->register($locked_widget);
        }
    }
    
    /**
     * Plugin action links
     */
    public function plugin_action_links($links) {
        $settings_link = '<a href="' . admin_url('admin.php?page=uxd-elementor-extension') . '">' . esc_html__('Settings', 'uxd-elementor-extension') . '</a>';
        array_unshift($links, $settings_link);
        
        if (!$this->is_premium_available()) {
            $upgrade_link = '<a href="' . esc_url(uxd_ee_get_pricing_url()) . '" style="color: #39b54a; font-weight: bold;" target="_blank">' . esc_html__('Upgrade to Pro', 'uxd-elementor-extension') . '</a>';
            array_unshift($links, $upgrade_link);
        }
        
        return $links;
    }
    
    /**
     * Admin notice - Missing main plugin
     */
    public function admin_notice_missing_main_plugin() {
        if (isset($_GET['activate'])) unset($_GET['activate']);
        
        $message = sprintf(
            esc_html__('"%1$s" requires "%2$s" to be installed and activated.', 'uxd-elementor-extension'),
            '<strong>' . esc_html__('UXD Elementor Extension', 'uxd-elementor-extension') . '</strong>',
            '<strong>' . esc_html__('Elementor', 'uxd-elementor-extension') . '</strong>'
        );
        
        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }
    
    /**
     * Admin notice - Minimum Elementor version
     */
    public function admin_notice_minimum_elementor_version() {
        if (isset($_GET['activate'])) unset($_GET['activate']);
        
        $message = sprintf(
            esc_html__('"%1$s" requires "%2$s" version %3$s or greater.', 'uxd-elementor-extension'),
            '<strong>' . esc_html__('UXD Elementor Extension', 'uxd-elementor-extension') . '</strong>',
            '<strong>' . esc_html__('Elementor', 'uxd-elementor-extension') . '</strong>',
            UXD_EE_MINIMUM_ELEMENTOR_VERSION
        );
        
        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }
    
    /**
     * Admin notice - Minimum PHP version
     */
    public function admin_notice_minimum_php_version() {
        if (isset($_GET['activate'])) unset($_GET['activate']);
        
        $message = sprintf(
            esc_html__('"%1$s" requires "%2$s" version %3$s or greater.', 'uxd-elementor-extension'),
            '<strong>' . esc_html__('UXD Elementor Extension', 'uxd-elementor-extension') . '</strong>',
            '<strong>' . esc_html__('PHP', 'uxd-elementor-extension') . '</strong>',
            UXD_EE_MINIMUM_PHP_VERSION
        );
        
        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }
    
    /**
     * Admin notice - Missing WooCommerce
     */
    public function admin_notice_missing_woocommerce() {
        if (isset($_GET['activate'])) unset($_GET['activate']);
        
        $message = sprintf(
            esc_html__('"%1$s" requires "%2$s" for WooCommerce widgets to work properly.', 'uxd-elementor-extension'),
            '<strong>' . esc_html__('UXD Elementor Extension Pro', 'uxd-elementor-extension') . '</strong>',
            '<strong>' . esc_html__('WooCommerce', 'uxd-elementor-extension') . '</strong>'
        );
        
        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }
    
    /**
     * Plugin activation hook
     */
    public static function activate() {
        if (!get_option('uxd_ee_settings')) {
            update_option('uxd_ee_settings', [
                'enable_analytics' => true,
                'cache_timeout' => 3600,
                'default_theme' => 'default',
            ]);
        }
        
        $upload_dir = wp_upload_dir();
        $plugin_upload_dir = $upload_dir['basedir'] . '/uxd-elementor-extension';
        
        if (!file_exists($plugin_upload_dir)) {
            wp_mkdir_p($plugin_upload_dir);
        }
        
        update_option('uxd_ee_activation_redirect', true);
        wp_cache_flush();
    }
    
    /**
     * Plugin deactivation hook
     */
    public static function deactivate() {
        wp_clear_scheduled_hook('uxd_ee_cleanup_temp_data');
        wp_clear_scheduled_hook('uxd_ee_sync_analytics');
        delete_transient('uxd_ee_widget_cache');
        delete_transient('uxd_ee_system_status');
        wp_cache_flush();
    }
    
    /**
     * Handle activation redirect
     */
    public function activation_redirect() {
        if (get_option('uxd_ee_activation_redirect', false)) {
            delete_option('uxd_ee_activation_redirect');
            
            if (!isset($_GET['activate-multi'])) {
                wp_safe_redirect(admin_url('admin.php?page=uxd-elementor-extension'));
                exit;
            }
        }
    }
    
    /**
     * Register activation and deactivation hooks
     */
    public function register_hooks() {
        register_activation_hook(UXD_EE_PLUGIN_FILE, [__CLASS__, 'activate']);
        register_deactivation_hook(UXD_EE_PLUGIN_FILE, [__CLASS__, 'deactivate']);
        add_action('admin_init', [$this, 'activation_redirect']);
    }
}

/**
 * Returns the main instance of UXD_Elementor_Extension.
 */
function UXD_Elementor_Extension() {
    return UXD_Elementor_Extension::instance();
}

// Initialize the plugin
$uxd_extension = UXD_Elementor_Extension();
$uxd_extension->register_hooks();