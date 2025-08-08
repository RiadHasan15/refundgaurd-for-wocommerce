<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'admin_menu', function() {
    add_submenu_page(
        'woocommerce',
        __( 'RefundGuard Restock Importer', 'refundguard-for-woocommerce' ),
        __( 'Restock Importer', 'refundguard-for-woocommerce' ),
        'manage_woocommerce',
        'refundguard-restock-importer',
        'refundguard_render_restock_importer_page'
    );
} );

function refundguard_render_restock_importer_page() {
    if ( isset( $_POST['refundguard_restock_import'] ) && ! empty( $_FILES['restock_csv']['tmp_name'] ) ) {
        $file = $_FILES['restock_csv']['tmp_name'];
        $handle = fopen( $file, 'r' );
        $updated = 0;
        if ( $handle ) {
            while ( ( $row = fgetcsv( $handle ) ) !== false ) {
                $product_id = intval( $row[0] );
                $stock = intval( $row[1] );
                $product = wc_get_product( $product_id );
                if ( $product ) {
                    $product->set_stock_quantity( $stock );
                    $product->save();
                    $updated++;
                }
            }
            fclose( $handle );
        }
        echo '<div class="updated"><p>' . sprintf( __( 'Updated stock for %d products.', 'refundguard-for-woocommerce' ), $updated ) . '</p></div>';
    }
    ?>
    <div class="wrap">
        <h1><?php _e( 'Restock Importer', 'refundguard-for-woocommerce' ); ?></h1>
        <form method="post" enctype="multipart/form-data">
            <input type="file" name="restock_csv" accept=".csv" required />
            <input type="submit" name="refundguard_restock_import" class="button-primary" value="<?php esc_attr_e( 'Import CSV', 'refundguard-for-woocommerce' ); ?>" />
        </form>
        <p><?php _e( 'CSV format: Product ID, Stock Quantity', 'refundguard-for-woocommerce' ); ?></p>
    </div>
    <?php
}