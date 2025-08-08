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
        // WhatsApp alert (Twilio)
        $sid = get_option('refundguard_twilio_sid', '');
        $token = get_option('refundguard_twilio_token', '');
        $from = get_option('refundguard_twilio_from', '');
        $to = get_option('refundguard_twilio_to', '');
        if ($sid && $token && $from && $to) {
            refundguard_send_whatsapp_twilio($sid, $token, $from, $to, $message);
        }
        // Custom WhatsApp hook for other providers
        do_action( 'refundguard_send_whatsapp', [
            'order_id' => $order_id,
            'message' => $message,
        ] );
    }
} );

function refundguard_send_whatsapp_twilio($sid, $token, $from, $to, $message) {
    $url = 'https://api.twilio.com/2010-04-01/Accounts/' . rawurlencode($sid) . '/Messages.json';
    $args = [
        'body' => [
            'From' => $from,
            'To' => $to,
            'Body' => $message,
        ],
        'headers' => [
            'Authorization' => 'Basic ' . base64_encode($sid . ':' . $token),
        ],
        'timeout' => 15,
    ];
    $response = wp_remote_post($url, $args);
    // Optionally log or handle errors here
}

function refundguard_render_alerts_page_pro() {
    echo '<div class="wrap"><h1>' . __( 'Alerts', 'refundguard-for-woocommerce' ) . '</h1>';
    echo '<p>' . __( 'High-risk order alerts are sent to the admin email and via WhatsApp (if configured) when a high-risk order is detected.', 'refundguard-for-woocommerce' ) . '</p>';
    echo '<ul>';
    echo '<li>' . __( 'Email alerts: Sent to the site admin email.', 'refundguard-for-woocommerce' ) . '</li>';
    echo '<li>' . __( 'WhatsApp alerts: Sent via Twilio if configured in settings.', 'refundguard-for-woocommerce' ) . '</li>';
    echo '</ul>';
    echo '<p><em>' . __( 'To use WhatsApp alerts, enter your Twilio credentials in the settings page.', 'refundguard-for-woocommerce' ) . '</em></p>';
    // Show recent high-risk orders
    $orders = wc_get_orders([
        'limit' => 20,
        'status' => array_keys( wc_get_order_statuses() ),
        'return' => 'objects',
    ]);
    echo '<h2>' . __( 'Recent High-Risk Orders', 'refundguard-for-woocommerce' ) . '</h2>';
    echo '<table class="widefat fixed striped"><thead><tr>';
    echo '<th>' . __( 'Order ID', 'refundguard-for-woocommerce' ) . '</th>';
    echo '<th>' . __( 'Customer', 'refundguard-for-woocommerce' ) . '</th>';
    echo '<th>' . __( 'Email', 'refundguard-for-woocommerce' ) . '</th>';
    echo '<th>' . __( 'Total', 'refundguard-for-woocommerce' ) . '</th>';
    echo '<th>' . __( 'Risk', 'refundguard-for-woocommerce' ) . '</th>';
    echo '<th>' . __( 'Reason', 'refundguard-for-woocommerce' ) . '</th>';
    echo '<th>' . __( 'Alert Status', 'refundguard-for-woocommerce' ) . '</th>';
    echo '</tr></thead><tbody>';
    foreach ( $orders as $order ) {
        $risk = refundguard_get_ai_risk_score( $order->get_id() );
        if ( $risk['score'] === 'high' ) {
            echo '<tr>';
            echo '<td><a href="' . esc_url( get_edit_post_link( $order->get_id() ) ) . '">' . esc_html( $order->get_id() ) . '</a></td>';
            echo '<td>' . esc_html( $order->get_formatted_billing_full_name() ) . '</td>';
            echo '<td>' . esc_html( $order->get_billing_email() ) . '</td>';
            echo '<td>' . wc_price( $order->get_total() ) . '</td>';
            echo '<td><span style="color:#fff;padding:2px 8px;border-radius:12px;background:#e74c3c;font-weight:bold;">High</span></td>';
            echo '<td>' . esc_html( $risk['reason'] ) . '</td>';
            echo '<td>' . __( 'Alert Sent', 'refundguard-for-woocommerce' ) . '</td>';
            echo '</tr>';
        }
    }
    echo '</tbody></table>';
    echo '</div>';
}