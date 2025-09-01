<?php
/**
 * UXD Wishlist Functionality
 */

if (!defined('ABSPATH')) {
    exit;
}

class UXD_Wishlist {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_action('wp_ajax_uxd_add_to_wishlist', [$this, 'ajax_add_to_wishlist']);
        add_action('wp_ajax_nopriv_uxd_add_to_wishlist', [$this, 'ajax_add_to_wishlist']);
        add_action('wp_ajax_uxd_remove_from_wishlist', [$this, 'ajax_remove_from_wishlist']);
        add_action('wp_ajax_nopriv_uxd_remove_from_wishlist', [$this, 'ajax_remove_from_wishlist']);
        add_action('wp_ajax_uxd_get_wishlist_count', [$this, 'ajax_get_wishlist_count']);
        add_action('wp_ajax_nopriv_uxd_get_wishlist_count', [$this, 'ajax_get_wishlist_count']);
        add_shortcode('uxd_wishlist', [$this, 'render_wishlist_shortcode']);
        add_shortcode('uxd_wishlist_count', [$this, 'render_wishlist_count_shortcode']);
        add_action('wp_head', [$this, 'add_wishlist_styles']);
        
        // Add wishlist button to single product page if enabled
        $settings = get_option('uxd_settings', []);
        if (isset($settings['integrations']['show_wishlist_on_single']) && $settings['integrations']['show_wishlist_on_single'] === 'on') {
            add_action('woocommerce_single_product_summary', [$this, 'add_single_product_wishlist_button'], 35);
        }
    }
    
    /**
     * Enqueue wishlist scripts and styles
     */
    public function enqueue_scripts() {
        wp_enqueue_style(
            'uxd-wishlist-css',
            UXD_EE_PLUGIN_URL . 'includes/wishlist/wishlist.css',
            [],
            UXD_EE_VERSION
        );
        
        wp_enqueue_script(
            'uxd-wishlist-js',
            UXD_EE_PLUGIN_URL . 'includes/wishlist/wishlist.js',
            ['jquery'],
            UXD_EE_VERSION,
            true
        );
        
        wp_localize_script('uxd-wishlist-js', 'uxd_wishlist_ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('uxd_wishlist_nonce'),
            'strings' => [
                'add_to_wishlist' => __('Add to Wishlist', 'uxd-elementor-extension'),
                'remove_from_wishlist' => __('Remove from Wishlist', 'uxd-elementor-extension'),
                'added_to_wishlist' => __('Added to wishlist!', 'uxd-elementor-extension'),
                'removed_from_wishlist' => __('Removed from wishlist!', 'uxd-elementor-extension'),
                'error' => __('Something went wrong. Please try again.', 'uxd-elementor-extension'),
            ]
        ]);
    }
    
    /**
     * Get user wishlist
     */
    public function get_user_wishlist($user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        
        if ($user_id) {
            // Logged in user - use user meta
            $wishlist = get_user_meta($user_id, 'uxd_wishlist', true);
            return is_array($wishlist) ? $wishlist : [];
        } else {
            // Guest user - use session
            if (!session_id()) {
                session_start();
            }
            return isset($_SESSION['uxd_wishlist']) ? $_SESSION['uxd_wishlist'] : [];
        }
    }
    
    /**
     * Add product to wishlist
     */
    public function add_to_wishlist($product_id, $user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        
        $wishlist = $this->get_user_wishlist($user_id);
        
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
            
            // Trigger action
            do_action('uxd_added_to_wishlist', $product_id, $user_id);
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Remove product from wishlist
     */
    public function remove_from_wishlist($product_id, $user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        
        $wishlist = $this->get_user_wishlist($user_id);
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
            
            // Trigger action
            do_action('uxd_removed_from_wishlist', $product_id, $user_id);
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Check if product is in wishlist
     */
    public function is_in_wishlist($product_id, $user_id = null) {
        $wishlist = $this->get_user_wishlist($user_id);
        return in_array($product_id, $wishlist);
    }
    
    /**
     * Get wishlist count
     */
    public function get_wishlist_count($user_id = null) {
        $wishlist = $this->get_user_wishlist($user_id);
        return count($wishlist);
    }
    
    /**
     * AJAX add to wishlist
     */
    public function ajax_add_to_wishlist() {
        check_ajax_referer('uxd_wishlist_nonce', 'nonce');
        
        $product_id = intval($_POST['product_id']);
        
        if (!$product_id || !wc_get_product($product_id)) {
            wp_send_json_error(__('Invalid product', 'uxd-elementor-extension'));
        }
        
        if ($this->add_to_wishlist($product_id)) {
            wp_send_json_success([
                'message' => __('Product added to wishlist', 'uxd-elementor-extension'),
                'count' => $this->get_wishlist_count(),
            ]);
        } else {
            wp_send_json_error(__('Product already in wishlist', 'uxd-elementor-extension'));
        }
    }
    
    /**
     * AJAX remove from wishlist
     */
    public function ajax_remove_from_wishlist() {
        check_ajax_referer('uxd_wishlist_nonce', 'nonce');
        
        $product_id = intval($_POST['product_id']);
        
        if ($this->remove_from_wishlist($product_id)) {
            wp_send_json_success([
                'message' => __('Product removed from wishlist', 'uxd-elementor-extension'),
                'count' => $this->get_wishlist_count(),
            ]);
        } else {
            wp_send_json_error(__('Product not in wishlist', 'uxd-elementor-extension'));
        }
    }
    
    /**
     * AJAX get wishlist count
     */
    public function ajax_get_wishlist_count() {
        check_ajax_referer('uxd_wishlist_nonce', 'nonce');
        
        wp_send_json_success([
            'count' => $this->get_wishlist_count(),
        ]);
    }
    
    /**
     * Render wishlist shortcode
     */
    public function render_wishlist_shortcode($atts) {
        $atts = shortcode_atts([
            'columns' => 3,
            'show_remove' => 'yes',
            'show_add_to_cart' => 'yes',
            'show_price' => 'yes',
            'show_rating' => 'yes',
        ], $atts);
        
        ob_start();
        include UXD_EE_PLUGIN_PATH . 'includes/wishlist/wishlist-shortcode.php';
        return ob_get_clean();
    }
    
    /**
     * Render wishlist count shortcode
     */
    public function render_wishlist_count_shortcode($atts) {
        $atts = shortcode_atts([
            'show_text' => 'yes',
            'text' => __('Wishlist', 'uxd-elementor-extension'),
            'show_icon' => 'yes',
        ], $atts);
        
        $count = $this->get_wishlist_count();
        $settings = get_option('uxd_settings', []);
        $wishlist_page_id = isset($settings['integrations']['wishlist_page_id']) ? $settings['integrations']['wishlist_page_id'] : '';
        $wishlist_url = $wishlist_page_id ? get_permalink($wishlist_page_id) : '#';
        
        ob_start();
        ?>
        <div class="uxd-wishlist-count-wrapper">
            <a href="<?php echo esc_url($wishlist_url); ?>" class="uxd-wishlist-count-link">
                <?php if ($atts['show_icon'] === 'yes'): ?>
                    <span class="uxd-wishlist-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                        </svg>
                    </span>
                <?php endif; ?>
                
                <?php if ($atts['show_text'] === 'yes'): ?>
                    <span class="uxd-wishlist-text"><?php echo esc_html($atts['text']); ?></span>
                <?php endif; ?>
                
                <span class="uxd-wishlist-count"><?php echo esc_html($count); ?></span>
            </a>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Render wishlist button
     */
    public function render_wishlist_button($product_id, $args = []) {
        $args = wp_parse_args($args, [
            'show_text' => true,
            'button_class' => 'uxd-wishlist-button',
            'icon_position' => 'before', // before, after, only
        ]);
        
        $is_in_wishlist = $this->is_in_wishlist($product_id);
        $button_text = $is_in_wishlist ? 
            __('Remove from Wishlist', 'uxd-elementor-extension') : 
            __('Add to Wishlist', 'uxd-elementor-extension');
        
        $classes = [$args['button_class']];
        if ($is_in_wishlist) {
            $classes[] = 'uxd-in-wishlist';
        }
        
        ob_start();
        ?>
        <button type="button" 
                class="<?php echo esc_attr(implode(' ', $classes)); ?>"
                data-product-id="<?php echo esc_attr($product_id); ?>"
                title="<?php echo esc_attr($button_text); ?>">
            
            <?php if (in_array($args['icon_position'], ['before', 'only'])): ?>
                <span class="uxd-wishlist-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                    </svg>
                </span>
            <?php endif; ?>
            
            <?php if ($args['show_text'] && $args['icon_position'] !== 'only'): ?>
                <span class="uxd-wishlist-text"><?php echo esc_html($button_text); ?></span>
            <?php endif; ?>
            
            <?php if ($args['icon_position'] === 'after'): ?>
                <span class="uxd-wishlist-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                    </svg>
                </span>
            <?php endif; ?>
        </button>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Add wishlist button to single product page
     */
    public function add_single_product_wishlist_button() {
        global $product;
        
        if (!$product) {
            return;
        }
        
        echo '<div class="uxd-single-product-wishlist">';
        echo $this->render_wishlist_button($product->get_id(), [
            'show_text' => true,
            'button_class' => 'uxd-wishlist-button uxd-single-wishlist-btn button',
            'icon_position' => 'before',
        ]);
        echo '</div>';
    }
    
    /**
     * Add basic wishlist styles to head
     */
    public function add_wishlist_styles() {
        ?>
        <style>
        .uxd-wishlist-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: transparent;
            border: 2px solid #ddd;
            border-radius: 4px;
            color: #666;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
            font-size: 14px;
        }
        
        .uxd-wishlist-button:hover {
            border-color: #e74c3c;
            color: #e74c3c;
            background: rgba(231, 76, 60, 0.05);
        }
        
        .uxd-wishlist-button.uxd-in-wishlist {
            border-color: #e74c3c;
            color: #e74c3c;
            background: rgba(231, 76, 60, 0.1);
        }
        
        .uxd-wishlist-button.uxd-in-wishlist:hover {
            background: rgba(231, 76, 60, 0.2);
        }
        
        .uxd-wishlist-icon svg {
            width: 16px;
            height: 16px;
            transition: transform 0.3s ease;
        }
        
        .uxd-wishlist-button:hover .uxd-wishlist-icon svg {
            transform: scale(1.1);
        }
        
        .uxd-single-product-wishlist {
            margin: 15px 0;
        }
        
        .uxd-single-wishlist-btn {
            width: 100%;
            justify-content: center;
            padding: 12px 24px;
            font-size: 16px;
        }
        
        .uxd-wishlist-count-wrapper {
            display: inline-block;
        }
        
        .uxd-wishlist-count-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            color: inherit;
            transition: color 0.3s ease;
        }
        
        .uxd-wishlist-count-link:hover {
            color: #e74c3c;
        }
        
        .uxd-wishlist-count {
            background: #e74c3c;
            color: white;
            border-radius: 50%;
            min-width: 20px;
            height: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
        }
        </style>
        <?php
    }
    
    /**
     * Get wishlist products
     */
    public function get_wishlist_products($user_id = null) {
        $wishlist = $this->get_user_wishlist($user_id);
        $products = [];
        
        foreach ($wishlist as $product_id) {
            $product = wc_get_product($product_id);
            if ($product && $product->exists()) {
                $products[] = $product;
            }
        }
        
        return $products;
    }
    
    /**
     * Clear user wishlist
     */
    public function clear_wishlist($user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        
        if ($user_id) {
            delete_user_meta($user_id, 'uxd_wishlist');
        } else {
            if (!session_id()) {
                session_start();
            }
            unset($_SESSION['uxd_wishlist']);
        }
        
        do_action('uxd_wishlist_cleared', $user_id);
        return true;
    }
    
    /**
     * Transfer guest wishlist to user account
     */
    public function transfer_guest_wishlist_to_user($user_id) {
        if (!session_id()) {
            session_start();
        }
        
        $guest_wishlist = isset($_SESSION['uxd_wishlist']) ? $_SESSION['uxd_wishlist'] : [];
        
        if (!empty($guest_wishlist)) {
            $user_wishlist = $this->get_user_wishlist($user_id);
            $merged_wishlist = array_unique(array_merge($user_wishlist, $guest_wishlist));
            
            update_user_meta($user_id, 'uxd_wishlist', $merged_wishlist);
            unset($_SESSION['uxd_wishlist']);
            
            do_action('uxd_guest_wishlist_transferred', $user_id, $guest_wishlist);
        }
    }
    
    /**
     * Get wishlist share URL
     */
    public function get_wishlist_share_url($user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        
        if (!$user_id) {
            return '';
        }
        
        $settings = get_option('uxd_settings', []);
        $wishlist_page_id = isset($settings['integrations']['wishlist_page_id']) ? $settings['integrations']['wishlist_page_id'] : '';
        
        if (!$wishlist_page_id) {
            return '';
        }
        
        $share_key = get_user_meta($user_id, 'uxd_wishlist_share_key', true);
        if (!$share_key) {
            $share_key = wp_generate_password(20, false);
            update_user_meta($user_id, 'uxd_wishlist_share_key', $share_key);
        }
        
        return add_query_arg('wishlist_share', $share_key, get_permalink($wishlist_page_id));
    }
    
    /**
     * Get shared wishlist data
     */
    public function get_shared_wishlist_data($share_key) {
        if (!$share_key) {
            return false;
        }
        
        $users = get_users([
            'meta_key' => 'uxd_wishlist_share_key',
            'meta_value' => $share_key,
            'number' => 1,
        ]);
        
        if (empty($users)) {
            return false;
        }
        
        $user = $users[0];
        $wishlist = $this->get_user_wishlist($user->ID);
        
        return [
            'user_id' => $user->ID,
            'user_name' => $user->display_name,
            'wishlist' => $wishlist,
            'products' => $this->get_wishlist_products($user->ID),
        ];
    }
}