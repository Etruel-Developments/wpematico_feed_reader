<?php
/**
 * Activation handler
 *
 * @package     WPeMatico\ActivationHandler
 * @since       1.0.0
 */


// Exit if accessed directly
if ( !defined('ABSPATH') ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

/**
 * WPeMatico Extension Activation Handler Class
 *
 * @since       1.0.0
 */
class WPeMatico_Extension_Activation {

    public $plugin_name, $plugin_path, $plugin_file, $has_wpematico, $wpematico_base;

    /**
     * Setup the activation class
     *
     * @access      public
     * @since       1.0.0
     * @return      void
     */
    public function __construct( $plugin_path, $plugin_file ) {
        // We need plugin.php!
        require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

        $plugins = get_plugins();

        // Set plugin directory
        $plugin_path = array_filter( explode( '/', $plugin_path ) );
        $this->plugin_path = end( $plugin_path );

        // Set plugin file
        $this->plugin_file = $plugin_file;

        // Set plugin name
        if( isset( $plugins[$this->plugin_path . '/' . $this->plugin_file]['Name'] ) ) {
            $this->plugin_name = str_replace( 'WPeMatico - ', '', $plugins[$this->plugin_path . '/' . $this->plugin_file]['Name'] );
        } else {
            $this->plugin_name = esc_html__( 'This plugin', 'wpematico_rss_feed_reader' );
        }

        // Is WPeMatico installed?
        foreach( $plugins as $plugin_path => $plugin ) {
            if( $plugin['Name'] == 'WPeMatico' ) {
                $this->has_wpematico = true;
                $this->wpematico_base = $plugin_path;
                break;
            }
        }
    }


    /**
     * Process plugin deactivation
     *
     * @access      public
     * @since       1.0.0
     * @return      void
     */
    public function run() {
        // Display notice
        add_action( 'admin_notices', array( $this, 'wpematico_missing_notice' ) );
    }


    /**
     * Display notice if WPeMatico isn't installed
     *
     * @access      public
     * @since       1.0.0
     * @return      void The notice to display
     */
    public function wpematico_missing_notice() {
        if( $this->has_wpematico ) {
            $url  = esc_url( wp_nonce_url( admin_url( 'plugins.php?action=activate&plugin=' . $this->wpematico_base ), 'activate-plugin_' . $this->wpematico_base ) );
            $link = '<a href="' . $url . '">' . esc_html__( 'activate it', 'wpematico_rss_feed_reader' ) . '</a>';
        } else {
            $url  = esc_url( wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=wpematico' ), 'install-plugin_wpematico' ) );
            $link = '<a href="' . $url . '">' . esc_html__( 'install it', 'wpematico_rss_feed_reader' ) . '</a>';
        }
        
        echo '<div class="error"><p>' . $this->plugin_name . sprintf( esc_html__( ' requires WPeMatico! Please %s to continue!', 'wpematico_rss_feed_reader' ), $link ) . '</p></div>';
        echo '<div class="error"><p>' . esc_html($this->plugin_name) . 
				// translators: %s: link to activate or install core plugin 
				sprintf( __( ' requires WPeMatico! Please %s to continue!', 'wpematico-extension-activation' ), $link ) . '</p></div>';
    }
}
