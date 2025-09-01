<?php
/**
 * UXD Locked Widget Class
 * Shows upgrade prompts for premium widgets
 */

if (!defined('ABSPATH')) {
    exit;
}

class UXD_Locked_Widget extends \Elementor\Widget_Base {
    
    private $widget_slug;
    private $widget_data;
    
    /**
     * Constructor
     */
    public function __construct($slug, $data = []) {
        $this->widget_slug = $slug;
        $this->widget_data = $data;
        
        parent::__construct([], [
            'widget_name' => $this->widget_data['title'] ?? 'Premium Widget'
        ]);
    }
    
    /**
     * Get widget name
     */
    public function get_name() {
        return 'uxd-locked-' . $this->widget_slug;
    }
    
    /**
     * Get widget title
     */
    public function get_title() {
        return $this->widget_data['title'] ?? 'Premium Widget';
    }
    
    /**
     * Get widget icon
     */
    public function get_icon() {
        return $this->widget_data['icon'] ?? 'eicon-lock';
    }
    
    /**
     * Get widget categories
     */
    public function get_categories() {
        return ['uxd-free'];
    }
    
    /**
     * Get widget keywords
     */
    public function get_keywords() {
        return ['premium', 'pro', 'locked', 'uxd'];
    }
    
    /**
     * Register widget controls
     */
    protected function register_controls() {
        $this->start_controls_section(
            'section_locked',
            [
                'label' => esc_html__('Premium Feature', 'uxd-elementor-extension'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        
        $this->add_control(
            'locked_notice',
            [
                'type' => \Elementor\Controls_Manager::RAW_HTML,
                'raw' => $this->get_upgrade_message(),
                'content_classes' => 'uxd-locked-widget-notice',
            ]
        );
        
        $this->end_controls_section();
    }
    
    /**
     * Get upgrade message
     */
    private function get_upgrade_message() {
        $description = $this->widget_data['description'] ?? 'This is a premium widget with advanced features.';
        
        $message = sprintf(
            '<div class="uxd-locked-widget-container">
                <div class="uxd-locked-icon">
                    <i class="eicon-lock" aria-hidden="true"></i>
                </div>
                <h3>%1$s</h3>
                <p>%2$s</p>
                <div class="uxd-locked-features">
                    <h4>Premium Features Include:</h4>
                    <ul>
                        <li>✓ Advanced Customization Options</li>
                        <li>✓ Multiple Layout Variations</li>
                        <li>✓ Professional Animations</li>
                        <li>✓ Priority Support</li>
                        <li>✓ Regular Updates</li>
                    </ul>
                </div>
                <div class="uxd-locked-actions">
                    <a href="%3$s" class="uxd-upgrade-btn" target="_blank">
                        <i class="eicon-star" aria-hidden="true"></i>
                        Upgrade to Pro
                    </a>
                    <a href="%4$s" class="uxd-trial-btn" target="_blank">
                        <i class="eicon-play" aria-hidden="true"></i>
                        Start Free Trial
                    </a>
                </div>
                <div class="uxd-money-back">
                    <small>💰 30-day money-back guarantee</small>
                </div>
            </div>',
            esc_html($this->widget_data['title']),
            esc_html($description),
            esc_url(uxd_ee_get_pricing_url()),
            esc_url(uxd_ee_get_pricing_url() . '&trial=true')
        );
        
        return $message;
    }
    
    /**
     * Render widget output
     */
    protected function render() {
        ?>
        <div class="uxd-locked-widget-preview">
            <?php echo $this->get_upgrade_message(); ?>
        </div>
        
        <style>
        .uxd-locked-widget-container {
            text-align: center;
            padding: 40px 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
        }
        
        .uxd-locked-widget-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="25" cy="75" r="0.5" fill="rgba(255,255,255,0.05)"/><circle cx="75" cy="25" r="0.5" fill="rgba(255,255,255,0.05)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
            pointer-events: none;
        }
        
        .uxd-locked-widget-container > * {
            position: relative;
            z-index: 1;
        }
        
        .uxd-locked-icon {
            font-size: 48px;
            margin-bottom: 20px;
            opacity: 0.8;
        }
        
        .uxd-locked-widget-container h3 {
            margin: 0 0 15px 0;
            font-size: 24px;
            font-weight: 600;
        }
        
        .uxd-locked-widget-container p {
            margin: 0 0 25px 0;
            font-size: 16px;
            opacity: 0.9;
            line-height: 1.5;
        }
        
        .uxd-locked-features {
            margin: 25px 0;
            padding: 20px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            backdrop-filter: blur(10px);
        }
        
        .uxd-locked-features h4 {
            margin: 0 0 15px 0;
            font-size: 16px;
            font-weight: 600;
        }
        
        .uxd-locked-features ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .uxd-locked-features li {
            padding: 5px 0;
            font-size: 14px;
            opacity: 0.9;
        }
        
        .uxd-locked-actions {
            margin: 25px 0;
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .uxd-upgrade-btn,
        .uxd-trial-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .uxd-upgrade-btn {
            background: #ff6b6b;
            color: white;
        }
        
        .uxd-upgrade-btn:hover {
            background: #ff5252;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 107, 107, 0.4);
        }
        
        .uxd-trial-btn {
            background: transparent;
            color: white;
            border-color: rgba(255, 255, 255, 0.3);
        }
        
        .uxd-trial-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.5);
            transform: translateY(-1px);
        }
        
        .uxd-money-back {
            margin-top: 20px;
            opacity: 0.8;
        }
        
        .uxd-money-back small {
            font-size: 12px;
            background: rgba(255, 255, 255, 0.1);
            padding: 5px 12px;
            border-radius: 15px;
            display: inline-block;
        }
        
        @media (max-width: 600px) {
            .uxd-locked-widget-container {
                padding: 30px 15px;
            }
            
            .uxd-locked-actions {
                flex-direction: column;
                align-items: center;
            }
            
            .uxd-upgrade-btn,
            .uxd-trial-btn {
                width: 100%;
                max-width: 200px;
                justify-content: center;
            }
        }
        </style>
        <?php
    }
    
    /**
     * Render widget in editor
     */
    protected function content_template() {
        ?>
        <div class="uxd-locked-widget-preview">
            <div class="uxd-locked-widget-container">
                <div class="uxd-locked-icon">
                    <i class="eicon-lock" aria-hidden="true"></i>
                </div>
                <h3><?php echo esc_html($this->widget_data['title']); ?></h3>
                <p><?php echo esc_html($this->widget_data['description'] ?? 'This is a premium widget.'); ?></p>
                <div class="uxd-locked-features">
                    <h4>Premium Features Include:</h4>
                    <ul>
                        <li>✓ Advanced Customization Options</li>
                        <li>✓ Multiple Layout Variations</li>
                        <li>✓ Professional Animations</li>
                        <li>✓ Priority Support</li>
                    </ul>
                </div>
                <div class="uxd-locked-actions">
                    <a href="#" class="uxd-upgrade-btn">
                        <i class="eicon-star" aria-hidden="true"></i>
                        Upgrade to Pro
                    </a>
                    <a href="#" class="uxd-trial-btn">
                        <i class="eicon-play" aria-hidden="true"></i>
                        Start Free Trial
                    </a>
                </div>
                <div class="uxd-money-back">
                    <small>💰 30-day money-back guarantee</small>
                </div>
            </div>
        </div>
        <?php
    }
}