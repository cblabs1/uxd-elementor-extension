<?php
/**
 * Advanced Product Filter Widget - Premium
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once UXD_EE_PLUGIN_PATH . 'includes/class-uxd-locked-widget.php';

class UXD_Advanced_Product_Filter_Widget extends UXD_Locked_Widget_Base {

    public function get_name() {
        return 'uxd-advanced-product-filter';
    }

    public function get_title() {
        return esc_html__('Advanced Product Filter', 'uxd-elementor-extension');
    }

    public function get_icon() {
        return 'eicon-filter';
    }

    public function get_categories() {
        return ['uxd-woocommerce'];
    }

    public function get_keywords() {
        return ['woocommerce', 'products', 'filter', 'search', 'ajax', 'uxd'];
    }

    protected function is_premium_widget() {
        return true;
    }

    protected function get_premium_features_list() {
        return '<ul>
            <li>' . esc_html__('AJAX product filtering', 'uxd-elementor-extension') . '</li>
            <li>' . esc_html__('Price range slider', 'uxd-elementor-extension') . '</li>
            <li>' . esc_html__('Category & tag filters', 'uxd-elementor-extension') . '</li>
            <li>' . esc_html__('Attribute filters', 'uxd-elementor-extension') . '</li>
            <li>' . esc_html__('Stock status filter', 'uxd-elementor-extension') . '</li>
            <li>' . esc_html__('Rating filter', 'uxd-elementor-extension') . '</li>
        </ul>';
    }

    protected function register_controls() {
        // Filter Settings
        $this->start_controls_section(
            'filter_settings',
            [
                'label' => esc_html__('Filter Settings', 'uxd-elementor-extension'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'show_search',
            [
                'label' => esc_html__('Show Search Box', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_price_filter',
            [
                'label' => esc_html__('Show Price Filter', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_category_filter',
            [
                'label' => esc_html__('Show Category Filter', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_rating_filter',
            [
                'label' => esc_html__('Show Rating Filter', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();
    }

    protected function render_widget_content() {
        $settings = $this->get_settings_for_display();
        ?>
        <div class="uxd-advanced-filter">
            <h3><?php esc_html_e('Filter Products', 'uxd-elementor-extension'); ?></h3>
            
            <?php if ($settings['show_search'] === 'yes'): ?>
                <div class="uxd-filter-search">
                    <input type="text" placeholder="<?php esc_attr_e('Search products...', 'uxd-elementor-extension'); ?>">
                </div>
            <?php endif; ?>
            
            <?php if ($settings['show_price_filter'] === 'yes'): ?>
                <div class="uxd-filter-price">
                    <h4><?php esc_html_e('Price Range', 'uxd-elementor-extension'); ?></h4>
                    <div class="uxd-price-slider"></div>
                </div>
            <?php endif; ?>
            
            <?php if ($settings['show_category_filter'] === 'yes'): ?>
                <div class="uxd-filter-categories">
                    <h4><?php esc_html_e('Categories', 'uxd-elementor-extension'); ?></h4>
                    <!-- Category checkboxes would be here -->
                </div>
            <?php endif; ?>
            
            <?php if ($settings['show_rating_filter'] === 'yes'): ?>
                <div class="uxd-filter-rating">
                    <h4><?php esc_html_e('Customer Rating', 'uxd-elementor-extension'); ?></h4>
                    <!-- Rating options would be here -->
                </div>
            <?php endif; ?>
        </div>
        <?php
    }
}