<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// Only load if WooCommerce is active
if ( ! class_exists( 'WooCommerce' ) ) return;

// Pro features loader
require_once __DIR__ . '/ai-scoring.php';
require_once __DIR__ . '/analytics.php';
require_once __DIR__ . '/export.php';
require_once __DIR__ . '/whatsapp-alert.php';
require_once __DIR__ . '/po-generator.php';
require_once __DIR__ . '/restock-importer.php';

// Override risk score logic to use AI if available
add_filter( 'refundguard_risk_score', function( $risk, $order_id ) {
    return refundguard_get_ai_risk_score( $order_id );
}, 10, 2 );

// Add OpenAI API key field to settings page
add_action( 'refundguard_settings_fields', function() {
    $api_key = get_option( 'refundguard_openai_api_key', '' );
    echo '<tr><th>' . __( 'OpenAI API Key (Pro)', 'refundguard-for-woocommerce' ) . '</th><td><input type="text" name="openai_api_key" value="' . esc_attr( $api_key ) . '" size="40" /></td></tr>';
} );

add_action( 'refundguard_settings_save', function() {
    if ( isset( $_POST['openai_api_key'] ) ) {
        update_option( 'refundguard_openai_api_key', sanitize_text_field( $_POST['openai_api_key'] ) );
    }
} );

// TODO: Implement AI-powered risk scoring, auto-flag orders, advanced analytics, export, alerts, PO generation, CSV importer.