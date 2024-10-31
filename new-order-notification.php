<?php

/**
 * Plugin Name:       New Order Popup
 * Plugin URI:        https://woodeliveryplugins.com/new-order-notification
 * Description:       Triggers A Full-Screen Popup when new  orders come to woocommerce 
 * Version:           1.2.5
 * Requires at least: 5.2
 * Tested up to: 6.0.1
 *
 * Requires PHP: 5.6
 * PHP tested up to: 8.1
 *
 * WC requires at least: 2.5
 * WC tested up to: 6.7
 * Author:            Mozzoplugins
 * Author URI:        https://woodeliveryplugins.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       new-order-notification
 * Domain Path:       /languages
 */

if ( function_exists( 'woo_fs' ) ) {
    woo_fs()->set_basename( false, __FILE__ );
} else {
    // Create a helper function for easy SDK access.
    /**
     * woo_fs
     * 
     * @return object
     */
    function woo_fs()
    {
        global  $woo_fs ;
        
        if ( !isset( $woo_fs ) ) {
            // Include Freemius SDK.
            require_once dirname( __FILE__ ) . '/freemius/start.php';
            $woo_fs = fs_dynamic_init( array(
                'id'             => '10614',
                'slug'           => 'woonewordernotification',
                'type'           => 'plugin',
                'public_key'     => 'pk_bf9c8ac5447fdccf40f02bd75136f',
                'is_premium'     => false,
                'has_addons'     => false,
                'has_paid_plans' => true,
                'trial'          => array(
                'days'               => 7,
                'is_require_payment' => false,
            ),
                'menu'           => array(
                'slug'           => 'nop-notification-settings',
                'override_exact' => true,
                'first-path'     => 'options-general.php?page=nop-notification-settings',
                'support'        => false,
                'parent'         => array(
                'slug' => 'options-general.php',
            ),
            ),
                'is_live'        => true,
            ) );
        }
        
        return $woo_fs;
    }
    
    // Init Freemius.
    woo_fs();
    // Signal that SDK was initiated.
    do_action( 'woo_fs_loaded' );
    function woo_fs_settings_url()
    {
        return admin_url( 'options-general.php?page=nop-notification-settings' );
    }
    
    woo_fs()->add_filter( 'connect_url', 'woo_fs_settings_url' );
    woo_fs()->add_filter( 'after_skip_url', 'woo_fs_settings_url' );
    woo_fs()->add_filter( 'after_connect_url', 'woo_fs_settings_url' );
    woo_fs()->add_filter( 'after_pending_connect_url', 'woo_fs_settings_url' );
}


if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly
}

