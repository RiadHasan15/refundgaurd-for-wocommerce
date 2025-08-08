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

function refundguard_render_alerts_page_pro() {
    echo '<div class="wrap"><h1>' . __( 'Alerts', 'refundguard-for-woocommerce' ) . '</h1>';
    echo '<p>' . __( 'High-risk order alerts are sent to the admin email and via WhatsApp (if configured) when a high-risk order is detected.', 'refundguard-for-woocommerce' ) . '</p>';
    echo '<ul>';
    echo '<li>' . __( 'Email alerts: Sent to the site admin email.', 'refundguard-for-woocommerce' ) . '</li>';
    echo '<li>' . __( 'WhatsApp alerts: Integrate with your WhatsApp provider using the refundguard_send_whatsapp action.', 'refundguard-for-woocommerce' ) . '</li>';
    echo '</ul>';
    echo '<p><em>' . __( 'To customize WhatsApp integration, hook into refundguard_send_whatsapp.', 'refundguard-for-woocommerce' ) . '</em></p>';
    echo '</div>';
}