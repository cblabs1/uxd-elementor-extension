<?php
/**
 * UXD Gallery Grid Widget - Updated with Masonry Support
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Gallery Grid Widget Class
 */
class UXD_Gallery_Grid_Widget extends \Elementor\Widget_Base {
    
    /**
     * Get widget name.
     */
    public function get_name() {
        return 'uxd-gallery-grid';
    }
    
    /**
     * Get widget title.
     */
    public function get_title() {
        return esc_html__('UXD Gallery Grid', 'uxd-elementor-extension');
    }
    
    /**
     * Get widget icon.
     */
    public function get_icon() {
        return 'eicon-gallery-grid';
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
        return ['gallery', 'images', 'grid', 'masonry', 'lightbox', 'search', 'uxd'];
    }
    
    /**
     * Register widget controls.
     */
    protected function register_controls() {
        
        // Content Tab - Gallery Settings
        $this->start_controls_section(
            'gallery_content',
            [
                'label' => esc_html__('Gallery Images', 'uxd-elementor-extension'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        
        $this->add_control(
            'gallery_images',
            [
                'label' => esc_html__('Add Images', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::GALLERY,
                'default' => [],
                'show_label' => false,
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );
        
        $this->add_control(
            'image_size',
            [
                'label' => esc_html__('Image Size', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'medium',
                'options' => [
                    'thumbnail' => esc_html__('Thumbnail', 'uxd-elementor-extension'),
                    'medium' => esc_html__('Medium', 'uxd-elementor-extension'),
                    'medium_large' => esc_html__('Medium Large', 'uxd-elementor-extension'),
                    'large' => esc_html__('Large', 'uxd-elementor-extension'),
                    'full' => esc_html__('Full Size', 'uxd-elementor-extension'),
                ],
            ]
        );
        
        $this->add_control(
            'lightbox_size',
            [
                'label' => esc_html__('Lightbox Image Size', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'large',
                'options' => [
                    'medium' => esc_html__('Medium', 'uxd-elementor-extension'),
                    'medium_large' => esc_html__('Medium Large', 'uxd-elementor-extension'),
                    'large' => esc_html__('Large', 'uxd-elementor-extension'),
                    'full' => esc_html__('Full Size', 'uxd-elementor-extension'),
                ],
            ]
        );
        
        $this->end_controls_section();
        
        // Search Settings
        $this->start_controls_section(
            'search_settings',
            [
                'label' => esc_html__('Search Settings', 'uxd-elementor-extension'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        
        $this->add_control(
            'show_search',
            [
                'label' => esc_html__('Show Search Bar', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );
        
        $this->add_control(
            'search_placeholder',
            [
                'label' => esc_html__('Search Placeholder', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__('Search images...', 'uxd-elementor-extension'),
                'condition' => [
                    'show_search' => 'yes',
                ],
            ]
        );
        
        $this->add_control(
            'search_button_text',
            [
                'label' => esc_html__('Search Button Text', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__('Search', 'uxd-elementor-extension'),
                'condition' => [
                    'show_search' => 'yes',
                ],
            ]
        );
        
        $this->add_control(
            'search_fields',
            [
                'label' => esc_html__('Search In', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'multiple' => true,
                'default' => ['title', 'caption'],
                'options' => [
                    'title' => esc_html__('Image Title', 'uxd-elementor-extension'),
                    'caption' => esc_html__('Image Caption', 'uxd-elementor-extension'),
                    'alt' => esc_html__('Alt Text', 'uxd-elementor-extension'),
                    'description' => esc_html__('Description', 'uxd-elementor-extension'),
                ],
                'condition' => [
                    'show_search' => 'yes',
                ],
            ]
        );
        
        $this->add_control(
            'no_results_text',
            [
                'label' => esc_html__('No Results Text', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__('No images found matching your search.', 'uxd-elementor-extension'),
                'condition' => [
                    'show_search' => 'yes',
                ],
            ]
        );
        
        $this->end_controls_section();
        
        // Layout Settings
        $this->start_controls_section(
            'layout_settings',
            [
                'label' => esc_html__('Layout', 'uxd-elementor-extension'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        
        $this->add_control(
            'layout_type',
            [
                'label' => esc_html__('Layout Type', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'grid',
                'options' => [
                    'grid' => esc_html__('Grid', 'uxd-elementor-extension'),
                    'masonry' => esc_html__('Masonry', 'uxd-elementor-extension'),
                ],
            ]
        );
        
        $this->add_responsive_control(
            'columns',
            [
                'label' => esc_html__('Columns', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '4',
                'tablet_default' => '3',
                'mobile_default' => '2',
                'options' => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                    '6' => '6',
                    '7' => '7',
                    '8' => '8',
                ],
                'selectors' => [
                    '{{WRAPPER}} .uxd-gallery-grid.uxd-layout-grid' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
                    '{{WRAPPER}} .uxd-gallery-grid.uxd-layout-masonry' => 'column-count: {{VALUE}};',
                ],
                'condition' => [
                    'layout_type' => 'grid',
                ],
            ]
        );

        $this->add_responsive_control(
            'masonry_columns',
            [
                'label' => esc_html__('Columns', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '4',
                'tablet_default' => '3',
                'mobile_default' => '2',
                'options' => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                    '6' => '6',
                    '7' => '7',
                    '8' => '8',
                ],
                'selectors' => [
                    '{{WRAPPER}} .uxd-gallery-grid.uxd-layout-masonry' => 'column-count: {{VALUE}};',
                ],
                'condition' => [
                    'layout_type' => 'masonry',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'gap',
            [
                'label' => esc_html__('Gap', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 10,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 15,
                ],
                'tablet_default' => [
                    'unit' => 'px',
                    'size' => 10,
                ],
                'mobile_default' => [
                    'unit' => 'px',
                    'size' => 8,
                ],
                'selectors' => [
                    '{{WRAPPER}} .uxd-gallery-grid.uxd-layout-grid' => 'gap: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .uxd-gallery-grid.uxd-layout-masonry' => 'column-gap: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .uxd-gallery-grid.uxd-layout-masonry .uxd-gallery-item' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        
        $this->add_control(
            'aspect_ratio',
            [
                'label' => esc_html__('Image Aspect Ratio', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '1/1',
                'options' => [
                    'auto' => esc_html__('Original', 'uxd-elementor-extension'),
                    '1/1' => esc_html__('Square (1:1)', 'uxd-elementor-extension'),
                    '4/3' => esc_html__('Standard (4:3)', 'uxd-elementor-extension'),
                    '3/2' => esc_html__('Classic (3:2)', 'uxd-elementor-extension'),
                    '16/9' => esc_html__('Wide (16:9)', 'uxd-elementor-extension'),
                    '2/3' => esc_html__('Portrait (2:3)', 'uxd-elementor-extension'),
                ],
                'selectors' => [
                    '{{WRAPPER}} .uxd-gallery-grid.uxd-layout-grid .uxd-gallery-item img' => 'aspect-ratio: {{VALUE}};',
                ],
                'condition' => [
                    'layout_type' => 'grid',
                ],
            ]
        );
        
        $this->add_control(
            'object_fit',
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
                    '{{WRAPPER}} .uxd-gallery-grid.uxd-layout-grid .uxd-gallery-item img' => 'object-fit: {{VALUE}};',
                ],
                'condition' => [
                    'aspect_ratio!' => 'auto',
                    'layout_type' => 'grid',
                ],
            ]
        );
        
        $this->add_control(
            'masonry_break_inside',
            [
                'label' => esc_html__('Prevent Breaking Items', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
                'description' => esc_html__('Prevents gallery items from breaking across columns', 'uxd-elementor-extension'),
                'selectors' => [
                    '{{WRAPPER}} .uxd-gallery-grid.uxd-layout-masonry .uxd-gallery-item' => 'break-inside: avoid;',
                ],
                'condition' => [
                    'layout_type' => 'masonry',
                ],
            ]
        );
        
        $this->end_controls_section();
        
        // Display Settings
        $this->start_controls_section(
            'display_settings',
            [
                'label' => esc_html__('Display Options', 'uxd-elementor-extension'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        
        $this->add_control(
            'show_caption',
            [
                'label' => esc_html__('Show Caption', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );
        
        $this->add_control(
            'caption_position',
            [
                'label' => esc_html__('Caption Position', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'overlay',
                'options' => [
                    'overlay' => esc_html__('Overlay on Hover', 'uxd-elementor-extension'),
                    'below' => esc_html__('Below Image', 'uxd-elementor-extension'),
                ],
                'condition' => [
                    'show_caption' => 'yes',
                ],
            ]
        );
        
        $this->add_control(
            'hover_effect',
            [
                'label' => esc_html__('Hover Effect', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'zoom',
                'options' => [
                    'none' => esc_html__('None', 'uxd-elementor-extension'),
                    'zoom' => esc_html__('Zoom', 'uxd-elementor-extension'),
                    'fade' => esc_html__('Fade', 'uxd-elementor-extension'),
                    'slide-up' => esc_html__('Slide Up', 'uxd-elementor-extension'),
                    'rotate' => esc_html__('Rotate', 'uxd-elementor-extension'),
                ],
            ]
        );
        
        $this->add_control(
            'loading_animation',
            [
                'label' => esc_html__('Loading Animation', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );
        
        $this->end_controls_section();
        
        // Style Tab - Search Bar
        $this->start_controls_section(
            'search_style',
            [
                'label' => esc_html__('Search Bar', 'uxd-elementor-extension'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_search' => 'yes',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'search_margin',
            [
                'label' => esc_html__('Margin', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'default' => [
                    'top' => 0,
                    'right' => 0,
                    'bottom' => 30,
                    'left' => 0,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .uxd-gallery-search' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'search_input_typography',
                'label' => esc_html__('Input Typography', 'uxd-elementor-extension'),
                'selector' => '{{WRAPPER}} .uxd-gallery-search input',
            ]
        );
        
        $this->add_control(
            'search_input_color',
            [
                'label' => esc_html__('Input Text Color', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .uxd-gallery-search input' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_control(
            'search_input_background',
            [
                'label' => esc_html__('Input Background', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .uxd-gallery-search input' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'search_input_border',
                'selector' => '{{WRAPPER}} .uxd-gallery-search input',
            ]
        );
        
        $this->add_responsive_control(
            'search_input_border_radius',
            [
                'label' => esc_html__('Input Border Radius', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .uxd-gallery-search input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'search_input_padding',
            [
                'label' => esc_html__('Input Padding', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .uxd-gallery-search input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        
        // Search Button Styles
        $this->add_control(
            'search_button_heading',
            [
                'label' => esc_html__('Search Button', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'search_button_typography',
                'selector' => '{{WRAPPER}} .uxd-gallery-search button',
            ]
        );
        
        $this->start_controls_tabs('search_button_tabs');
        
        $this->start_controls_tab(
            'search_button_normal',
            [
                'label' => esc_html__('Normal', 'uxd-elementor-extension'),
            ]
        );
        
        $this->add_control(
            'search_button_color',
            [
                'label' => esc_html__('Text Color', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .uxd-gallery-search button' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_control(
            'search_button_background',
            [
                'label' => esc_html__('Background Color', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .uxd-gallery-search button' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        
        $this->end_controls_tab();
        
        $this->start_controls_tab(
            'search_button_hover',
            [
                'label' => esc_html__('Hover', 'uxd-elementor-extension'),
            ]
        );
        
        $this->add_control(
            'search_button_hover_color',
            [
                'label' => esc_html__('Text Color', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .uxd-gallery-search button:hover' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_control(
            'search_button_hover_background',
            [
                'label' => esc_html__('Background Color', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .uxd-gallery-search button:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        
        $this->end_controls_tab();
        
        $this->end_controls_tabs();
        
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'search_button_border',
                'selector' => '{{WRAPPER}} .uxd-gallery-search button',
            ]
        );
        
        $this->add_responsive_control(
            'search_button_border_radius',
            [
                'label' => esc_html__('Button Border Radius', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .uxd-gallery-search button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'search_button_padding',
            [
                'label' => esc_html__('Button Padding', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .uxd-gallery-search button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        
        $this->end_controls_section();
        
        // Style Tab - Gallery Items
        $this->start_controls_section(
            'gallery_style',
            [
                'label' => esc_html__('Gallery Items', 'uxd-elementor-extension'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'image_border',
                'selector' => '{{WRAPPER}} .uxd-gallery-item img',
            ]
        );
        
        $this->add_responsive_control(
            'image_border_radius',
            [
                'label' => esc_html__('Border Radius', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .uxd-gallery-item img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'image_box_shadow',
                'selector' => '{{WRAPPER}} .uxd-gallery-item',
            ]
        );
        
        $this->add_control(
            'overlay_color',
            [
                'label' => esc_html__('Hover Overlay Color', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .uxd-gallery-item::before' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        
        $this->end_controls_section();
        
        // Style Tab - Caption
        $this->start_controls_section(
            'caption_style',
            [
                'label' => esc_html__('Caption', 'uxd-elementor-extension'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_caption' => 'yes',
                ],
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'caption_typography',
                'selector' => '{{WRAPPER}} .uxd-gallery-caption',
            ]
        );
        
        $this->add_control(
            'caption_color',
            [
                'label' => esc_html__('Color', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .uxd-gallery-caption' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_control(
            'caption_background',
            [
                'label' => esc_html__('Background Color', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .uxd-gallery-caption' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'caption_padding',
            [
                'label' => esc_html__('Padding', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .uxd-gallery-caption' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        
        $this->add_control(
            'caption_alignment',
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
                    '{{WRAPPER}} .uxd-gallery-caption' => 'text-align: {{VALUE}};',
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
        $gallery_images = $settings['gallery_images'];
        
        if (empty($gallery_images)) {
            echo '<div class="uxd-gallery-empty">' . esc_html__('No images selected for gallery.', 'uxd-elementor-extension') . '</div>';
            return;
        }
        
        $widget_id = 'uxd-gallery-' . $this->get_id();
        $layout_class = 'uxd-layout-' . $settings['layout_type'];
        ?>
        <div class="uxd-gallery-wrapper" id="<?php echo esc_attr($widget_id); ?>">
            
            <?php if ($settings['show_search'] === 'yes') : ?>
                <div class="uxd-gallery-search">
                    <div class="uxd-search-form">
                        <input type="text" 
                               class="uxd-search-input" 
                               placeholder="<?php echo esc_attr($settings['search_placeholder']); ?>"
                               data-search-fields="<?php echo esc_attr(implode(',', $settings['search_fields'])); ?>"
                        >
                        <button type="button" class="uxd-search-button">
                            <?php echo esc_html($settings['search_button_text']); ?>
                        </button>
                        <button type="button" class="uxd-search-clear" style="display: none;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            <?php endif; ?>
            
            <div class="uxd-gallery-grid uxd-hover-<?php echo esc_attr($settings['hover_effect']); ?> <?php echo esc_attr($layout_class); ?>" data-lightbox-size="<?php echo esc_attr($settings['lightbox_size']); ?>">
                <?php foreach ($gallery_images as $image) : ?>
                    <?php
                    $image_data = wp_get_attachment_metadata($image['id']);
                    $image_title = get_the_title($image['id']);
                    $image_caption = wp_get_attachment_caption($image['id']);
                    $image_alt = get_post_meta($image['id'], '_wp_attachment_image_alt', true);
                    $image_description = get_post_field('post_content', $image['id']);
                    
                    $search_data = [
                        'title' => $image_title,
                        'caption' => $image_caption,
                        'alt' => $image_alt,
                        'description' => $image_description,
                    ];
                    ?>
                    <div class="uxd-gallery-item" 
                         data-image-id="<?php echo esc_attr($image['id']); ?>"
                         data-search='<?php echo esc_attr(wp_json_encode($search_data)); ?>'>
                        
                        <?php if ($settings['loading_animation'] === 'yes') : ?>
                            <div class="uxd-image-loader">
                                <div class="uxd-loader-spinner"></div>
                            </div>
                        <?php endif; ?>
                        
                        <img src="<?php echo esc_url(wp_get_attachment_image_url($image['id'], $settings['image_size'])); ?>" 
                             alt="<?php echo esc_attr($image_alt); ?>"
                             data-full-src="<?php echo esc_url(wp_get_attachment_image_url($image['id'], $settings['lightbox_size'])); ?>"
                             data-caption="<?php echo esc_attr($image_caption); ?>"
                             data-title="<?php echo esc_attr($image_title); ?>"
                             loading="lazy"
                             style="<?php echo $settings['loading_animation'] === 'yes' ? 'opacity: 0;' : ''; ?>"
                        >
                        
                        <?php if ($settings['show_caption'] === 'yes' && $settings['caption_position'] === 'overlay') : ?>
                            <div class="uxd-gallery-caption uxd-caption-overlay">
                                <?php echo esc_html($image_caption ?: $image_title); ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="uxd-gallery-zoom-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                                <path d="M12 10h-2v2H9v-2H7V9h2V7h1v2h2v1z"/>
                            </svg>
                        </div>
                    </div>
                    
                    <?php if ($settings['show_caption'] === 'yes' && $settings['caption_position'] === 'below') : ?>
                        <div class="uxd-gallery-caption uxd-caption-below">
                            <?php echo esc_html($image_caption ?: $image_title); ?>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            
            <?php if ($settings['show_search'] === 'yes') : ?>
                <div class="uxd-gallery-no-results" style="display: none;">
                    <p><?php echo esc_html($settings['no_results_text']); ?></p>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Lightbox Modal -->
        <div class="uxd-lightbox-overlay" style="display: none;">
            <div class="uxd-lightbox-container">
                <button class="uxd-lightbox-close">&times;</button>
                <button class="uxd-lightbox-prev">&#8249;</button>
                <button class="uxd-lightbox-next">&#8250;</button>
                <div class="uxd-lightbox-content">
                    <img src="" alt="" class="uxd-lightbox-image">
                    <div class="uxd-lightbox-caption"></div>
                </div>
                <div class="uxd-lightbox-counter">
                    <span class="uxd-current">1</span> / <span class="uxd-total">1</span>
                </div>
            </div>
        </div>
        <?php
    }
}