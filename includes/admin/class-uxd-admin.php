<?php
/**
 * UXD Admin Controller - Corrected Version
 */

if (!defined('ABSPATH')) {
    exit;
}

class UXD_Admin {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
        add_action('admin_init', [$this, 'init_settings']);
        add_action('wp_ajax_uxd_save_settings', [$this, 'save_settings']);
        add_action('wp_ajax_uxd_reset_settings', [$this, 'reset_settings']);
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            __('UXD Elementor Extension', 'uxd-elementor-extension'),
            __('UXD Elements', 'uxd-elementor-extension'),
            'manage_options',
            'uxd-elementor-extension',
            [$this, 'admin_page'],
            'dashicons-layout',
            60
        );
        
        // Add submenu pages
        add_submenu_page(
            'uxd-elementor-extension',
            __('Settings', 'uxd-elementor-extension'),
            __('Settings', 'uxd-elementor-extension'),
            'manage_options',
            'uxd-elementor-extension',
            [$this, 'admin_page']
        );
        
        add_submenu_page(
            'uxd-elementor-extension',
            __('Widgets', 'uxd-elementor-extension'),
            __('Widgets', 'uxd-elementor-extension'),
            'manage_options',
            'uxd-widgets',
            [$this, 'widgets_page']
        );
        
        if (!$this->can_use_premium_features()) {
            add_submenu_page(
                'uxd-elementor-extension',
                __('Upgrade to Pro', 'uxd-elementor-extension'),
                '<span style="color: #ffa726;">' . __('Upgrade to Pro', 'uxd-elementor-extension') . '</span>',
                'manage_options',
                'uxd-upgrade',
                [$this, 'upgrade_page']
            );
        }
    }
    
    /**
     * Check if premium features can be used
     */
    private function can_use_premium_features() {
        return function_exists('uxd_ee_can_use_plugin') ? uxd_ee_can_use_plugin() : false;
    }
    
    /**
     * Get upgrade URL
     */
    private function get_upgrade_url() {
        return function_exists('uxd_ee_get_pricing_url') ? uxd_ee_get_pricing_url() : 'https://uxdesignexperts.com/pricing/';
    }
    
    /**
     * Enqueue admin scripts
     */
    public function enqueue_admin_scripts($hook) {
        if (strpos($hook, 'uxd-') === false) {
            return;
        }
        
        wp_enqueue_style(
            'uxd-admin-css',
            UXD_EE_PLUGIN_URL . 'assets/css/admin.css',
            [],
            UXD_EE_VERSION
        );
        
        wp_enqueue_script(
            'uxd-admin-js',
            UXD_EE_PLUGIN_URL . 'assets/js/admin.js',
            ['jquery'],
            UXD_EE_VERSION,
            true
        );
        
        wp_localize_script('uxd-admin-js', 'uxd_admin_ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('uxd_admin_nonce'),
            'strings' => [
                'saving' => __('Saving...', 'uxd-elementor-extension'),
                'saved' => __('Settings saved!', 'uxd-elementor-extension'),
                'error' => __('Error saving settings', 'uxd-elementor-extension'),
                'confirm_reset' => __('Are you sure you want to reset all settings?', 'uxd-elementor-extension'),
                'enabled' => __('Enabled', 'uxd-elementor-extension'),
                'disabled' => __('Disabled', 'uxd-elementor-extension'),
            ]
        ]);
    }
    
    /**
     * Initialize settings
     */
    public function init_settings() {
        register_setting('uxd_settings', 'uxd_settings');
    }
    
    /**
     * Get default settings
     */
    private function get_default_settings() {
        return [
            'enabled_widgets' => [
                'gallery-grid' => 'on',
                'product-carousel' => 'on',
                'product-grid' => 'on',
                'taxonomy-accordion' => 'on',
                'post-grid' => 'on',
            ],
            'performance' => [
                'enable_css_minification' => 'off',
                'enable_js_minification' => 'off',
                'load_assets_conditionally' => 'on',
            ],
            'integrations' => [
                'enable_wishlist' => 'on',
                'wishlist_page_id' => '',
                'enable_quick_view' => 'on',
            ],
        ];
    }
    
    /**
     * Get settings
     */
    public function get_settings() {
        $settings = get_option('uxd_settings', []);
        return wp_parse_args($settings, $this->get_default_settings());
    }
    
    /**
     * Main admin page
     */
    public function admin_page() {
        $settings = $this->get_settings();
        $current_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'general';
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('UXD Elementor Extension Settings', 'uxd-elementor-extension'); ?></h1>
            
            <div class="uxd-admin-container">
                <nav class="uxd-admin-nav">
                    <ul class="uxd-tabs">
                        <li class="<?php echo $current_tab === 'general' ? 'active' : ''; ?>">
                            <a href="?page=uxd-elementor-extension&tab=general">
                                <span class="dashicons dashicons-admin-settings"></span>
                                <?php esc_html_e('General', 'uxd-elementor-extension'); ?>
                            </a>
                        </li>
                        <li class="<?php echo $current_tab === 'performance' ? 'active' : ''; ?>">
                            <a href="?page=uxd-elementor-extension&tab=performance">
                                <span class="dashicons dashicons-performance"></span>
                                <?php esc_html_e('Performance', 'uxd-elementor-extension'); ?>
                            </a>
                        </li>
                        <li class="<?php echo $current_tab === 'integrations' ? 'active' : ''; ?>">
                            <a href="?page=uxd-elementor-extension&tab=integrations">
                                <span class="dashicons dashicons-admin-plugins"></span>
                                <?php esc_html_e('Integrations', 'uxd-elementor-extension'); ?>
                            </a>
                        </li>
                        <li class="<?php echo $current_tab === 'support' ? 'active' : ''; ?>">
                            <a href="?page=uxd-elementor-extension&tab=support">
                                <span class="dashicons dashicons-sos"></span>
                                <?php esc_html_e('Support', 'uxd-elementor-extension'); ?>
                            </a>
                        </li>
                    </ul>
                </nav>
                
                <div class="uxd-admin-content">
                    <form id="uxd-settings-form" method="post">
                        <?php wp_nonce_field('uxd_admin_nonce', 'uxd_nonce'); ?>
                        
                        <?php if ($current_tab === 'general'): ?>
                            <div class="uxd-tab-content">
                                <h2><?php esc_html_e('General Settings', 'uxd-elementor-extension'); ?></h2>
                                
                                <div class="uxd-settings-section">
                                    <h3><?php esc_html_e('Widget Status', 'uxd-elementor-extension'); ?></h3>
                                    <p><?php esc_html_e('Enable or disable widgets as needed. Disabled widgets won\'t appear in Elementor editor.', 'uxd-elementor-extension'); ?></p>
                                    
                                    <table class="uxd-widgets-table">
                                        <thead>
                                            <tr>
                                                <th><?php esc_html_e('Widget', 'uxd-elementor-extension'); ?></th>
                                                <th><?php esc_html_e('Description', 'uxd-elementor-extension'); ?></th>
                                                <th><?php esc_html_e('Status', 'uxd-elementor-extension'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td data-label="Widget"><strong><?php esc_html_e('Gallery Grid', 'uxd-elementor-extension'); ?></strong></td>
                                                <td data-label="Description"><?php esc_html_e('Display images in grid or masonry layout', 'uxd-elementor-extension'); ?></td>
                                                <td data-label="Status">
                                                    <label class="uxd-switch">
                                                        <input type="checkbox" name="enabled_widgets[gallery-grid]" <?php checked($settings['enabled_widgets']['gallery-grid'], 'on'); ?>>
                                                        <span class="uxd-slider"></span>
                                                    </label>
                                                    <span class="uxd-widget-status"><?php echo $settings['enabled_widgets']['gallery-grid'] === 'on' ? 'Enabled' : 'Disabled'; ?></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td data-label="Widget">
                                                    <strong><?php esc_html_e('Product Carousel', 'uxd-elementor-extension'); ?></strong>
                                                    <?php if (!$this->can_use_premium_features()): ?>
                                                        <span class="uxd-pro-badge">PRO</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td data-label="Description"><?php esc_html_e('WooCommerce product carousel slider', 'uxd-elementor-extension'); ?></td>
                                                <td data-label="Status">
                                                    <?php if ($this->can_use_premium_features()): ?>
                                                        <label class="uxd-switch">
                                                            <input type="checkbox" name="enabled_widgets[product-carousel]" <?php checked($settings['enabled_widgets']['product-carousel'], 'on'); ?>>
                                                            <span class="uxd-slider"></span>
                                                        </label>
                                                        <span class="uxd-widget-status"><?php echo $settings['enabled_widgets']['product-carousel'] === 'on' ? 'Enabled' : 'Disabled'; ?></span>
                                                    <?php else: ?>
                                                        <a href="<?php echo esc_url($this->get_upgrade_url()); ?>" class="button button-primary button-small" target="_blank">
                                                            <?php esc_html_e('Upgrade', 'uxd-elementor-extension'); ?>
                                                        </a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td data-label="Widget">
                                                    <strong><?php esc_html_e('Product Grid', 'uxd-elementor-extension'); ?></strong>
                                                    <?php if (!$this->can_use_premium_features()): ?>
                                                        <span class="uxd-pro-badge">PRO</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td data-label="Description"><?php esc_html_e('WooCommerce product grid with filters', 'uxd-elementor-extension'); ?></td>
                                                <td data-label="Status">
                                                    <?php if ($this->can_use_premium_features()): ?>
                                                        <label class="uxd-switch">
                                                            <input type="checkbox" name="enabled_widgets[product-grid]" <?php checked($settings['enabled_widgets']['product-grid'], 'on'); ?>>
                                                            <span class="uxd-slider"></span>
                                                        </label>
                                                        <span class="uxd-widget-status"><?php echo $settings['enabled_widgets']['product-grid'] === 'on' ? 'Enabled' : 'Disabled'; ?></span>
                                                    <?php else: ?>
                                                        <a href="<?php echo esc_url($this->get_upgrade_url()); ?>" class="button button-primary button-small" target="_blank">
                                                            <?php esc_html_e('Upgrade', 'uxd-elementor-extension'); ?>
                                                        </a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td data-label="Widget">
                                                    <strong><?php esc_html_e('Taxonomy Accordion', 'uxd-elementor-extension'); ?></strong>
                                                    <?php if (!$this->can_use_premium_features()): ?>
                                                        <span class="uxd-pro-badge">PRO</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td data-label="Description"><?php esc_html_e('Collapsible taxonomy listings', 'uxd-elementor-extension'); ?></td>
                                                <td data-label="Status">
                                                    <?php if ($this->can_use_premium_features()): ?>
                                                        <label class="uxd-switch">
                                                            <input type="checkbox" name="enabled_widgets[taxonomy-accordion]" <?php checked($settings['enabled_widgets']['taxonomy-accordion'], 'on'); ?>>
                                                            <span class="uxd-slider"></span>
                                                        </label>
                                                        <span class="uxd-widget-status"><?php echo $settings['enabled_widgets']['taxonomy-accordion'] === 'on' ? 'Enabled' : 'Disabled'; ?></span>
                                                    <?php else: ?>
                                                        <a href="<?php echo esc_url($this->get_upgrade_url()); ?>" class="button button-primary button-small" target="_blank">
                                                            <?php esc_html_e('Upgrade', 'uxd-elementor-extension'); ?>
                                                        </a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td data-label="Widget">
                                                    <strong><?php esc_html_e('Post Grid', 'uxd-elementor-extension'); ?></strong>
                                                    <?php if (!$this->can_use_premium_features()): ?>
                                                        <span class="uxd-pro-badge">PRO</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td data-label="Description"><?php esc_html_e('Advanced blog post grid layouts', 'uxd-elementor-extension'); ?></td>
                                                <td data-label="Status">
                                                    <?php if ($this->can_use_premium_features()): ?>
                                                        <label class="uxd-switch">
                                                            <input type="checkbox" name="enabled_widgets[post-grid]" <?php checked($settings['enabled_widgets']['post-grid'], 'on'); ?>>
                                                            <span class="uxd-slider"></span>
                                                        </label>
                                                        <span class="uxd-widget-status"><?php echo $settings['enabled_widgets']['post-grid'] === 'on' ? 'Enabled' : 'Disabled'; ?></span>
                                                    <?php else: ?>
                                                        <a href="<?php echo esc_url($this->get_upgrade_url()); ?>" class="button button-primary button-small" target="_blank">
                                                            <?php esc_html_e('Upgrade', 'uxd-elementor-extension'); ?>
                                                        </a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        
                        <?php elseif ($current_tab === 'performance'): ?>
                            <div class="uxd-tab-content">
                                <h2><?php esc_html_e('Performance Settings', 'uxd-elementor-extension'); ?></h2>
                                
                                <div class="uxd-settings-section">
                                    <h3><?php esc_html_e('Asset Optimization', 'uxd-elementor-extension'); ?></h3>
                                    
                                    <table class="form-table">
                                        <tr>
                                            <th scope="row"><?php esc_html_e('CSS Minification', 'uxd-elementor-extension'); ?></th>
                                            <td>
                                                <label class="uxd-switch">
                                                    <input type="checkbox" name="performance[enable_css_minification]" <?php checked($settings['performance']['enable_css_minification'], 'on'); ?>>
                                                    <span class="uxd-slider"></span>
                                                </label>
                                                <p class="description"><?php esc_html_e('Minify CSS files for better performance', 'uxd-elementor-extension'); ?></p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><?php esc_html_e('JavaScript Minification', 'uxd-elementor-extension'); ?></th>
                                            <td>
                                                <label class="uxd-switch">
                                                    <input type="checkbox" name="performance[enable_js_minification]" <?php checked($settings['performance']['enable_js_minification'], 'on'); ?>>
                                                    <span class="uxd-slider"></span>
                                                </label>
                                                <p class="description"><?php esc_html_e('Minify JavaScript files for better performance', 'uxd-elementor-extension'); ?></p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><?php esc_html_e('Conditional Loading', 'uxd-elementor-extension'); ?></th>
                                            <td>
                                                <label class="uxd-switch">
                                                    <input type="checkbox" name="performance[load_assets_conditionally]" <?php checked($settings['performance']['load_assets_conditionally'], 'on'); ?>>
                                                    <span class="uxd-slider"></span>
                                                </label>
                                                <p class="description"><?php esc_html_e('Load widget assets only when widgets are used on the page', 'uxd-elementor-extension'); ?></p>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        
                        <?php elseif ($current_tab === 'integrations'): ?>
                            <div class="uxd-tab-content">
                                <h2><?php esc_html_e('Integration Settings', 'uxd-elementor-extension'); ?></h2>
                                
                                <div class="uxd-settings-section">
                                    <h3><?php esc_html_e('WooCommerce Integration', 'uxd-elementor-extension'); ?></h3>
                                    
                                    <table class="form-table">
                                        <tr>
                                            <th scope="row"><?php esc_html_e('Enable Wishlist', 'uxd-elementor-extension'); ?></th>
                                            <td>
                                                <label class="uxd-switch">
                                                    <input type="checkbox" name="integrations[enable_wishlist]" <?php checked($settings['integrations']['enable_wishlist'], 'on'); ?>>
                                                    <span class="uxd-slider"></span>
                                                </label>
                                                <p class="description"><?php esc_html_e('Enable wishlist functionality for products', 'uxd-elementor-extension'); ?></p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><?php esc_html_e('Wishlist Page', 'uxd-elementor-extension'); ?></th>
                                            <td>
                                                <?php
                                                wp_dropdown_pages([
                                                    'name' => 'integrations[wishlist_page_id]',
                                                    'selected' => $settings['integrations']['wishlist_page_id'],
                                                    'show_option_none' => __('Select Page', 'uxd-elementor-extension'),
                                                    'option_none_value' => '',
                                                ]);
                                                ?>
                                                <p class="description">
                                                    <?php esc_html_e('Select the page where wishlist will be displayed. Use shortcode [uxd_wishlist] on the page.', 'uxd-elementor-extension'); ?>
                                                </p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><?php esc_html_e('Enable Quick View', 'uxd-elementor-extension'); ?></th>
                                            <td>
                                                <label class="uxd-switch">
                                                    <input type="checkbox" name="integrations[enable_quick_view]" <?php checked($settings['integrations']['enable_quick_view'], 'on'); ?>>
                                                    <span class="uxd-slider"></span>
                                                </label>
                                                <p class="description"><?php esc_html_e('Enable quick view functionality for products', 'uxd-elementor-extension'); ?></p>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        
                        <?php elseif ($current_tab === 'support'): ?>
                            <div class="uxd-tab-content">
                                <h2><?php esc_html_e('Support & Documentation', 'uxd-elementor-extension'); ?></h2>
                                
                                <div class="uxd-support-grid">
                                    <div class="uxd-support-card">
                                        <div class="uxd-support-icon">📚</div>
                                        <h3><?php esc_html_e('Documentation', 'uxd-elementor-extension'); ?></h3>
                                        <p><?php esc_html_e('Comprehensive guides and tutorials for all widgets and features.', 'uxd-elementor-extension'); ?></p>
                                        <a href="https://uxdesignexperts.com/docs/" class="button button-primary" target="_blank">
                                            <?php esc_html_e('View Documentation', 'uxd-elementor-extension'); ?>
                                        </a>
                                    </div>
                                    
                                    <div class="uxd-support-card">
                                        <div class="uxd-support-icon">💬</div>
                                        <h3><?php esc_html_e('Community Support', 'uxd-elementor-extension'); ?></h3>
                                        <p><?php esc_html_e('Get help from our community forum and support team.', 'uxd-elementor-extension'); ?></p>
                                        <a href="https://wordpress.org/support/plugin/uxd-elementor-extension/" class="button" target="_blank">
                                            <?php esc_html_e('Visit Forum', 'uxd-elementor-extension'); ?>
                                        </a>
                                    </div>
                                    
                                    <div class="uxd-support-card">
                                        <div class="uxd-support-icon">🐛</div>
                                        <h3><?php esc_html_e('Report Bug', 'uxd-elementor-extension'); ?></h3>
                                        <p><?php esc_html_e('Found a bug? Report it and help us improve the plugin.', 'uxd-elementor-extension'); ?></p>
                                        <a href="https://github.com/uxdesignexperts/uxd-elementor-extension/issues" class="button" target="_blank">
                                            <?php esc_html_e('Report Issue', 'uxd-elementor-extension'); ?>
                                        </a>
                                    </div>
                                    
                                    <div class="uxd-support-card">
                                        <div class="uxd-support-icon">⭐</div>
                                        <h3><?php esc_html_e('Rate Plugin', 'uxd-elementor-extension'); ?></h3>
                                        <p><?php esc_html_e('Love the plugin? Leave us a 5-star review on WordPress.org.', 'uxd-elementor-extension'); ?></p>
                                        <a href="https://wordpress.org/support/plugin/uxd-elementor-extension/reviews/" class="button" target="_blank">
                                            <?php esc_html_e('Leave Review', 'uxd-elementor-extension'); ?>
                                        </a>
                                    </div>
                                </div>
                                
                                <div class="uxd-system-info">
                                    <h3><?php esc_html_e('System Information', 'uxd-elementor-extension'); ?></h3>
                                    <div class="uxd-system-table">
                                        <table class="widefat">
                                            <tbody>
                                                <tr>
                                                    <td><strong><?php esc_html_e('Plugin Version', 'uxd-elementor-extension'); ?></strong></td>
                                                    <td><?php echo esc_html(UXD_EE_VERSION); ?></td>
                                                </tr>
                                                <tr>
                                                    <td><strong><?php esc_html_e('WordPress Version', 'uxd-elementor-extension'); ?></strong></td>
                                                    <td><?php echo esc_html(get_bloginfo('version')); ?></td>
                                                </tr>
                                                <tr>
                                                    <td><strong><?php esc_html_e('PHP Version', 'uxd-elementor-extension'); ?></strong></td>
                                                    <td><?php echo esc_html(PHP_VERSION); ?></td>
                                                </tr>
                                                <tr>
                                                    <td><strong><?php esc_html_e('Elementor Version', 'uxd-elementor-extension'); ?></strong></td>
                                                    <td><?php echo defined('ELEMENTOR_VERSION') ? esc_html(ELEMENTOR_VERSION) : esc_html__('Not installed', 'uxd-elementor-extension'); ?></td>
                                                </tr>
                                                <tr>
                                                    <td><strong><?php esc_html_e('WooCommerce Version', 'uxd-elementor-extension'); ?></strong></td>
                                                    <td><?php echo defined('WC_VERSION') ? esc_html(WC_VERSION) : esc_html__('Not installed', 'uxd-elementor-extension'); ?></td>
                                                </tr>
                                                <tr>
                                                    <td><strong><?php esc_html_e('Memory Limit', 'uxd-elementor-extension'); ?></strong></td>
                                                    <td><?php echo esc_html(ini_get('memory_limit')); ?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($current_tab !== 'support'): ?>
                            <div class="uxd-admin-footer">
                                <button type="submit" class="button button-primary button-hero">
                                    <?php esc_html_e('Save Settings', 'uxd-elementor-extension'); ?>
                                </button>
                                <button type="button" class="button button-secondary" id="uxd-reset-settings">
                                    <?php esc_html_e('Reset to Default', 'uxd-elementor-extension'); ?>
                                </button>
                            </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Widgets page
     */
    public function widgets_page() {
        echo '<div class="wrap"><h1>Widgets Page</h1><p>This is a placeholder for the widgets management page.</p></div>';
    }
    
    /**
     * Upgrade page
     */
    public function upgrade_page() {
        ?>
        <div class="wrap">
            <div class="uxd-upgrade-page">
                <div class="uxd-upgrade-header">
                    <h1><?php esc_html_e('Upgrade to UXD Elementor Extension Pro', 'uxd-elementor-extension'); ?></h1>
                    <p class="subtitle"><?php esc_html_e('Unlock premium widgets and advanced features', 'uxd-elementor-extension'); ?></p>
                </div>
                
                <div class="uxd-pricing-table">
                    <div class="uxd-pricing-card uxd-recommended">
                        <div class="uxd-pricing-header">
                            <h3><?php esc_html_e('Professional', 'uxd-elementor-extension'); ?></h3>
                            <div class="uxd-price">
                                <span class="uxd-currency">$</span>
                                <span class="uxd-amount">49</span>
                                <span class="uxd-period">/year</span>
                            </div>
                            <div class="uxd-recommended-badge"><?php esc_html_e('Most Popular', 'uxd-elementor-extension'); ?></div>
                        </div>
                        
                        <ul class="uxd-features-list">
                            <li><?php esc_html_e('All Premium Widgets', 'uxd-elementor-extension'); ?></li>
                            <li><?php esc_html_e('Advanced Styling Options', 'uxd-elementor-extension'); ?></li>
                            <li><?php esc_html_e('Custom Query Builder', 'uxd-elementor-extension'); ?></li>
                            <li><?php esc_html_e('Priority Support', 'uxd-elementor-extension'); ?></li>
                            <li><?php esc_html_e('Regular Updates', 'uxd-elementor-extension'); ?></li>
                            <li><?php esc_html_e('1 Year Support & Updates', 'uxd-elementor-extension'); ?></li>
                        </ul>
                        
                        <div class="uxd-pricing-footer">
                            <a href="<?php echo esc_url($this->get_upgrade_url()); ?>" class="button button-primary button-hero" target="_blank">
                                <?php esc_html_e('Upgrade Now', 'uxd-elementor-extension'); ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <style>
            .uxd-upgrade-page {
                max-width: 800px;
                margin: 0 auto;
                padding: 20px;
            }
            .uxd-upgrade-header {
                text-align: center;
                margin-bottom: 40px;
            }
            .uxd-upgrade-header h1 {
                font-size: 2.5em;
                margin-bottom: 10px;
                background: linear-gradient(45deg, #667eea, #764ba2);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
            }
            .uxd-pricing-table {
                display: flex;
                justify-content: center;
                margin-bottom: 60px;
            }
            .uxd-pricing-card {
                background: white;
                border-radius: 12px;
                padding: 40px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.1);
                text-align: center;
                position: relative;
                max-width: 400px;
            }
            .uxd-recommended {
                border: 2px solid #ffa726;
                transform: scale(1.05);
            }
            .uxd-recommended-badge {
                position: absolute;
                top: -12px;
                left: 50%;
                transform: translateX(-50%);
                background: #ffa726;
                color: white;
                padding: 8px 20px;
                border-radius: 20px;
                font-size: 12px;
                font-weight: bold;
            }
            .uxd-price {
                font-size: 3em;
                font-weight: bold;
                margin: 20px 0;
                color: #333;
            }
            .uxd-currency,
            .uxd-period {
                font-size: 0.5em;
                color: #666;
            }
            .uxd-features-list {
                list-style: none;
                padding: 0;
                margin: 30px 0;
            }
            .uxd-features-list li {
                padding: 10px 0;
                border-bottom: 1px solid #eee;
                position: relative;
                padding-left: 30px;
            }
            .uxd-features-list li::before {
                content: '✓';
                position: absolute;
                left: 0;
                color: #4caf50;
                font-weight: bold;
                font-size: 18px;
            }
            </style>
        </div>
        <?php
    }
    
    /**
     * Save settings via AJAX
     */
    public function save_settings() {
        check_ajax_referer('uxd_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Insufficient permissions', 'uxd-elementor-extension'));
        }
        
        $settings = isset($_POST['settings']) ? $_POST['settings'] : [];
        $sanitized_settings = $this->sanitize_settings($settings);
        
        update_option('uxd_settings', $sanitized_settings);
        
        wp_send_json_success(['message' => __('Settings saved successfully', 'uxd-elementor-extension')]);
    }
    
    /**
     * Reset settings via AJAX
     */
    public function reset_settings() {
        check_ajax_referer('uxd_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Insufficient permissions', 'uxd-elementor-extension'));
        }
        
        delete_option('uxd_settings');
        
        wp_send_json_success(['message' => __('Settings reset successfully', 'uxd-elementor-extension')]);
    }
    
    /**
     * Sanitize settings
     */
    private function sanitize_settings($settings) {
        $sanitized = [];
        
        if (isset($settings['enabled_widgets'])) {
            $sanitized['enabled_widgets'] = [];
            foreach ($settings['enabled_widgets'] as $widget => $status) {
                $sanitized['enabled_widgets'][sanitize_key($widget)] = $status === 'on' ? 'on' : 'off';
            }
        }
        
        if (isset($settings['performance'])) {
            $sanitized['performance'] = [];
            foreach ($settings['performance'] as $key => $value) {
                $sanitized['performance'][sanitize_key($key)] = sanitize_text_field($value);
            }
        }
        
        if (isset($settings['integrations'])) {
            $sanitized['integrations'] = [];
            foreach ($settings['integrations'] as $key => $value) {
                if ($key === 'wishlist_page_id') {
                    $sanitized['integrations'][$key] = intval($value);
                } else {
                    $sanitized['integrations'][sanitize_key($key)] = sanitize_text_field($value);
                }
            }
        }
        
        return $sanitized;
    }
}