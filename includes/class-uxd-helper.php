<?php
/**
 * UXD Helper Class
 *
 * @package UXD_Elementor_Extension
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Helper class for common utilities
 */
class UXD_Helper {

    /**
     * Get post types for select control
     *
     * @return array
     */
    public static function get_post_types() {
        $post_types = get_post_types(['public' => true], 'objects');
        $options = [];
        
        foreach ($post_types as $post_type) {
            if ('attachment' !== $post_type->name) {
                $options[$post_type->name] = $post_type->label;
            }
        }
        
        return $options;
    }

    /**
     * Get post categories for select control
     *
     * @return array
     */
    public static function get_post_categories() {
        $categories = [];
        $terms = get_terms([
            'taxonomy'   => 'category',
            'hide_empty' => false,
        ]);
        
        if (!is_wp_error($terms)) {
            foreach ($terms as $term) {
                $categories[$term->term_id] = $term->name;
            }
        }
        
        return $categories;
    }

    /**
     * Get post tags for select control
     *
     * @return array
     */
    public static function get_post_tags() {
        $tags = [];
        $terms = get_terms([
            'taxonomy'   => 'post_tag',
            'hide_empty' => false,
        ]);
        
        if (!is_wp_error($terms)) {
            foreach ($terms as $term) {
                $tags[$term->term_id] = $term->name;
            }
        }
        
        return $tags;
    }

    /**
     * Get product categories (WooCommerce)
     *
     * @return array
     */
    public static function get_product_categories() {
        if (!class_exists('WooCommerce')) {
            return [];
        }

        $categories = [];
        $terms = get_terms([
            'taxonomy'   => 'product_cat',
            'hide_empty' => false,
        ]);
        
        if (!is_wp_error($terms)) {
            foreach ($terms as $term) {
                $categories[$term->term_id] = $term->name;
            }
        }
        
        return $categories;
    }

    /**
     * Get product tags (WooCommerce)
     *
     * @return array
     */
    public static function get_product_tags() {
        if (!class_exists('WooCommerce')) {
            return [];
        }

        $tags = [];
        $terms = get_terms([
            'taxonomy'   => 'product_tag',
            'hide_empty' => false,
        ]);
        
        if (!is_wp_error($terms)) {
            foreach ($terms as $term) {
                $tags[$term->term_id] = $term->name;
            }
        }
        
        return $tags;
    }

    /**
     * Get available taxonomies
     *
     * @return array
     */
    public static function get_available_taxonomies() {
        $taxonomies = get_taxonomies(['public' => true], 'objects');
        $options = [];
        
        foreach ($taxonomies as $taxonomy) {
            $options[$taxonomy->name] = $taxonomy->label;
        }
        
        return $options;
    }

    /**
     * Get custom excerpt
     *
     * @param int $length Excerpt length in words
     * @return string
     */
    public static function get_excerpt($length = 15) {
        $excerpt = get_the_excerpt();
        
        if (empty($excerpt)) {
            $excerpt = get_the_content();
        }
        
        $excerpt = wp_strip_all_tags($excerpt);
        $words = explode(' ', $excerpt, $length + 1);
        
        if (count($words) > $length) {
            array_pop($words);
            $excerpt = implode(' ', $words) . '...';
        } else {
            $excerpt = implode(' ', $words);
        }
        
        return $excerpt;
    }

    /**
     * Sanitize widget settings
     *
     * @param array $settings Raw settings
     * @return array Sanitized settings
     */
    public static function sanitize_widget_settings($settings) {
        $sanitized = [];

        foreach ($settings as $key => $value) {
            if (is_array($value)) {
                $sanitized[$key] = self::sanitize_widget_settings($value);
            } elseif (is_string($value)) {
                $sanitized[$key] = sanitize_text_field($value);
            } elseif (is_int($value)) {
                $sanitized[$key] = absint($value);
            } elseif (is_bool($value)) {
                $sanitized[$key] = (bool) $value;
            } else {
                $sanitized[$key] = $value;
            }
        }

        return $sanitized;
    }

    /**
     * Check if WooCommerce is active
     *
     * @return bool
     */
    public static function is_woocommerce_active() {
        return class_exists('WooCommerce');
    }

    /**
     * Check if Elementor Pro is active
     *
     * @return bool
     */
    public static function is_elementor_pro_active() {
        return defined('ELEMENTOR_PRO_VERSION');
    }

