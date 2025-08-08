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
        <div style="background: #f8fafc; border: 1px solid #e0e6ed; border-radius: 10px; padding: 24px 32px; margin-bottom: 32px; max-width: 700px;">
            <h2 style="margin-top:0; color:#1a202c; font-size:1.5em;"><span style="color:#3b82f6;">Upgrade to RefundGuard Pro</span></h2>
            <ul style="list-style: none; padding: 0; margin: 0;">
                <li style="margin-bottom: 12px;"><span style="font-size:1.2em; color:#10b981; font-weight:bold;">🤖 AI-Powered Risk Scoring</span><br><span style="color:#555;">Analyze orders with OpenAI or your own ML model for smarter, more accurate risk detection.</span></li>
                <li style="margin-bottom: 12px;"><span style="font-size:1.2em; color:#f59e42; font-weight:bold;">🔔 Auto-Flag High-Risk Orders</span><br><span style="color:#555;">Automatically set risky orders to "On Hold" or "Manual Review" before fulfillment.</span></li>
                <li style="margin-bottom: 12px;"><span style="font-size:1.2em; color:#3b82f6; font-weight:bold;">📊 Advanced Analytics Dashboard</span><br><span style="color:#555;">Visualize refund risk by product, category, and country with interactive charts.</span></li>
                <li style="margin-bottom: 12px;"><span style="font-size:1.2em; color:#6366f1; font-weight:bold;">📤 Export High-Risk Orders</span><br><span style="color:#555;">Export flagged orders to CSV for reporting or further review.</span></li>
                <li style="margin-bottom: 12px;"><span style="font-size:1.2em; color:#ef4444; font-weight:bold;">📲 Instant Alerts</span><br><span style="color:#555;">Get notified by email or WhatsApp when a high-risk order is detected.</span></li>
            </ul>
            <div style="margin-top:18px;">
                <span style="background:#3b82f6;color:#fff;padding:8px 18px;border-radius:6px;font-weight:bold;font-size:1.1em;">All Pro features are unlocked with a valid license key!</span>
            </div>
        </div>
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