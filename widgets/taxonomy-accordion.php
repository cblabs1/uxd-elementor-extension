<?php
/**
 * UXD Taxonomy Accordion Widget
 * 
 * File: widgets/taxonomy-accordion.php
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class UXD_Taxonomy_Accordion_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'uxd_taxonomy_accordion';
    }

    public function get_title() {
        return esc_html__('Taxonomy Accordion', 'uxd-elementor-extension');
    }

    public function get_icon() {
        return 'eicon-accordion';
    }

    public function get_categories() {
        return ['uxd-general'];
    }

    public function get_keywords() {
        return ['taxonomy', 'accordion', 'categories', 'collapse', 'expand', 'product categories', 'uxd'];
    }

    public function get_script_depends() {
        return ['uxd-elementor-extension-js'];
    }

    public function get_style_depends() {
        return ['uxd-elementor-extension-css'];
    }

    protected function register_controls() {

        // Content Section
        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__('Content', 'uxd-elementor-extension'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'taxonomy_type',
            [
                'label' => esc_html__('Taxonomy Type', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'product_cat',
                'options' => $this->get_available_taxonomies(),
            ]
        );

        $this->add_control(
            'show_count',
            [
                'label' => esc_html__('Show Count', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'uxd-elementor-extension'),
                'label_off' => esc_html__('Hide', 'uxd-elementor-extension'),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );

        $this->add_control(
            'show_hierarchy',
            [
                'label' => esc_html__('Show Hierarchy', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'uxd-elementor-extension'),
                'label_off' => esc_html__('No', 'uxd-elementor-extension'),
                'return_value' => 'yes',
                'default' => 'yes',
                'description' => esc_html__('Show parent-child relationships', 'uxd-elementor-extension'),
            ]
        );

        $this->add_control(
            'hide_empty',
            [
                'label' => esc_html__('Hide Empty', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'uxd-elementor-extension'),
                'label_off' => esc_html__('No', 'uxd-elementor-extension'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'orderby',
            [
                'label' => esc_html__('Order By', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'name',
                'options' => [
                    'name' => esc_html__('Name', 'uxd-elementor-extension'),
                    'slug' => esc_html__('Slug', 'uxd-elementor-extension'),
                    'count' => esc_html__('Count', 'uxd-elementor-extension'),
                    'id' => esc_html__('ID', 'uxd-elementor-extension'),
                    'menu_order' => esc_html__('Menu Order', 'uxd-elementor-extension'),
                ],
            ]
        );

        $this->add_control(
            'order',
            [
                'label' => esc_html__('Order', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'ASC',
                'options' => [
                    'ASC' => esc_html__('ASC', 'uxd-elementor-extension'),
                    'DESC' => esc_html__('DESC', 'uxd-elementor-extension'),
                ],
            ]
        );

        $this->add_control(
            'limit',
            [
                'label' => esc_html__('Limit', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => -1,
                'max' => 100,
                'step' => 1,
                'default' => -1,
                'description' => esc_html__('Set -1 for no limit', 'uxd-elementor-extension'),
            ]
        );

        $this->add_control(
            'include_terms',
            [
                'label' => esc_html__('Include Terms', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => esc_html__('Enter term IDs separated by commas', 'uxd-elementor-extension'),
                'description' => esc_html__('Leave empty to show all terms', 'uxd-elementor-extension'),
            ]
        );

        $this->add_control(
            'exclude_terms',
            [
                'label' => esc_html__('Exclude Terms', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => esc_html__('Enter term IDs separated by commas', 'uxd-elementor-extension'),
                'description' => esc_html__('Terms to exclude from display', 'uxd-elementor-extension'),
            ]
        );

        $this->end_controls_section();

        // Behavior Section
        $this->start_controls_section(
            'behavior_section',
            [
                'label' => esc_html__('Behavior', 'uxd-elementor-extension'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'accordion_type',
            [
                'label' => esc_html__('Accordion Type', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'multiple',
                'options' => [
                    'single' => esc_html__('Single (Close Others)', 'uxd-elementor-extension'),
                    'multiple' => esc_html__('Multiple (Keep Others Open)', 'uxd-elementor-extension'),
                ],
            ]
        );

        $this->add_control(
            'default_expanded',
            [
                'label' => esc_html__('Default Expanded', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'uxd-elementor-extension'),
                'label_off' => esc_html__('No', 'uxd-elementor-extension'),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );

        $this->add_control(
            'animation_speed',
            [
                'label' => esc_html__('Animation Speed (ms)', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['ms'],
                'range' => [
                    'ms' => [
                        'min' => 100,
                        'max' => 1000,
                        'step' => 50,
                    ],
                ],
                'default' => [
                    'unit' => 'ms',
                    'size' => 300,
                ],
            ]
        );

        $this->add_control(
            'expand_icon',
            [
                'label' => esc_html__('Expand Icon', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::ICONS,
                'default' => [
                    'value' => 'fas fa-chevron-down',
                    'library' => 'fa-solid',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Container
        $this->start_controls_section(
            'style_container',
            [
                'label' => esc_html__('Container', 'uxd-elementor-extension'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'container_padding',
            [
                'label' => esc_html__('Padding', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .uxd-taxonomy-accordion-widget' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'container_border',
                'selector' => '{{WRAPPER}} .uxd-taxonomy-accordion-widget',
            ]
        );

        $this->add_responsive_control(
            'container_border_radius',
            [
                'label' => esc_html__('Border Radius', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .uxd-taxonomy-accordion-widget' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'container_background',
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .uxd-taxonomy-accordion-widget',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'container_box_shadow',
                'selector' => '{{WRAPPER}} .uxd-taxonomy-accordion-widget',
            ]
        );

        $this->end_controls_section();

        // Style Section - Items
        $this->start_controls_section(
            'style_items',
            [
                'label' => esc_html__('Items', 'uxd-elementor-extension'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'item_typography',
                'selector' => '{{WRAPPER}} .uxd-taxonomy-item > a',
            ]
        );

        $this->start_controls_tabs('item_style_tabs');

        $this->start_controls_tab(
            'item_normal_tab',
            [
                'label' => esc_html__('Normal', 'uxd-elementor-extension'),
            ]
        );

        $this->add_control(
            'item_text_color',
            [
                'label' => esc_html__('Text Color', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .uxd-taxonomy-item > a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'item_background',
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .uxd-taxonomy-item > a',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'item_hover_tab',
            [
                'label' => esc_html__('Hover', 'uxd-elementor-extension'),
            ]
        );

        $this->add_control(
            'item_hover_text_color',
            [
                'label' => esc_html__('Text Color', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .uxd-taxonomy-item > a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'item_hover_background',
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .uxd-taxonomy-item > a:hover',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'item_active_tab',
            [
                'label' => esc_html__('Current/Active', 'uxd-elementor-extension'),
            ]
        );

        $this->add_control(
            'item_active_text_color',
            [
                'label' => esc_html__('Text Color', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .uxd-taxonomy-item.current-item > a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'item_active_background',
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .uxd-taxonomy-item.current-item > a',
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control(
            'item_padding',
            [
                'label' => esc_html__('Padding', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'separator' => 'before',
                'selectors' => [
                    '{{WRAPPER}} .uxd-taxonomy-item > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'item_margin',
            [
                'label' => esc_html__('Margin', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .uxd-taxonomy-item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'item_border',
                'selector' => '{{WRAPPER}} .uxd-taxonomy-item',
            ]
        );

        $this->add_responsive_control(
            'item_border_radius',
            [
                'label' => esc_html__('Border Radius', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .uxd-taxonomy-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Child Items
        $this->start_controls_section(
            'style_child_items',
            [
                'label' => esc_html__('Child Items', 'uxd-elementor-extension'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'child_item_typography',
                'selector' => '{{WRAPPER}} .uxd-taxonomy-children .uxd-taxonomy-item > a',
            ]
        );

        $this->start_controls_tabs('child_item_style_tabs');

        $this->start_controls_tab(
            'child_item_normal_tab',
            [
                'label' => esc_html__('Normal', 'uxd-elementor-extension'),
            ]
        );

        $this->add_control(
            'child_item_text_color',
            [
                'label' => esc_html__('Text Color', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .uxd-taxonomy-children .uxd-taxonomy-item > a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'child_item_background',
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .uxd-taxonomy-children .uxd-taxonomy-item > a',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'child_item_hover_tab',
            [
                'label' => esc_html__('Hover', 'uxd-elementor-extension'),
            ]
        );

        $this->add_control(
            'child_item_hover_text_color',
            [
                'label' => esc_html__('Text Color', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .uxd-taxonomy-children .uxd-taxonomy-item > a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'child_item_hover_background',
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .uxd-taxonomy-children .uxd-taxonomy-item > a:hover',
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control(
            'child_item_padding',
            [
                'label' => esc_html__('Padding', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'separator' => 'before',
                'selectors' => [
                    '{{WRAPPER}} .uxd-taxonomy-children .uxd-taxonomy-item > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'child_item_indent',
            [
                'label' => esc_html__('Indent', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'em'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 5,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 20,
                ],
                'selectors' => [
                    '{{WRAPPER}} .uxd-taxonomy-children' => 'padding-left: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'child_item_border',
                'selector' => '{{WRAPPER}} .uxd-taxonomy-children .uxd-taxonomy-item',
            ]
        );

        $this->end_controls_section();

        // Style Section - Icon
        $this->start_controls_section(
            'style_icon',
            [
                'label' => esc_html__('Icon', 'uxd-elementor-extension'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'icon_color',
            [
                'label' => esc_html__('Icon Color', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .uxd-taxonomy-item > a .uxd-accordion-icon' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'icon_hover_color',
            [
                'label' => esc_html__('Icon Hover Color', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .uxd-taxonomy-item > a:hover .uxd-accordion-icon' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'icon_size',
            [
                'label' => esc_html__('Icon Size', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => [
                        'min' => 8,
                        'max' => 30,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 14,
                ],
                'selectors' => [
                    '{{WRAPPER}} .uxd-taxonomy-item > a .uxd-accordion-icon' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .uxd-taxonomy-item > a .uxd-accordion-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        

        $this->add_responsive_control(
            'icon_spacing',
            [
                'label' => esc_html__('Icon Spacing', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => [
                        'min' => 5,
                        'max' => 50,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 15,
                ],
                'selectors' => [
                    '{{WRAPPER}} .uxd-taxonomy-item.has-children > a .uxd-accordion-icon' => 'margin-left: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Count
        $this->start_controls_section(
            'style_count',
            [
                'label' => esc_html__('Count', 'uxd-elementor-extension'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_count' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'count_color',
            [
                'label' => esc_html__('Count Color', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .uxd-taxonomy-count' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'count_typography',
                'selector' => '{{WRAPPER}} .uxd-taxonomy-count',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'count_background',
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .uxd-taxonomy-count',
            ]
        );

        $this->add_responsive_control(
            'count_padding',
            [
                'label' => esc_html__('Padding', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .uxd-taxonomy-count' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'count_margin',
            [
                'label' => esc_html__('Margin', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .uxd-taxonomy-count' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'count_border',
                'selector' => '{{WRAPPER}} .uxd-taxonomy-count',
            ]
        );

        $this->add_responsive_control(
            'count_border_radius',
            [
                'label' => esc_html__('Border Radius', 'uxd-elementor-extension'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .uxd-taxonomy-count' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Get available taxonomies
     */
    private function get_available_taxonomies() {
        $taxonomies = get_taxonomies(['public' => true], 'objects');
        $options = [];
        
        foreach ($taxonomies as $taxonomy) {
            $options[$taxonomy->name] = $taxonomy->label;
        }
        
        return $options;
    }

    /**
     * Render the widget output on the frontend
     */
    protected function render() {
        $settings = $this->get_settings_for_display();
        
        $taxonomy = $settings['taxonomy_type'];
        $show_count = $settings['show_count'] === 'yes';
        $show_hierarchy = $settings['show_hierarchy'] === 'yes';
        $hide_empty = $settings['hide_empty'] === 'yes';
        $orderby = $settings['orderby'];
        $order = $settings['order'];
        $limit = $settings['limit'];
        $accordion_type = $settings['accordion_type'];
        $default_expanded = $settings['default_expanded'] === 'yes';
        $animation_speed = $settings['animation_speed']['size'];
        $include_terms = !empty($settings['include_terms']) ? array_map('trim', explode(',', $settings['include_terms'])) : [];
        $exclude_terms = !empty($settings['exclude_terms']) ? array_map('trim', explode(',', $settings['exclude_terms'])) : [];

        // Get terms
        $args = [
            'taxonomy' => $taxonomy,
            'hide_empty' => $hide_empty,
            'orderby' => $orderby,
            'order' => $order,
            'parent' => 0, // Get only top-level terms first
        ];

        if ($limit > 0) {
            $args['number'] = $limit;
        }

        if (!empty($include_terms)) {
            $args['include'] = $include_terms;
        }

        if (!empty($exclude_terms)) {
            $args['exclude'] = $exclude_terms;
        }

        $terms = get_terms($args);

        if (empty($terms) || is_wp_error($terms)) {
            echo '<div class="uxd-taxonomy-accordion-empty">' . esc_html__('No terms found.', 'uxd-elementor-extension') . '</div>';
            return;
        }

        // Get current term for highlighting
        $current_term_id = 0;
        if (is_tax() || is_category() || is_tag()) {
            $current_term = get_queried_object();
            if ($current_term && isset($current_term->term_id)) {
                $current_term_id = $current_term->term_id;
            }
        }

        echo '<div class="uxd-taxonomy-accordion-widget" data-accordion-type="' . esc_attr($accordion_type) . '" data-animation-speed="' . esc_attr($animation_speed) . '">';
        echo '<ul class="uxd-taxonomy-list">';

        foreach ($terms as $term) {
            $this->render_term($term, $taxonomy, $show_count, $show_hierarchy, $hide_empty, $current_term_id, $default_expanded, $settings);
        }

        echo '</ul>';
        echo '</div>';
    }

    /**
     * Render individual term
     */
    private function render_term($term, $taxonomy, $show_count, $show_hierarchy, $hide_empty, $current_term_id, $default_expanded, $settings, $level = 0) {
        $term_link = get_term_link($term);
        $is_current = ($current_term_id === $term->term_id);
        
        // Get child terms
        $child_terms = [];
        if ($show_hierarchy) {
            $child_args = [
                'taxonomy' => $taxonomy,
                'hide_empty' => $hide_empty,
                'parent' => $term->term_id,
                'orderby' => 'name',
                'order' => 'ASC',
            ];
            $child_terms = get_terms($child_args);
        }

        $has_children = !empty($child_terms) && !is_wp_error($child_terms);
        $item_classes = ['uxd-taxonomy-item'];
        
        if ($has_children) {
            $item_classes[] = 'has-children';
            if ($default_expanded) {
                $item_classes[] = 'expanded';
            }
        }
        
        if ($is_current) {
            $item_classes[] = 'current-item';
        }

        echo '<li class="' . esc_attr(implode(' ', $item_classes)) . '">';
        
        if ($has_children) {
            echo '<a href="#" class="uxd-accordion-toggle" data-term-id="' . esc_attr($term->term_id) . '">';
        } else {
            echo '<a href="' . esc_url($term_link) . '">';
        }
        
        echo esc_html($term->name);
        
        if ($show_count) {
            echo ' <span class="uxd-taxonomy-count">(' . $term->count . ')</span>';
        }

            $expand_icon = $settings['expand_icon'];
            echo '<span class="uxd-accordion-icon">';
            if (!empty($expand_icon['value'])) {
                if ($expand_icon['library'] === 'svg') {
                    echo $expand_icon['value'];
                } else {
                    \Elementor\Icons_Manager::render_icon($expand_icon, ['aria-hidden' => 'true']);
                }
            } else {
                // Fallback icon
                echo '<i class="fa fa-chevron-down"></i>';
            }
            echo '</span>';
        
        echo '</a>';

        // Render child terms
        if ($has_children) {
            $children_classes = ['uxd-taxonomy-children'];
            if ($default_expanded) {
                $children_classes[] = 'expanded';
            }
            
            echo '<ul class="' . esc_attr(implode(' ', $children_classes)) . '">';
            foreach ($child_terms as $child_term) {
                $this->render_term($child_term, $taxonomy, $show_count, false, $hide_empty, $current_term_id, false, $settings, $level + 1);
            }
            echo '</ul>';
        }

        echo '</li>';
    }

    /**
     * Render the widget output in the editor
     */
    protected function content_template() {
        ?>
        <div class="uxd-taxonomy-accordion-widget">
            <ul class="uxd-taxonomy-list">
                <li class="uxd-taxonomy-item has-children">
                    <a href="#" class="uxd-accordion-toggle">
                        Sample Parent Category
                        <span class="uxd-taxonomy-count">(5)</span>
                        <span class="uxd-accordion-icon">
                            <i class="fas fa-chevron-down"></i>
                        </span>
                    </a>
                    <ul class="uxd-taxonomy-children">
                        <li class="uxd-taxonomy-item">
                            <a href="#">Sample Child Category <span class="uxd-taxonomy-count">(2)</span></a>
                        </li>
                        <li class="uxd-taxonomy-item">
                            <a href="#">Another Child Category <span class="uxd-taxonomy-count">(3)</span></a>
                        </li>
                    </ul>
                </li>
                <li class="uxd-taxonomy-item current-item">
                    <a href="#">Current Category <span class="uxd-taxonomy-count">(8)</span></a>
                </li>
                <li class="uxd-taxonomy-item">
                    <a href="#">Another Category <span class="uxd-taxonomy-count">(12)</span></a>
                </li>
            </ul>
        </div>
        <?php
    }
}