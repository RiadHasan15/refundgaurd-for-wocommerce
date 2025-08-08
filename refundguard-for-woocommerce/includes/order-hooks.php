<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// Add badge to admin order list
add_filter( 'manage_edit-shop_order_columns', function( $columns ) {
    $columns['refundguard_risk'] = __( 'Refund Risk', 'refundguard-for-woocommerce' );
    return $columns;
} );

add_action( 'manage_shop_order_posts_custom_column', function( $column, $post_id ) {
    if ( $column === 'refundguard_risk' ) {
        $risk = refundguard_get_risk_score( $post_id );
        $color = $risk['score'] === 'high' ? '#e74c3c' : ( $risk['score'] === 'medium' ? '#f39c12' : '#27ae60' );
        $label = ucfirst( $risk['score'] );
        echo '<span style="display:inline-block;padding:2px 8px;border-radius:12px;background:' . esc_attr( $color ) . ';color:#fff;font-weight:bold;">' . esc_html( $label ) . '</span>';
    }
}, 10, 2 );

// Add badge to single order view (admin)
add_action( 'woocommerce_admin_order_data_after_order_details', function( $order ) {
    $risk = refundguard_get_risk_score( $order->get_id() );
    $color = $risk['score'] === 'high' ? '#e74c3c' : ( $risk['score'] === 'medium' ? '#f39c12' : '#27ae60' );
    $label = ucfirst( $risk['score'] );
    echo '<p><strong>' . __( 'Refund Risk:', 'refundguard-for-woocommerce' ) . '</strong> <span style="display:inline-block;padding:2px 8px;border-radius:12px;background:' . esc_attr( $color ) . ';color:#fff;font-weight:bold;">' . esc_html( $label ) . '</span> <span style="color:#888;font-size:11px;">' . esc_html( $risk['reason'] ) . '</span></p>';
} );