    /**
     * Get system info for debugging
     *
     * @return array
     */
    public static function get_system_info() {
        return [
            'plugin_version'     => UXD_EE_VERSION,
            'wp_version'         => get_bloginfo('version'),
            'elementor_version'  => defined('ELEMENTOR_VERSION') ? ELEMENTOR_VERSION : esc_html__('Not installed', 'uxd-elementor-extension'),
            'elementor_pro'      => self::is_elementor_pro_active() ? esc_html__('Active', 'uxd-elementor-extension') : esc_html__('Not active', 'uxd-elementor-extension'),
            'woocommerce_version' => self::is_woocommerce_active() ? WC()->version : esc_html__('Not installed', 'uxd-elementor-extension'),
            'php_version'        => PHP_VERSION,
            'memory_limit'       => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
        ];
    }

    /**
     * Check if current user can manage plugin
     *
     * @return bool
     */
    public static function current_user_can_manage() {
        return current_user_can('manage_options');
    }

    /**
     * Log debug message
     *
     * @param string $message Debug message
     * @param string $level Log level (error, warning, info, debug)
     */
    public static function log($message, $level = 'info') {
        if (!defined('WP_DEBUG') || !WP_DEBUG) {
            return;
        }

        $log_message = sprintf(
            '[%s] UXD Elementor Extension: %s',
            strtoupper($level),
            $message
        );

        error_log($log_message);
    }

    /**
     * Get plugin upgrade URL
     *
     * @return string
     */
    public static function get_upgrade_url() {
        if (function_exists('uxd_ee_get_pricing_url')) {
            return uxd_ee_get_pricing_url();
        }
        
        return 'https://uxdesignexperts.com/uxd-elementor-extension/';
    }

    /**
     * Check if current page is plugin admin page
     *
     * @return bool
     */
    public static function is_plugin_admin_page() {
        $screen = get_current_screen();
        
        if (!$screen) {
            return false;
        }

        return false !== strpos($screen->id, 'uxd-elementor-extension');
    }

    /**
     * Render upgrade notice
     *
     * @param string $feature_name Feature name
     * @param string $description Feature description
     * @return string HTML content
     */
    public static function render_upgrade_notice($feature_name = '', $description = '') {
        $feature_name = !empty($feature_name) ? $feature_name : esc_html__('Premium Feature', 'uxd-elementor-extension');
        $description = !empty($description) ? $description : esc_html__('This feature is available in the Pro version.', 'uxd-elementor-extension');
        
        ob_start();
        ?>
        <div class="uxd-upgrade-notice">
            <div class="uxd-upgrade-content">
                <h3><?php echo esc_html($feature_name); ?> <span class="pro-badge"><?php esc_html_e('PRO', 'uxd-elementor-extension'); ?></span></h3>
                <p><?php echo esc_html($description); ?></p>
                <a href="<?php echo esc_url(self::get_upgrade_url()); ?>" class="button button-primary" target="_blank">
                    <?php esc_html_e('Upgrade to Pro', 'uxd-elementor-extension'); ?>
                </a>
            </div>
        </div>
        <?php
        
        return ob_get_clean();
    }

    /**
     * Get current page terms
     *
     * @return array
     */
    public static function get_current_page_terms() {
        $current_terms = [];
        
        if (is_category() || is_tag() || is_tax()) {
            $current_object = get_queried_object();
            if ($current_object && isset($current_object->term_id)) {
                $current_terms[] = $current_object->term_id;
            }
        } elseif (is_single()) {
            $post = get_queried_object();
            if ($post) {
                $taxonomies = get_object_taxonomies($post->post_type);
                foreach ($taxonomies as $taxonomy) {
                    $terms = get_the_terms($post->ID, $taxonomy);
                    if ($terms && !is_wp_error($terms)) {
                        foreach ($terms as $term) {
                            $current_terms[] = $term->term_id;
                        }
                    }
                }
            }
        }
        
        return $current_terms;
    }

    /**
     * Get taxonomies by post type
     *
     * @param string $post_type Post type
     * @return array
     */
    public static function get_taxonomies_by_post_type($post_type = '') {
        if (empty($post_type)) {
            $taxonomies = get_taxonomies(['public' => true], 'objects');
        } else {
            $taxonomies = get_object_taxonomies($post_type, 'objects');
        }
        
        $options = [];
        foreach ($taxonomies as $taxonomy) {
            $options[$taxonomy->name] = $taxonomy->label;
        }
        
        return $options;
    }
}