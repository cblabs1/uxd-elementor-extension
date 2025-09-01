<?php
/**
 * UXD Product Carousel Widget - Part 1: Header and Query Controls
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Product Carousel Widget Class
 */
class UXD_Product_Carousel_Widget extends \Elementor\Widget_Base {
    
    /**
     * Get widget name.
     */
    public function get_name() {
        return 'uxd-product-carousel';
    }
    
    /**
     * Get widget title.
     */
    public function get_title() {
        return esc_html__('UXD Product Carousel', 'uxd-elementor-extension');
    }
    
    /**
     * Get widget icon.
     */
    public function get_icon() {
        return 'eicon-media-carousel';
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
        return ['woocommerce', 'products', 'carousel', 'slider', 'uxd'];
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
                'default' => 8,
                'min' => 1,
                'max' => 50,
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
        
        $this->end_controls_section();

        // Carousel Settings
        $this->start_controls_section(
            'carousel_settings',
            [
                'label' => esc_html__('Carousel Settings', 'uxd-elementor-extension'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        
        $this->add_responsive_control(
            'slides_to_show',
            [
                'label' => esc_html__('Slides to Show', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 4,
                'tablet_default' => 2,
                'mobile_default' => 1,
                'min' => 1,
                'max' => 8,
            ]
        );
        
        $this->add_control(
            'slides_to_scroll',
            [
                'label' => esc_html__('Slides to Scroll', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 1,
                'min' => 1,
                'max' => 4,
            ]
        );
        
        $this->add_responsive_control(
            'space_between',
            [
                'label' => esc_html__('Space Between', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'size' => 30,
                ],
                'tablet_default' => [
                    'size' => 20,
                ],
                'mobile_default' => [
                    'size' => 15,
                ],
            ]
        );
        
        $this->add_control(
            'autoplay',
            [
                'label' => esc_html__('Autoplay', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );
        
        $this->add_control(
            'autoplay_delay',
            [
                'label' => esc_html__('Autoplay Delay (ms)', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 3000,
                'condition' => [
                    'autoplay' => 'yes',
                ],
            ]
        );
        
        $this->add_control(
            'pause_on_hover',
            [
                'label' => esc_html__('Pause on Hover', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
                'condition' => [
                    'autoplay' => 'yes',
                ],
            ]
        );
        
        $this->add_control(
            'loop',
            [
                'label' => esc_html__('Infinite Loop', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );
        
        $this->add_control(
            'speed',
            [
                'label' => esc_html__('Animation Speed (ms)', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 800,
                'min' => 100,
                'max' => 3000,
            ]
        );
        
        $this->add_control(
            'navigation',
            [
                'label' => esc_html__('Navigation Arrows', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );
        
        $this->add_control(
            'pagination',
            [
                'label' => esc_html__('Pagination Dots', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );
        
        $this->add_control(
            'pagination_alignment',
            [
                'label' => esc_html__('Pagination Alignment', 'uxd-elementor-extension'),
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
                ],
                'default' => 'center',
                'selectors' => [
                    '{{WRAPPER}} .swiper-pagination' => 'justify-content: {{VALUE}};',
                ],
                'condition' => [
                    'pagination' => 'yes',
                ],
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
                'default' => 15,
                'condition' => [
                    'show_excerpt' => 'yes',
                ],
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
            'custom_wishlist_url',
            [
                'label' => esc_html__('Custom Wishlist Page URL', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::URL,
                'placeholder' => esc_html__('https://yoursite.com/wishlist', 'uxd-elementor-extension'),
                'description' => esc_html__('Leave empty to use default behavior. If set, wishlist icon will link to this URL.', 'uxd-elementor-extension'),
                'default' => [
                    'url' => '',
                    'is_external' => false,
                    'nofollow' => false,
                ],
                'condition' => [
                    'show_wishlist' => 'yes',
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
                'condition' => [
                    'show_wishlist' => 'yes',
                ],
            ]
        );
        
        $this->end_controls_section();

        // Product Item Style Settings
        $this->start_controls_section(
            'product_item_section',
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
        
        // Navigation Style
        $this->start_controls_section(
            'navigation_style',
            [
                'label' => esc_html__('Navigation', 'uxd-elementor-extension'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'navigation' => 'yes',
                ],
            ]
        );
        
        $this->add_control(
            'navigation_size',
            [
                'label' => esc_html__('Size', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 20,
                        'max' => 80,
                    ],
                ],
                'default' => [
                    'size' => 40,
                ],
                'selectors' => [
                    '{{WRAPPER}} .swiper-button-next, {{WRAPPER}} .swiper-button-prev' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        
        $this->add_control(
            'navigation_color',
            [
                'label' => esc_html__('Color', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .swiper-button-next, {{WRAPPER}} .swiper-button-prev' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_control(
            'navigation_background',
            [
                'label' => esc_html__('Background Color', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .swiper-button-next, {{WRAPPER}} .swiper-button-prev' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_control(
            'navigation_hover_color',
            [
                'label' => esc_html__('Hover Color', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .swiper-button-next:hover, {{WRAPPER}} .swiper-button-prev:hover' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_control(
            'navigation_hover_background',
            [
                'label' => esc_html__('Hover Background Color', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .swiper-button-next:hover, {{WRAPPER}} .swiper-button-prev:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        
        $this->end_controls_section();

        // Pagination Style
        $this->start_controls_section(
            'pagination_style',
            [
                'label' => esc_html__('Pagination', 'uxd-elementor-extension'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'pagination' => 'yes',
                ],
            ]
        );
        
        $this->add_control(
            'pagination_bullet_size',
            [
                'label' => esc_html__('Size', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 5,
                        'max' => 25,
                    ],
                ],
                'default' => [
                    'size' => 12,
                ],
                'selectors' => [
                    '{{WRAPPER}} .swiper-pagination-bullet' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        
        $this->add_control(
            'pagination_bullet_color',
            [
                'label' => esc_html__('Color', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .swiper-pagination-bullet' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_control(
            'pagination_bullet_active_color',
            [
                'label' => esc_html__('Active Color', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .swiper-pagination-bullet-active' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        
        $this->end_controls_section();
    }
    
    /**
     * Render widget output on the frontend.
     */
    protected function render() {
        $settings = $this->get_settings_for_display();
        
        $query_args = [
            'post_type' => 'product',
            'post_status' => 'publish',
            'posts_per_page' => $settings['products_count'],
            'orderby' => $settings['orderby'],
            'order' => $settings['order'],
            'meta_query' => WC()->query->get_meta_query(),
        ];
        
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
        
        // Handle category filtering
        if (!empty($settings['product_categories'])) {
            $query_args['tax_query'] = [
                [
                    'taxonomy' => 'product_cat',
                    'field' => 'term_id',
                    'terms' => $settings['product_categories'],
                ],
            ];
        }
        
        $products = new WP_Query($query_args);
        
        if ($products->have_posts()) {
            $carousel_id = 'uxd-carousel-' . $this->get_id();
            $carousel_settings = [
                'slidesPerView' => $settings['slides_to_show'],
                'slidesPerGroup' => $settings['slides_to_scroll'],
                'spaceBetween' => $settings['space_between']['size'],
                'loop' => $settings['loop'] === 'yes',
                'speed' => $settings['speed'],
                'autoplay' => $settings['autoplay'] === 'yes',
                'autoplayDelay' => $settings['autoplay_delay'],
                'pauseOnHover' => $settings['pause_on_hover'] === 'yes',
                'navigation' => $settings['navigation'] === 'yes',
                'pagination' => $settings['pagination'] === 'yes',
            ];
            
            $content_class = 'text-' . $settings['content_alignment'];
            $hover_effects_class = $settings['product_item_hover_effects'] === 'yes' ? 'uxd-hover-effects' : '';
            ?>
            <div class="uxd-product-carousel-wrapper <?php echo esc_attr($hover_effects_class); ?>">
                <div class="swiper" id="<?php echo esc_attr($carousel_id); ?>" data-settings='<?php echo wp_json_encode($carousel_settings); ?>'>
                    <div class="swiper-wrapper">
                        <?php while ($products->have_posts()) : $products->the_post(); ?>
                            <?php global $product; ?>
                            <div class="swiper-slide">
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
                                        <?php if ($settings['show_title'] === 'yes') : ?>
                                            <h3 class="uxd-product-title">
                                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                            </h3>
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
                            </div>
                        <?php endwhile; ?>
                    </div>
                    
                    <?php if ($settings['navigation'] === 'yes') : ?>
                        <div class="swiper-button-next"></div>
                        <div class="swiper-button-prev"></div>
                    <?php endif; ?>
                    
                    <?php if ($settings['pagination'] === 'yes') : ?>
                        <div class="swiper-pagination"></div>
                    <?php endif; ?>
                </div>
            </div>
            <?php
        }
        
        wp_reset_postdata();
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