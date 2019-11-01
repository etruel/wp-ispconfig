<?php
/**
 * @package         etruel\ISPConfig
 * @subpackage 	   Dashboard
 * @author          Esteban Truelsegaard <esteban@netmdp.com>
 */
// Exit if accessed directly
if(!defined('ABSPATH')) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}

if(!class_exists('WPISPConfig_Dashboard')) :

	class WPISPConfig_Dashboard {

		/**
		 * Static function hooks
		 * @access public
		 * @return void
		 * @since 1.0.0
		 */
		public static function hooks() {

			if(is_admin()) { // admin actions
				add_action('admin_menu', array(__CLASS__, 'settings_menu'));
			}
			add_action('admin_post_ispconfig_refresh_list', array(__CLASS__, 'post_refresh_list'));
		}

		/**
		 * Static function add_styles
		 * @access public
		 * @return void
		 * @since 1.0.0
		 */
		public static function add_styles() {
			
		}

		/**
		 * Static function add_scripts
		 * @access public
		 * @return void
		 * @since 1.0.0
		 */
		public static function add_scripts() {
			
		}

		/**
		 * Static function post_refresh_list
		 * @access public
		 * @return void
		 * @since 1.0.0
		 */
		public static function post_refresh_list() {
			$nonce = '';
			if(!empty($_REQUEST['nonce'])) {
				$nonce = $_REQUEST['nonce'];
			}
			if(!wp_verify_nonce($nonce, 'wpispc_refresh_list')) {
				wp_die(__('Security check', 'wpispconfig'));
			}
			try {
				self::refresh_website_list();
			}catch (Exception $e) {
				WPISPConfig_notices::add(array('text' => $e->getMessage(), 'error' => true));
			}
			WPISPConfig_notices::add(__('The list of websites has been updated.', 'wpispconfig'));
			wp_redirect(admin_url('admin.php?page=ispconfig_dashboard'));
			die();
		}

		/**
		 * Static function refresh_website_list
		 * Refresh the list of websites in the dashboard page. 
		 * @access public
		 * @return $results Array with data of the websites.
		 * @since 1.0.0
		 */
		public static function refresh_website_list() {

			$results = array();
			$options = WPISPConfig_Settings::get_option();
			try {
				$api	 = wpispconfig_get_current_api($options);
				$clients = $api->client_get_all();
				foreach($clients as $key => $client_id) {
					$sys_groupid	 = $api->client_get_groupid($client_id);
					$new_websites	 = $api->sites_web_domain_get(array('sys_groupid' => $sys_groupid));
					$results		 = array_merge($new_websites, $results);
				}
				set_transient('wpispconfig_websites_dashboard', $results, 6 * HOUR_IN_SECONDS);
			}catch (Exception $e) {
				throw new Exception($e->getMessage(), 1);
			}

			return $results;
		}

		/**
		 * Static function settings_menu
		 * Add menus and hooks to the dashboard page.
		 * @access public
		 * @return void
		 * @since 1.0.0
		 */
		public static function settings_menu() {
			$page = add_menu_page(
				__('WP-ISPConfig', 'wpispconfig'),
				__('WP-ISPConfig', 'wpispconfig'),
				'manage_options',
				'ispconfig_dashboard',
				array(__CLASS__, 'page'),
				WPISPCONFIG_PLUGIN_URL . 'assets/images/prou.png',
				3
			);

			add_action('admin_print_styles-' . $page, array(__CLASS__, 'add_styles'));
			add_action('admin_print_scripts-' . $page, array(__CLASS__, 'add_scripts'));
		}

		public static function testingdns() {
			$api			 = null;
			$options		 = WPISPConfig_Settings::get_option();
			$default_values	 = WPISPConfig_DefaultValues::get_option();
			$template_dns	 = array();
			$servers		 = array();
			try {
				$api			 = wpispconfig_get_current_api($options);
				//$soap = new SoapIspconfig($options);
				//print_r($soap->server_get_all());
				$template_dns	 = $api->dns_templatezone_get_all();
				$servers		 = $api->server_get_all();
			}catch (Exception $e) {
				echo '<div class="notice notice-error">' . sprintf(__('Failed to connect with ISPConfig API. Please check your <a href="%s">Settings</a> and test the connection:', 'wpispconfig'), admin_url('admin.php?page=ispconfig_settings')) . '<strong> ' . $e->getMessage() . '</strong></div>';
			}

			$new_dns_templatezone = array(
				'client_id'		 => 1,
				'template_id'	 => '1',
				'domain'		 => 'aaatest.netmdp.com',
				'ip'			 => '*',
				'ns1'			 => 'ns1.etruel.com',
				'ns2'			 => 'ns2.etruel.com',
				'email'			 => 'etruel.gmail.com',
			);

			$dns_zone = $api->dns_templatezone_add($new_dns_templatezone);
			print_r($dns_zone);
			die('die testing');
		}

		/**
		 * Static function page
		 * This function takes care in print the HTML of the dashboard page.
		 * @access public
		 * @return void
		 * @since 1.0.0
		 */
		public static function page() {
			global $wp_settings_sections;
			if(!current_user_can('manage_options')) {
				wp_die(__('You do not have sufficient permissions to access this page.'));
			}

//												self::testingdns();
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
										<a class="button" href="<?php echo admin_url('admin-post.php?action=ispconfig_refresh_list&nonce=' . wp_create_nonce('wpispc_refresh_list')); ?>">Refresh List</a>								
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

										<?php
										$results = get_transient('wpispconfig_websites_dashboard');
										if($results === false) {
											try {
												$results = self::refresh_website_list();
											}catch (Exception $e) {
												echo '<div class="notice notice-error">' . sprintf(__('Failed to connect with ISPConfig API. Please check your <a href="%s">Settings</a> and test the connection:', 'wpispconfig'), admin_url('admin.php?page=ispconfig_settings')) . '<strong> ' . $e->getMessage() . '</strong></div>';
											}
										}else {
											if(!is_array($results)) {
												$results = array();
											}
										}
										?>
										<table class="wp-list-table widefat fixed striped posts">
											<?php
											if(!empty($results)) {
												foreach($results as $key => $value) {
													echo '<tr><td>' . $value['domain'] . '</td><td>' . ($value['hd_quota'] == '-1' ? __('Unlimited', 'wpispconfig') : $value['hd_quota'] . ' KB') . '</td></tr>';
												}
											}else {
												echo '<tr><td>' . __('There are not websites to show.', 'wpispconfig') . '</td></tr>';
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
