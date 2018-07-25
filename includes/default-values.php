<?php
/**
* @package         etruel\ISPConfig
* @subpackage 	   Settings
* @author          Esteban Truelsegaard <esteban@netmdp.com>
*/
// Exit if accessed directly
if ( !defined('ABSPATH') ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

if (!class_exists('WPISPConfig_DefaultValues')) :
class WPISPConfig_DefaultValues {

	public static $setting_page;
	public static $setting_id;
	public static $options;
	const OPTION_KEY = 'WPISPConfig_DefaultValues';
	/**
	* Static function hooks
	* @access public
	* @return void
	* @since 1.0.0
	*/
	public static function hooks() {
		self::$setting_page = 'ispconfig_defaultvalues';
		self::$setting_id = 'wpematico_exporter_settings_id';
		if ( is_admin() ){ // admin actions
			add_action('admin_init', array(__CLASS__, 'register_settings') );
			add_action('admin_menu', array(__CLASS__, 'settings_menu') );

			
		}
		add_filter('parent_file',  array( __CLASS__, 'tax_menu_correction'));
		add_filter('submenu_file',  array( __CLASS__, 'tax_submenu_correction'));
	}

	

	public static function add_styles() {
		
	}
	public static function add_scripts() {
		
	}
	
	public static function get_option() {
		if (is_null(self::$options)) {
			self::$options = self::sanitize_option(get_option( self::OPTION_KEY, array() ) );
		}
		return self::$options;
	}

	public static function settings_menu() {
		
	    $page = add_submenu_page( 
	    	null, 
	    	 __( 'Defaul Values', 'wpispconfig' ),
	    	 __( 'Default Values', 'wpispconfig' ),
    		'manage_options', 
    		'ispconfig_defaultvalues',
    		array(__CLASS__, 'settings_page')
    	); 
	
		add_action( 'admin_print_styles-' . $page, array(__CLASS__, 'add_styles') );
		add_action( 'admin_print_scripts-' . $page, array(__CLASS__, 'add_scripts') );

	}
	// highlight the proper top level menu
	static function tax_menu_correction($parent_file) {
		global $current_screen;
		if ($current_screen->id == "admin_page_ispconfig_defaultvalues") {
			$parent_file = 'ispconfig_dashboard';
		}
		return $parent_file;
	}
	
	// highlight the proper sub level menu
	static function tax_submenu_correction($submenu_file) {
		global $current_screen, $plugin_page;
		if ($current_screen->id == "admin_page_ispconfig_defaultvalues") {
			$submenu_file = 'ispconfig_settings';
			$plugin_page = 'ispconfig_settings';
		}
		return $submenu_file;
	}

	public static function register_settings() { // whitelist options
																			  
		register_setting( self::$setting_id, self::OPTION_KEY, array(__CLASS__, 'sanitize_option')); 

		add_settings_section(
            'wpispconfig_defaultvalues_settings', // ID
            '', // Title
            array(__CLASS__, 'section_empty' ), // Callback
            'wpispconfig_defaultvalues_settings' // Page
        );  


        add_settings_field(
            'client_ip', // ID
            __('Client IP:', 'wpispconfig'), // Title 
            array(__CLASS__, 'settings_input' ), // Callback
            'wpispconfig_defaultvalues_settings', // Page
            'wpispconfig_defaultvalues_settings', // Section
            array( 
            	'option_name' => self::OPTION_KEY,
            	'option_id' => 'client_ip',
                'option_class' => 'regular-text',
            	'label_for' => self::OPTION_KEY . '_' . 'client_ip',
            	
            )     
        ); 

        add_settings_field(
            'email', // ID
            __('Email:', 'wpispconfig'), // Title 
            array(__CLASS__, 'settings_input' ), // Callback
            'wpispconfig_defaultvalues_settings', // Page
            'wpispconfig_defaultvalues_settings', // Section
            array( 
            	'option_name' => self::OPTION_KEY,
            	'option_id' => 'email',
                'option_class' => 'regular-text',
            	'label_for' => self::OPTION_KEY . '_' . 'email',
            	
            )     
        );

        add_settings_field(
            'ns1', // ID
            __('NameServer 1:', 'wpispconfig'), // Title 
            array(__CLASS__, 'settings_input' ), // Callback
            'wpispconfig_defaultvalues_settings', // Page
            'wpispconfig_defaultvalues_settings', // Section
            array( 
            	'option_name' => self::OPTION_KEY,
            	'option_id' => 'ns1',
                'option_class' => 'regular-text',
            	'label_for' => self::OPTION_KEY . '_' . 'ns1',
            	
            )     
        ); 

        add_settings_field(
            'ns2', // ID
            __('NameServer 2:', 'wpispconfig'), // Title 
            array(__CLASS__, 'settings_input' ), // Callback
            'wpispconfig_defaultvalues_settings', // Page
            'wpispconfig_defaultvalues_settings', // Section
            array( 
            	'option_name' => self::OPTION_KEY,
            	'option_id' => 'ns2',
                'option_class' => 'regular-text',
            	'label_for' => self::OPTION_KEY . '_' . 'ns2',
            	
            )     
        ); 
        
		
	}

	

    public static function settings_input($args) {
    	$type_input = (!empty($args['input_type']) ? $args['input_type'] : 'text');
    	$value = esc_attr( self::$options[$args['option_id']]);
		switch($type_input) {
			case 'checkbox':
				$value = 1; 
				?><label>
				<input type="<?php echo $type_input; ?>" <?php checked(1, self::$options[$args['option_id']], true); ?> id="<?php echo $args['option_name']. '_'. $args['option_id']; ?>" name="<?php echo $args['option_name']; ?>[<?php echo $args['option_id']; ?>]" value="<?php echo $value; ?>" /> 
				<strong><?php echo $args['label_text']; ?></strong></label>
				<?php 
				if (!empty($args['text_after'])) {
					echo $args['text_after'];
				}
				if (!empty($args['help_text'])) : ?>
					<p class="description"><?php echo $args['help_text']; ?></p>
				<?php endif;

				break;

			case 'radio':

				foreach ($args['radios'] as $value => $text) : ?>
					<label>
					<input class="<?php echo $args['option_class']; ?>" type="<?php echo $type_input; ?>" <?php checked($value, self::$options[$args['option_id']], true); ?> id="<?php echo $args['option_name']. '_'. $args['option_id'] . '_' . $value; ?>" name="<?php echo $args['option_name']; ?>[<?php echo $args['option_id']; ?>]" value="<?php echo $value; ?>" /> 
					<strong><?php echo $text; ?></strong></label><br/>
				<?php 
				endforeach;

				if (!empty($args['text_after'])) {
					echo $args['text_after'];
				}
				if (!empty($args['help_text'])) : ?>
					<p class="description"><?php echo $args['help_text']; ?></p>
				<?php endif;
				break;
			default:
				?>
				<input class="<?php echo $args['option_class']; ?>" type="<?php echo $type_input; ?>" id="<?php echo $args['option_name']. '_'. $args['option_id']; ?>" name="<?php echo $args['option_name']; ?>[<?php echo $args['option_id']; ?>]" value="<?php echo $value; ?>" /> 
				<?php 
				if (!empty($args['text_after'])) {
					echo $args['text_after'];
				}
				if (!empty($args['help_text'])) : ?>
					<p class="description"><?php echo $args['help_text']; ?></p>
				<?php endif;
				break;
		}
    }
   
	/**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public static function sanitize_option( $input ) {
        $new_input = array();

        $new_input['client_ip'] = ( !empty( $input['client_ip'] ) ? $input['client_ip']  : (!empty($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '') );
		
		$new_input['email'] = ( !empty( $input['email'] ) ? $input['email']  : (!empty( get_bloginfo('admin_email') ) ? get_bloginfo('admin_email') : '') );
        

		$new_input['ns1'] = ( !empty( $input['ns1'] ) ? $input['ns1']  : (!empty($_SERVER['SERVER_NAME']) ? 'ns1.' .$_SERVER['SERVER_NAME'] : '') );
		
		$new_input['ns2'] = ( !empty( $input['ns2'] ) ? $input['ns2']  : (!empty($_SERVER['SERVER_NAME']) ? 'ns2.' . $_SERVER['SERVER_NAME'] : '') );
	
        return $new_input;
    }

	public static function section_empty($args) {
         
    }
	public static function settings_page() {
		global $wp_settings_sections, $current_screen;
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
		<h2><?php _e('ISPConfig Default Values', 'wpispconfig'); ?></h2>
		<form name="wpispconfig-default-values" method="post" autocomplete="off" action="options.php">
			<?php
			settings_fields( self::$setting_id );
			
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
							<h3 class="hndle"><span class="dashicons dashicons-admin-tools"></span> <span><?php _e('Default Values', 'wpispconfig'); ?></span></h3>
							<div class="inside">
								<p></p>
								<?php
									do_settings_sections( 'wpispconfig_defaultvalues_settings' );
								?>
								
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
WPISPConfig_DefaultValues::hooks();
