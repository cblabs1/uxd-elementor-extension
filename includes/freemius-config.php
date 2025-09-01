<?php
/**
 * Freemius Configuration
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('uxd_ee_freemius')) {
    /**
     * Initialize Freemius SDK
     */
    function uxd_ee_freemius() {
        global $uxd_ee_fs;

        if (!isset($uxd_ee_fs)) {
            // Include Freemius SDK only if it doesn't exist
            if (!class_exists('Freemius')) {
                require_once dirname(dirname(__FILE__)) . '/freemius/start.php';
            }

            $uxd_ee_fs = fs_dynamic_init([
                'id'                  => '20507', // Your Freemius plugin ID
                'slug'                => 'uxd-elementor-extension',
                'type'                => 'plugin',
                'public_key'          => 'pk_5f21923716f449129556c1862359d',
                'is_premium'          => true,
                'has_addons'          => false,
                'has_paid_plans'      => true,
                'trial'               => [
                    'days'               => 14,
                    'is_require_payment' => false,
                ],
                'menu'                => [
                    'slug'           => 'uxd-elementor-extension',
                    'first-path'     => 'admin.php?page=uxd-elementor-extension',
                    'account'        => false,
                    'pricing'        => false,
                    'contact'        => false,
                    'support'        => false,
                ],
            ]);
        }

        return $uxd_ee_fs;
    }

    // Init Freemius
    uxd_ee_freemius();
    
    // Signal that SDK was initiated
    do_action('uxd_ee_fs_loaded');
}

/**
 * Check if user can use plugin (premium features)
 */
function uxd_ee_can_use_plugin() {
    // For development/testing - you can temporarily return true
    // return true;
    
    if (function_exists('uxd_ee_fs')) {
        $fs = uxd_ee_fs();
        return $fs->is_premium() || $fs->is_trial();
    }
    
    // Fallback - allow if Freemius is not initialized
    return true;
}

/**
 * Get pricing URL
 */
function uxd_ee_get_pricing_url() {
    if (function_exists('uxd_ee_fs')) {
        $fs = uxd_ee_fs();
        return $fs->get_upgrade_url();
    }
    
    // Fallback URL
    return 'https://uxdesignexperts.com/pricing/';
}

/**
 * Check if this is a trial
 */
function uxd_ee_is_trial() {
    if (function_exists('uxd_ee_fs')) {
        $fs = uxd_ee_fs();
        return $fs->is_trial();
    }
    
    return false;
}

/**
 * Get trial days left
 */
function uxd_ee_get_trial_days_left() {
    if (function_exists('uxd_ee_fs')) {
        $fs = uxd_ee_fs();
        if ($fs->is_trial()) {
            return $fs->get_trial_plan()->days_left;
        }
    }
    
    return 0;
}

add_action('admin_notices', function() {
    if (isset($_GET['page']) && strpos($_GET['page'], 'uxd-') !== false) {
        ?>
        <div class="notice notice-info">
            <p>
                <strong>UXD Elementor Extension:</strong> 
                You're using the free version with the Gallery Grid widget. 
                <a href="<?php echo esc_url(uxd_ee_get_pricing_url()); ?>" target="_blank">Upgrade to Pro</a> 
                for access to WooCommerce widgets and advanced features.
            </p>
        </div>
        <?php
    }
});