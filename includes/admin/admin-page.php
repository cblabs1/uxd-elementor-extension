<?php
/**
 * UXD Admin Page Template
 */

if (!defined('ABSPATH')) {
    exit;
}

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
                                        <td><strong><?php esc_html_e('Gallery Grid', 'uxd-elementor-extension'); ?></strong></td>
                                        <td><?php esc_html_e('Display images in grid or masonry layout', 'uxd-elementor-extension'); ?></td>
                                        <td>
                                            <label class="uxd-switch">
                                                <input type="checkbox" name="enabled_widgets[gallery-grid]" <?php checked($settings['enabled_widgets']['gallery-grid'], 'on'); ?>>
                                                <span class="uxd-slider"></span>
                                            </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong><?php esc_html_e('Product Carousel', 'uxd-elementor-extension'); ?></strong>
                                            <?php if (!uxd_can_use_premium_features()): ?>
                                                <span class="uxd-pro-badge">PRO</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php esc_html_e('WooCommerce product carousel slider', 'uxd-elementor-extension'); ?></td>
                                        <td>
                                            <?php if (uxd_can_use_premium_features()): ?>
                                                <label class="uxd-switch">
                                                    <input type="checkbox" name="enabled_widgets[product-carousel]" <?php checked($settings['enabled_widgets']['product-carousel'], 'on'); ?>>
                                                    <span class="uxd-slider"></span>
                                                </label>
                                            <?php else: ?>
                                                <a href="<?php echo esc_url(uxd_get_upgrade_url()); ?>" class="button button-primary button-small" target="_blank">
                                                    <?php esc_html_e('Upgrade', 'uxd-elementor-extension'); ?>
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong><?php esc_html_e('Product Grid', 'uxd-elementor-extension'); ?></strong>
                                            <?php if (!uxd_can_use_premium_features()): ?>
                                                <span class="uxd-pro-badge">PRO</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php esc_html_e('WooCommerce product grid with filters', 'uxd-elementor-extension'); ?></td>
                                        <td>
                                            <?php if (uxd_can_use_premium_features()): ?>
                                                <label class="uxd-switch">
                                                    <input type="checkbox" name="enabled_widgets[product-grid]" <?php checked($settings['enabled_widgets']['product-grid'], 'on'); ?>>
                                                    <span class="uxd-slider"></span>
                                                </label>
                                            <?php else: ?>
                                                <a href="<?php echo esc_url(uxd_get_upgrade_url()); ?>" class="button button-primary button-small" target="_blank">
                                                    <?php esc_html_e('Upgrade', 'uxd-elementor-extension'); ?>
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong><?php esc_html_e('Taxonomy Accordion', 'uxd-elementor-extension'); ?></strong>
                                            <?php if (!uxd_can_use_premium_features()): ?>
                                                <span class="uxd-pro-badge">PRO</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php esc_html_e('Collapsible taxonomy listings', 'uxd-elementor-extension'); ?></td>
                                        <td>
                                            <?php if (uxd_can_use_premium_features()): ?>
                                                <label class="uxd-switch">
                                                    <input type="checkbox" name="enabled_widgets[taxonomy-accordion]" <?php checked($settings['enabled_widgets']['taxonomy-accordion'], 'on'); ?>>
                                                    <span class="uxd-slider"></span>
                                                </label>
                                            <?php else: ?>
                                                <a href="<?php echo esc_url(uxd_get_upgrade_url()); ?>" class="button button-primary button-small" target="_blank">
                                                    <?php esc_html_e('Upgrade', 'uxd-elementor-extension'); ?>
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong><?php esc_html_e('Post Grid', 'uxd-elementor-extension'); ?></strong>
                                            <?php if (!uxd_can_use_premium_features()): ?>
                                                <span class="uxd-pro-badge">PRO</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php esc_html_e('Advanced blog post grid layouts', 'uxd-elementor-extension'); ?></td>
                                        <td>
                                            <?php if (uxd_can_use_premium_features()): ?>
                                                <label class="uxd-switch">
                                                    <input type="checkbox" name="enabled_widgets[post-grid]" <?php checked($settings['enabled_widgets']['post-grid'], 'on'); ?>>
                                                    <span class="uxd-slider"></span>
                                                </label>
                                            <?php else: ?>
                                                <a href="<?php echo esc_url(uxd_get_upgrade_url()); ?>" class="button button-primary button-small" target="_blank">
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
                        <button type="button" class="button button-reset" id="uxd-reset-settings">
                            <?php esc_html_e('Reset to Default', 'uxd-elementor-extension'); ?>
                        </button>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>