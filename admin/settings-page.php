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
        // Save Pro settings
        if ( file_exists( REFUNDGUARD_PLUGIN_DIR . 'pro/refundguard-pro.php' ) && get_option('refundguard_pro_license', '') ) {
            update_option( 'refundguard_openai_api_key', sanitize_text_field( $_POST['openai_api_key'] ?? '' ) );
            update_option( 'refundguard_pro_ai_enabled', !empty($_POST['pro_ai_enabled']) ? '1' : '0' );
            update_option( 'refundguard_pro_auto_flag', !empty($_POST['pro_auto_flag']) ? '1' : '0' );
            update_option( 'refundguard_pro_email_alerts', !empty($_POST['pro_email_alerts']) ? '1' : '0' );
        }
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
                <?php
                // Pro settings
                $license = get_option('refundguard_pro_license', '');
                if ( file_exists( REFUNDGUARD_PLUGIN_DIR . 'pro/refundguard-pro.php' ) && !empty($license) ) {
                    $openai_key = get_option('refundguard_openai_api_key', '');
                    $ai_enabled = get_option('refundguard_pro_ai_enabled', '1');
                    $auto_flag = get_option('refundguard_pro_auto_flag', '1');
                    $email_alerts = get_option('refundguard_pro_email_alerts', '1');
                    ?>
                    <tr><th colspan="2"><hr><strong><?php _e('Pro Features', 'refundguard-for-woocommerce'); ?></strong></th></tr>
                    <tr>
                        <th><?php _e('OpenAI API Key', 'refundguard-for-woocommerce'); ?></th>
                        <td><input type="text" name="openai_api_key" value="<?php echo esc_attr($openai_key); ?>" size="40" /></td>
                    </tr>
                    <tr>
                        <th><?php _e('Enable AI Risk Scoring', 'refundguard-for-woocommerce'); ?></th>
                        <td><input type="checkbox" name="pro_ai_enabled" value="1" <?php checked($ai_enabled, '1'); ?> /></td>
                    </tr>
                    <tr>
                        <th><?php _e('Enable Auto-Flag High-Risk Orders', 'refundguard-for-woocommerce'); ?></th>
                        <td><input type="checkbox" name="pro_auto_flag" value="1" <?php checked($auto_flag, '1'); ?> /></td>
                    </tr>
                    <tr>
                        <th><?php _e('Enable Email Alerts for High-Risk Orders', 'refundguard-for-woocommerce'); ?></th>
                        <td><input type="checkbox" name="pro_email_alerts" value="1" <?php checked($email_alerts, '1'); ?> /></td>
                    </tr>
                    <tr>
                        <th><?php _e('WhatsApp Alerts', 'refundguard-for-woocommerce'); ?></th>
                        <td><em><?php _e('Integrate with your WhatsApp provider using the refundguard_send_whatsapp action. See Alerts tab for more info.', 'refundguard-for-woocommerce'); ?></em></td>
                    </tr>
                <?php } ?>
            </table>
            <p><input type="submit" name="refundguard_save_settings" class="button-primary" value="<?php esc_attr_e( 'Save Settings', 'refundguard-for-woocommerce' ); ?>" /></p>
        </form>
    </div>
    <?php
}