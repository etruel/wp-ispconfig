<?php
/**
 * @package         etruel\ISPConfig
 * @subpackage 	   Settings
 * @author          Esteban Truelsegaard <esteban@netmdp.com>
 */
// Exit if accessed directly
if(!defined('ABSPATH')) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}

if(!class_exists('WPISPConfig_Settings')) :

	class WPISPConfig_Settings {

		public static $setting_page;
		public static $setting_id;
		public static $options;

		/**
		 * Static function hooks
		 * @access public
		 * @return void
		 * @since 1.0.0
		 */
		public static function hooks() {
			self::$setting_page	 = 'ispconfig_settings';
			self::$setting_id	 = 'ispconfig_settings_id';
			if(is_admin()) { // admin actions
				add_action('admin_init', array(__CLASS__, 'register_settings'));
				add_action('admin_menu', array(__CLASS__, 'settings_menu'));
			}
			add_action('wp_ajax_ispconfig_testconnection', array(__CLASS__, 'test_connection'));
		}

		public static function test_connection() {
			$nonce = '';
			if(isset($_REQUEST['_wpnonce'])) {
				$nonce = $_REQUEST['_wpnonce'];
			}

			if(!wp_verify_nonce($nonce, 'test-connection-settings')) {
				wp_die('Security check');
			}
			$options = array();
			if(isset($_REQUEST['options'])) {
				parse_str($_REQUEST['options'], $options);
			}

			try {

				$api = wpispconfig_get_current_api($options['WPISPConfig_Options']);
			}catch (Exception $e) {
				wp_die($e->getMessage());
			}

			wp_die('connection-success');
		}

		public static function add_styles() {
			wp_enqueue_style('style-wpcispconfig-settings', WPISPCONFIG_PLUGIN_URL . 'assets/css/settings.css', array(), WPISPCONFIG_VERSION);
		}

		public static function add_scripts() {
			wp_enqueue_script('wpcispconfig-settings', WPISPCONFIG_PLUGIN_URL . 'assets/js/settings.js', array('jquery'), WPISPCONFIG_VERSION, true);

			wp_localize_script('wpcispconfig-settings', 'settings_obj',
				array(
					'ajax_url'			 => admin_url('admin-ajax.php'),
					'text_loading'		 => __('Loading...', 'wpispconfig'),
					'text_error_fail'	 => __('An error has been occurred. Please check the compatibility with this plugin.', 'wpispconfig'),
					'text_success'		 => __('You have logged successfully.', 'wpispconfig'),
					'nonce_test_con'	 => wp_create_nonce('test-connection-settings'),
				)
			);
		}

		public static function get_option() {
			if(is_null(self::$options)) {
				self::$options = self::sanitize_option(get_option(WPISPConfig::OPTION_KEY, array()));
			}
			return self::$options;
		}

		public static function settings_menu() {
			/*
			  $page = add_menu_page(
			  __( 'WP-ISPConfig', 'wpispconfig' ),
			  __( 'WP-ISPConfig', 'wpispconfig' ),
			  'manage_options',
			  'ispconfig_settings',
			  array(__CLASS__, 'settings_page'),
			  WPISPCONFIG_PLUGIN_URL . 'assets/images/prou.png',
			  3
			  );
			 */
			$page = add_submenu_page(
				'ispconfig_dashboard',
				__('Settings', 'wpispconfig'),
				__('Settings', 'wpispconfig'),
				'manage_options',
				'ispconfig_settings',
				array(__CLASS__, 'settings_page')
			);

			add_action('admin_print_styles-' . $page, array(__CLASS__, 'add_styles'));
			add_action('admin_print_scripts-' . $page, array(__CLASS__, 'add_scripts'));
		}

		public static function register_settings() { // whitelist options
			register_setting(self::$setting_id, WPISPConfig::OPTION_KEY, array(__CLASS__, 'sanitize_option'));

			add_settings_section(
				'wpispconfig_settings_general', // ID
				'', // Title
				array(__CLASS__, 'section_empty'), // Callback
				'wpispconfig_settings_general' // Page
			);


			add_settings_field(
				'soapusername', // ID
				__('Remote Username:', 'wpispconfig'), // Title 
				array(__CLASS__, 'settings_input'), // Callback
				'wpispconfig_settings_general', // Page
				'wpispconfig_settings_general', // Section
				array(
					'option_name'	 => WPISPConfig::OPTION_KEY,
					'option_id'		 => 'soapusername',
					'option_class'	 => 'regular-text',
					'label_for'		 => WPISPConfig::OPTION_KEY . '_' . 'soapusername',
				)
			);
			add_settings_field(
				'soappassword', // ID
				__('Remote Password:', 'wpispconfig'), // Title 
				array(__CLASS__, 'settings_input'), // Callback
				'wpispconfig_settings_general', // Page
				'wpispconfig_settings_general', // Section
				array(
					'option_name'	 => WPISPConfig::OPTION_KEY,
					'option_id'		 => 'soappassword',
					'option_class'	 => 'regular-text',
					'label_for'		 => WPISPConfig::OPTION_KEY . '_' . 'soappassword',
					'input_type'	 => 'password',
				)
			);

			add_settings_field(
				'remote_type',
				__('Select type:', 'wpispconfig'),
				array(__CLASS__, 'settings_input'),
				'wpispconfig_settings_general',
				'wpispconfig_settings_general',
				array(
					'option_name'	 => WPISPConfig::OPTION_KEY,
					'option_id'		 => 'remote_type',
					'label_for'		 => WPISPConfig::OPTION_KEY . '_' . 'remote_type',
					'input_type'	 => 'radio',
					'radios'		 => array('restapi' => 'Rest Api', 'soap' => 'Soap'),
					'option_class'	 => 'settings-api-type',
				)
			);


			add_settings_field(
				'soap_location', // ID
				__('Soap Location:', 'wpispconfig'), // Title 
				array(__CLASS__, 'settings_input'), // Callback
				'wpispconfig_settings_general', // Page
				'wpispconfig_settings_general', // Section
				array(
					'option_name'	 => WPISPConfig::OPTION_KEY,
					'option_id'		 => 'soap_location',
					'option_class'	 => 'regular-text',
					'label_for'		 => WPISPConfig::OPTION_KEY . '_' . 'soap_location',
				)
			);

			add_settings_field(
				'restapi_location', // ID
				__('Rest Api Location:', 'wpispconfig'), // Title 
				array(__CLASS__, 'settings_input'), // Callback
				'wpispconfig_settings_general', // Page
				'wpispconfig_settings_general', // Section
				array(
					'option_name'	 => WPISPConfig::OPTION_KEY,
					'option_id'		 => 'restapi_location',
					'option_class'	 => 'regular-text',
					'label_for'		 => WPISPConfig::OPTION_KEY . '_' . 'restapi_location',
				)
			);


			add_settings_field(
				'soap_uri', // ID
				__('Remote URI:', 'wpispconfig'), // Title 
				array(__CLASS__, 'settings_input'), // Callback
				'wpispconfig_settings_general', // Page
				'wpispconfig_settings_general', // Section
				array(
					'option_name'	 => WPISPConfig::OPTION_KEY,
					'option_id'		 => 'soap_uri',
					'option_class'	 => 'regular-text',
					'label_for'		 => WPISPConfig::OPTION_KEY . '_' . 'soap_uri',
				)
			);
			add_settings_field(
				'skip_ssl', // ID
				__('Skip certificate check:', 'wpispconfig'), // Title 
				array(__CLASS__, 'settings_input'), // Callback
				'wpispconfig_settings_general', // Page
				'wpispconfig_settings_general', // Section
				array(
					'option_name'	 => WPISPConfig::OPTION_KEY,
					'option_id'		 => 'skip_ssl',
					'option_class'	 => 'regular-text',
					'label_for'		 => WPISPConfig::OPTION_KEY . '_' . 'skip_ssl',
					'input_type'	 => 'checkbox',
					'label_text'	 => '',
				)
			);
		}

		public static function settings_input($args) {
			$type_input	 = (!empty($args['input_type']) ? $args['input_type'] : 'text');
			$value		 = esc_attr(self::$options[$args['option_id']]);
			switch ($type_input) {
				case 'checkbox':
					$value = 1;
					?><label>
						<input type="<?php echo $type_input; ?>" <?php checked(1, self::$options[$args['option_id']], true); ?> id="<?php echo $args['option_name'] . '_' . $args['option_id']; ?>" name="<?php echo $args['option_name']; ?>[<?php echo $args['option_id']; ?>]" value="<?php echo $value; ?>" /> 
						<strong><?php echo $args['label_text']; ?></strong></label>
					<?php
					if(!empty($args['text_after'])) {
						echo $args['text_after'];
					}
					if(!empty($args['help_text'])) :
						?>
						<p class="description"><?php echo $args['help_text']; ?></p>
					<?php
					endif;

					break;

				case 'radio':

					foreach($args['radios'] as $value => $text) :
						?>
						<label>
							<input class="<?php echo $args['option_class']; ?>" type="<?php echo $type_input; ?>" <?php checked($value, self::$options[$args['option_id']], true); ?> id="<?php echo $args['option_name'] . '_' . $args['option_id'] . '_' . $value; ?>" name="<?php echo $args['option_name']; ?>[<?php echo $args['option_id']; ?>]" value="<?php echo $value; ?>" /> 
							<strong><?php echo $text; ?></strong></label><br/>
						<?php
					endforeach;

					if(!empty($args['text_after'])) {
						echo $args['text_after'];
					}
					if(!empty($args['help_text'])) :
						?>
						<p class="description"><?php echo $args['help_text']; ?></p>
					<?php
					endif;
					break;
				default:
					?>
					<input class="<?php echo $args['option_class']; ?>" type="<?php echo $type_input; ?>" id="<?php echo $args['option_name'] . '_' . $args['option_id']; ?>" name="<?php echo $args['option_name']; ?>[<?php echo $args['option_id']; ?>]" value="<?php echo $value; ?>" /> 
					<?php
					if(!empty($args['text_after'])) {
						echo $args['text_after'];
					}
					if(!empty($args['help_text'])) :
						?>
						<p class="description"><?php echo $args['help_text']; ?></p>
					<?php
					endif;
					break;
			}
		}

		/**
		 * Sanitize each setting field as needed
		 *
		 * @param array $input Contains all settings fields as array keys
		 */
		public static function sanitize_option($input) {
			$new_input = array();


			$new_input['soapusername']	 = (!empty($input['soapusername']) ? $input['soapusername'] : 'remote_user' );
			$new_input['soappassword']	 = (!empty($input['soappassword']) ? $input['soappassword'] : 'remote_user_pass' );

			$new_input['soap_location'] = (!empty($input['soap_location']) ? $input['soap_location'] : 'https://localhost:8080/remote/index.php' );

			$new_input['soap_uri'] = (!empty($input['soap_uri']) ? $input['soap_uri'] : 'http://localhost:8080/remote/' );

			$new_input['skip_ssl'] = ( isset($input['skip_ssl']) ? absint($input['skip_ssl']) : 0 );

			$new_input['remote_type'] = (!empty($input['remote_type']) ? $input['remote_type'] : 'soap' );

			$new_input['restapi_location'] = (!empty($input['restapi_location']) ? $input['restapi_location'] : 'https://localhost:8080/remote/json.php' );


			return $new_input;
		}

		public static function section_empty($args) {
			
		}

		public static function settings_page() {
			global $wp_settings_sections;

			if(!current_user_can('manage_options')) {
				wp_die(__('You do not have sufficient permissions to access this page.'));
			}


			$options = self::get_option();
			?>

			<div class="wrap">
			<?php
			wpispconfig_get_settings_tabs();
			settings_errors();
			?>
				<h2><?php _e('ISPConfig settings', 'wpispconfig'); ?></h2>
				<form name="wpispconfig-settings" method="post" autocomplete="off" action="options.php">
				<?php
				settings_fields(self::$setting_id);
				?>
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
											<p style="text-align: right;">
			<?php submit_button(__('Save Settings', 'wpispconfig'), 'primary', 'wpe-export-save-settings', false); ?>
											</p>								
										</div>
									</div>



								</div>		<!-- #side-sortables -->
							</div>		<!--  postbox-container-1 -->		

			<?php do_action('wpematico_exporter_setting_page_before'); ?>
							<div id="postbox-container-2" class="postbox-container">
								<div id="normal-sortables" class="meta-box-sortables ui-sortable">

									<div id="advancedfetching" class="postbox">
										<button type="button" class="handlediv button-link" aria-expanded="true">
											<span class="screen-reader-text"><?php _e('Click to toggle'); ?></span>
											<span class="toggle-indicator" aria-hidden="true"></span>
										</button>
										<h3 class="hndle"><span class="dashicons dashicons-admin-tools"></span> <span><?php _e('General Settings', 'wpispconfig'); ?></span></h3>
										<div class="inside">
											<p></p>
			<?php
			do_settings_sections('wpispconfig_settings_general');
			?>
											<button class="button" type="button" id="btn-test-connection"><?php _e('Test connection', 'wpispconfig'); ?></button>
											<div id="test-connection-div" style="display: none;">
												<span class="spinner"></span>
												<p id="test-connection-message"><?php _e('Loading...', 'wpispconfig'); ?></p></div>
											<p></p>

										</div>
									</div>


								</div>		<!-- #normal-sortables -->
							</div>		<!--  postbox-container-2 -->		

							<div>
								<p>
			<?php submit_button(__('Save settings', 'wpispconfig'), 'primary', 'wpispconfig-save-settings2', false); ?>
								</p>
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
WPISPConfig_Settings::hooks();
