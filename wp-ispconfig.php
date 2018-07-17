<?php
/**
* Plugin Name: WP-ISPConfig
* Description: This plugin allow manage some features of ISPConfig with remote user.
* Version: 1.1
* Author: Esteban Truelsegaard <esteban@netmdp.com>
* Author URI: https://etruel.com/
* Text Domain: wpispconfig
 * 
 * @package WP-ISPConfig
 * @category Core
 * @author etruel <esteban@netmdp.com>
 */
// don't load directly 
if ( !defined('ABSPATH') ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

// Plugin version
if (!defined('WPISPCONFIG_VERSION')) {
	define('WPISPCONFIG_VERSION', '1.1' ); 
} 


if (!class_exists('WPISPConfig')) :
/**
* Main WPISPConfig class
*
* @since 1.1
*/
class WPISPConfig {

	 /**
     * @var         WPISPConfig $init Bool  if the class was started
     * @since       1.1
     */
    private static $init = false;

    const OPTION_KEY = 'WPISPConfig_Options';
	
	 /**
     * This function starts the main class.
     * @access      public
     * @since       1.1
     * @return      void
     */
    public static function init() {
        if( !self::$init ) {
            self::constants();
            self::includes();
            self::load_text_domain();
            self::hooks();
        }
        self::$init = true;
    }
	/**
	* Static function constants
	* @access public
	* @return void
	* @since 1.1
	*/
	public static function constants() {
		// Plugin Folder Path
		if (!defined('WPISPCONFIG_PLUGIN_DIR')) {
			define('WPISPCONFIG_PLUGIN_DIR', plugin_dir_path( __FILE__ ));
		}

		// Plugin Folder URL
		if (!defined('WPISPCONFIG_PLUGIN_URL')) {
			define('WPISPCONFIG_PLUGIN_URL', plugin_dir_url(__FILE__));
		}

		// Plugin Root File
		if (!defined('WPISPCONFIG_PLUGIN_FILE')) {
			define('WPISPCONFIG_PLUGIN_FILE', __FILE__ );
		}
		
		// Plugin text domain
		if (!defined('WPISPCONFIG_TEXT_DOMAIN')) {
			define('WPISPCONFIG_TEXT_DOMAIN', 'wpispconfig' );
		}


	}
	/**
	* Static function includes
	* @access public
	* @return void
	* @since 1.0.0
	*/
	public static function includes() {
		require_once WPISPCONFIG_PLUGIN_DIR . 'includes/settings.php';
		require_once WPISPCONFIG_PLUGIN_DIR . 'includes/all_in_one.php';
		require_once WPISPCONFIG_PLUGIN_DIR . 'includes/soap_ispconfig.php';
		require_once WPISPCONFIG_PLUGIN_DIR . 'includes/notices.php';
		require_once WPISPCONFIG_PLUGIN_DIR . 'includes/add2client.php';
		require_once WPISPCONFIG_PLUGIN_DIR . 'includes/domain-alias.php';
		
	}

	/**
	* Static function hooks
	* Add all hooks needs to primary feature.
	* @access public
	* @return void
	* @since 1.0.0
	*/
	public static function hooks() {
		//add_filter( 'wpematico_plugins_updater_args', array(__CLASS__, 'add_updater'), 10, 1);
	}

	/**
	* Static function load_text_domain 
	* Load the text domain.
	* @access public
	* @return void
	* @since 1.0.0
	*/
	public static function load_text_domain() {
		// Set filter for plugin's languages directory
		$lang_dir = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
		$lang_dir = apply_filters('wpispconfig_languages_directory', $lang_dir );

		// Traditional WordPress plugin locale filter
		$locale        = apply_filters( 'plugin_locale',  get_locale(), WPISPCONFIG_TEXT_DOMAIN);
		$mofile        = sprintf( '%1$s-%2$s.mo', WPISPCONFIG_TEXT_DOMAIN, $locale );

		// Setup paths to current locale file
		$mofile_local  = $lang_dir . $mofile;
		$mofile_global = WP_LANG_DIR . '/'.WPISPCONFIG_TEXT_DOMAIN.'/' . $mofile;

		if ( file_exists( $mofile_global ) ) {
			// Look in global /wp-content/languages/wpematico_cache/ folder
			load_textdomain(WPISPCONFIG_TEXT_DOMAIN, $mofile_global );
		} elseif ( file_exists( $mofile_local ) ) {
			// Look in local /wp-content/plugins/wpematico_cache/languages/ folder
			load_textdomain(WPISPCONFIG_TEXT_DOMAIN, $mofile_local );
		} else {
			// Load the default language files
			load_plugin_textdomain(WPISPCONFIG_TEXT_DOMAIN, false, $lang_dir );
		}
		
	}

	
	

}
endif;

// Start main class
add_action( 'plugins_loaded', array('WPISPConfig', 'init'));

?>