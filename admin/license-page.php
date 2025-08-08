<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function refundguard_render_license_page() {
    if ( ! current_user_can( 'manage_woocommerce' ) ) return;
    if ( isset( $_POST['refundguard_save_license'] ) && check_admin_referer( 'refundguard_save_license' ) ) {
        update_option( 'refundguard_pro_license', sanitize_text_field( $_POST['refundguard_pro_license'] ) );
        echo '<div class="updated"><p>' . __( 'License saved.', 'refundguard-for-woocommerce' ) . '</p></div>';
    }
    $license = get_option( 'refundguard_pro_license', '' );
    ?>
    <div class="wrap">
        <h1><?php _e( 'RefundGuard Pro License', 'refundguard-for-woocommerce' ); ?></h1>
        <form method="post">
            <?php wp_nonce_field( 'refundguard_save_license' ); ?>
            <table class="form-table">
                <tr>
                    <th><?php _e( 'License Key', 'refundguard-for-woocommerce' ); ?></th>
                    <td><input type="text" name="refundguard_pro_license" value="<?php echo esc_attr( $license ); ?>" size="40" /></td>
                </tr>
            </table>
            <p><input type="submit" name="refundguard_save_license" class="button-primary" value="<?php esc_attr_e( 'Save License', 'refundguard-for-woocommerce' ); ?>" /></p>
        </form>
        <p><?php _e( 'Enter your RefundGuard Pro license key. Pro features will be enabled if a valid license is entered. (For testing, any value will enable Pro features.)', 'refundguard-for-woocommerce' ); ?></p>
    </div>
    <?php
}