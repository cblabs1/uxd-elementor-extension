<?php
/**
 * UXD Post Grid Widget
 *
 * @package UXD_Elementor_Extension
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class UXD_Post_Widget extends \Elementor\Widget_Base {

    /**
     * Get widget name.
     */
    public function get_name() {
        return 'uxd-post-grid';
    }

    /**
     * Get widget title.
     */
    public function get_title() {
        return esc_html__('UXD Post Grid', 'uxd-elementor-extension');
    }

    /**
     * Get widget icon.
     */
    public function get_icon() {
        return 'eicon-posts-grid';
    }

    /**
     * Get widget categories.
     */
    public function get_categories() {
        return ['uxd-widgets'];
    }

    /**
     * Get widget keywords.
     */
    public function get_keywords() {
        return ['post', 'grid', 'blog', 'list', 'uxd'];
    }

    /**
     * Register widget controls.
     */
    protected function register_controls() {

        // ========================
        // CONTENT TAB - QUERY SETTINGS
        // ========================
        $this->start_controls_section(
            'section_query',
            [
                'label' => esc_html__('Query', 'uxd-elementor-extension'),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'query_type',
            [
                'label'   => esc_html__('Query Type', 'uxd-elementor-extension'),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 'custom',
                'options' => [
                    'custom'  => esc_html__('Custom Query', 'uxd-elementor-extension'),
                    'archive' => esc_html__('Archive Query (Current Archive)', 'uxd-elementor-extension'),
                ],
                'description' => esc_html__('Choose "Archive Query" to automatically show posts from the current archive page (category, tag, author, date, etc.)', 'uxd-elementor-extension'),
            ]
        );

        $this->add_control(
            'archive_notice',
            [
                'type' => \Elementor\Controls_Manager::RAW_HTML,
                'raw' => '<div style="background: #e3f2fd; padding: 10px; border-left: 4px solid #2196f3; margin: 10px 0;">
                    <strong>Archive Mode:</strong><br>
                    • On category pages: Shows posts from that category<br>
                    • On tag pages: Shows posts with that tag<br>
                    • On author pages: Shows posts by that author<br>
                    • On date archives: Shows posts from that time period<br>
                    • On blog page: Shows latest posts<br>
                    • On search results: Shows search results
                </div>',
                'condition' => [
                    'query_type' => 'archive',
                ],
            ]
        );

        $this->add_control(
            'posts_per_page',
            [
                'label'   => esc_html__('Posts Per Page', 'uxd-elementor-extension'),
                'type'    => \Elementor\Controls_Manager::NUMBER,
                'default' => 6,
                'min'     => 1,
                'max'     => 50,
                'description' => esc_html__('Set to -1 to show all posts (not recommended for large archives)', 'uxd-elementor-extension'),
            ]
        );

        $this->add_control(
            'inherit_archive_pagination',
            [
                'label'   => esc_html__('Inherit Archive Pagination', 'uxd-elementor-extension'),
                'type'    => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
                'description' => esc_html__('Use the current page number from archive pagination', 'uxd-elementor-extension'),
                'condition' => [
                    'query_type' => 'archive',
                ],
            ]
        );

        $this->add_control(
            'post_type',
            [
                'label'   => esc_html__('Post Type', 'uxd-elementor-extension'),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 'post',
                'options' => $this->get_post_types(),
                'condition' => [
                    'query_type' => 'custom',
                ],
            ]
        );

        $this->add_control(
            'orderby',
            [
                'label'   => esc_html__('Order By', 'uxd-elementor-extension'),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 'date',
                'options' => [
                    'date'          => esc_html__('Date', 'uxd-elementor-extension'),
                    'title'         => esc_html__('Title', 'uxd-elementor-extension'),
                    'menu_order'    => esc_html__('Menu Order', 'uxd-elementor-extension'),
                    'rand'          => esc_html__('Random', 'uxd-elementor-extension'),
                    'comment_count' => esc_html__('Comment Count', 'uxd-elementor-extension'),
                    'modified'      => esc_html__('Modified', 'uxd-elementor-extension'),
                ],
                'condition' => [
                    'query_type' => 'custom',
                ],
            ]
        );

        $this->add_control(
            'order',
            [
                'label'   => esc_html__('Order', 'uxd-elementor-extension'),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 'desc',
                'options' => [
                    'asc'  => esc_html__('Ascending', 'uxd-elementor-extension'),
                    'desc' => esc_html__('Descending', 'uxd-elementor-extension'),
                ],
                'condition' => [
                    'query_type' => 'custom',
                ],
            ]
        );

        $this->add_control(
            'categories',
            [
                'label'       => esc_html__('Categories', 'uxd-elementor-extension'),
                'type'        => \Elementor\Controls_Manager::SELECT2,
                'options'     => $this->get_post_categories(),
                'multiple'    => true,
                'label_block' => true,
                'condition'   => [
                    'post_type' => 'post',
                    'query_type' => 'custom',
                ],
            ]
        );

        $this->add_control(
            'tags',
            [
                'label'       => esc_html__('Tags', 'uxd-elementor-extension'),
                'type'        => \Elementor\Controls_Manager::SELECT2,
                'options'     => $this->get_post_tags(),
                'multiple'    => true,
                'label_block' => true,
                'condition'   => [
                    'post_type' => 'post',
                    'query_type' => 'custom',
                ],
            ]
        );

        $this->add_control(
            'exclude_current',
            [
                'label'   => esc_html__('Exclude Current Post', 'uxd-elementor-extension'),
                'type'    => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
                'condition' => [
                    'query_type' => 'custom',
                ],
            ]
        );

        // Archive-specific controls
        $this->add_control(
            'archive_heading',
            [
                'label' => esc_html__('Archive Settings', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'query_type' => 'archive',
                ],
            ]
        );

        $this->add_control(
            'override_archive_order',
            [
                'label'   => esc_html__('Override Archive Order', 'uxd-elementor-extension'),
                'type'    => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'no',
                'description' => esc_html__('Override the default archive ordering with custom settings', 'uxd-elementor-extension'),
                'condition' => [
                    'query_type' => 'archive',
                ],
            ]
        );

        $this->add_control(
            'archive_orderby',
            [
                'label'   => esc_html__('Order By', 'uxd-elementor-extension'),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 'date',
                'options' => [
                    'date'          => esc_html__('Date', 'uxd-elementor-extension'),
                    'title'         => esc_html__('Title', 'uxd-elementor-extension'),
                    'menu_order'    => esc_html__('Menu Order', 'uxd-elementor-extension'),
                    'rand'          => esc_html__('Random', 'uxd-elementor-extension'),
                    'comment_count' => esc_html__('Comment Count', 'uxd-elementor-extension'),
                    'modified'      => esc_html__('Modified', 'uxd-elementor-extension'),
                ],
                'condition' => [
                    'query_type' => 'archive',
                    'override_archive_order' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'archive_order',
            [
                'label'   => esc_html__('Order', 'uxd-elementor-extension'),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 'desc',
                'options' => [
                    'asc'  => esc_html__('Ascending', 'uxd-elementor-extension'),
                    'desc' => esc_html__('Descending', 'uxd-elementor-extension'),
                ],
                'condition' => [
                    'query_type' => 'archive',
                    'override_archive_order' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();

        // ========================
        // CONTENT TAB - LAYOUT SETTINGS
        // ========================
        $this->start_controls_section(
            'section_layout',
            [
                'label' => esc_html__('Layout', 'uxd-elementor-extension'),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'layout_type',
            [
                'label'   => esc_html__('Layout Type', 'uxd-elementor-extension'),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 'grid',
                'options' => [
                    'grid' => esc_html__('Grid', 'uxd-elementor-extension'),
                    'list' => esc_html__('List', 'uxd-elementor-extension'),
                ],
            ]
        );

        $this->add_responsive_control(
            'columns',
            [
                'label'          => esc_html__('Columns', 'uxd-elementor-extension'),
                'type'           => \Elementor\Controls_Manager::SELECT,
                'default'        => '3',
                'tablet_default' => '2',
                'mobile_default' => '1',
                'options'        => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                    '6' => '6',
                ],
                'condition'      => [
                    'layout_type' => 'grid',
                ],
                'selectors'      => [
                    '{{WRAPPER}} .uxd-post-grid' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
                ],
            ]
        );

        $this->add_responsive_control(
            'column_gap',
            [
                'label'      => esc_html__('Column Gap', 'uxd-elementor-extension'),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', 'em'],
                'range'      => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default'    => [
                    'size' => 30,
                ],
                'selectors'  => [
                    '{{WRAPPER}} .uxd-post-grid' => 'gap: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .uxd-post-list .uxd-post-item' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'list_items_gap',
            [
                'label'      => esc_html__('List Items Gap', 'uxd-elementor-extension'),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', 'em'],
                'range'      => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default'    => [
                    'size' => 20,
                ],
                'selectors'  => [
                    '{{WRAPPER}} .uxd-post-list .uxd-list-main-content' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        

        $this->add_control(
            'enable_hover_effects',
            [
                'label'   => esc_html__('Enable Hover Effects', 'uxd-elementor-extension'),
                'type'    => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();

        // ========================
        // CONTENT TAB - CONTENT SETTINGS
        // ========================
        $this->start_controls_section(
            'section_content_settings',
            [
                'label' => esc_html__('Content', 'uxd-elementor-extension'),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'show_image',
            [
                'label'     => esc_html__('Show Featured Image', 'uxd-elementor-extension'),
                'type'      => \Elementor\Controls_Manager::SWITCHER,
                'default'   => 'yes',
                'condition' => [
                    'layout_type' => 'grid',
                ],
            ]
        );

        $this->add_control(
            'show_title',
            [
                'label'   => esc_html__('Show Title', 'uxd-elementor-extension'),
                'type'    => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'title_tag',
            [
                'label'     => esc_html__('Title HTML Tag', 'uxd-elementor-extension'),
                'type'      => \Elementor\Controls_Manager::SELECT,
                'default'   => 'h3',
                'options'   => [
                    'h1' => 'H1',
                    'h2' => 'H2',
                    'h3' => 'H3',
                    'h4' => 'H4',
                    'h5' => 'H5',
                    'h6' => 'H6',
                ],
                'condition' => [
                    'show_title' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'show_date',
            [
                'label'   => esc_html__('Show Date', 'uxd-elementor-extension'),
                'type'    => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_author',
            [
                'label'   => esc_html__('Show Author', 'uxd-elementor-extension'),
                'type'    => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_excerpt',
            [
                'label'     => esc_html__('Show Excerpt', 'uxd-elementor-extension'),
                'type'      => \Elementor\Controls_Manager::SWITCHER,
                'default'   => 'yes',
                'condition' => [
                    'layout_type' => 'grid',
                ],
            ]
        );

        $this->add_control(
            'excerpt_length',
            [
                'label'     => esc_html__('Excerpt Length (words)', 'uxd-elementor-extension'),
                'type'      => \Elementor\Controls_Manager::NUMBER,
                'default'   => 15,
                'min'       => 5,
                'max'       => 100,
                'condition' => [
                    'show_excerpt' => 'yes',
                    'layout_type' => 'grid',
                ],
            ]
        );

        $this->add_control(
            'show_read_more',
            [
                'label'     => esc_html__('Show Read More', 'uxd-elementor-extension'),
                'type'      => \Elementor\Controls_Manager::SWITCHER,
                'default'   => 'no',
                'condition' => [
                    'layout_type' => 'grid',
                ],
            ]
        );

        $this->add_control(
            'read_more_text',
            [
                'label'     => esc_html__('Read More Text', 'uxd-elementor-extension'),
                'type'      => \Elementor\Controls_Manager::TEXT,
                'default'   => esc_html__('Read More', 'uxd-elementor-extension'),
                'condition' => [
                    'show_read_more' => 'yes',
                    'layout_type' => 'grid',
                ],
            ]
        );

        $this->end_controls_section();

        

        // ========================
        //  LIST LAYOUT SETTINGS
        // ========================
        $this->start_controls_section(
            'section_list_settings',
            [
                'label'     => esc_html__('List Settings', 'uxd-elementor-extension'),
                'tab'       => \Elementor\Controls_Manager::TAB_CONTENT,
                'condition' => [
                    'layout_type' => 'list',
                ],
            ]
        );

        // Icon Controls Group
        $this->add_control(
            'icon_heading',
            [
                'label' => esc_html__('Icon Settings', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::HEADING,
            ]
        );

        $this->add_control(
            'show_list_icon',
            [
                'label'   => esc_html__('Show List Icon', 'uxd-elementor-extension'),
                'type'    => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'list_icon',
            [
                'label'     => esc_html__('Icon', 'uxd-elementor-extension'),
                'type'      => \Elementor\Controls_Manager::ICONS,
                'default'   => [
                    'value'   => 'fas fa-arrow-right',
                    'library' => 'fa-solid',
                ],
                'condition' => [
                    'show_list_icon' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'icon_position',
            [
                'label'     => esc_html__('Icon Position', 'uxd-elementor-extension'),
                'type'      => \Elementor\Controls_Manager::SELECT,
                'default'   => 'before',
                'options'   => [
                    'before' => esc_html__('Before Title', 'uxd-elementor-extension'),
                    'after'  => esc_html__('After Meta', 'uxd-elementor-extension'),
                ],
                'condition' => [
                    'show_list_icon' => 'yes',
                ],
            ]
        );

        // Layout Direction Control
        $this->add_control(
            'layout_heading',
            [
                'label' => esc_html__('Layout Direction', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'list_layout_direction',
            [
                'label'     => esc_html__('Direction', 'uxd-elementor-extension'),
                'type'      => \Elementor\Controls_Manager::SELECT,
                'default'   => 'horizontal',
                'options'   => [
                    'horizontal' => esc_html__('Horizontal', 'uxd-elementor-extension'),
                    'vertical'   => esc_html__('Vertical', 'uxd-elementor-extension'),
                ],
                'prefix_class' => 'uxd-list-direction-',
            ]
        );

        // Horizontal Alignment Controls
        $this->add_control(
            'horizontal_alignment',
            [
                'label' => esc_html__('Horizontal Alignment', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'flex-start' => [
                        'title' => esc_html__('Start', 'uxd-elementor-extension'),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'uxd-elementor-extension'),
                        'icon' => 'eicon-h-align-center',
                    ],
                    'flex-end' => [
                        'title' => esc_html__('End', 'uxd-elementor-extension'),
                        'icon' => 'eicon-h-align-right',
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
                'default' => 'flex-start',
                'selectors' => [
                    '{{WRAPPER}} .uxd-list-content' => 'justify-content: {{VALUE}};',
                ],
            ]
        );

        // Vertical Alignment Controls
        $this->add_control(
            'vertical_alignment',
            [
                'label' => esc_html__('Vertical Alignment', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'flex-start' => [
                        'title' => esc_html__('Top', 'uxd-elementor-extension'),
                        'icon' => 'eicon-v-align-top',
                    ],
                    'center' => [
                        'title' => esc_html__('Middle', 'uxd-elementor-extension'),
                        'icon' => 'eicon-v-align-middle',
                    ],
                    'flex-end' => [
                        'title' => esc_html__('Bottom', 'uxd-elementor-extension'),
                        'icon' => 'eicon-v-align-bottom',
                    ],
                    'stretch' => [
                        'title' => esc_html__('Stretch', 'uxd-elementor-extension'),
                        'icon' => 'eicon-v-align-stretch',
                    ],
                    'baseline' => [
                        'title' => esc_html__('Baseline', 'uxd-elementor-extension'),
                        'icon' => 'eicon-align-start-v',
                    ],
                ],
                'default' => 'center',
                'selectors' => [
                    '{{WRAPPER}} .uxd-list-content' => 'align-items: {{VALUE}};',
                ],
            ]
        );

        // Gap Control
        $this->add_responsive_control(
            'list_content_gap',
            [
                'label'      => esc_html__('Content Gap', 'uxd-elementor-extension'),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', 'rem'],
                'range'      => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                    'em' => [
                        'min' => 0,
                        'max' => 10,
                    ],
                ],
                'default'    => [
                    'size' => 15,
                    'unit' => 'px',
                ],
                'selectors'  => [
                    '{{WRAPPER}} .uxd-list-content' => 'gap: {{SIZE}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->end_controls_section();

        // ========================
        // PAGINATION SETTINGS
        // ========================
        $this->start_controls_section(
            'section_pagination',
            [
                'label' => esc_html__('Pagination', 'uxd-elementor-extension'),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'show_pagination',
            [
                'label'   => esc_html__('Show Pagination', 'uxd-elementor-extension'),
                'type'    => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        $this->end_controls_section();

        // ========================
        // STYLE TAB - CONTAINER
        // ========================
        $this->start_controls_section(
            'section_container_style',
            [
                'label' => esc_html__('Container', 'uxd-elementor-extension'),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'container_padding',
            [
                'label'      => esc_html__('Padding', 'uxd-elementor-extension'),
                'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .uxd-post-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'container_background',
            [
                'label'     => esc_html__('Background Color', 'uxd-elementor-extension'),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .uxd-post-wrapper' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        // ========================
        // STYLE TAB - ITEMS
        // ========================
        $this->start_controls_section(
            'section_item_style',
            [
                'label' => esc_html__('Post Items', 'uxd-elementor-extension'),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'item_padding',
            [
                'label'      => esc_html__('Item Padding', 'uxd-elementor-extension'),
                'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .uxd-grid-item .uxd-post-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .uxd-list-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'item_background',
            [
                'label'     => esc_html__('Background Color', 'uxd-elementor-extension'),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .uxd-post-item' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name'     => 'item_border',
                'selector' => '{{WRAPPER}} .uxd-post-item',
            ]
        );

        $this->add_responsive_control(
            'item_border_radius',
            [
                'label'      => esc_html__('Border Radius', 'uxd-elementor-extension'),
                'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .uxd-post-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'item_shadow',
                'selector' => '{{WRAPPER}} .uxd-post-item',
            ]
        );

        $this->end_controls_section();

        // ========================
        // STYLE TAB - TITLE
        // ========================
        $this->start_controls_section(
            'section_title_style',
            [
                'label' => esc_html__('Title', 'uxd-elementor-extension'),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name'     => 'title_typography',
                'selector' => '{{WRAPPER}} .uxd-post-title',
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label'     => esc_html__('Color', 'uxd-elementor-extension'),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#333333',
                'selectors' => [
                    '{{WRAPPER}} .uxd-post-title a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'title_hover_color',
            [
                'label'     => esc_html__('Hover Color', 'uxd-elementor-extension'),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#007cba',
                'selectors' => [
                    '{{WRAPPER}} .uxd-post-title a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'title_spacing',
            [
                'label'      => esc_html__('Bottom Spacing', 'uxd-elementor-extension'),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', 'em'],
                'range'      => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .uxd-post-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // ========================
        // STYLE TAB - META
        // ========================
        $this->start_controls_section(
            'section_meta_style',
            [
                'label' => esc_html__('Meta', 'uxd-elementor-extension'),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name'     => 'meta_typography',
                'selector' => '{{WRAPPER}} .uxd-post-meta',
            ]
        );

        $this->add_control(
            'meta_color',
            [
                'label'     => esc_html__('Color', 'uxd-elementor-extension'),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#666666',
                'selectors' => [
                    '{{WRAPPER}} .uxd-post-meta' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'meta_spacing',
            [
                'label'      => esc_html__('Bottom Spacing', 'uxd-elementor-extension'),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', 'em'],
                'range'      => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .uxd-post-meta' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // ========================
        // STYLE TAB - READ MORE BUTTON
        // ========================
        $this->start_controls_section(
            'section_read_more_style',
            [
                'label'     => esc_html__('Read More Button', 'uxd-elementor-extension'),
                'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'layout_type' => 'grid',
                    'show_read_more' => 'yes',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name'     => 'read_more_typography',
                'selector' => '{{WRAPPER}} .uxd-read-more',
            ]
        );
        
        $this->add_responsive_control(
            'read_more_padding',
            [
                'label'      => esc_html__('Padding', 'uxd-elementor-extension'),
                'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .uxd-read-more' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->start_controls_tabs('read_more_tabs');

        $this->start_controls_tab(
            'read_more_tab_normal',
            [
                'label' => esc_html__('Normal', 'uxd-elementor-extension'),
            ]
        );

        $this->add_control(
            'read_more_color',
            [
                'label'     => esc_html__('Text Color', 'uxd-elementor-extension'),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .uxd-read-more' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_control(
            'read_more_background_color',
            [
                'label'     => esc_html__('Background Color', 'uxd-elementor-extension'),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .uxd-read-more' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name'      => 'read_more_border',
                'selector'  => '{{WRAPPER}} .uxd-read-more',
            ]
        );

        $this->add_control(
            'read_more_border_radius',
            [
                'label'      => esc_html__('Border Radius', 'uxd-elementor-extension'),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .uxd-read-more' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'read_more_tab_hover',
            [
                'label' => esc_html__('Hover', 'uxd-elementor-extension'),
            ]
        );

        $this->add_control(
            'read_more_hover_color',
            [
                'label'     => esc_html__('Text Color', 'uxd-elementor-extension'),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .uxd-read-more:hover' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_control(
            'read_more_hover_background_color',
            [
                'label'     => esc_html__('Background Color', 'uxd-elementor-extension'),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .uxd-read-more:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'read_more_hover_border_color',
            [
                'label'     => esc_html__('Border Color', 'uxd-elementor-extension'),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .uxd-read-more:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'read_more_hover_transition',
            [
                'label'     => esc_html__('Transition Duration', 'uxd-elementor-extension'),
                'type'      => \Elementor\Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'max' => 3,
                        'step' => 0.1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .uxd-read-more' => 'transition: all {{SIZE}}s ease-in-out;',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        // ========================
        // ICON STYLE SECTION
        // ========================
        $this->start_controls_section(
            'section_list_icon_style',
            [
                'label'     => esc_html__('List Icon', 'uxd-elementor-extension'),
                'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'layout_type' => 'list',
                    'show_list_icon' => 'yes',
                ],
            ]
        );

        // Icon Color Controls
        $this->start_controls_tabs('icon_style_tabs');

        $this->start_controls_tab(
            'icon_normal_tab',
            [
                'label' => esc_html__('Normal', 'uxd-elementor-extension'),
            ]
        );

        $this->add_control(
            'icon_color',
            [
                'label'     => esc_html__('Color', 'uxd-elementor-extension'),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#007cba',
                'selectors' => [
                    '{{WRAPPER}} .uxd-post-list-icon' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .uxd-post-list-icon svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'icon_background',
            [
                'label'     => esc_html__('Background Color', 'uxd-elementor-extension'),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .uxd-post-list-icon' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'icon_hover_tab',
            [
                'label' => esc_html__('Hover', 'uxd-elementor-extension'),
            ]
        );

        $this->add_control(
            'icon_hover_color',
            [
                'label'     => esc_html__('Hover Color', 'uxd-elementor-extension'),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#005a87',
                'selectors' => [
                    '{{WRAPPER}} .uxd-list-item:hover .uxd-post-list-icon' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .uxd-list-item:hover .uxd-post-list-icon svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'icon_hover_background',
            [
                'label'     => esc_html__('Hover Background Color', 'uxd-elementor-extension'),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .uxd-list-item:hover .uxd-post-list-icon' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        // Icon Size and Spacing
        $this->add_responsive_control(
            'icon_size',
            [
                'label'      => esc_html__('Icon Size', 'uxd-elementor-extension'),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', 'rem'],
                'range'      => [
                    'px' => [
                        'min' => 8,
                        'max' => 100,
                    ],
                    'em' => [
                        'min' => 0.5,
                        'max' => 6,
                    ],
                ],
                'default'    => [
                    'size' => 16,
                    'unit' => 'px',
                ],
                'selectors'  => [
                    '{{WRAPPER}} .uxd-post-list-icon' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .uxd-post-list-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'icon_padding',
            [
                'label'      => esc_html__('Padding', 'uxd-elementor-extension'),
                'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .uxd-post-list-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'icon_margin',
            [
                'label'      => esc_html__('Margin', 'uxd-elementor-extension'),
                'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .uxd-post-list-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        // Border and Shadow
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name'      => 'icon_border',
                'selector'  => '{{WRAPPER}} .uxd-post-list-icon',
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'icon_border_radius',
            [
                'label'      => esc_html__('Border Radius', 'uxd-elementor-extension'),
                'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .uxd-post-list-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'icon_shadow',
                'selector' => '{{WRAPPER}} .uxd-post-list-icon',
            ]
        );

        // Hover Animation
        $this->add_control(
            'icon_hover_animation',
            [
                'label' => esc_html__('Hover Animation', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'none',
                'options' => [
                    'none' => esc_html__('None', 'uxd-elementor-extension'),
                    'pulse' => esc_html__('Pulse', 'uxd-elementor-extension'),
                    'bounce' => esc_html__('Bounce', 'uxd-elementor-extension'),
                    'shake' => esc_html__('Shake', 'uxd-elementor-extension'),
                    'rotate' => esc_html__('Rotate', 'uxd-elementor-extension'),
                    'scale' => esc_html__('Scale', 'uxd-elementor-extension'),
                    'slide-right' => esc_html__('Slide Right', 'uxd-elementor-extension'),
                ],
                'prefix_class' => 'uxd-icon-hover-',
                'separator' => 'before',
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Get post types for select control
     */
    private function get_post_types() {
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
     * Get post categories for select control
     */
    private function get_post_categories() {
        $categories = [];
        $terms = get_terms([
            'taxonomy' => 'category',
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
     */
    private function get_post_tags() {
        $tags = [];
        $terms = get_terms([
            'taxonomy' => 'post_tag',
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
     * Get custom excerpt
     */
    private function get_excerpt($length = 15) {
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
     * Render grid item
     */
    protected function render_grid_item($settings) {
        $title_tag = $settings['title_tag'];
        ?>
        <article class="uxd-post-item uxd-grid-item" itemscope itemtype="https://schema.org/Article">
            <?php if ($settings['show_image'] === 'yes' && has_post_thumbnail()) : ?>
                <div class="uxd-post-image">
                    <a href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>">
                        <?php the_post_thumbnail('medium_large', ['itemprop' => 'image']); ?>
                    </a>
                </div>
            <?php endif; ?>
            
            <div class="uxd-post-content">
                <?php if ($settings['show_title'] === 'yes') : ?>
                    <<?php echo esc_attr($title_tag); ?> class="uxd-post-title" itemprop="headline">
                        <a href="<?php the_permalink(); ?>" rel="bookmark" itemprop="url">
                            <?php the_title(); ?>
                        </a>
                    </<?php echo esc_attr($title_tag); ?>>
                <?php endif; ?>
                
                <?php if ($settings['show_date'] === 'yes' || $settings['show_author'] === 'yes') : ?>
                    <div class="uxd-post-meta">
                        <?php if ($settings['show_date'] === 'yes') : ?>
                            <time class="uxd-post-date" datetime="<?php echo get_the_date('c'); ?>" itemprop="datePublished">
                                <?php echo get_the_date(); ?>
                            </time>
                        <?php endif; ?>
                        <?php if ($settings['show_author'] === 'yes') : ?>
                            <?php if ($settings['show_date'] === 'yes') echo ' | '; ?>
                            <span class="uxd-post-author" itemprop="author" itemscope itemtype="https://schema.org/Person">
                                <?php esc_html_e('By', 'uxd-elementor-extension'); ?> 
                                <span itemprop="name"><?php the_author(); ?></span>
                            </span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($settings['show_excerpt'] === 'yes') : ?>
                    <div class="uxd-post-excerpt" itemprop="description">
                        <?php echo esc_html($this->get_excerpt($settings['excerpt_length'])); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($settings['show_read_more'] === 'yes') : ?>
                    <div class="uxd-post-read-more">
                        <a href="<?php the_permalink(); ?>" class="uxd-read-more" aria-label="<?php echo esc_attr(sprintf(__('Read more about %s', 'uxd-elementor-extension'), get_the_title())); ?>">
                            <?php echo esc_html($settings['read_more_text']); ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </article>
        <?php
    }

    /**
     * Render list item
     */
    protected function render_list_item($settings) {
        $title_tag = $settings['title_tag'];
        $icon_html = '';
        $layout_direction = isset($settings['list_layout_direction']) ? $settings['list_layout_direction'] : 'horizontal';
        
        // Generate icon HTML
        if ('yes' === $settings['show_list_icon'] && !empty($settings['list_icon']['value'])) {
            $icon_class = 'uxd-post-list-icon uxd-post-list-icon-' . esc_attr($settings['icon_position']);
            ob_start();
            ?>
            <span class="<?php echo esc_attr($icon_class); ?>">
                <?php \Elementor\Icons_Manager::render_icon($settings['list_icon'], ['aria-hidden' => 'true']); ?>
            </span>
            <?php
            $icon_html = ob_get_clean();
        }
        
        // Determine layout classes
        $layout_class = 'uxd-list-layout-' . $layout_direction;
        $wrapper_classes = [
            'uxd-post-item',
            'uxd-list-item',
            $layout_class
        ];
        ?>
        <article class="<?php echo esc_attr(implode(' ', $wrapper_classes)); ?>" itemscope itemtype="https://schema.org/Article">
            <div class="uxd-list-content">
                <?php if ('before' === $settings['icon_position']):?>
                    <a href="<?php the_permalink(); ?>" rel="bookmark" itemprop="url">
                        <?php echo $icon_html; ?>
                    </a>
                <?php endif; ?>
                
                <div class="uxd-list-main-content">
                    <?php if ($settings['show_title'] === 'yes') : ?>
                        <<?php echo esc_attr($title_tag); ?> class="uxd-post-title" itemprop="headline">
                            <a href="<?php the_permalink(); ?>" rel="bookmark" itemprop="url">
                                <?php the_title(); ?>
                            </a>
                        </<?php echo esc_attr($title_tag); ?>>
                    <?php endif; ?>
                    
                    <?php if ($settings['show_date'] === 'yes' || $settings['show_author'] === 'yes') : ?>
                        <div class="uxd-post-meta">
                            <?php if ($settings['show_date'] === 'yes') : ?>
                                <time class="uxd-post-date" datetime="<?php echo get_the_date('c'); ?>" itemprop="datePublished">
                                    <?php echo get_the_date(); ?>
                                </time>
                            <?php endif; ?>
                            <?php if ($settings['show_author'] === 'yes') : ?>
                                <?php if ($settings['show_date'] === 'yes') echo ' | '; ?>
                                <span class="uxd-post-author" itemprop="author" itemscope itemtype="https://schema.org/Person">
                                    <?php esc_html_e('By', 'uxd-elementor-extension'); ?> 
                                    <span itemprop="name"><?php the_author(); ?></span>
                                </span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ('after' === $settings['icon_position']):?>
                    <a href="<?php the_permalink(); ?>" rel="bookmark" itemprop="url">
                        <?php echo $icon_html; ?>
                    </a>
                <?php endif; ?>
                
            </div>
        </article>
        <?php
    }

    /**
     * Build query arguments
     */
    private function build_query_args($settings) {
        // Handle Archive Query
        if ($settings['query_type'] === 'archive') {
            return $this->build_archive_query_args($settings);
        }

        // Handle Custom Query
        $args = [
            'post_type'      => $settings['post_type'],
            'post_status'    => 'publish',
            'posts_per_page' => $settings['posts_per_page'],
            'orderby'        => $settings['orderby'],
            'order'          => $settings['order'],
            'meta_query'     => [],
        ];

        // Categories
        if (!empty($settings['categories']) && $settings['post_type'] === 'post') {
            $args['cat'] = implode(',', $settings['categories']);
        }

        // Tags
        if (!empty($settings['tags']) && $settings['post_type'] === 'post') {
            $args['tag__in'] = $settings['tags'];
        }

        // Exclude current post
        if ($settings['exclude_current'] === 'yes' && is_single()) {
            $args['post__not_in'] = [get_the_ID()];
        }

        // Pagination
        if ($settings['show_pagination'] === 'yes') {
            $paged = get_query_var('paged') ? get_query_var('paged') : 1;
            $args['paged'] = $paged;
        }

        return $args;
    }

    /**
     * Build archive-specific query arguments
     */
    private function build_archive_query_args($settings) {
        global $wp_query;
        
        // Start with base arguments
        $args = [
            'post_status'    => 'publish',
            'posts_per_page' => $settings['posts_per_page'],
        ];

        // Handle pagination
        if ($settings['inherit_archive_pagination'] === 'yes') {
            $paged = get_query_var('paged') ? get_query_var('paged') : (get_query_var('page') ? get_query_var('page') : 1);
            $args['paged'] = $paged;
        } elseif ($settings['show_pagination'] === 'yes') {
            $paged = get_query_var('paged') ? get_query_var('paged') : 1;
            $args['paged'] = $paged;
        }

        // Override order if specified
        if ($settings['override_archive_order'] === 'yes') {
            $args['orderby'] = $settings['archive_orderby'];
            $args['order'] = $settings['archive_order'];
        }

        // Detect current archive type and add specific parameters
        if (is_category()) {
            // Category Archive
            $category = get_queried_object();
            $args['cat'] = $category->term_id;
            $args['post_type'] = 'post';
            
        } elseif (is_tag()) {
            // Tag Archive
            $tag = get_queried_object();
            $args['tag_id'] = $tag->term_id;
            $args['post_type'] = 'post';
            
        } elseif (is_author()) {
            // Author Archive
            $author = get_queried_object();
            $args['author'] = $author->ID;
            $args['post_type'] = 'post';
            
        } elseif (is_date()) {
            // Date Archive
            $args['post_type'] = 'post';
            
            if (is_year()) {
                $args['year'] = get_query_var('year');
            } elseif (is_month()) {
                $args['year'] = get_query_var('year');
                $args['monthnum'] = get_query_var('monthnum');
            } elseif (is_day()) {
                $args['year'] = get_query_var('year');
                $args['monthnum'] = get_query_var('monthnum');
                $args['day'] = get_query_var('day');
            }
            
        } elseif (is_tax()) {
            // Custom Taxonomy Archive
            $taxonomy = get_query_var('taxonomy');
            $term = get_queried_object();
            
            $args['tax_query'] = [
                [
                    'taxonomy' => $taxonomy,
                    'field'    => 'term_id',
                    'terms'    => $term->term_id,
                ]
            ];
            
            // Get post types associated with this taxonomy
            $post_types = get_taxonomy($taxonomy)->object_type;
            $args['post_type'] = $post_types;
            
        } elseif (is_post_type_archive()) {
            // Custom Post Type Archive
            $post_type = get_query_var('post_type');
            if (is_array($post_type)) {
                $args['post_type'] = $post_type;
            } else {
                $args['post_type'] = $post_type ?: get_queried_object()->name;
            }
            
        } elseif (is_search()) {
            // Search Results
            $args['s'] = get_search_query();
            $args['post_type'] = 'any'; // Search all post types by default
            
        } elseif (is_home() && !is_front_page()) {
            // Blog Archive (Posts Page)
            $args['post_type'] = 'post';
            
        } else {
            // Fallback - Latest Posts
            $args['post_type'] = 'post';
        }

        // Preserve any additional query vars that might be relevant
        if (!empty($wp_query->query_vars['meta_key'])) {
            $args['meta_key'] = $wp_query->query_vars['meta_key'];
        }
        
        if (!empty($wp_query->query_vars['meta_value'])) {
            $args['meta_value'] = $wp_query->query_vars['meta_value'];
        }

        return $args;
    }

    /**
     * Get current archive context for display
     */
    private function get_archive_context() {
        if (is_category()) {
            $category = get_queried_object();
            return sprintf(__('Category: %s', 'uxd-elementor-extension'), $category->name);
        } elseif (is_tag()) {
            $tag = get_queried_object();
            return sprintf(__('Tag: %s', 'uxd-elementor-extension'), $tag->name);
        } elseif (is_author()) {
            $author = get_queried_object();
            return sprintf(__('Author: %s', 'uxd-elementor-extension'), $author->display_name);
        } elseif (is_date()) {
            if (is_year()) {
                return sprintf(__('Year: %s', 'uxd-elementor-extension'), get_query_var('year'));
            } elseif (is_month()) {
                return sprintf(__('Month: %s %s', 'uxd-elementor-extension'), 
                    date_i18n('F', mktime(0, 0, 0, get_query_var('monthnum'), 1)), 
                    get_query_var('year')
                );
            } elseif (is_day()) {
                return sprintf(__('Day: %s', 'uxd-elementor-extension'), 
                    date_i18n(get_option('date_format'), mktime(0, 0, 0, get_query_var('monthnum'), get_query_var('day'), get_query_var('year')))
                );
            }
        } elseif (is_tax()) {
            $term = get_queried_object();
            $taxonomy = get_taxonomy($term->taxonomy);
            return sprintf(__('%s: %s', 'uxd-elementor-extension'), $taxonomy->label, $term->name);
        } elseif (is_post_type_archive()) {
            $post_type = get_queried_object();
            return sprintf(__('Archive: %s', 'uxd-elementor-extension'), $post_type->label);
        } elseif (is_search()) {
            return sprintf(__('Search Results for: %s', 'uxd-elementor-extension'), get_search_query());
        } elseif (is_home() && !is_front_page()) {
            return __('Blog Archive', 'uxd-elementor-extension');
        }
        
        return __('Archive', 'uxd-elementor-extension');
    }

    /**
     * Render widget output on the frontend
     */
    protected function render() {
        $settings = $this->get_settings_for_display();
        
        // Build query
        $args = $this->build_query_args($settings);
        $posts_query = new \WP_Query($args);
        
        if (!$posts_query->have_posts()) {
            echo '<p>' . esc_html__('No posts found.', 'uxd-elementor-extension') . '</p>';
            return;
        }

        // Add wrapper classes based on settings
        $wrapper_classes = [
            'uxd-post-wrapper',
            'uxd-' . $settings['layout_type'] . '-layout'
        ];
        
        if (isset($settings['enable_hover_effects']) && 'yes' === $settings['enable_hover_effects']) {
            $wrapper_classes[] = 'uxd-hover-enabled';
        }
        
        if (isset($settings['list_layout_direction'])) {
            $wrapper_classes[] = 'uxd-list-direction-' . $settings['list_layout_direction'];
        }
        ?>
        <div class="<?php echo esc_attr(implode(' ', $wrapper_classes)); ?>">
            <?php if ('grid' === $settings['layout_type']) : ?>
                <div class="uxd-post-grid">
                    <?php while ($posts_query->have_posts()) : $posts_query->the_post(); ?>
                        <?php $this->render_grid_item($settings); ?>
                    <?php endwhile; ?>
                </div>
            <?php else : ?>
                <div class="uxd-post-list">
                    <?php while ($posts_query->have_posts()) : $posts_query->the_post(); ?>
                        <?php $this->render_list_item($settings); ?>
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($settings['show_pagination'] === 'yes' && $posts_query->max_num_pages > 1) : ?>
                <div class="uxd-pagination">
                    <?php
                    echo paginate_links([
                        'total' => $posts_query->max_num_pages,
                        'current' => max(1, get_query_var('paged')),
                        'format' => '?paged=%#%',
                        'show_all' => false,
                        'end_size' => 1,
                        'mid_size' => 2,
                        'prev_next' => true,
                        'prev_text' => '&laquo; ' . esc_html__('Previous', 'uxd-elementor-extension'),
                        'next_text' => esc_html__('Next', 'uxd-elementor-extension') . ' &raquo;',
                        'type' => 'plain',
                    ]);
                    ?>
                </div>
            <?php endif; ?>
        </div>
        <?php
        
        wp_reset_postdata();
    }

    /**
     * Render widget output in the editor
     */
    protected function content_template() {
        ?>
        <#
        view.addInlineEditingAttributes('read_more_text', 'none');
        #>
        <div class="uxd-post-wrapper uxd-{{{ settings.layout_type }}}-layout">
            <# if (settings.layout_type === 'grid') { #>
                <div class="uxd-post-grid">
                    <!-- Grid items would be rendered here -->
                    <div class="uxd-post-item uxd-grid-item">
                        <div class="uxd-post-image">
                            <img src="https://via.placeholder.com/400x250" alt="Sample Post">
                        </div>
                        <div class="uxd-post-content">
                            <# if (settings.show_title === 'yes') { #>
                                <{{{ settings.title_tag }}} class="uxd-post-title">
                                    <a href="#">Sample Post Title</a>
                                </{{{ settings.title_tag }}}>
                            <# } #>
                            <# if (settings.show_date === 'yes' || settings.show_author === 'yes') { #>
                                <div class="uxd-post-meta">
                                    <# if (settings.show_date === 'yes') { #>
                                        <span class="uxd-post-date">January 1, 2024</span>
                                    <# } #>
                                    <# if (settings.show_author === 'yes') { #>
                                        <# if (settings.show_date === 'yes') { #> | <# } #>
                                        <span class="uxd-post-author">By Admin</span>
                                    <# } #>
                                </div>
                            <# } #>
                            <# if (settings.show_excerpt === 'yes') { #>
                                <div class="uxd-post-excerpt">
                                    This is a sample excerpt for the post content preview.
                                </div>
                            <# } #>
                            <# if (settings.show_read_more === 'yes') { #>
                                <div class="uxd-post-read-more">
                                    <a href="#" class="uxd-read-more" {{{ view.getRenderAttributeString('read_more_text') }}}>
                                        {{{ settings.read_more_text }}}
                                    </a>
                                </div>
                            <# } #>
                        </div>
                    </div>
                </div>
            <# } else { #>
                <div class="uxd-post-list">
                    <!-- List items would be rendered here -->
                    <div class="uxd-post-item uxd-list-item uxd-list-layout-{{{ settings.list_layout_direction }}}">
                        <div class="uxd-list-content">
                            <# if (settings.show_list_icon === 'yes' && settings.icon_position === 'before') { #>
                                <span class="uxd-post-list-icon uxd-post-list-icon-before">
                                    <i class="{{{ settings.list_icon.value }}}"></i>
                                </span>
                            <# } #>
                            <div class="uxd-list-main-content">
                                <# if (settings.show_title === 'yes') { #>
                                    <{{{ settings.title_tag }}} class="uxd-post-title">
                                        <a href="#">Sample Post Title</a>
                                    </{{{ settings.title_tag }}}>
                                <# } #>
                                <# if (settings.show_date === 'yes' || settings.show_author === 'yes') { #>
                                    <div class="uxd-post-meta">
                                        <# if (settings.show_date === 'yes') { #>
                                            <span class="uxd-post-date">January 1, 2024</span>
                                        <# } #>
                                        <# if (settings.show_author === 'yes') { #>
                                            <# if (settings.show_date === 'yes') { #> | <# } #>
                                            <span class="uxd-post-author">By Admin</span>
                                        <# } #>
                                    </div>
                                <# } #>
                            </div>
                            <# if (settings.show_list_icon === 'yes' && settings.icon_position === 'after') { #>
                                <span class="uxd-post-list-icon uxd-post-list-icon-after">
                                    <i class="{{{ settings.list_icon.value }}}"></i>
                                </span>
                            <# } #>
                        </div>
                    </div>
                </div>
            <# } #>
        </div>
        <?php
    }
}
?>