<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function refundguard_render_settings_page() {
    if ( ! current_user_can( 'manage_woocommerce' ) ) return;
    $settings = get_option( 'refundguard_settings', [
        'rule_category' => 1,
        'rule_total' => 1,
        'rule_country' => 1,
        'rule_previous_orders' => 1,
    ] );
    if ( isset( $_POST['refundguard_save_settings'] ) && check_admin_referer( 'refundguard_save_settings' ) ) {
        $settings['rule_category'] = ! empty( $_POST['rule_category'] ) ? 1 : 0;
        $settings['rule_total'] = ! empty( $_POST['rule_total'] ) ? 1 : 0;
        $settings['rule_country'] = ! empty( $_POST['rule_country'] ) ? 1 : 0;
        $settings['rule_previous_orders'] = ! empty( $_POST['rule_previous_orders'] ) ? 1 : 0;
        update_option( 'refundguard_settings', $settings );
        do_action( 'refundguard_settings_save' );
        echo '<div class="updated"><p>' . __( 'Settings saved.', 'refundguard-for-woocommerce' ) . '</p></div>';
    }
    ?>
    <div class="wrap">
        <h1><?php _e( 'RefundGuard Settings', 'refundguard-for-woocommerce' ); ?></h1>
        <form method="post">
            <?php wp_nonce_field( 'refundguard_save_settings' ); ?>
            <table class="form-table">
                <tr>
                    <th><?php _e( 'Enable Product Category Rule', 'refundguard-for-woocommerce' ); ?></th>
                    <td><input type="checkbox" name="rule_category" value="1" <?php checked( $settings['rule_category'], 1 ); ?> /></td>
                </tr>
                <tr>
                    <th><?php _e( 'Enable Order Total Rule', 'refundguard-for-woocommerce' ); ?></th>
                    <td><input type="checkbox" name="rule_total" value="1" <?php checked( $settings['rule_total'], 1 ); ?> /></td>
                </tr>
                <tr>
                    <th><?php _e( 'Enable Shipping Country Rule', 'refundguard-for-woocommerce' ); ?></th>
                    <td><input type="checkbox" name="rule_country" value="1" <?php checked( $settings['rule_country'], 1 ); ?> /></td>
                </tr>
                <tr>
                    <th><?php _e( 'Enable Previous Orders Rule', 'refundguard-for-woocommerce' ); ?></th>
                    <td><input type="checkbox" name="rule_previous_orders" value="1" <?php checked( $settings['rule_previous_orders'], 1 ); ?> /></td>
                </tr>
                <?php do_action( 'refundguard_settings_fields' ); ?>
            </table>
            <p><input type="submit" name="refundguard_save_settings" class="button-primary" value="<?php esc_attr_e( 'Save Settings', 'refundguard-for-woocommerce' ); ?>" /></p>
        </form>
    </div>
    <?php
}