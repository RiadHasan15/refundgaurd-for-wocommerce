<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'wp_dashboard_setup', function() {
    wp_add_dashboard_widget(
        'refundguard_dashboard_widget',
        __( 'RefundGuard Risk Overview', 'refundguard-for-woocommerce' ),
        'refundguard_render_dashboard_widget'
    );
} );

function refundguard_render_dashboard_widget() {
    $today = date( 'Y-m-d' );
    $args = [
        'limit' => -1,
        'status' => array_keys( wc_get_order_statuses() ),
        'date_created' => $today,
        'return' => 'ids',
    ];
    $orders_today = wc_get_orders( $args );
    $high_risk_count = 0;
    foreach ( $orders_today as $order_id ) {
        $risk = refundguard_get_risk_score( $order_id );
        $risk = apply_filters( 'refundguard_risk_score', $risk, $order_id );
        if ( $risk['score'] === 'high' ) {
            $high_risk_count++;
        }
    }
    // Last 7 days average
    $week_ago = date( 'Y-m-d', strtotime( '-6 days' ) );
    $args7 = [
        'limit' => -1,
        'status' => array_keys( wc_get_order_statuses() ),
        'date_created' => $week_ago . '...' . $today,
        'return' => 'ids',
    ];
    $orders_7 = wc_get_orders( $args7 );
    $total_score = 0;
    $count = 0;
    foreach ( $orders_7 as $order_id ) {
        $risk = refundguard_get_risk_score( $order_id );
        $risk = apply_filters( 'refundguard_risk_score', $risk, $order_id );
        if ( $risk['score'] === 'high' ) $total_score += 3;
        elseif ( $risk['score'] === 'medium' ) $total_score += 2;
        else $total_score += 1;
        $count++;
    }
    $avg = $count ? round( $total_score / $count, 2 ) : 0;
    $avg_label = $avg >= 2.5 ? __( 'High', 'refundguard-for-woocommerce' ) : ( $avg >= 1.5 ? __( 'Medium', 'refundguard-for-woocommerce' ) : __( 'Low', 'refundguard-for-woocommerce' ) );
    echo '<ul style="margin:0 0 1em 1em;">';
    echo '<li><strong>' . __( 'Today\'s High-Risk Orders:', 'refundguard-for-woocommerce' ) . '</strong> ' . intval( $high_risk_count ) . '</li>';
    echo '<li><strong>' . __( 'Avg. Risk Score (7 days):', 'refundguard-for-woocommerce' ) . '</strong> ' . esc_html( $avg_label ) . ' (' . esc_html( $avg ) . ')</li>';
    echo '</ul>';
}