<?php
/**
* @package         etruel\ISPConfig
* @subpackage 	   Dashboard
* @author          Esteban Truelsegaard <esteban@netmdp.com>
*/
// Exit if accessed directly
if ( !defined('ABSPATH') ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

if (!class_exists('WPISPConfig_Dashboard')) :
class WPISPConfig_Dashboard {

	/**
	* Static function hooks
	* @access public
	* @return void
	* @since 1.0.0
	*/
	public static function hooks() {

		if ( is_admin() ){ // admin actions
			//add_action('admin_init', array(__CLASS__, 'register_settings') );
			add_action('admin_menu', array(__CLASS__, 'settings_menu') );
		}
		
	}


	public static function add_styles() {
		/*
		wp_enqueue_style('style-wpcispconfig-settings',WPISPCONFIG_PLUGIN_URL .'assets/css/settings.css', array(), WPISPCONFIG_VERSION);	
		*/
	}
	public static function add_scripts() {
		/*
		wp_enqueue_script( 'wpcispconfig-settings', WPISPCONFIG_PLUGIN_URL . 'assets/js/settings.js', array( 'jquery' ), WPISPCONFIG_VERSION, true );
	
		wp_localize_script('wpcispconfig-settings', 'settings_obj',
				array(
					'ajax_url' 			=> admin_url( 'admin-ajax.php' ),
					'text_loading' 		=> __('Loading...', 'wpispconfig'),
					'text_error_fail' 	=> __('An error has been occurred. Please check the compatibility with this plugin.', 'wpispconfig'),
					'text_success' 		=> __('You have logged successfully.', 'wpispconfig'),
					'nonce_test_con' 	=> wp_create_nonce('test-connection-settings'),

				)
			);
			*/
	}
	

	public static function settings_menu() {
		$page = add_menu_page( 
	        __( 'WP-ISPConfig', 'wpispconfig' ),
	         __( 'WP-ISPConfig', 'wpispconfig' ),
	        'manage_options',
	        'ispconfig_dashboard',
	        array(__CLASS__, 'page'),
	       	WPISPCONFIG_PLUGIN_URL . 'assets/images/prou.png',
	        3
	    ); 
	
		add_action( 'admin_print_styles-' . $page, array(__CLASS__, 'add_styles') );
		add_action( 'admin_print_scripts-' . $page, array(__CLASS__, 'add_scripts') );

	}

	
	public static function page() {
		global $wp_settings_sections;
        if(!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
        
       
        ?>
        <div class="wrap">

		<h2><?php _e('ISPConfig Dashboard', 'wpispconfig'); ?></h2>
			<div id="poststuff">
				<div id="post-body" class="metabox-holder columns-<?php echo 1 == get_current_screen()->get_columns() ? '1' : '2'; ?>">
					<div id="post-body-content">
						<!-- #post-body-content -->
					</div>
					<div id="postbox-container-1" class="postbox-container">
					<div id="side-sortables" class="meta-box-sortables ui-sortable">
						<div id="wpem-about" class="postbox">
							<button type="button" class="handlediv button-link" aria-expanded="true">
								<span class="screen-reader-text"><?php _e('Click to toggle'); ?></span>
								<span class="toggle-indicator" aria-hidden="true"></span>
							</button>
							<h2 style="background-color: #980b13; color: white;" class="hndle"><span class="dashicons dashicons-share-alt2"></span> <?php _e('About', 'wpispconfig'); ?></h2>
							<div class="inside">
								<p><b>WP-ISPConfig</b> <?php echo WPISPCONFIG_VERSION; ?> Version</p>
								<p></p>
								<p style="text-align: right;"></p>								
							</div>
						</div>

					</div>		<!-- #side-sortables -->
					</div>		<!--  postbox-container-1 -->		

					<div id="postbox-container-2" class="postbox-container">
					<div id="normal-sortables" class="meta-box-sortables ui-sortable">

						<div id="advancedfetching" class="postbox">
							<button type="button" class="handlediv button-link" aria-expanded="true">
								<span class="screen-reader-text"><?php _e('Click to toggle'); ?></span>
								<span class="toggle-indicator" aria-hidden="true"></span>
							</button>
							<h3 class="hndle"><span class="dashicons dashicons-list-view"></span> <span><?php _e('Website Harddisk Quota', 'wpispconfig'); ?></span></h3>
							<div class="inside">
								<table class="wp-list-table widefat fixed striped posts">

									<?php
									$options = WPISPConfig_Settings::get_option(); 
										try {
											$api = wpispconfig_get_current_api($options);
											$clients = $api->client_get_all();
											foreach ($clients as $key => $client_id) {
												$sys_groupid = $api->client_get_groupid($client_id);
												$results = $api->sites_web_domain_get(array('sys_groupid' => $sys_groupid ));
												//$result_quotas = $api->get_ftptrafficquota_data();
												foreach ($results as $key => $value) {
													echo '<tr><td>' . $value['domain'] . '</td><td>' . ($value['hd_quota'] == '-1' ? __('Unlimited', 'wpispconfig') : $value['hd_quota']. ' KB')  .  '</td></tr>';
												}
											}
										} catch (Exception $e) {
											echo '<div class="notice notice-error">' .$e->getMessage() . '</div>';
										}
									?>

								</table>
								<p></p>
								
								
							
							</div>
						</div>

						
					</div>		<!-- #normal-sortables -->
					</div>		<!--  postbox-container-2 -->		

					<div>
						
					</div>
				</div> <!-- #post-body -->
			</div> <!-- #poststuff -->
		</form>		
	</div><!-- .wrap -->
	<script type="text/javascript">
		
	</script>

        <?php 
    }
		
	
}
endif;  //class_exists
WPISPConfig_Dashboard::hooks();