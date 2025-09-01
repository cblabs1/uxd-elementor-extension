<?php
/**
 * UXD Product Grid Widget
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Product Grid Widget Class
 */
class UXD_Product_Grid_Widget extends \Elementor\Widget_Base {
    
    /**
     * Get widget name.
     */
    public function get_name() {
        return 'uxd-product-grid';
    }
    
    /**
     * Get widget title.
     */
    public function get_title() {
        return esc_html__('UXD Product Grid', 'uxd-elementor-extension');
    }
    
    /**
     * Get widget icon.
     */
    public function get_icon() {
        return 'eicon-products';
    }
    
    /**
     * Get widget categories.
     */
    public function get_categories() {
        return ['uxd-woocommerce'];
    }
    
    /**
     * Get widget keywords.
     */
    public function get_keywords() {
        return ['woocommerce', 'products', 'grid', 'shop', 'uxd'];
    }
    
    /**
     * Register widget controls.
     */
    protected function register_controls() {
        
        // Content Tab - Query Settings
        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__('Query', 'uxd-elementor-extension'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        
        $this->add_control(
            'products_count',
            [
                'label' => esc_html__('Products Count', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 12,
                'min' => 1,
                'max' => 100,
            ]
        );
        
        $this->add_control(
            'orderby',
            [
                'label' => esc_html__('Order By', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'date',
                'options' => [
                    'date' => esc_html__('Date', 'uxd-elementor-extension'),
                    'title' => esc_html__('Title', 'uxd-elementor-extension'),
                    'price' => esc_html__('Price', 'uxd-elementor-extension'),
                    'popularity' => esc_html__('Popularity', 'uxd-elementor-extension'),
                    'rating' => esc_html__('Rating', 'uxd-elementor-extension'),
                    'menu_order' => esc_html__('Menu Order', 'uxd-elementor-extension'),
                    'rand' => esc_html__('Random', 'uxd-elementor-extension'),
                ],
            ]
        );
        
        $this->add_control(
            'order',
            [
                'label' => esc_html__('Order', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'DESC',
                'options' => [
                    'ASC' => esc_html__('Ascending', 'uxd-elementor-extension'),
                    'DESC' => esc_html__('Descending', 'uxd-elementor-extension'),
                ],
            ]
        );
        
        $this->add_control(
            'product_categories',
            [
                'label' => esc_html__('Categories', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => $this->get_product_categories(),
                'label_block' => true,
            ]
        );
        
        $this->add_control(
            'product_type',
            [
                'label' => esc_html__('Product Type', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '',
                'options' => [
                    '' => esc_html__('All Products', 'uxd-elementor-extension'),
                    'featured' => esc_html__('Featured Products', 'uxd-elementor-extension'),
                    'on_sale' => esc_html__('On Sale Products', 'uxd-elementor-extension'),
                    'top_rated' => esc_html__('Top Rated Products', 'uxd-elementor-extension'),
                ],
            ]
        );

       $this->add_control(
            'auto_detect_archive',
            [
                'label' => esc_html__('Auto-Detect Archive Page', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'no',
                'description' => esc_html__('Automatically show products from current category/archive page', 'uxd-elementor-extension'),
            ]
        );

        $this->add_control(
            'archive_fallback_behavior',
            [
                'label' => esc_html__('Fallback Behavior', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'all_products',
                'options' => [
                    'all_products' => esc_html__('Show All Products', 'uxd-elementor-extension'),
                    'hide_widget' => esc_html__('Hide Widget', 'uxd-elementor-extension'),
                    'show_message' => esc_html__('Show Custom Message', 'uxd-elementor-extension'),
                ],
                'condition' => [
                    'auto_detect_archive' => 'yes',
                ],
                'description' => esc_html__('What to do when not on a category/archive page', 'uxd-elementor-extension'),
            ]
        );

        $this->add_control(
            'archive_fallback_message',
            [
                'label' => esc_html__('Fallback Message', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => esc_html__('This widget displays products from the current category. Please visit a product category page.', 'uxd-elementor-extension'),
                'condition' => [
                    'auto_detect_archive' => 'yes',
                    'archive_fallback_behavior' => 'show_message',
                ],
            ]
        );

        $this->add_control(
            'show_archive_info',
            [
                'label' => esc_html__('Show Archive Info', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
                'description' => esc_html__('Display the current category/archive information above the products', 'uxd-elementor-extension'),
                'condition' => [
                    'auto_detect_archive' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'archive_info_note',
            [
                'type' => \Elementor\Controls_Manager::RAW_HTML,
                'raw' => '<div style="background: #e8f4fd; padding: 15px; border-radius: 5px; margin-top: 10px;">
                    <h4 style="margin: 0 0 10px 0; color: #007cba;">📋 Archive Detection Info</h4>
                    <p style="margin: 0; font-size: 13px; line-height: 1.5;">
                        <strong>Supported Pages:</strong><br>
                        • Product Category Pages<br>
                        • Product Tag Pages<br>
                        • Custom Product Taxonomy Pages<br>
                        • Shop Page (shows all products)<br>
                        • Product Search Results<br><br>
                        <strong>Note:</strong> When enabled, manual category selection will be ignored on archive pages.
                    </p>
                </div>',
                'condition' => [
                    'auto_detect_archive' => 'yes',
                ],
            ]
        );
        $this->add_control(
            'exclude_out_of_stock',
            [
                'label' => esc_html__('Exclude Out of Stock', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );
        
        $this->end_controls_section();

        // Layout Settings
        $this->start_controls_section(
            'layout_section',
            [
                'label' => esc_html__('Layout', 'uxd-elementor-extension'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        
        $this->add_responsive_control(
            'columns',
            [
                'label' => esc_html__('Columns', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '4',
                'tablet_default' => '2',
                'mobile_default' => '1',
                'options' => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                    '6' => '6',
                ],
                'selectors' => [
                    '{{WRAPPER}} .uxd-products-grid' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'column_gap',
            [
                'label' => esc_html__('Column Gap', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 10,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 30,
                ],
                'tablet_default' => [
                    'unit' => 'px',
                    'size' => 20,
                ],
                'mobile_default' => [
                    'unit' => 'px',
                    'size' => 15,
                ],
                'selectors' => [
                    '{{WRAPPER}} .uxd-products-grid' => 'column-gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'row_gap',
            [
                'label' => esc_html__('Row Gap', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 10,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 30,
                ],
                'tablet_default' => [
                    'unit' => 'px',
                    'size' => 20,
                ],
                'mobile_default' => [
                    'unit' => 'px',
                    'size' => 15,
                ],
                'selectors' => [
                    '{{WRAPPER}} .uxd-products-grid' => 'row-gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        
        $this->add_control(
            'equal_height',
            [
                'label' => esc_html__('Equal Height Items', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
                'description' => esc_html__('Make all product items the same height', 'uxd-elementor-extension'),
            ]
        );
        
        $this->end_controls_section();
        
        // Product Elements
        $this->start_controls_section(
            'product_elements',
            [
                'label' => esc_html__('Product Elements', 'uxd-elementor-extension'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        
        $this->add_control(
            'show_image',
            [
                'label' => esc_html__('Show Image', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );
        
        $this->add_control(
            'show_title',
            [
                'label' => esc_html__('Show Title', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );
        
        $this->add_control(
            'title_tag',
            [
                'label' => esc_html__('Title HTML Tag', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'h3',
                'options' => [
                    'h1' => 'H1',
                    'h2' => 'H2',
                    'h3' => 'H3',
                    'h4' => 'H4',
                    'h5' => 'H5',
                    'h6' => 'H6',
                    'div' => 'div',
                    'span' => 'span',
                ],
                'condition' => [
                    'show_title' => 'yes',
                ],
            ]
        );
        
        $this->add_control(
            'show_price',
            [
                'label' => esc_html__('Show Price', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );
        
        $this->add_control(
            'show_rating',
            [
                'label' => esc_html__('Show Rating', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );
        
        $this->add_control(
            'show_excerpt',
            [
                'label' => esc_html__('Show Excerpt', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );
        
        $this->add_control(
            'excerpt_length',
            [
                'label' => esc_html__('Excerpt Length', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 20,
                'condition' => [
                    'show_excerpt' => 'yes',
                ],
            ]
        );
        
        $this->add_control(
            'show_category',
            [
                'label' => esc_html__('Show Category', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );
        
        $this->add_control(
            'show_add_to_cart',
            [
                'label' => esc_html__('Show Add to Cart', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );
        
        $this->add_control(
            'show_quick_view',
            [
                'label' => esc_html__('Show Quick View', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );
        
        $this->add_control(
            'show_wishlist',
            [
                'label' => esc_html__('Show Wishlist', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );
        
        $this->end_controls_section();

        // Pagination Settings
        $this->start_controls_section(
            'pagination_section',
            [
                'label' => esc_html__('Pagination', 'uxd-elementor-extension'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        
        $this->add_control(
            'show_pagination',
            [
                'label' => esc_html__('Show Pagination', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );
        
        $this->add_control(
            'pagination_type',
            [
                'label' => esc_html__('Pagination Type', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'numbers',
                'options' => [
                    'numbers' => esc_html__('Numbers', 'uxd-elementor-extension'),
                    'prev_next' => esc_html__('Previous/Next', 'uxd-elementor-extension'),
                    'numbers_and_prev_next' => esc_html__('Numbers + Previous/Next', 'uxd-elementor-extension'),
                ],
                'condition' => [
                    'show_pagination' => 'yes',
                ],
            ]
        );
        
        $this->add_control(
            'pagination_alignment',
            [
                'label' => esc_html__('Alignment', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'flex-start' => [
                        'title' => esc_html__('Left', 'uxd-elementor-extension'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'uxd-elementor-extension'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'flex-end' => [
                        'title' => esc_html__('Right', 'uxd-elementor-extension'),
                        'icon' => 'eicon-text-align-right',
                    ],
                    'space-between' => [
                        'title' => esc_html__('Space Between', 'uxd-elementor-extension'),
                        'icon' => 'eicon-justify-space-between-h',
                    ],
                    'space-around' => [
                        'title' => esc_html__('Space Around', 'uxd-elementor-extension'),
                        'icon' => 'eicon-justify-space-around-h',
                    ],
                    'space-evenly' => [
                        'title' => esc_html__('Space Evenly', 'uxd-elementor-extension'),
                        'icon' => 'eicon-justify-space-evenly-h',
                    ],
                ],
                'default' => 'center',
                'selectors' => [
                    '{{WRAPPER}} .uxd-pagination .page-numbers' => 'justify-content: {{VALUE}};',
                ],
                'condition' => [
                    'show_pagination' => 'yes',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'pagination_spacing',
            [
                'label' => esc_html__('Items Spacing', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 5,
                ],
                'selectors' => [
                    '{{WRAPPER}} .uxd-pagination .page-numbers' => 'gap: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'show_pagination' => 'yes',
                ],
            ]
        );
        
        $this->end_controls_section();
        
        // Wishlist Settings
        $this->start_controls_section(
            'wishlist_settings',
            [
                'label' => esc_html__('Wishlist Settings', 'uxd-elementor-extension'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
                'condition' => [
                    'show_wishlist' => 'yes',
                ],
            ]
        );
        
        $this->add_control(
            'wishlist_page_info',
            [
                'type' => \Elementor\Controls_Manager::RAW_HTML,
                'raw' => '<div style="background: #e8f4fd; padding: 15px; border-radius: 5px; margin-bottom: 15px;">
                    <h4 style="margin: 0 0 10px 0; color: #007cba;">🔍 Wishlist Display Information</h4>
                    <p style="margin: 0; font-size: 13px; line-height: 1.5;">
                        <strong>Where wishlists are stored:</strong><br>
                        • <strong>Logged-in users:</strong> Saved in user profile (persistent across sessions)<br>
                        • <strong>Guest users:</strong> Saved in browser session (lost on browser close)<br><br>
                        
                        <strong>How to display wishlist:</strong><br>
                        • Use the shortcode <code>[uxd_wishlist]</code> on any page/post<br>
                        • Create a "Wishlist" page and add the shortcode<br>
                        • Or use the custom link option below
                    </p>
                </div>',
            ]
        );
        
        $this->add_control(
            'custom_wishlist_url',
            [
                'label' => esc_html__('Custom Wishlist Page URL', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::URL,
                'placeholder' => esc_html__('https://yoursite.com/wishlist', 'uxd-elementor-extension'),
                'description' => esc_html__('Leave empty to use default behavior. If set, wishlist icon will link to this URL instead of adding to wishlist.', 'uxd-elementor-extension'),
                'default' => [
                    'url' => '',
                    'is_external' => false,
                    'nofollow' => false,
                ],
            ]
        );
        
        $this->add_control(
            'wishlist_behavior',
            [
                'label' => esc_html__('Wishlist Button Behavior', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'toggle',
                'options' => [
                    'toggle' => esc_html__('Add/Remove from Wishlist (Default)', 'uxd-elementor-extension'),
                    'link' => esc_html__('Link to Wishlist Page', 'uxd-elementor-extension'),
                ],
            ]
        );
        
        $this->end_controls_section();

        // Style Tab - Product Item
        $this->start_controls_section(
            'product_item_style',
            [
                'label' => esc_html__('Product Item', 'uxd-elementor-extension'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_control(
            'content_alignment',
            [
                'label' => esc_html__('Content Alignment', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left', 'uxd-elementor-extension'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'uxd-elementor-extension'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__('Right', 'uxd-elementor-extension'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'center',
                'selectors' => [
                    '{{WRAPPER}} .uxd-product-content' => 'text-align: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_control(
            'product_item_background',
            [
                'label' => esc_html__('Background Color', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .uxd-product-item' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'product_item_border',
                'selector' => '{{WRAPPER}} .uxd-product-item',
            ]
        );
        
        $this->add_responsive_control(
            'product_item_border_radius',
            [
                'label' => esc_html__('Border Radius', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .uxd-product-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'product_item_box_shadow',
                'selector' => '{{WRAPPER}} .uxd-product-item',
            ]
        );
        
        $this->add_responsive_control(
            'product_item_padding',
            [
                'label' => esc_html__('Padding', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .uxd-product-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        
        $this->add_control(
            'product_item_hover_effects',
            [
                'label' => esc_html__('Hover Effects', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );
        
        $this->end_controls_section();
        
        // Style Tab - Product Image
        $this->start_controls_section(
            'image_style',
            [
                'label' => esc_html__('Product Image', 'uxd-elementor-extension'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_image' => 'yes',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'image_width',
            [
                'label' => esc_html__('Width', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 50,
                        'max' => 500,
                    ],
                    '%' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => '%',
                    'size' => 100,
                ],
                'selectors' => [
                    '{{WRAPPER}} .uxd-product-image img' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'image_height',
            [
                'label' => esc_html__('Height', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'vh'],
                'range' => [
                    'px' => [
                        'min' => 100,
                        'max' => 600,
                    ],
                    '%' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                    'vh' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 250,
                ],
                'selectors' => [
                    '{{WRAPPER}} .uxd-product-image img' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        
        $this->add_control(
            'image_object_fit',
            [
                'label' => esc_html__('Object Fit', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'cover',
                'options' => [
                    'fill' => esc_html__('Fill', 'uxd-elementor-extension'),
                    'contain' => esc_html__('Contain', 'uxd-elementor-extension'),
                    'cover' => esc_html__('Cover', 'uxd-elementor-extension'),
                    'none' => esc_html__('None', 'uxd-elementor-extension'),
                    'scale-down' => esc_html__('Scale Down', 'uxd-elementor-extension'),
                ],
                'selectors' => [
                    '{{WRAPPER}} .uxd-product-image img' => 'object-fit: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_control(
            'image_object_position',
            [
                'label' => esc_html__('Object Position', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'center center',
                'options' => [
                    'center center' => esc_html__('Center Center', 'uxd-elementor-extension'),
                    'center top' => esc_html__('Center Top', 'uxd-elementor-extension'),
                    'center bottom' => esc_html__('Center Bottom', 'uxd-elementor-extension'),
                    'left top' => esc_html__('Left Top', 'uxd-elementor-extension'),
                    'left center' => esc_html__('Left Center', 'uxd-elementor-extension'),
                    'left bottom' => esc_html__('Left Bottom', 'uxd-elementor-extension'),
                    'right top' => esc_html__('Right Top', 'uxd-elementor-extension'),
                    'right center' => esc_html__('Right Center', 'uxd-elementor-extension'),
                    'right bottom' => esc_html__('Right Bottom', 'uxd-elementor-extension'),
                ],
                'selectors' => [
                    '{{WRAPPER}} .uxd-product-image img' => 'object-position: {{VALUE}};',
                ],
                'condition' => [
                    'image_object_fit!' => 'fill',
                ],
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'image_border',
                'selector' => '{{WRAPPER}} .uxd-product-image img',
            ]
        );
        
        $this->add_responsive_control(
            'image_border_radius',
            [
                'label' => esc_html__('Border Radius', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .uxd-product-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'image_box_shadow',
                'selector' => '{{WRAPPER}} .uxd-product-image img',
            ]
        );
        
        $this->add_control(
            'image_hover_animation',
            [
                'label' => esc_html__('Hover Animation', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'scale',
                'options' => [
                    'none' => esc_html__('None', 'uxd-elementor-extension'),
                    'scale' => esc_html__('Scale', 'uxd-elementor-extension'),
                    'rotate' => esc_html__('Rotate', 'uxd-elementor-extension'),
                    'blur' => esc_html__('Blur', 'uxd-elementor-extension'),
                    'grayscale' => esc_html__('Grayscale', 'uxd-elementor-extension'),
                ],
            ]
        );
        
        $this->end_controls_section();
        
        // Product Title Style
        $this->start_controls_section(
            'title_style',
            [
                'label' => esc_html__('Product Title', 'uxd-elementor-extension'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_title' => 'yes',
                ],
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .uxd-product-title, {{WRAPPER}} .uxd-product-title a',
            ]
        );
        
        $this->add_control(
            'title_color',
            [
                'label' => esc_html__('Color', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .uxd-product-title a' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_control(
            'title_hover_color',
            [
                'label' => esc_html__('Hover Color', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .uxd-product-title a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'title_margin',
            [
                'label' => esc_html__('Margin', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .uxd-product-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'title_padding',
            [
                'label' => esc_html__('Padding', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .uxd-product-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        
        $this->end_controls_section();
        
        // Product Price Style
        $this->start_controls_section(
            'price_style',
            [
                'label' => esc_html__('Product Price', 'uxd-elementor-extension'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_price' => 'yes',
                ],
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'price_typography',
                'selector' => '{{WRAPPER}} .uxd-product-price, {{WRAPPER}} .uxd-product-price .price',
            ]
        );
        
        $this->add_control(
            'price_color',
            [
                'label' => esc_html__('Color', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .uxd-product-price .price' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_control(
            'sale_price_color',
            [
                'label' => esc_html__('Sale Price Color', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .uxd-product-price .price ins' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_control(
            'regular_price_color',
            [
                'label' => esc_html__('Regular Price Color', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .uxd-product-price .price del' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'price_margin',
            [
                'label' => esc_html__('Margin', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .uxd-product-price' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        
        $this->end_controls_section();

        // Add to Cart Button Style
        $this->start_controls_section(
            'cart_button_style',
            [
                'label' => esc_html__('Add to Cart Button', 'uxd-elementor-extension'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_add_to_cart' => 'yes',
                ],
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'cart_button_typography',
                'selector' => '{{WRAPPER}} .uxd-add-to-cart .button',
            ]
        );
        
        $this->add_control(
            'cart_button_width',
            [
                'label' => esc_html__('Width', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'full',
                'options' => [
                    'auto' => esc_html__('Auto', 'uxd-elementor-extension'),
                    'full' => esc_html__('Full Width', 'uxd-elementor-extension'),
                ],
                'selectors' => [
                    '{{WRAPPER}} .uxd-add-to-cart .button' => 'width: {{VALUE}};',
                ],
                'selectors_dictionary' => [
                    'auto' => 'auto',
                    'full' => '100%',
                ],
            ]
        );
        
        $this->add_control(
            'cart_button_align',
            [
                'label' => esc_html__('Alignment', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left', 'uxd-elementor-extension'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'uxd-elementor-extension'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__('Right', 'uxd-elementor-extension'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'center',
                'selectors' => [
                    '{{WRAPPER}} .uxd-add-to-cart' => 'text-align: {{VALUE}};',
                ],
                'condition' => [
                    'cart_button_width' => 'auto',
                ],
            ]
        );
        
        $this->start_controls_tabs('cart_button_tabs');
        
        $this->start_controls_tab(
            'cart_button_normal',
            [
                'label' => esc_html__('Normal', 'uxd-elementor-extension'),
            ]
        );
        
        $this->add_control(
            'cart_button_color',
            [
                'label' => esc_html__('Text Color', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .uxd-add-to-cart .button' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_control(
            'cart_button_background',
            [
                'label' => esc_html__('Background Color', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .uxd-add-to-cart .button' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'cart_button_border',
                'selector' => '{{WRAPPER}} .uxd-add-to-cart .button',
            ]
        );
        
        $this->end_controls_tab();
        
        $this->start_controls_tab(
            'cart_button_hover',
            [
                'label' => esc_html__('Hover', 'uxd-elementor-extension'),
            ]
        );
        
        $this->add_control(
            'cart_button_hover_color',
            [
                'label' => esc_html__('Text Color', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .uxd-add-to-cart .button:hover' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_control(
            'cart_button_hover_background',
            [
                'label' => esc_html__('Background Color', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .uxd-add-to-cart .button:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_control(
            'cart_button_hover_border_color',
            [
                'label' => esc_html__('Border Color', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .uxd-add-to-cart .button:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );
        
        $this->end_controls_tab();
        
        $this->end_controls_tabs();
        
        $this->add_responsive_control(
            'cart_button_padding',
            [
                'label' => esc_html__('Padding', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .uxd-add-to-cart .button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'cart_button_margin',
            [
                'label' => esc_html__('Margin', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .uxd-add-to-cart' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'cart_button_border_radius',
            [
                'label' => esc_html__('Border Radius', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .uxd-add-to-cart .button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        
        $this->end_controls_section();

        // Quick View & Wishlist Style
        $this->start_controls_section(
            'action_buttons_style',
            [
                'label' => esc_html__('Action Buttons', 'uxd-elementor-extension'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'show_quick_view',
                            'operator' => '===',
                            'value' => 'yes',
                        ],
                        [
                            'name' => 'show_wishlist',
                            'operator' => '===',
                            'value' => 'yes',
                        ],
                    ],
                ],
            ]
        );
        
        $this->add_control(
            'action_buttons_position',
            [
                'label' => esc_html__('Position', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'top-right',
                'options' => [
                    'top-left' => esc_html__('Top Left', 'uxd-elementor-extension'),
                    'top-right' => esc_html__('Top Right', 'uxd-elementor-extension'),
                    'bottom-left' => esc_html__('Bottom Left', 'uxd-elementor-extension'),
                    'bottom-right' => esc_html__('Bottom Right', 'uxd-elementor-extension'),
                    'center' => esc_html__('Center', 'uxd-elementor-extension'),
                ],
            ]
        );
        
        $this->add_responsive_control(
            'action_buttons_size',
            [
                'label' => esc_html__('Button Size', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 25,
                        'max' => 60,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 35,
                ],
                'selectors' => [
                    '{{WRAPPER}} .uxd-quick-view, {{WRAPPER}} .uxd-wishlist' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        
        $this->add_control(
            'action_buttons_spacing',
            [
                'label' => esc_html__('Spacing Between Buttons', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 20,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 5,
                ],
                'selectors' => [
                    '{{WRAPPER}} .uxd-product-actions' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        
        $this->add_control(
            'quick_view_icon',
            [
                'label' => esc_html__('Quick View Icon', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::ICONS,
                'default' => [
                    'value' => 'fas fa-eye',
                    'library' => 'fa-solid',
                ],
                'condition' => [
                    'show_quick_view' => 'yes',
                ],
            ]
        );
        
        $this->add_control(
            'wishlist_icon',
            [
                'label' => esc_html__('Wishlist Icon', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::ICONS,
                'default' => [
                    'value' => 'fas fa-heart',
                    'library' => 'fa-solid',
                ],
                'condition' => [
                    'show_wishlist' => 'yes',
                ],
            ]
        );
        
        $this->start_controls_tabs('action_buttons_tabs');
        
        $this->start_controls_tab(
            'action_buttons_normal',
            [
                'label' => esc_html__('Normal', 'uxd-elementor-extension'),
            ]
        );
        
        $this->add_control(
            'action_buttons_color',
            [
                'label' => esc_html__('Icon Color', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#333',
                'selectors' => [
                    '{{WRAPPER}} .uxd-quick-view, {{WRAPPER}} .uxd-wishlist' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_control(
            'action_buttons_background',
            [
                'label' => esc_html__('Background Color', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => 'rgba(255, 255, 255, 0.9)',
                'selectors' => [
                    '{{WRAPPER}} .uxd-quick-view, {{WRAPPER}} .uxd-wishlist' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        
        $this->end_controls_tab();
        
        $this->start_controls_tab(
            'action_buttons_hover',
            [
                'label' => esc_html__('Hover', 'uxd-elementor-extension'),
            ]
        );
        
        $this->add_control(
            'action_buttons_hover_color',
            [
                'label' => esc_html__('Icon Color', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#fff',
                'selectors' => [
                    '{{WRAPPER}} .uxd-quick-view:hover, {{WRAPPER}} .uxd-wishlist:hover' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_control(
            'action_buttons_hover_background',
            [
                'label' => esc_html__('Background Color', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#007cba',
                'selectors' => [
                    '{{WRAPPER}} .uxd-quick-view:hover, {{WRAPPER}} .uxd-wishlist:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        
        $this->end_controls_tab();
        
        $this->end_controls_tabs();
        
        $this->add_control(
            'wishlist_active_color',
            [
                'label' => esc_html__('Wishlist Active Color', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#e74c3c',
                'selectors' => [
                    '{{WRAPPER}} .uxd-wishlist.active' => 'color: {{VALUE}};',
                ],
                'separator' => 'before',
            ]
        );
        
        $this->end_controls_section();
    }

    /**
     * Render widget output on the frontend.
     */
    protected function render() {
        $settings = $this->get_settings_for_display();
        
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        
        // Check if auto-detect archive is enabled and we're on an archive page
        $is_archive_mode = $settings['auto_detect_archive'] === 'yes';
        $current_archive_info = $this->get_current_archive_info();
        
        if ($is_archive_mode) {
            // Handle archive detection
            if (!$current_archive_info['is_archive']) {
                // Not on an archive page, handle fallback
                switch ($settings['archive_fallback_behavior']) {
                    case 'hide_widget':
                        return; // Don't render anything
                    
                    case 'show_message':
                        ?>
                        <div class="uxd-archive-fallback-message">
                            <p><?php echo esc_html($settings['archive_fallback_message']); ?></p>
                        </div>
                        <?php
                        return;
                    
                    case 'all_products':
                    default:
                        // Continue with normal query (show all products)
                        break;
                }
            }
        }
        
        $query_args = [
            'post_type' => 'product',
            'post_status' => 'publish',
            'posts_per_page' => $settings['products_count'],
            'orderby' => $settings['orderby'],
            'order' => $settings['order'],
            'paged' => $paged,
            'meta_query' => WC()->query->get_meta_query(),
        ];
        
        // Handle archive mode queries
        if ($is_archive_mode && $current_archive_info['is_archive']) {
            // Override query with archive-specific parameters
            $query_args = array_merge($query_args, $current_archive_info['query_vars']);
            
            // For search pages, add search query
            if ($current_archive_info['archive_type'] === 'search' && !empty($current_archive_info['search_query'])) {
                $query_args['s'] = $current_archive_info['search_query'];
            }
        } else {
            // Normal mode - use manual settings
            
            // Handle product type filtering
            if (!empty($settings['product_type'])) {
                switch ($settings['product_type']) {
                    case 'featured':
                        $query_args['meta_query'][] = [
                            'key' => '_featured',
                            'value' => 'yes',
                        ];
                        break;
                    case 'on_sale':
                        $query_args['meta_query'][] = [
                            'key' => '_sale_price',
                            'value' => '',
                            'compare' => '!=',
                        ];
                        break;
                    case 'top_rated':
                        $query_args['meta_key'] = '_wc_average_rating';
                        $query_args['orderby'] = 'meta_value_num';
                        $query_args['order'] = 'DESC';
                        break;
                }
            }
            
            // Handle category filtering (only in manual mode)
            if (!empty($settings['product_categories'])) {
                $query_args['tax_query'] = [
                    [
                        'taxonomy' => 'product_cat',
                        'field' => 'term_id',
                        'terms' => $settings['product_categories'],
                    ],
                ];
            }
        }
        
        // Exclude out of stock products (applies to both modes)
        if ($settings['exclude_out_of_stock'] === 'yes') {
            $query_args['meta_query'][] = [
                'key' => '_stock_status',
                'value' => 'instock',
            ];
        }
        
        $products = new WP_Query($query_args);
        
        if ($products->have_posts()) {
            $grid_classes = ['uxd-products-grid'];
            $content_class = 'text-' . $settings['content_alignment'];
            
            if ($settings['equal_height'] === 'yes') {
                $grid_classes[] = 'uxd-equal-height';
            }
            
            if ($settings['product_item_hover_effects'] === 'yes') {
                $grid_classes[] = 'uxd-hover-effects';
            }
            
            if (!empty($settings['image_hover_animation'])) {
                $grid_classes[] = 'uxd-image-hover-' . $settings['image_hover_animation'];
            }
            
            // Add archive mode class
            if ($is_archive_mode && $current_archive_info['is_archive']) {
                $grid_classes[] = 'uxd-archive-mode';
                $grid_classes[] = 'uxd-archive-' . $current_archive_info['archive_type'];
            }
            ?>
            <div class="uxd-products-grid-wrapper">
                <?php if ($is_archive_mode && $current_archive_info['is_archive'] && $settings['show_archive_info'] === 'yes') : ?>
                    <div class="uxd-archive-info">
                        <span class="uxd-archive-label">
                            <?php 
                            switch ($current_archive_info['archive_type']) {
                                case 'category':
                                    printf(esc_html__('Category: %s', 'uxd-elementor-extension'), esc_html($current_archive_info['title']));
                                    break;
                                case 'tag':
                                    printf(esc_html__('Tag: %s', 'uxd-elementor-extension'), esc_html($current_archive_info['title']));
                                    break;
                                case 'taxonomy':
                                    printf(esc_html__('%s: %s', 'uxd-elementor-extension'), 
                                        esc_html($current_archive_info['taxonomy_label']), 
                                        esc_html($current_archive_info['title'])
                                    );
                                    break;
                                case 'shop':
                                    esc_html_e('All Products', 'uxd-elementor-extension');
                                    break;
                                case 'search':
                                    printf(esc_html__('Search Results for: "%s"', 'uxd-elementor-extension'), esc_html($current_archive_info['search_query']));
                                    break;
                            }
                            ?>
                            <span class="uxd-products-count">(<?php echo $products->found_posts; ?> <?php esc_html_e('products', 'uxd-elementor-extension'); ?>)</span>
                        </span>
                    </div>
                <?php endif; ?>
                
                <div class="<?php echo esc_attr(implode(' ', $grid_classes)); ?>">
                    <?php while ($products->have_posts()) : $products->the_post(); ?>
                        <?php global $product; ?>
                        <div class="uxd-product-item">
                            <?php if ($settings['show_image'] === 'yes') : ?>
                                <div class="uxd-product-image">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php echo woocommerce_get_product_thumbnail(); ?>
                                    </a>
                                    
                                    <?php if ($product->is_on_sale()) : ?>
                                        <span class="onsale"><?php esc_html_e('Sale!', 'uxd-elementor-extension'); ?></span>
                                    <?php endif; ?>
                                    
                                    <?php if ($settings['show_quick_view'] === 'yes' || $settings['show_wishlist'] === 'yes') : ?>
                                        <div class="uxd-product-actions uxd-position-<?php echo esc_attr($settings['action_buttons_position']); ?>">
                                            <?php if ($settings['show_quick_view'] === 'yes') : ?>
                                                <button class="uxd-quick-view" data-product-id="<?php echo esc_attr(get_the_ID()); ?>" title="<?php esc_attr_e('Quick View', 'uxd-elementor-extension'); ?>">
                                                    <?php 
                                                    if (!empty($settings['quick_view_icon']['value'])) {
                                                        \Elementor\Icons_Manager::render_icon($settings['quick_view_icon'], ['aria-hidden' => 'true']);
                                                    } else {
                                                        // Fallback SVG
                                                        echo '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>';
                                                    }
                                                    ?>
                                                </button>
                                            <?php endif; ?>
                                            
                                            <?php if ($settings['show_wishlist'] === 'yes') : ?>
                                                <?php 
                                                $wishlist_behavior = $settings['wishlist_behavior'];
                                                $custom_url = !empty($settings['custom_wishlist_url']['url']) ? $settings['custom_wishlist_url']['url'] : '';
                                                
                                                if ($wishlist_behavior === 'link' && $custom_url) {
                                                    $target = $settings['custom_wishlist_url']['is_external'] ? '_blank' : '_self';
                                                    $nofollow = $settings['custom_wishlist_url']['nofollow'] ? 'nofollow' : '';
                                                    ?>
                                                    <a href="<?php echo esc_url($custom_url); ?>" target="<?php echo esc_attr($target); ?>" rel="<?php echo esc_attr($nofollow); ?>" class="uxd-wishlist uxd-wishlist-link" title="<?php esc_attr_e('View Wishlist', 'uxd-elementor-extension'); ?>">
                                                        <?php 
                                                        if (!empty($settings['wishlist_icon']['value'])) {
                                                            \Elementor\Icons_Manager::render_icon($settings['wishlist_icon'], ['aria-hidden' => 'true']);
                                                        } else {
                                                            // Fallback SVG
                                                            echo '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>';
                                                        }
                                                        ?>
                                                    </a>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <button class="uxd-wishlist" data-product-id="<?php echo esc_attr(get_the_ID()); ?>" title="<?php esc_attr_e('Add to Wishlist', 'uxd-elementor-extension'); ?>">
                                                        <?php 
                                                        if (!empty($settings['wishlist_icon']['value'])) {
                                                            \Elementor\Icons_Manager::render_icon($settings['wishlist_icon'], ['aria-hidden' => 'true']);
                                                        } else {
                                                            // Fallback SVG
                                                            echo '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>';
                                                        }
                                                        ?>
                                                    </button>
                                                    <?php
                                                }
                                                ?>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="uxd-product-content <?php echo esc_attr($content_class); ?>">
                                <?php if ($settings['show_category'] === 'yes') : ?>
                                    <div class="uxd-product-category">
                                        <?php echo wc_get_product_category_list(get_the_ID(), ', '); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($settings['show_title'] === 'yes') : ?>
                                    <<?php echo esc_attr($settings['title_tag']); ?> class="uxd-product-title">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </<?php echo esc_attr($settings['title_tag']); ?>>
                                <?php endif; ?>
                                
                                <?php if ($settings['show_rating'] === 'yes') : ?>
                                    <div class="uxd-product-rating">
                                        <?php woocommerce_template_loop_rating(); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($settings['show_price'] === 'yes') : ?>
                                    <div class="uxd-product-price">
                                        <?php echo $product->get_price_html(); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($settings['show_excerpt'] === 'yes') : ?>
                                    <div class="uxd-product-excerpt">
                                        <?php 
                                        $excerpt = wp_trim_words(get_the_excerpt(), $settings['excerpt_length'], '...');
                                        echo esc_html($excerpt);
                                        ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($settings['show_add_to_cart'] === 'yes') : ?>
                                    <div class="uxd-add-to-cart">
                                        <?php woocommerce_template_loop_add_to_cart(); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
                
                <?php if ($settings['show_pagination'] === 'yes' && $products->max_num_pages > 1) : ?>
                    <div class="uxd-pagination">
                        <?php
                        $pagination_args = [
                            'total' => $products->max_num_pages,
                            'current' => $paged,
                            'base' => get_pagenum_link(1) . '%_%',
                            'format' => 'page/%#%/',
                            'show_all' => false,
                            'end_size' => 1,
                            'mid_size' => 2,
                            'prev_next' => true,
                            'prev_text' => esc_html__('« Previous', 'uxd-elementor-extension'),
                            'next_text' => esc_html__('Next »', 'uxd-elementor-extension'),
                            'type' => 'list',
                        ];
                        
                        switch ($settings['pagination_type']) {
                            case 'numbers':
                                $pagination_args['prev_next'] = false;
                                break;
                            case 'prev_next':
                                $pagination_args['show_all'] = false;
                                $pagination_args['end_size'] = 0;
                                $pagination_args['mid_size'] = 0;
                                break;
                            case 'numbers_and_prev_next':
                            default:
                                // Keep default settings
                                break;
                        }
                        
                        echo paginate_links($pagination_args);
                        ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php
        } else {
            ?>
            <div class="uxd-no-products">
                <p>
                    <?php 
                    if ($is_archive_mode && $current_archive_info['is_archive']) {
                        switch ($current_archive_info['archive_type']) {
                            case 'search':
                                printf(esc_html__('No products found for "%s".', 'uxd-elementor-extension'), esc_html($current_archive_info['search_query']));
                                break;
                            default:
                                printf(esc_html__('No products found in %s.', 'uxd-elementor-extension'), esc_html($current_archive_info['title']));
                                break;
                        }
                    } else {
                        esc_html_e('No products found.', 'uxd-elementor-extension');
                    }
                    ?>
                </p>
            </div>
            <?php
        }
        
        wp_reset_postdata();
    }

    private function get_current_archive_info() {
        $info = [
            'is_archive' => false,
            'archive_type' => '',
            'title' => '',
            'taxonomy_label' => '',
            'search_query' => '',
            'query_vars' => [],
        ];
        
        // Check for WooCommerce shop page
        if (is_shop()) {
            $info['is_archive'] = true;
            $info['archive_type'] = 'shop';
            $info['title'] = get_the_title(wc_get_page_id('shop'));
            return $info;
        }
        
        // Check for product category
        if (is_product_category()) {
            $term = get_queried_object();
            $info['is_archive'] = true;
            $info['archive_type'] = 'category';
            $info['title'] = $term->name;
            $info['query_vars'] = [
                'tax_query' => [
                    [
                        'taxonomy' => 'product_cat',
                        'field' => 'term_id',
                        'terms' => [$term->term_id],
                    ],
                ],
            ];
            return $info;
        }
        
        // Check for product tag
        if (is_product_tag()) {
            $term = get_queried_object();
            $info['is_archive'] = true;
            $info['archive_type'] = 'tag';
            $info['title'] = $term->name;
            $info['query_vars'] = [
                'tax_query' => [
                    [
                        'taxonomy' => 'product_tag',
                        'field' => 'term_id',
                        'terms' => [$term->term_id],
                    ],
                ],
            ];
            return $info;
        }
        
        // Check for custom product taxonomy
        if (is_tax()) {
            $term = get_queried_object();
            $taxonomy = get_taxonomy($term->taxonomy);
            
            // Check if this taxonomy is related to products
            if ($taxonomy && in_array('product', $taxonomy->object_type)) {
                $info['is_archive'] = true;
                $info['archive_type'] = 'taxonomy';
                $info['title'] = $term->name;
                $info['taxonomy_label'] = $taxonomy->label;
                $info['query_vars'] = [
                    'tax_query' => [
                        [
                            'taxonomy' => $term->taxonomy,
                            'field' => 'term_id',
                            'terms' => [$term->term_id],
                        ],
                    ],
                ];
                return $info;
            }
        }
        
        // Check for product search
        if (is_search() && isset($_GET['post_type']) && $_GET['post_type'] === 'product') {
            $search_query = get_search_query();
            $info['is_archive'] = true;
            $info['archive_type'] = 'search';
            $info['search_query'] = $search_query;
            $info['title'] = sprintf(esc_html__('Search Results for "%s"', 'uxd-elementor-extension'), $search_query);
            $info['query_vars'] = [
                'post_type' => 'product',
            ];
            return $info;
        }
        
        return $info;
    }
    
    /**
     * Get product categories for select control.
     */
    private function get_product_categories() {
        $categories = [];
        $terms = get_terms([
            'taxonomy' => 'product_cat',
            'hide_empty' => true,
        ]);
        
        if (!is_wp_error($terms)) {
            foreach ($terms as $term) {
                $categories[$term->term_id] = $term->name;
            }
        }
        
        return $categories;
    }
}