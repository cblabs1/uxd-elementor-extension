<?php
/**
 * UXD Elementor Extension - Global Functions
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get plugin version
 */
function uxd_get_version() {
    return UXD_EE_VERSION;
}

/**
 * Get plugin URL
 */
function uxd_get_plugin_url() {
    return UXD_EE_PLUGIN_URL;
}

/**
 * Get plugin path
 */
function uxd_get_plugin_path() {
    return UXD_EE_PLUGIN_PATH;
}

/**
 * Check if Elementor is active
 */
function uxd_is_elementor_active() {
    return did_action('elementor/loaded');
}

/**
 * Check if WooCommerce is active
 */
function uxd_is_woocommerce_active() {
    return class_exists('WooCommerce');
}

/**
 * Check if we're in Elementor editor
 */
function uxd_is_elementor_editor() {
    return \Elementor\Plugin::$instance->editor->is_edit_mode();
}

/**
 * Check if we're in Elementor preview
 */
function uxd_is_elementor_preview() {
    return \Elementor\Plugin::$instance->preview->is_preview_mode();
}

/**
 * Get current user wishlist
 */
function uxd_get_user_wishlist($user_id = null) {
    if (!$user_id) {
        $user_id = get_current_user_id();
    }
    
    if ($user_id) {
        $wishlist = get_user_meta($user_id, 'uxd_wishlist', true);
        return is_array($wishlist) ? $wishlist : [];
    } else {
        // Guest user
        if (!session_id()) {
            session_start();
        }
        return isset($_SESSION['uxd_wishlist']) ? $_SESSION['uxd_wishlist'] : [];
    }
}

/**
 * Add product to wishlist
 */
function uxd_add_to_wishlist($product_id, $user_id = null) {
    if (!$user_id) {
        $user_id = get_current_user_id();
    }
    
    $wishlist = uxd_get_user_wishlist($user_id);
    
    if (!in_array($product_id, $wishlist)) {
        $wishlist[] = $product_id;
        
        if ($user_id) {
            update_user_meta($user_id, 'uxd_wishlist', $wishlist);
        } else {
            if (!session_id()) {
                session_start();
            }
            $_SESSION['uxd_wishlist'] = $wishlist;
        }
        
        return true;
    }
    
    return false;
}

/**
 * Remove product from wishlist
 */
function uxd_remove_from_wishlist($product_id, $user_id = null) {
    if (!$user_id) {
        $user_id = get_current_user_id();
    }
    
    $wishlist = uxd_get_user_wishlist($user_id);
    $key = array_search($product_id, $wishlist);
    
    if ($key !== false) {
        unset($wishlist[$key]);
        $wishlist = array_values($wishlist);
        
        if ($user_id) {
            update_user_meta($user_id, 'uxd_wishlist', $wishlist);
        } else {
            if (!session_id()) {
                session_start();
            }
            $_SESSION['uxd_wishlist'] = $wishlist;
        }
        
        return true;
    }
    
    return false;
}

/**
 * Check if product is in wishlist
 */
function uxd_is_in_wishlist($product_id, $user_id = null) {
    $wishlist = uxd_get_user_wishlist($user_id);
    return in_array($product_id, $wishlist);
}

/**
 * Get wishlist count
 */
function uxd_get_wishlist_count($user_id = null) {
    $wishlist = uxd_get_user_wishlist($user_id);
    return count($wishlist);
}

/**
 * Get product categories for select
 */
function uxd_get_product_categories_options() {
    if (!uxd_is_woocommerce_active()) {
        return [];
    }
    
    $categories = get_terms([
        'taxonomy' => 'product_cat',
        'hide_empty' => false,
    ]);
    
    $options = [];
    if (!is_wp_error($categories)) {
        foreach ($categories as $category) {
            $options[$category->term_id] = $category->name;
        }
    }
    
    return $options;
}

/**
 * Get product tags for select
 */