//direct access prohibited
define( 'NOP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
/**
 * connect_message_on_update
 * 
 * @param string $message Description for $message
 * @param string $user_first_name Description for $user_first_name
 * @param string $plugin_title Description for $plugin_title
 * @param string $user_login Description for user_login
 * @param string $site_link site link
 * @param string $freemius_link freemius_link
 * 
 * @return string
 */
function nop_connect_message_on_update(
    $message,
    $user_first_name,
    $plugin_title,
    $user_login,
    $site_link,
    $freemius_link
)
{
    return sprintf(
        __( 'Hey %1$s' ) . ',<br>' . __( 'Please help us improve %2$s! If you opt-in, some data about your usage of %2$s will be sent to %5$s. If you skip this, that\'s okay! %2$s will still work just fine.', 'woonewordernotification' ),
        esc_html( $user_first_name ),
        '<b>' . esc_html( $plugin_title ) . '</b>',
        '<b>' . esc_html( $user_login ) . '</b>',
        esc_url( $site_link ),
        esc_url( $freemius_link )
    );
}


if ( $woo_fs ) {
    woo_fs()->add_filter(
        'connect_message_on_update',
        'nop_connect_message_on_update',
        10,
        6
    );
    woo_fs()->add_action( 'after_uninstall', 'woo_fs_uninstall_cleanup' );
}

// Template Factory
if ( !class_exists( 'Nop_Notify_Template_Loader' ) ) {
    require_once plugin_dir_path( __FILE__ ) . 'admin/classes/class-nop-notify-template-loader.php';
}
if ( !class_exists( 'Nop_Template_Loader' ) ) {
    require_once plugin_dir_path( __FILE__ ) . 'admin/classes/class-nop-template-loader.php';
}
require_once plugin_dir_path( __FILE__ ) . 'nop-powerhouse.php';
/**
 * notif_run_deactivator
 * 
 * @return void
 */
function nop_notif_run_activator()
{
    //do stuffs
}

/**
 * notif_run_deactivator
 * 
 * @return void
 */
function nop_notif_run_deactivator()
{
    //do stuffs
    delete_option( 'nop-notification-settings' );
}

/**
 * enqueue scripts 
 * 
 * @return void
 */
function nop_notif_add_scripts()
{
    wp_enqueue_style(
        'nop-mgnf',
        plugins_url( 'assets/css/magnific-nop.css', __FILE__ ),
        array(),
        '1.0.0'
    );
    wp_enqueue_script(
        'nop-mgnf',
        plugins_url( 'assets/js/magnific-nop.min.js', __FILE__ ),
        array( 'jquery' ),
        '1.0.0'
    );
    wp_register_style(
        'nop-main-css',
        plugins_url( 'assets/css/nop-main.css', __FILE__ ),
        array(),
        '1.0.0'
    );
    wp_enqueue_style( 'nop-main-css' );
    wp_enqueue_script(
        'nop-main-js',
        plugins_url( 'assets/js/nop-main.js', __FILE__ ),
        array( 'jquery' ),
        '1.0.7'
    );
}

//add admin box
require_once plugin_dir_path( __FILE__ ) . 'admin/inc/nop-settings-page.php';
/**
 * woocommerce_new_order
 * 
 * @param int $order_id order id
 * @return void
 */
function nop_woocommerce_new_order( $order_id )
{
    $nop_new_order = new WC_Order( $order_id );
    $nop_new_data = array(
        'new_order_id'       => $nop_new_order->get_id(),
        'nop_new_order_stat' => 1,
    );
    
    if ( add_option( 'nop_new_order', $nop_new_data ) === false ) {
        update_option( 'nop_new_order', $nop_new_data );
    } else {
        add_option( 'nop_new_order', $wpb_new_data );
    }

}

add_action( 'admin_enqueue_scripts', 'nop_notif_add_scripts' );
add_action( 'woocommerce_new_order', 'nop_woocommerce_new_order' );
add_action( 'admin_footer', 'nop_new_order_scripts' );
global  $nopbnid ;
add_action( 'wp_ajax_check_new_order', 'nop_new_order_detection' );
add_action( 'wp_ajax_nopriv_check_new_order', 'nop_new_order_detection' );
/**
 * new order detection
 * 
 * @return void
 */
function nop_new_order_detection()
{
    $new_order_option_wpb = get_option( 'nop_new_order' );
    $resArr = array();
    
    if ( intval( $new_order_option_wpb['nop_new_order_stat'] ) === 1 ) {
        $resArr = array(
            'success' => 'true',
            'norder'  => intval( $new_order_option_wpb['nop_new_order_stat'] ),
            'oid'     => $new_order_option_wpb['new_order_id'],
        );
        $nopbnid = $new_order_option_wpb['new_order_id'];
        wp_send_json( $resArr );
    }
    
    $resArr = array(
        'success' => 'false',
        'norder'  => 0,
        'oid'     => 5525,
    );
    $nopbnid = 0;
    wp_send_json( $resArr );
}

/**
 * add new order scripts
 * 
 * @return void
 */
function nop_new_order_scripts()
{
    global 
        $wpb_template_loder,
        $order_notify_image_url,
        $order_notify_sound_url,
        $woo_fs
    ;
    $order_notify_image_url = plugins_url( 'assets/img/confetti.png', __FILE__ );
    $uploaded_file = '';
    $uploaded_sound_file = '';
    if ( $woo_fs && $woo_fs->is__premium_only() ) {
        
        if ( $woo_fs->can_use_premium_code() ) {
            foreach ( glob( NOP_PLUGIN_DIR . '/assets/img/notification_image.*' ) as $filename ) {
                
                if ( $filename ) {
                    $info = pathinfo( $filename );
                    $uploaded_file = $info['basename'];
                }
            
            }
            foreach ( glob( NOP_PLUGIN_DIR . '/assets/sound/notification_sound.*' ) as $filename ) {
                
                if ( $filename ) {
                    $info = pathinfo( $filename );
                    $uploaded_sound_file = $info['basename'];
                }
            
            }
        }
    
    }
    if ( !empty($uploaded_file) ) {
        $order_notify_image_url = plugins_url( 'assets/img/' . $uploaded_file, __FILE__ );
    }
    // sound files
    $order_notify_sound_url = plugins_url( 'assets/sound/mixkit-fairy-message-notification-861.mp3', __FILE__ );
    if ( !empty($uploaded_sound_file) ) {
        $order_notify_sound_url = plugins_url( 'assets/sound/' . $uploaded_sound_file, __FILE__ );
    }
    $wpb_template_loder->get_template_part( 'nop-render-popup' );
}

add_action( 'admin_init', 'nop_require_woocommerce' );
//require woocommerce
function nop_require_woocommerce()
{
    
    if ( is_admin() && current_user_can( 'activate_plugins' ) && !is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
        deactivate_plugins( plugin_basename( __FILE__ ) );
        add_action( 'admin_notices', 'nop_require_woocommerce_notice' );
        if ( isset( $_GET['activate'] ) ) {
            unset( $_GET['activate'] );
        }
    }

}

// show admin notice if woocommerce is not active
function nop_require_woocommerce_notice()
{
    $class = 'notice notice-error is-dismissible';
    $message = __( 'Sorry, but New Order Popup requires the Woocommerce plugin to be installed and activated.', 'cn-close-store' );
    printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
}
