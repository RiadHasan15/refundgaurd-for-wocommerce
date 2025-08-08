<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// Only load if WooCommerce is active
if ( ! class_exists( 'WooCommerce' ) ) return;

// Only load Pro features if license is present
add_action('admin_init', function() {
    $license = get_option('refundguard_pro_license', '');
    if (empty($license)) {
        add_action('admin_notices', function() {
            echo '<div class="notice notice-warning"><p>' . __( 'RefundGuard Pro is installed but not activated. Please enter your license key in RefundGuard > License.', 'refundguard-for-woocommerce' ) . '</p></div>';
        });
        return;
    }
    // Pro features loader (require other pro files here)
    require_once __DIR__ . '/ai-scoring.php';
    require_once __DIR__ . '/analytics.php';
    require_once __DIR__ . '/export.php';
    require_once __DIR__ . '/whatsapp-alert.php';
    require_once __DIR__ . '/po-generator.php';
    require_once __DIR__ . '/restock-importer.php';
});

// TODO: Implement AI-powered risk scoring, auto-flag orders, advanced analytics, export, alerts, PO generation, CSV importer.