function uxd_get_product_tags_options() {
    if (!uxd_is_woocommerce_active()) {
        return [];
    }
    
    $tags = get_terms([
        'taxonomy' => 'product_tag',
        'hide_empty' => false,
    ]);
    
    $options = [];
    if (!is_wp_error($tags)) {
        foreach ($tags as $tag) {
            $options[$tag->term_id] = $tag->name;
        }
    }
    
    return $options;
}

/**
 * Get post categories for select
 */
function uxd_get_post_categories_options() {
    $categories = get_categories(['hide_empty' => false]);
    
    $options = [];
    foreach ($categories as $category) {
        $options[$category->term_id] = $category->name;
    }
    
    return $options;
}

/**
 * Get post tags for select
 */
function uxd_get_post_tags_options() {
    $tags = get_tags(['hide_empty' => false]);
    
    $options = [];
    foreach ($tags as $tag) {
        $options[$tag->term_id] = $tag->name;
    }
    
    return $options;
}

/**
 * Get available taxonomies
 */
function uxd_get_taxonomies_options() {
    $taxonomies = get_taxonomies(['public' => true], 'objects');
    
    $options = [];
    foreach ($taxonomies as $taxonomy) {
        $options[$taxonomy->name] = $taxonomy->label;
    }
    
    return $options;
}

/**
 * Get post types
 */
function uxd_get_post_types_options() {
    $post_types = get_post_types(['public' => true], 'objects');
    
    $options = [];
    foreach ($post_types as $post_type) {
        if ($post_type->name !== 'attachment') {
            $options[$post_type->name] = $post_type->label;
        }
    }
    
    return $options;
}

/**
 * Sanitize widget settings
 */
function uxd_sanitize_widget_settings($settings) {
    $sanitized = [];
    
    foreach ($settings as $key => $value) {
        if (is_array($value)) {
            $sanitized[$key] = uxd_sanitize_widget_settings($value);
        } elseif (is_string($value)) {
            // Handle different types of content
            if (in_array($key, ['custom_css', 'html_content', 'javascript_code'])) {
                $sanitized[$key] = wp_kses_post($value);
            } elseif (filter_var($value, FILTER_VALIDATE_URL)) {
                $sanitized[$key] = esc_url($value);
            } elseif (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $sanitized[$key] = sanitize_email($value);
            } else {
                $sanitized[$key] = sanitize_text_field($value);
            }
        } else {
            $sanitized[$key] = $value;
        }
    }
    
    return $sanitized;
}

/**
 * Get excerpt with custom length
 */
function uxd_get_excerpt($post_id = null, $length = 55, $more = '...') {
    if (!$post_id) {
        global $post;
        $post_id = $post->ID;
    }
    
    $excerpt = get_post_field('post_excerpt', $post_id);
    
    if (empty($excerpt)) {
        $content = get_post_field('post_content', $post_id);
        $excerpt = wp_strip_all_tags($content);
    }
    
    if (str_word_count($excerpt) > $length) {
        $words = explode(' ', $excerpt, $length + 1);
        array_pop($words);
        $excerpt = implode(' ', $words) . $more;
    }
    
    return $excerpt;
}

/**
 * Log debug information
 */
function uxd_log($message, $level = 'info') {
    if (!WP_DEBUG_LOG) {
        return;
    }
    
    $log_entry = sprintf(
        '[%s] [UXD] [%s] %s',
        current_time('Y-m-d H:i:s'),
        strtoupper($level),
        is_array($message) || is_object($message) ? print_r($message, true) : $message
    );
    
    error_log($log_entry);
}

/**
 * Get template part
 */
function uxd_get_template_part($slug, $name = null, $args = []) {
    $template_path = UXD_EE_PLUGIN_PATH . 'templates/';
    
    if ($name) {
        $template = $template_path . "{$slug}-{$name}.php";
    } else {
        $template = $template_path . "{$slug}.php";
    }
    
    if (file_exists($template)) {
        if (!empty($args)) {
            extract($args);
        }
        include $template;
        return true;
    }
    
    return false;
}

/**
 * Enqueue widget styles
 */
