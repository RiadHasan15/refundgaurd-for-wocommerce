<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function refundguard_get_risk_score( $order_id ) {
    $order = wc_get_order( $order_id );
    if ( ! $order ) return [ 'score' => 'low', 'reason' => __( 'Order not found', 'refundguard-for-woocommerce' ) ];

    $settings = get_option( 'refundguard_settings', [
        'rule_category' => 1,
        'rule_total' => 1,
        'rule_country' => 1,
        'rule_previous_orders' => 1,
    ] );

    $score = 0;
    $reasons = [];

    // Rule: Product Category
    if ( ! empty( $settings['rule_category'] ) ) {
        $categories = [];
        foreach ( $order->get_items() as $item ) {
            $product = $item->get_product();
            if ( $product ) {
                $terms = get_the_terms( $product->get_id(), 'product_cat' );
                if ( $terms && ! is_wp_error( $terms ) ) {
                    foreach ( $terms as $term ) {
                        $categories[] = $term->slug;
                    }
                }
            }
        }
        if ( in_array( 'high-risk', $categories ) ) {
            $score += 2;
            $reasons[] = __( 'High-risk product category', 'refundguard-for-woocommerce' );
        }
    }

    // Rule: Order Total
    if ( ! empty( $settings['rule_total'] ) ) {
        $total = floatval( $order->get_total() );
        if ( $total > 500 ) {
            $score += 2;
            $reasons[] = __( 'High order total', 'refundguard-for-woocommerce' );
        } elseif ( $total > 200 ) {
            $score += 1;
            $reasons[] = __( 'Moderate order total', 'refundguard-for-woocommerce' );
        }
    }

    // Rule: Shipping Country
    if ( ! empty( $settings['rule_country'] ) ) {
        $country = $order->get_shipping_country();
        $high_risk_countries = [ 'NG', 'RU', 'UA', 'CN' ];
        if ( in_array( $country, $high_risk_countries ) ) {
            $score += 2;
            $reasons[] = __( 'High-risk shipping country', 'refundguard-for-woocommerce' );
        }
    }

    // Rule: Number of Previous Orders
    if ( ! empty( $settings['rule_previous_orders'] ) ) {
        $customer_id = $order->get_customer_id();
        if ( $customer_id ) {
            $customer_orders = wc_get_orders([
                'customer_id' => $customer_id,
                'exclude' => [ $order_id ],
                'return' => 'ids',
            ]);
            $num_orders = count( $customer_orders );
            if ( $num_orders < 2 ) {
                $score += 2;
                $reasons[] = __( 'New customer', 'refundguard-for-woocommerce' );
            } elseif ( $num_orders < 5 ) {
                $score += 1;
                $reasons[] = __( 'Few previous orders', 'refundguard-for-woocommerce' );
            }
        }
    }

    // Score to label
    if ( $score >= 4 ) {
        $label = 'high';
    } elseif ( $score >= 2 ) {
        $label = 'medium';
    } else {
        $label = 'low';
    }

    return [
        'score' => $label,
        'reason' => implode( ', ', $reasons )
    ];
}