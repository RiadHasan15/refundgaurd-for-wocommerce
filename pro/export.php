<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function refundguard_render_export_page_pro() {
    echo '<div class="wrap"><h1>' . __( 'Export High-Risk Orders', 'refundguard-for-woocommerce' ) . '</h1>';
    echo '<form method="get" action="" style="margin-bottom:1em;">';
    echo '<input type="hidden" name="page" value="refundguard-export" />';
    echo '<button type="submit" name="export_csv" class="button button-secondary">' . __( 'Export High-Risk Orders to CSV', 'refundguard-for-woocommerce' ) . '</button>';
    echo '</form>';

    $orders = wc_get_orders([
        'limit' => 100,
        'status' => array_keys( wc_get_order_statuses() ),
        'return' => 'objects',
    ]);
    echo '<table class="widefat fixed striped"><thead><tr>';
    echo '<th>' . __( 'Order ID', 'refundguard-for-woocommerce' ) . '</th>';
    echo '<th>' . __( 'Customer', 'refundguard-for-woocommerce' ) . '</th>';
    echo '<th>' . __( 'Email', 'refundguard-for-woocommerce' ) . '</th>';
    echo '<th>' . __( 'Total', 'refundguard-for-woocommerce' ) . '</th>';
    echo '<th>' . __( 'Risk', 'refundguard-for-woocommerce' ) . '</th>';
    echo '<th>' . __( 'Reason', 'refundguard-for-woocommerce' ) . '</th>';
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
            echo '</tr>';
        }
    }
    echo '</tbody></table>';
}

add_action( 'wp_ajax_refundguard_export_csv', function() {
    if ( ! current_user_can( 'manage_woocommerce' ) ) wp_die();
    $orders = wc_get_orders([
        'limit' => -1,
        'status' => array_keys( wc_get_order_statuses() ),
        'return' => 'objects',
    ]);
    header( 'Content-Type: text/csv' );
    header( 'Content-Disposition: attachment; filename=high-risk-orders.csv' );
    $out = fopen( 'php://output', 'w' );
    fputcsv( $out, [ 'Order ID', 'Customer', 'Email', 'Total', 'Risk', 'Reason' ] );
    foreach ( $orders as $order ) {
        $risk = refundguard_get_ai_risk_score( $order->get_id() );
        if ( $risk['score'] === 'high' ) {
            fputcsv( $out, [
                $order->get_id(),
                $order->get_formatted_billing_full_name(),
                $order->get_billing_email(),
                $order->get_total(),
                ucfirst( $risk['score'] ),
                $risk['reason']
            ] );
        }
    }
    fclose( $out );
    exit;
} );