function uxd_enqueue_widget_style($widget_name) {
    $css_file = UXD_EE_PLUGIN_PATH . "assets/css/widgets/{$widget_name}.css";
    
    if (file_exists($css_file)) {
        wp_enqueue_style(
            "uxd-{$widget_name}",
            UXD_EE_PLUGIN_URL . "assets/css/widgets/{$widget_name}.css",
            [],
            UXD_EE_VERSION
        );
    }
}

/**
 * Enqueue widget scripts
 */
function uxd_enqueue_widget_script($widget_name, $deps = ['jquery']) {
    $js_file = UXD_EE_PLUGIN_PATH . "assets/js/widgets/{$widget_name}.js";
    
    if (file_exists($js_file)) {
        wp_enqueue_script(
            "uxd-{$widget_name}",
            UXD_EE_PLUGIN_URL . "assets/js/widgets/{$widget_name}.js",
            $deps,
            UXD_EE_VERSION,
            true
        );
    }
}

/**
 * Get current page context
 */
function uxd_get_page_context() {
    $context = [
        'is_shop' => false,
        'is_product_category' => false,
        'is_product_tag' => false,
        'is_product' => false,
        'is_cart' => false,
        'is_checkout' => false,
        'is_account' => false,
        'current_category' => null,
        'current_tag' => null,
        'current_product' => null,
    ];
    
    if (uxd_is_woocommerce_active()) {
        $context['is_shop'] = is_shop();
        $context['is_product_category'] = is_product_category();
        $context['is_product_tag'] = is_product_tag();
        $context['is_product'] = is_product();
        $context['is_cart'] = is_cart();
        $context['is_checkout'] = is_checkout();
        $context['is_account'] = is_account_page();
        
        if ($context['is_product_category']) {
            $context['current_category'] = get_queried_object();
        }
        
        if ($context['is_product_tag']) {
            $context['current_tag'] = get_queried_object();
        }
        
        if ($context['is_product']) {
            $context['current_product'] = wc_get_product();
        }
    }
    
    return $context;
}

/**
 * Format price with currency
 */
function uxd_format_price($price, $currency = null) {
    if (!uxd_is_woocommerce_active()) {
        return $price;
    }
    
    if ($currency) {
        return get_woocommerce_currency_symbol($currency) . number_format($price, 2);
    }
    
    return wc_price($price);
}

/**
 * Get product rating HTML
 */
function uxd_get_product_rating($product_id = null) {
    if (!uxd_is_woocommerce_active() || !$product_id) {
        return '';
    }
    
    $product = wc_get_product($product_id);
    if (!$product) {
        return '';
    }
    
    $rating = $product->get_average_rating();
    $count = $product->get_review_count();
    
    if ($rating > 0) {
        return wc_get_rating_html($rating, $count);
    }
    
    return '';
}

/**
 * Get image sizes options
 */
function uxd_get_image_sizes_options() {
    $image_sizes = get_intermediate_image_sizes();
    $options = [];
    
    foreach ($image_sizes as $size) {
        $options[$size] = ucwords(str_replace(['_', '-'], ' ', $size));
    }
    
    $options['full'] = 'Full Size';
    
    return $options;
}

/**
 * Check if user can access premium features
 */
function uxd_can_use_premium_features() {
    return function_exists('uxd_fs') && uxd_fs()->is__premium_only();
}

/**
 * Get premium upgrade URL
 */
function uxd_get_upgrade_url() {
    if (function_exists('uxd_fs')) {
        return uxd_fs()->get_upgrade_url();
    }
    return 'https://uxdesignexperts.com/upgrade/';
}

/**
 * Display premium notice
 */
function uxd_premium_notice($feature_name = '') {
    if (uxd_can_use_premium_features()) {
        return '';
    }
    
    $message = $feature_name ? 
        sprintf(__('%s is available in Premium version only.', 'uxd-elementor-extension'), $feature_name) :
        __('This feature is available in Premium version only.', 'uxd-elementor-extension');
    
    return sprintf(
        '<div class="uxd-premium-notice"><p>%s <a href="%s" target="_blank">%s</a></p></div>',
        esc_html($message),
        esc_url(uxd_get_upgrade_url()),
        esc_html__('Upgrade Now', 'uxd-elementor-extension')
    );
}