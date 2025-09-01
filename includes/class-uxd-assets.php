<?php
/**
 * UXD Assets Management Class
 *
 * @package UXD_Elementor_Extension
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Assets management class
 */
class UXD_Assets {

    /**
     * Constructor
     */
    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend_scripts']);
        add_action('elementor/frontend/after_enqueue_styles', [$this, 'enqueue_frontend_styles']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
        add_action('elementor/editor/before_enqueue_scripts', [$this, 'enqueue_editor_scripts']);
    }

    /**
     * Enqueue frontend scripts
     */
    public function enqueue_frontend_scripts() {
        // Only enqueue on pages with Elementor content
        if (!$this->should_enqueue_frontend_assets()) {
            return;
        }

        // Enqueue Swiper JS (for carousels)
        wp_enqueue_script(
            'uxd-swiper-js',
            'https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js',
            [],
            '8.0.0',
            true
        );
        
        // Enqueue main plugin JS
        wp_enqueue_script(
            'uxd-elementor-extension-js',
            UXD_EE_PLUGIN_URL . 'assets/js/uxd-elementor-extension.js',
            ['jquery', 'uxd-swiper-js'],
            UXD_EE_VERSION,
            true
        );
        
        // Localize script for AJAX
        wp_localize_script(
            'uxd-elementor-extension-js',
            'uxd_ajax_object',
            [
                'ajax_url'            => admin_url('admin-ajax.php'),
                'nonce'              => wp_create_nonce('uxd_nonce'),
                'add_to_cart_text'   => esc_html__('Add to Cart', 'uxd-elementor-extension'),
                'added_to_cart_text' => esc_html__('Added to Cart', 'uxd-elementor-extension'),
                'loading_text'       => esc_html__('Loading...', 'uxd-elementor-extension'),
                'is_licensed'        => function_exists('uxd_ee_can_use_plugin') ? uxd_ee_can_use_plugin() : false,
                'upgrade_url'        => UXD_Helper::get_upgrade_url(),
                'i18n'               => [
                    'taxonomy_accordion' => [
                        'loading' => esc_html__('Loading terms...', 'uxd-elementor-extension'),
                        'error'   => esc_html__('Error loading terms', 'uxd-elementor-extension'),
                        'empty'   => esc_html__('No terms found', 'uxd-elementor-extension'),
                    ],
                    'gallery' => [
                        'loading'        => esc_html__('Loading image...', 'uxd-elementor-extension'),
                        'no_results'     => esc_html__('No images found', 'uxd-elementor-extension'),
                        'search_placeholder' => esc_html__('Search images...', 'uxd-elementor-extension'),
                    ],
                    'wishlist' => [
                        'added'    => esc_html__('Added to wishlist!', 'uxd-elementor-extension'),
                        'removed'  => esc_html__('Removed from wishlist!', 'uxd-elementor-extension'),
                        'error'    => esc_html__('Error updating wishlist', 'uxd-elementor-extension'),
                    ],
                ],
            ]
        );

        // Enqueue premium scripts if licensed
        if (function_exists('uxd_ee_can_use_plugin') && uxd_ee_can_use_plugin()) {
            $this->enqueue_premium_scripts();
        }
    }

    /**
     * Enqueue frontend styles
     */
    public function enqueue_frontend_styles() {
        // Only enqueue on pages with Elementor content
        if (!$this->should_enqueue_frontend_assets()) {
            return;
        }

        // Enqueue Swiper CSS
        wp_enqueue_style(
            'uxd-swiper-css',
            'https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css',
            [],
            '8.0.0'
        );
        
        // Enqueue main plugin CSS
        wp_enqueue_style(
            'uxd-elementor-extension-css',
            UXD_EE_PLUGIN_URL . 'assets/css/uxd-elementor-extension.css',
            ['uxd-swiper-css'],
            UXD_EE_VERSION
        );

        // Enqueue premium styles if licensed
        if (function_exists('uxd_ee_can_use_plugin') && uxd_ee_can_use_plugin()) {
            $this->enqueue_premium_styles();
        }

        // Add inline styles for upgrade notices
        if (!function_exists('uxd_ee_can_use_plugin') || !uxd_ee_can_use_plugin()) {
            $this->add_upgrade_notice_styles();
        }
    }

    /**
     * Enqueue admin scripts
     */
    public function enqueue_admin_scripts($hook_suffix) {
        // Only enqueue on plugin admin pages
        if (!UXD_Helper::is_plugin_admin_page()) {
            return;
        }

        wp_enqueue_script(
            'uxd-admin-js',
            UXD_EE_PLUGIN_URL . 'includes/admin/admin.js',
            ['jquery'],
            UXD_EE_VERSION,
            true
        );

        wp_localize_script(
            'uxd-admin-js',
            'uxd_admin_object',
            [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce'    => wp_create_nonce('uxd_admin_nonce'),
                'i18n'     => [
                    'copied'          => esc_html__('Copied to clipboard!', 'uxd-elementor-extension'),
                    'copy_failed'     => esc_html__('Failed to copy', 'uxd-elementor-extension'),
                    'system_info'     => esc_html__('System Information', 'uxd-elementor-extension'),
                    'please_wait'     => esc_html__('Please wait...', 'uxd-elementor-extension'),
                ],
            ]
        );

        wp_enqueue_style(
            'uxd-admin-css',
            UXD_EE_PLUGIN_URL . 'includes/admin/admin.css',
            ['wp-admin'],
            UXD_EE_VERSION
        );
    }

