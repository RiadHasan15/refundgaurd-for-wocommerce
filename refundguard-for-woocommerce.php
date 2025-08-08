<?php
/*
Plugin Name: RefundGuard for WooCommerce
Plugin URI: https://wordpress.org/plugins/refundguard-for-woocommerce/
Description: RefundGuard for WooCommerce is a smart refund risk scanner that helps WooCommerce store owners detect and manage potentially risky orders. With refund score badges, analytics, and optional AI-powered tools, RefundGuard gives you peace of mind before shipping anything.
Version: 1.0.0
Author: RefundGuard Team
Author URI: https://refundguard.com/
License: GPL2
Text Domain: refundguard-for-woocommerce
Domain Path: /languages
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

define( 'REFUNDGUARD_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'REFUNDGUARD_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

define( 'REFUNDGUARD_PRO_PATH', REFUNDGUARD_PLUGIN_DIR . 'pro/refundguard-pro.php' );

// Activation/Deactivation Hooks
register_activation_hook( __FILE__, 'refundguard_activate' );
register_deactivation_hook( __FILE__, 'refundguard_deactivate' );

function refundguard_activate() {
    // Activation logic here
}

function refundguard_deactivate() {
    // Deactivation logic here
}

// Always load Free core/admin files
require_once REFUNDGUARD_PLUGIN_DIR . 'includes/risk-score.php';
require_once REFUNDGUARD_PLUGIN_DIR . 'includes/order-hooks.php';
require_once REFUNDGUARD_PLUGIN_DIR . 'admin/dashboard.php';
require_once REFUNDGUARD_PLUGIN_DIR . 'admin/settings-page.php';
require_once REFUNDGUARD_PLUGIN_DIR . 'admin/license-page.php';

// Load Pro features if present
if ( file_exists( REFUNDGUARD_PRO_PATH ) ) {
    require_once REFUNDGUARD_PRO_PATH;
}

// Admin Menu Registration (top-level RefundGuard menu)
add_action( 'admin_menu', 'refundguard_admin_menu' );
function refundguard_admin_menu() {
    add_menu_page(
        __( 'RefundGuard', 'refundguard-for-woocommerce' ),
        __( 'RefundGuard', 'refundguard-for-woocommerce' ),
        'manage_woocommerce',
        'refundguard',
        'refundguard_render_settings_page',
        'dashicons-shield-alt',
        56
    );
    add_submenu_page(
        'refundguard',
        __( 'Settings', 'refundguard-for-woocommerce' ),
        __( 'Settings', 'refundguard-for-woocommerce' ),
        'manage_woocommerce',
        'refundguard',
        'refundguard_render_settings_page'
    );
    add_submenu_page(
        'refundguard',
        __( 'Analytics', 'refundguard-for-woocommerce' ),
        __( 'Analytics', 'refundguard-for-woocommerce' ),
        'manage_woocommerce',
        'refundguard-analytics',
        'refundguard_render_analytics_page'
    );
    add_submenu_page(
        'refundguard',
        __( 'License', 'refundguard-for-woocommerce' ),
        __( 'License', 'refundguard-for-woocommerce' ),
        'manage_woocommerce',
        'refundguard-license',
        'refundguard_render_license_page'
    );
}

function refundguard_render_pro_upsell_page() {
    echo '<div class="wrap"><h1>RefundGuard Pro</h1><p>' . __( 'This feature is available in RefundGuard Pro. Please enter your license key in the License tab to unlock Pro features.', 'refundguard-for-woocommerce' ) . '</p></div>';
}