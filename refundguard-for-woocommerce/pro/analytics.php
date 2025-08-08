<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'admin_menu', function() {
    add_submenu_page(
        'woocommerce',
        __( 'RefundGuard Analytics', 'refundguard-for-woocommerce' ),
        __( 'RefundGuard Analytics', 'refundguard-for-woocommerce' ),
        'manage_woocommerce',
        'refundguard-analytics',
        'refundguard_render_analytics_page'
    );
} );

function refundguard_render_analytics_page() {
    ?>
    <div class="wrap">
        <h1><?php _e( 'RefundGuard Analytics', 'refundguard-for-woocommerce' ); ?></h1>
        <?php do_action('refundguard_analytics_export_button'); ?>
        <canvas id="riskByProduct" height="100"></canvas>
        <canvas id="riskByCategory" height="100"></canvas>
        <canvas id="riskByCountry" height="100"></canvas>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        fetch('<?php echo admin_url('admin-ajax.php?action=refundguard_analytics_data'); ?>')
            .then(r => r.json())
            .then(data => {
                new Chart(document.getElementById('riskByProduct'), {
                    type: 'bar',
                    data: data.product,
                    options: { plugins: { title: { display: true, text: 'Risk by Product' } } }
                });
                new Chart(document.getElementById('riskByCategory'), {
                    type: 'bar',
                    data: data.category,
                    options: { plugins: { title: { display: true, text: 'Risk by Category' } } }
                });
                new Chart(document.getElementById('riskByCountry'), {
                    type: 'bar',
                    data: data.country,
                    options: { plugins: { title: { display: true, text: 'Risk by Shipping Country' } } }
                });
            });
    });
    </script>
    <?php
}

add_action( 'wp_ajax_refundguard_analytics_data', function() {
    $orders = wc_get_orders([
        'limit' => 200,
        'status' => array_keys( wc_get_order_statuses() ),
        'return' => 'objects',
    ]);
    $product = [];
    $category = [];
    $country = [];
    foreach ( $orders as $order ) {
        $risk = refundguard_get_ai_risk_score( $order->get_id() );
        foreach ( $order->get_items() as $item ) {
            $name = $item->get_name();
            $product[$name][$risk['score']] = ($product[$name][$risk['score']] ?? 0) + 1;
            $prod = $item->get_product();
            if ( $prod ) {
                $terms = get_the_terms( $prod->get_id(), 'product_cat' );
                if ( $terms && ! is_wp_error( $terms ) ) {
                    foreach ( $terms as $term ) {
                        $category[$term->name][$risk['score']] = ($category[$term->name][$risk['score']] ?? 0) + 1;
                    }
                }
            }
        }
        $country_name = $order->get_shipping_country();
        $country[$country_name][$risk['score']] = ($country[$country_name][$risk['score']] ?? 0) + 1;
    }
    function format_chart($arr) {
        $labels = array_keys($arr);
        $datasets = [
            [ 'label' => 'Low', 'backgroundColor' => '#27ae60', 'data' => [] ],
            [ 'label' => 'Medium', 'backgroundColor' => '#f39c12', 'data' => [] ],
            [ 'label' => 'High', 'backgroundColor' => '#e74c3c', 'data' => [] ],
        ];
        foreach ( $labels as $i => $label ) {
            $datasets[0]['data'][$i] = $arr[$label]['low'] ?? 0;
            $datasets[1]['data'][$i] = $arr[$label]['medium'] ?? 0;
            $datasets[2]['data'][$i] = $arr[$label]['high'] ?? 0;
        }
        return [ 'labels' => $labels, 'datasets' => $datasets ];
    }
    wp_send_json([
        'product' => format_chart($product),
        'category' => format_chart($category),
        'country' => format_chart($country),
    ]);
    exit;
});