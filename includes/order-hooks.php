<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// Inject Refund Risk badge into the Status column in the Orders list
add_filter( 'woocommerce_admin_order_preview_line_items', function( $line_items, $order ) {
    // This filter is for the preview popup, not the table. We'll use a different hook for the table.
    return $line_items;
}, 10, 2 );

add_filter( 'manage_shop_order_posts_custom_column', function( $value, $column, $post_id ) {
    if ( $column === 'order_status' ) {
        $risk = refundguard_get_risk_score( $post_id );
        $risk = apply_filters( 'refundguard_risk_score', $risk, $post_id );
        $class = $risk['score'] === 'high' ? 'status-cancelled tips' : ($risk['score'] === 'medium' ? 'status-on-hold tips' : 'status-completed tips');
        $label = ucfirst( $risk['score'] );
        $badge = '<br><mark class="' . esc_attr($class) . '" style="padding:2px 8px;font-size:12px;">' . esc_html( $label ) . '</mark>';
        return $value . $badge;
    }
    return $value;
}, 10, 3 );

// Remove Refund Risk custom column and debug column if present
add_filter( 'manage_edit-shop_order_columns', function( $columns ) {
    unset($columns['refundguard_risk'], $columns['refundguard_debug']);
    return $columns;
}, 100 );

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