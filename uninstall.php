<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit;

delete_option( 'refundguard_settings' );
delete_option( 'refundguard_openai_api_key' );
// Add more delete_option calls if new options are added in the future