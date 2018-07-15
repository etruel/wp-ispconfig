<?php
// Exit if accessed directly
if ( !defined('ABSPATH') ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}
class wpe_licenses_handlers {
	public static $plugin_updater_path = null;
	public static $text_domain = '';
	public static $license_page = null;
	public static $plugin_url = null;
	public static $screen_id = '';
	public static $plugin_version = '0';
	
	public static function hooks() {
		add_action('admin_init', array(__CLASS__, 'plugin_updater'), 0 );
		add_action('wpempro_licenses_forms', array(__CLASS__, 'license_page') );
		add_action('admin_print_scripts', array(__CLASS__, 'scripts'));
		add_action('admin_print_styles', array(__CLASS__, 'styles'));
		
		add_action('wp_ajax_wpematico_check_license', array(__CLASS__, 'ajax_check_license'));
		add_action('wp_ajax_wpematico_status_license', array(__CLASS__, 'ajax_change_status_license'));
		
		add_action( 'admin_post_wpematico_save_licenses', array(__CLASS__, 'save_licenses'));

	}
	
	
	public static function plugin_updater() {
		
		$plugins_args = array();
		$plugins_args = apply_filters('wpematico_plugins_updater_args', $plugins_args);
		
		if(!class_exists( 'EDD_SL_Plugin_Updater') && !empty($plugins_args)) {
			if(file_exists(self::$plugin_updater_path)) {
				require_once(self::$plugin_updater_path);
			} 
		}
	
		foreach ($plugins_args as $plugin_name => $args) {
			$license_key = self::get_key($plugin_name);
			$edd_updater = new EDD_SL_Plugin_Updater($args['api_url'], $args['plugin_file'], array(
					'version' 	=> $args['api_data']['version'], 
					'license' 	=> $license_key, 		
					'item_name' => $args['api_data']['item_name'], 	
					'author' 	=> $args['api_data']['author']
				)
			);
			
			if( ! is_multisite() ) {
				//$current = get_site_transient( 'update_plugins' );
				add_action( 'after_plugin_row_' . plugin_basename($args['plugin_file']), 'wp_plugin_update_row', 10, 2 );
			}
			
		}
	}
	public static function get_key($plugin_name) {
		$keys = get_option('wpematico_license_keys');
		if ($keys === false) {
			$keys = array();
		}
		if (empty($keys[$plugin_name])) {
			return false;
		}
		return $keys[$plugin_name];
	}
	public static function get_license_status($plugin_name) {
		$keys = get_option('wpematico_license_status');
		if ($keys === false) {
			$keys = array();
		}
		if (empty($keys[$plugin_name])) {
			return false;
		}
		return $keys[$plugin_name];
	}
	public static function set_license_status($plugin_name, $status) {
		$keys = get_option('wpematico_license_status');
		if ($keys === false) {
			$keys = array();
		}
		$keys[$plugin_name] = $status;
		update_option( 'wpematico_license_status', $keys);
	}
	public static function change_status_license($plugin_name, $action) {
		$plugins_args = array();
		$plugins_args = apply_filters('wpematico_plugins_updater_args', $plugins_args);
		if (empty($plugins_args[$plugin_name])) {
			return false;
		}	
		$license = self::get_key($plugin_name);
		
		$api_params = array(
			'edd_action'=> $action,
			'license' 	=> $license,
			'item_name' => urlencode($plugins_args[$plugin_name]['api_data']['item_name']),
			'url'       => home_url()
		);

			
		$response = wp_remote_post( esc_url_raw($plugins_args[$plugin_name]['api_url']), array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );
		if (is_wp_error($response)) {
			return false;
		}
				
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );
		if (!$license_data) {
			$license_data = new stdClass();
			$license_data->license = 'invalid';
			$license_data->site_count = 0;
			$license_data->activations_left = 0;
			$license_data->license_limit = 0;
			$license_data->expires = 0;
			
		}
		self::set_license_status($plugin_name, $license_data->license);
		return $license_data;
	}
	public static function ajax_change_status_license() {
		if (!empty($_POST['plugin_name']) && !empty($_POST['status'])) {
			$action_return = self::change_status_license($_POST['plugin_name'], $_POST['status']);
			echo json_encode($action_return);
			wp_die();
			
		}
		
	}
	public static function ajax_check_license() {
		$plugin_name = $_POST['plugin_name'];
		$plugins_args = array();
		$plugins_args = apply_filters('wpematico_plugins_updater_args', $plugins_args);
		if (empty($plugins_args[$plugin_name])) {
			wp_die('error');
		}
		$license = $_POST['license'];
		$args = array(
			'license' 	=> $license,
			'item_name' => urlencode($plugins_args[$plugin_name]['api_data']['item_name']),
			'url'       => home_url(),
			'version' 	=> $plugins_args[$plugin_name]['api_data']['version'],
			'author' 	=> 'Esteban Truelsegaard'	
		);
		$api_url = $plugins_args[$plugin_name]['api_url'];
		$lisense_object = self::check_license($api_url, $args);
		echo json_encode($lisense_object);
		wp_die();
	}
	public static function check_license($api_url, $args) {
		$args['edd_action'] = 'check_license';
		$api_params = $args;
		$response = wp_remote_post( esc_url_raw($api_url), array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );
		if (is_wp_error($response)) {
			return false;
		}
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );
		return $license_data;
		
	}
	public static function styles() {
		$screen = get_current_screen();
		if ($screen->id == self::$screen_id) {
			wp_enqueue_style('wpematico-settings-licenses', self::$plugin_url.'assets/css/licenses_handlers.css');	
		}
		
	}
	public static function scripts() {
		$screen = get_current_screen();
		if ($screen->id == self::$screen_id) {
			wp_enqueue_script( 'wpematico-jquery-settings-licenses', self::$plugin_url. 'assets/js/licenses_handlers.js', array( 'jquery' ), self::$plugin_version, true );
			wp_localize_script('wpematico-jquery-settings-licenses', 'wpematico_license_object',
				array('ajax_url' => admin_url( 'admin-ajax.php' ),
					'txt_check_license' => __('Check License', self::$text_domain),
				)
			);
		}
	}
	public static function save_licenses() {
		if (!isset($_POST['wpematico_save_licenses_nonce']) || !wp_verify_nonce($_POST['wpematico_save_licenses_nonce'], 'wpematico_save_licenses')) {
			wp_redirect(self::$license_page);
			exit();
		}
		$keys = (isset($_POST['license_key']) && !empty($_POST['license_key']) ) ? $_POST['license_key'] : array();
		$plugins_args = array();
		$plugins_args = apply_filters('wpematico_plugins_updater_args', $plugins_args);
		update_option( 'wpematico_license_keys', $keys);
		foreach ($keys as $plugin_name => $key) {
			if (empty($plugins_args[$plugin_name])) {
				continue;
			}
			$license = $keys[$plugin_name];
			$args = array(
				'license' 	=> $license,
				'item_name' => urlencode($plugins_args[$plugin_name]['api_data']['item_name']),
				'url'       => home_url(),
				'version' 	=> $plugins_args[$plugin_name]['api_data']['version'],
				'author' 	=> 'Esteban Truelsegaard'	
			);

			$api_url = $plugins_args[$plugin_name]['api_url'];
			$license_data = self::check_license($api_url, $args);
			if (!$license_data) {
				$license_data = new stdClass();
				$license_data->license = 'invalid';
				$license_data->site_count = 0;
				$license_data->activations_left = 0;
				$license_data->license_limit = 0;
				$license_data->expires = 0;
				
			}
			self::set_license_status($plugin_name, $license_data->license);
		}
		wp_redirect(self::$license_page);
		exit();
	}
	public static function license_page() {
		$plugins_args = array();
		$plugins_args = apply_filters('wpematico_plugins_updater_args', $plugins_args);
		if (empty($plugins_args)) {
			echo '<div class="msg"><p>', __('This is where you would enter the license keys for one of our premium plugins, should you activate one.', 'wpematico'), '</p>';
  			 echo '<p>', __('See some of the WPeMatico Add-ons in the', 'wpematico'), ' <a href="', admin_url( 'plugins.php?page=wpemaddons').'">Extensions list</a>.</p></div>';
  			 return true;
		}
		echo '<div class="wrap">
				<div class="postbox ">
					<div class="inside">
						<form method="post" action="'.admin_url('admin-post.php' ).'">
						<input type="hidden" name="action" value="wpematico_save_licenses">
						'.wp_nonce_field('wpematico_save_licenses', 'wpematico_save_licenses_nonce').'
		';
		foreach ($plugins_args as $plugin_name => $args) {
			$license = self::get_key($plugin_name);
			$plugin_title_name = $args['api_data']['item_name'];
			$license_status = self::get_license_status($plugin_name);
			$status_license_html = '';
			if ($license_status != false && $license_status == 'valid') {
				$status_license_html = '<strong>'.__('Status', self::$text_domain).':</strong> '.__('Valid', self::$text_domain).'<span class="validcheck"> </span>
										<br/>
										<input id="'.$plugin_name.'_btn_license_deactivate" class="btn_license_deactivate button-secondary" name="'.$plugin_name.'_btn_license_deactivate" type="button" value="'.__('Deactivate License', self::$text_domain).'" style="vertical-align: middle;"/>';
			} else if ($license_status === 'invalid' || $license_status === 'expired' || $license_status === 'item_name_mismatch' ) {
				$status_license_html = '<strong>'.__('Status', self::$text_domain).':</strong> '.__('Invalid', self::$text_domain).'<i class="renewcheck"></i>';
			} elseif($license_status === 'inactive' || $license_status === 'deactivated' || $license_status === 'site_inactive' ) {
				$status_license_html = '<strong>'.__('Status', self::$text_domain).':</strong> '.__('Inactive', self::$text_domain).'<i class="warningcheck"></i>
				<br/>
				<input id="'.$plugin_name.'_btn_license_activate" class="btn_license_activate button-secondary" name="'.$plugin_name.'_btn_license_activate" type="button" value="'.__('Activate License', self::$text_domain).'"/>
				';
			}
			
			
			$html_addons = '
			
			
			<h2><span class="dashicons-before dashicons-admin-plugins"></span>'.__($plugin_title_name.' License', self::$text_domain).'</h2>
			<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row" valign="top">
						'.__('License Key', self::$text_domain).'
					</th>
					<td>
						<input id="license_key_'.$plugin_name.'" data-plugin="'.$plugin_name.'" class="regular-text inp_license_key" name="license_key['.$plugin_name.']" type="text" value="'.esc_attr( $license ).'" /><br />
						<label class="description" for="license_key_'.$plugin_name.'">'.__('Enter your license key', self::$text_domain).'</label>
					</td>
				</tr>';
				if ($license != false) {
					$html_div = '';
					$args_check = array(
						'license' 	=> $license,
						'item_name' => urlencode($args['api_data']['item_name']),
						'url'       => home_url(),
						'version' 	=> $args['api_data']['version'],
						'author' 	=> 'Esteban Truelsegaard'	
					);
					$api_url = $args['api_url'];
					$license_data = self::check_license($api_url, $args_check);
					if (!$license_data) {
						$license_data = new stdClass();
					}
					if (!isset($license_data->license)) {
						$license_data->license = 'invalid';
					}
					if (!isset($license_data->site_count)) {
						$license_data->site_count = 0;
					}
					if (!isset($license_data->activations_left)) {
						$license_data->activations_left = 0;
					}
					if (!isset($license_data->license_limit)) {
						$license_data->license_limit = 0;
					}
					if (!isset($license_data->expires)) {
						$license_data->expires = 0;
					}
					
					if (is_object($license_data)) {
						
						$currentActivations = $license_data->site_count;
						$activationsLeft = $license_data->activations_left;
						$activationsLimit = $license_data->license_limit;
						$expires = $license_data->expires;
						$expires = substr( $expires, 0, strpos( $expires, " "));
						
						if (!empty($license_data->payment_id) && !empty($license_data->license_limit)) {
							
							$html_div .= '<small>';
							if ($license_status !== 'valid' && $activationsLeft === 0) {
								$accountUrl = 'http://etruel.com/my-account/?action=manage_licenses&payment_id=' . $license_data->payment_id;
								$html_div .= '<a href="'.$accountUrl.'">'.__("No activations left. Click here to manage the sites you've activated licenses on.", self::$text_domain).'</a>
										<br/>';
								
							}
							if (!empty($expires)) {
								if ( strtotime($expires) < strtotime("+2 weeks") ) {
									$renewalUrl = esc_attr($args['api_url']. '/checkout/?edd_license_key=' . $license); 
									$html_div .= '<a href="'.$renewalUrl.'">'.__('Renew your license to continue receiving updates and support.', self::$text_domain).'</a>
											<br/>';
									
								}
							}
							
							$html_div .= '<strong>'.__('Activations', self::$text_domain).':</strong>
										'.$currentActivations.'/'.$activationsLimit.' ('.$activationsLeft.' left)
									<br/>
									<strong>'.__('Expires on', self::$text_domain).':</strong>
										<code>'.$expires.'</code>
									<br/>
									<strong>'.__('Registered to', self::$text_domain).':</strong>
										'.$license_data->customer_name.' (<code>'.$license_data->customer_email.'</code>)
								</small>';			
							
						}
					} 
								
					$html_addons .= '<tr id="tr_license_status_'.$plugin_name.'" class="tr_license_status" style="vertical-align: middle;">
						<th scope="row" style="vertical-align: middle;">
							'.__('Activated for updates', self::$text_domain).'
						</th>
						<td id="td_license_status_'.$plugin_name.'">
						<p>'.$status_license_html.'</p>
						<div id="'.$plugin_name.'_ajax_status_license">'.$html_div.'</div>
						</td>
					</tr>';
				} else {
					$html_addons .= '<tr id="tr_license_status_'.$plugin_name.'" class="tr_license_status" style="vertical-align: middle; display:none;">
						<th scope="row" style="vertical-align: middle;">
							'.__('Activated for updates', self::$text_domain).'
						</th>
						<td id="td_license_status_'.$plugin_name.'">
							
							<input id="'.$plugin_name.'_btn_license_check" class="btn_license_check button-secondary" name="'.$plugin_name.'_btn_license_check" type="button" value="'.__('Check License', self::$text_domain).'"/>
							<div id="'.$plugin_name.'_ajax_status_license" style="display:none;"></div>
						</td>
					</tr>';
					
				}
				
						
			$html_addons .= '</tbody>
			</table>
			
			';
			
			echo $html_addons;
			
		}
		submit_button();
		echo '</form>
			</div>
			</div>
		</div>';
	}
	
}
wpe_licenses_handlers::hooks();
?>