<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// AI Risk Scoring (stub)
function refundguard_get_ai_risk_score( $order_id ) {
    $ai_enabled = get_option('refundguard_pro_ai_enabled', '1');
    if ($ai_enabled !== '1') {
        if ( function_exists( 'refundguard_get_risk_score' ) ) {
            return refundguard_get_risk_score( $order_id );
        }
        return [ 'score' => 'low', 'reason' => __( 'AI scoring disabled, using rule-based', 'refundguard-for-woocommerce' ) ];
    }
    $api_key = get_option( 'refundguard_openai_api_key', '' );
    if ( empty( $api_key ) ) {
        // Fallback to rule-based
        if ( function_exists( 'refundguard_get_risk_score' ) ) {
            return refundguard_get_risk_score( $order_id );
        }
        return [ 'score' => 'low', 'reason' => __( 'No API key, using rule-based', 'refundguard-for-woocommerce' ) ];
    }
    $order = wc_get_order( $order_id );
    if ( ! $order ) return [ 'score' => 'low', 'reason' => __( 'Order not found', 'refundguard-for-woocommerce' ) ];
    // Compose prompt (simplified)
    $prompt = 'Analyze the following WooCommerce order for refund/return risk. Reply with only one of: Low, Medium, High.\nOrder Data: ' . json_encode( $order->get_data() );
    $response = refundguard_openai_request( $api_key, $prompt );
    $score = 'low';
    if ( stripos( $response, 'high' ) !== false ) $score = 'high';
    elseif ( stripos( $response, 'medium' ) !== false ) $score = 'medium';
    return [ 'score' => $score, 'reason' => __( 'AI-powered score', 'refundguard-for-woocommerce' ) ];
}

function refundguard_openai_request( $api_key, $prompt ) {
    $args = [
        'body' => json_encode([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [ 'role' => 'system', 'content' => 'You are a WooCommerce refund risk analyst.' ],
                [ 'role' => 'user', 'content' => $prompt ]
            ],
            'max_tokens' => 10,
        ]),
        'headers' => [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $api_key,
        ],
        'timeout' => 15,
    ];
    $response = wp_remote_post( 'https://api.openai.com/v1/chat/completions', $args );
    if ( is_wp_error( $response ) ) return 'Low';
    $body = json_decode( wp_remote_retrieve_body( $response ), true );
    if ( isset( $body['choices'][0]['message']['content'] ) ) {
        return $body['choices'][0]['message']['content'];
    }
    return 'Low';
}

// Auto-flag high-risk orders after payment
add_action( 'woocommerce_thankyou', function( $order_id ) {
    $auto_flag = get_option('refundguard_pro_auto_flag', '1');
    $email_alerts = get_option('refundguard_pro_email_alerts', '1');
    $risk = refundguard_get_ai_risk_score( $order_id );
    if ( $risk['score'] === 'high' ) {
        $order = wc_get_order( $order_id );
        if ( $auto_flag === '1' && $order && $order->get_status() !== 'on-hold' ) {
            $order->update_status( 'on-hold', __( 'Auto-flagged by RefundGuard: High refund risk', 'refundguard-for-woocommerce' ) );
        }
        if ( $email_alerts === '1' ) {
            $admin_email = get_option( 'admin_email' );
            $subject = __( 'High-Risk Order Alert', 'refundguard-for-woocommerce' );
            $message = sprintf( __( 'Order #%d has been flagged as HIGH RISK.\nReason: %s', 'refundguard-for-woocommerce' ), $order_id, $risk['reason'] );
            wp_mail( $admin_email, $subject, $message );
        }
    }
} );
// Optionally, flag on status change to processing
add_action( 'woocommerce_order_status_processing', function( $order_id ) {
    $risk = refundguard_get_ai_risk_score( $order_id );
    if ( $risk['score'] === 'high' ) {
        $order = wc_get_order( $order_id );
        if ( $order && $order->get_status() !== 'on-hold' ) {
            $order->update_status( 'on-hold', __( 'Auto-flagged by RefundGuard: High refund risk', 'refundguard-for-woocommerce' ) );
        }
    }
} );