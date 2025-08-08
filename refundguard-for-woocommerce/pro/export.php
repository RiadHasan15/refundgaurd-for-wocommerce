<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// Add export button to analytics page
add_action( 'refundguard_analytics_export_button', function() {
    echo '<a href="#" class="button button-secondary" id="refundguard-export-csv">' . __( 'Export High-Risk Orders to CSV', 'refundguard-for-woocommerce' ) . '</a>';
    echo '<script>
    document.addEventListener("DOMContentLoaded", function() {
        var btn = document.getElementById("refundguard-export-csv");
        if(btn) btn.onclick = function(e) {
            e.preventDefault();
            window.location = "' . admin_url( 'admin-ajax.php?action=refundguard_export_csv' ) . '";
        };
    });
    </script>';
} );

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