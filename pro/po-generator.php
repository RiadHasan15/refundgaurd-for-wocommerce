<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'woocommerce_thankyou', function( $order_id ) {
    $risk = refundguard_get_ai_risk_score( $order_id );
    if ( $risk['score'] === 'high' ) {
        $order = wc_get_order( $order_id );
        if ( ! $order ) return;
        foreach ( $order->get_items() as $item ) {
            $product = $item->get_product();
            if ( $product && $product->get_stock_quantity() !== null && $product->get_stock_quantity() < 5 ) {
                // Stub: Add order note for PO
                $order->add_order_note( sprintf( __( 'PO generated for supplier for low-stock item: %s (Stock: %d)', 'refundguard-for-woocommerce' ), $product->get_name(), $product->get_stock_quantity() ) );
                // TODO: Integrate with supplier API or email
            }
        }
    }
} );