<?php
/*
Plugin Name: WP Loyalty by AD5
Plugin URI: http://wordpress.ad5.jp/plugin/ad5-loyalty/
Description: Easiest way to bulid up membership site. Display Sign up and Sign in form with shortcode and display different contents for guests and members on any posts and pages you would like to.
Version: 1.0.2
Author: AD5
Author URI: http://wordpress.ad5.jp/
Text Domain: ad5-loyalty
Domain Path: /languages
*/

add_action( 'plugins_loaded', function () {
    load_plugin_textdomain( 'ad5-loyalty', false, basename( dirname( __FILE__ ) ) . '/languages' );
} );

register_uninstall_hook( __FILE__, 'ad5_loyalty_uninstall' ); 

function ad5_loyalty_uninstall() {
    delete_option( 'ad5_loyalty_setting' );
    delete_option( 'ad5_loyalty_default_content_guest' );
    delete_option( 'ad5_loyalty_default_content_user' );
}

if ( is_admin() ) {
    require_once( dirname(__FILE__) . '/includes/class-ad5-loyalty-admin.php' );
    $admin = new AD5_Loyalty_Admin();
    $admin->init();
}

require_once( dirname(__FILE__) . '/includes/class-ad5-loyalty-front.php' );
$front = new AD5_Loyalty_Front();
$front->init();