    /**
     * Enqueue editor scripts
     */
    public function enqueue_editor_scripts() {
        wp_enqueue_script(
            'uxd-editor-js',
            UXD_EE_PLUGIN_URL . 'assets/js/editor.js',
            ['elementor-editor'],
            UXD_EE_VERSION,
            true
        );

        wp_localize_script(
            'uxd-editor-js',
            'uxd_editor_object',
            [
                'is_licensed' => function_exists('uxd_ee_can_use_plugin') ? uxd_ee_can_use_plugin() : false,
                'upgrade_url' => UXD_Helper::get_upgrade_url(),
                'i18n'       => [
                    'premium_title'       => esc_html__('Premium Feature', 'uxd-elementor-extension'),
                    'premium_description' => esc_html__('This feature is available in the Pro version.', 'uxd-elementor-extension'),
                    'upgrade_now'         => esc_html__('Upgrade Now', 'uxd-elementor-extension'),
                ],
            ]
        );
    }

    /**
     * Enqueue premium scripts
     */
    private function enqueue_premium_scripts() {
        if (file_exists(UXD_EE_PLUGIN_PATH . 'assets/js/premium.js')) {
            wp_enqueue_script(
                'uxd-premium-js',
                UXD_EE_PLUGIN_URL . 'assets/js/premium.js',
                ['uxd-elementor-extension-js'],
                UXD_EE_VERSION,
                true
            );
        }
    }

    /**
     * Enqueue premium styles
     */
    private function enqueue_premium_styles() {
        if (file_exists(UXD_EE_PLUGIN_PATH . 'assets/css/premium.css')) {
            wp_enqueue_style(
                'uxd-premium-css',
                UXD_EE_PLUGIN_URL . 'assets/css/premium.css',
                ['uxd-elementor-extension-css'],
                UXD_EE_VERSION
            );
        }
    }

    /**
     * Add upgrade notice styles
     */
    private function add_upgrade_notice_styles() {
        $css = '
            .uxd-upgrade-notice {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 20px;
                border-radius: 8px;
                margin: 20px 0;
                text-align: center;
            }
            
            .uxd-upgrade-notice h3 {
                margin: 0 0 10px 0;
                color: white;
            }
            
            .uxd-upgrade-notice p {
                margin: 0 0 15px 0;
                opacity: 0.9;
            }
            
            .uxd-upgrade-notice .button {
                background: rgba(255, 255, 255, 0.2);
                border-color: rgba(255, 255, 255, 0.3);
                color: white;
                text-decoration: none;
            }
            
            .uxd-upgrade-notice .button:hover {
                background: rgba(255, 255, 255, 0.3);
                border-color: rgba(255, 255, 255, 0.5);
                color: white;
            }
            
            .pro-badge {
                background: #ff6b6b;
                color: white;
                padding: 2px 8px;
                border-radius: 12px;
                font-size: 11px;
                font-weight: bold;
                margin-left: 8px;
            }
            
            .uxd-locked-widget-preview {
                position: relative;
                opacity: 0.7;
                pointer-events: none;
            }
            
            .uxd-locked-widget-preview::after {
                content: "🔒 Premium Feature";
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                background: rgba(0, 0, 0, 0.8);
                color: white;
                padding: 10px 20px;
                border-radius: 5px;
                font-weight: bold;
                z-index: 1000;
            }
        ';

        wp_add_inline_style('uxd-elementor-extension-css', $css);
    }

    /**
     * Check if we should enqueue frontend assets
     *
     * @return bool
     */
    private function should_enqueue_frontend_assets() {
        // Always enqueue on Elementor pages
        if (did_action('elementor/loaded')) {
            return true;
        }

        // Check if page uses Elementor
        global $post;
        if ($post && get_post_meta($post->ID, '_elementor_edit_mode', true)) {
            return true;
        }

        // Check for shortcodes
        if ($post && (
            has_shortcode($post->post_content, 'uxd_wishlist') ||
            has_shortcode($post->post_content, 'uxd_gallery') ||
            has_shortcode($post->post_content, 'uxd_products')
        )) {
            return true;
        }

        return false;
    }

    /**
     * Get asset version
     *
     * @param string $file_path File path
     * @return string Version string
     */
    private function get_asset_version($file_path) {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            return filemtime($file_path);
        }
        
        return UXD_EE_VERSION;
    }

    /**
     * Preload critical assets
     */
    public function preload_critical_assets() {
        // Preload critical CSS
        echo '<link rel="preload" href="' . esc_url(UXD_EE_PLUGIN_URL . 'assets/css/uxd-elementor-extension.css') . '" as="style" onload="this.onload=null;this.rel=\'stylesheet\'">';
        
        // Preload Swiper for carousel widgets
        if ($this->page_has_carousel()) {
            echo '<link rel="preload" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" as="style">';
            echo '<link rel="preload" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js" as="script">';
        }
    }

    /**
     * Check if page has carousel widgets
     *
     * @return bool
     */
    private function page_has_carousel() {
        global $post;
        
        if (!$post) {
            return false;
        }

        // Check post content for carousel indicators
        $content = $post->post_content;
        
        return (
            strpos($content, 'uxd-product-carousel') !== false ||
            strpos($content, '"widgetType":"uxd-product-carousel"') !== false
        );
    }
}