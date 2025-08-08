<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// Add badge to admin order list (after Status, before Total)
add_filter( 'manage_edit-shop_order_columns', function( $columns ) {
    $new_columns = [];
    foreach ( $columns as $key => $label ) {
        $new_columns[$key] = $label;
        if ( $key === 'order_status' ) {
            $new_columns['refundguard_risk'] = __( 'Refund Risk', 'refundguard-for-woocommerce' );
        }
    }
    return $new_columns;
} );

add_action( 'manage_shop_order_posts_custom_column', function( $column, $post_id ) {
    if ( $column === 'refundguard_risk' ) {
        $risk = refundguard_get_risk_score( $post_id );
        $risk = apply_filters( 'refundguard_risk_score', $risk, $post_id );
        $color = $risk['score'] === 'high' ? '#e74c3c' : ( $risk['score'] === 'medium' ? '#f39c12' : '#27ae60' );
        $label = ucfirst( $risk['score'] );
        echo '<span style="display:inline-block;padding:2px 8px;border-radius:12px;background:' . esc_attr( $color ) . ';color:#fff;font-weight:bold;">' . esc_html( $label ) . '</span>';
        if ( !empty($risk['reason']) ) {
            echo '<br><span style="color:#888;font-size:11px;">' . esc_html( $risk['reason'] ) . '</span>';
        }
    }
}, 10, 2 );

// Add prominent RefundGuard Risk section to single order view (admin)
add_action( 'woocommerce_admin_order_data_after_order_details', function( $order ) {
    $risk = refundguard_get_risk_score( $order->get_id() );
    $risk = apply_filters( 'refundguard_risk_score', $risk, $order->get_id() );
    $color = $risk['score'] === 'high' ? '#e74c3c' : ( $risk['score'] === 'medium' ? '#f39c12' : '#27ae60' );
    $label = ucfirst( $risk['score'] );
    echo '<div style="margin:18px 0;padding:18px 24px;border-radius:8px;background:' . esc_attr($color) . ';color:#fff;box-shadow:0 2px 8px rgba(0,0,0,0.07);max-width:420px;">';
    echo '<h3 style="margin:0 0 8px 0;font-size:1.2em;">🛡️ ' . __( 'RefundGuard Risk', 'refundguard-for-woocommerce' ) . '</h3>';
    echo '<div style="font-size:1.1em;font-weight:bold;margin-bottom:4px;">' . esc_html( $label ) . '</div>';
    if ( !empty($risk['reason']) ) {
        echo '<div style="font-size:1em;opacity:0.95;">' . esc_html( $risk['reason'] ) . '</div>';
    }
    echo '</div>';
} );