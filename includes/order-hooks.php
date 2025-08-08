<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// Add Refund Risk column after Status, before Total
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
        $class = $risk['score'] === 'high' ? 'status-cancelled tips' : ($risk['score'] === 'medium' ? 'status-on-hold tips' : 'status-completed tips');
        $label = ucfirst( $risk['score'] );
        echo '<mark class="' . esc_attr($class) . '" style="padding:2px 8px;font-size:12px;">' . esc_html( $label ) . '</mark>';
    }
}, 10, 2 );

// Add RefundGuard Risk as a WooCommerce admin notice in single order view
add_action( 'woocommerce_admin_order_data_after_order_details', function( $order ) {
    $risk = refundguard_get_risk_score( $order->get_id() );
    $risk = apply_filters( 'refundguard_risk_score', $risk, $order->get_id() );
    $notice_class = $risk['score'] === 'high' ? 'notice-error' : ($risk['score'] === 'medium' ? 'notice-warning' : 'notice-success');
    $label = ucfirst( $risk['score'] );
    echo '<div class="notice ' . esc_attr($notice_class) . ' is-dismissible" style="margin:18px 0 0 0;">';
    echo '<p><strong>RefundGuard Risk: ' . esc_html($label) . '</strong>';
    if ( !empty($risk['reason']) ) {
        echo ' &mdash; <span style="color:#555;">' . esc_html($risk['reason']) . '</span>';
    }
    echo '</p></div>';
} );