<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'woocommerce_thankyou', function( $order_id ) {
    $risk = refundguard_get_ai_risk_score( $order_id );
    if ( $risk['score'] === 'high' ) {
        $order = wc_get_order( $order_id );
        // Email alert
        $admin_email = get_option( 'admin_email' );
        $subject = __( 'High-Risk Order Alert', 'refundguard-for-woocommerce' );
        $message = sprintf( __( 'Order #%d has been flagged as HIGH RISK.\nReason: %s', 'refundguard-for-woocommerce' ), $order_id, $risk['reason'] );
        wp_mail( $admin_email, $subject, $message );
        // WhatsApp alert (stub)
        do_action( 'refundguard_send_whatsapp', [
            'order_id' => $order_id,
            'message' => $message,
        ] );
    }
} );