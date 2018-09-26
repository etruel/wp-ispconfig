<?php
/**
* @package         etruel\ISPConfig
* @subpackage 	   Domain Alias
* @author          Esteban Truelsegaard <esteban@netmdp.com>
*/ 
if ( !defined('ABSPATH') ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}
if (!class_exists('WPISPConfig_Domain_Alias')) :

class WPISPConfig_Domain_Alias {

	public static function hooks() {
		if ( is_admin() ){ // admin actions
			add_action('admin_menu', array(__CLASS__, 'menu'), 15 );
		}

		add_action( 'wp_ajax_wpispconfig_cmb_domain',  array(__CLASS__, 'cmb_domain'));
		add_action( 'admin_post_ispconfig_domain_alias_save', array(__CLASS__, 'save'));
	}
	public static function menu() {
		
	     $page = add_submenu_page( 
	    	'ispconfig_dashboard',  
	    	__('Domain Alias', 'wpispconfig' ),
	    	'<img src="' . WPISPCONFIG_PLUGIN_URL .'assets/images/pror.png'.'" style="margin: 0pt 2px -2px 0pt;"><span>' . 'Domain Alias',
    		'manage_options', 
    		'wpispconfig_domain_alias',
    		array(__CLASS__, 'page')
    	);

    	add_action( 'admin_print_styles-' . $page, array(__CLASS__, 'add_styles') );
		add_action( 'admin_print_scripts-' . $page, array(__CLASS__, 'add_scripts') );
	}
	public static function add_styles() {
		
	}
	public static function add_scripts() {
		wp_enqueue_script( 'wpispconfig-domain-alias', WPISPCONFIG_PLUGIN_URL . 'assets/js/domain-alias.js', array('jquery'), WPISPCONFIG_VERSION, true);
		wp_localize_script( 'wpispconfig-domain-alias', 'js_wpconfig_domain_alias', 
							array( 
									'ajaxurl' => admin_url('admin-ajax.php'), 
									'nonce' => wp_create_nonce('wpispc_nonce'),
									'txt_loading' => __('Loading...', 'wpispconfig' ),
							)
						);
	}

	public static function cmb_domain() {
		$nonce = '';
		if (!empty($_POST['nonce'])) {
			$nonce = $_POST['nonce'];
		}
		
		if ( !wp_verify_nonce( $nonce, 'wpispc_nonce' ) ) {
			die(json_encode( array('type' => "error", 'message' => "Conection not verified.") ));
		}
		$client_id = '';
		if (!empty($_POST['client'])) {
			$client_id = $_POST['client'];
		}	
		$options = WPISPConfig_Settings::get_option(); 

		try {
			$api = wpispconfig_get_current_api($options);
			//$soap = new SoapIspconfig($options);
			
			$sys_groupid = $api->client_get_groupid($client_id);
			
			$results = $api->sites_web_domain_get(array('sys_groupid' => $sys_groupid ));
			$result = array();
			$select_html = '';
			if (empty($results)) {
				$select_html .= '<option value="0">'.__( 'Select a Source Domain', 'wpispconfig' ).'</option>';
			} else {
				foreach($results as $row ){
					$select_html.= '<option value="'.$row['domain_id'].'">'.$row['domain_id'].' | '.$row['domain'].'</option>';
				}
			}
			$result['type'] = "success";
			$result['data'] = $select_html;
			$result_encoded = json_encode($result);
			die($result_encoded);

		} catch (Exception $e) {

			die(json_encode( array('type' => "error", 'message' => $e->getMessage() ) ));

		}
	}
	public static function page() {
		if(!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
       	
       	$options = WPISPConfig_Settings::get_option(); 
       	$clients = array();
		$client_data = array();
		$servers = array();
		$template_dns = array();
		$default_values = WPISPConfig_DefaultValues::get_option();
        ?>

        <div class="wrap">
		<h2><?php _e('WP-ISPConfig Add Domain Alias to Existent Domain', 'wpispconfig'); ?></h2>
		<form name="wpispconfig-allinone" method="post" autocomplete="off" action="<?php echo admin_url( 'admin-post.php' ); ?>">
			<input type="hidden" name="action" value="ispconfig_domain_alias_save"/>
			<?php
				wp_nonce_field('ispconfig_domain_alias_save');
			?>
			<div id="poststuff">
				<div id="post-body" class="metabox-holder columns-1">
					
					<div id="postbox-container-1" class="postbox-container">

						<div id="all_in_one" class="postbox">
							
							
							<div class="inside">
								<strong><?php _e( 'Select Client and fill data for All in One parameters.', 'wpispconfig' );?></strong><br />
										<?php _e( 'This will create an alias domain for client and domain with DNS zone from DNS template.', 'wpispconfig' );?><br />
								
								<?php
									$api = null;
									try {
										$api = wpispconfig_get_current_api($options);
										//$soap = new SoapIspconfig($options);
										$clients = $api->client_get_all();
										foreach ($clients as $key => $client_id) {
											$client_data[] = $api->client_get($client_id);
										}
										$servers = $api->server_get_all();
										$template_dns = $api->dns_templatezone_get_all();

									} catch (Exception $e) {
										echo '<div class="notice notice-error">' . sprintf(__('Failed to connect with ISPConfig API. Please check your <a href="%s">Settings</a> and test the connection:', 'wpispconfig'), admin_url('admin.php?page=ispconfig_settings'))  . '<strong> ' . $e->getMessage() . '</strong></div>';
									}

									
								?>

								<table class="form-table" id="client_table">
									<tr>
										<th scope="row">
											<label for="client_name"><?php _e( 'Add to Client:', 'wpispconfig' ); ?></label>
										</th>
										<td>
											<select id="client_id" name="client_id">
												<option value="0"><?php _e( 'Select a client', 'wpispconfig' ); ?></option>
											<?php 
												foreach ($client_data as $key => $values) {
													echo '<option value="' . $values['client_id'] . '" data-email="' . $values['email'] . '">' . $values['client_id'] . ' | ' . $values['contact_name'] . ' | ' . $values['company_name'] . '</option>';
												}
											?>
											</select>
										</td>
									</tr>

									<tr>
										<th scope="row">
											<label for="domain_id"><?php _e( 'Domain:', 'wpispconfig' ); ?></label>
										</th>
										<td id="domain_id_td">
											<select id="domain_id" name="domain_id">
												<option value="0"><?php _e( 'Select a client', 'wpispconfig' ); ?></option>
											</select>
										</td>
									</tr>
									
									<tr>
										<th scope="row">
											<label for="domain"><?php _e( 'New Domain Alias:', 'wpispconfig' ); ?></label>
										</th>
										<td>
											<input class="regular-text" type="text" id="domain" name="domain" value="">
										</td>
									</tr>
									<tr>
										<th scope="row">
											<label for="server"><?php _e( 'Server:', 'wpispconfig' ); ?></label>
										</th>
										<td>
											<select id="server" name="server">
											<?php 
												foreach ($servers as $key => $values) {
													echo '<option value="' . $values['server_id'] . '">' . $values['server_name'] . '</option>';
												}
											?>	
											</select>
										</td>
									</tr>

									<tr>
										<th scope="row">
											<label for="dns_template_id"><?php _e( 'DNS Template ID:', 'wpispconfig' ); ?></label>
										</th>
										<td>
											<?php if(empty($template_dns)) { 

												echo '<input class="regular-text" type="text" id="dns_template_id" name="dns_template_id" value="1">';

											} else {
												
												echo '<select id="dns_template_id" name="dns_template_id">';
												foreach ($template_dns as $key => $values) {
													echo '<option value="' . $values['template_id'] . '">' . $values['name'] . '</option>';
												}
												echo '</select>';
											} ?>	
											
										</td>
									</tr>
									
								</table>
								<table class="form-table">
									
									
									
									<tr>
										<th scope="row">
											<label for="client_ip"><?php _e( 'Client IP:', 'wpispconfig' ); ?></label>
										</th>
										<td>
											<input class="regular-text" type="text" id="client_ip" name="client_ip" value="<?php echo $default_values['client_ip'] ?>">
										</td>
									</tr>	
									<tr>
										<th scope="row">
											<label for="ns1"><?php _e( 'NameServer 1:', 'wpispconfig' ); ?></label>
										</th>
										<td>
											<input class="regular-text" type="text" id="ns1" name="ns1" value="<?php echo $default_values['ns1'] ?>">
										</td>
									</tr>
									<tr>
										<th scope="row">
											<label for="ns2"><?php _e( 'NameServer 2:', 'wpispconfig' ); ?></label>
										</th>
										<td>
											<input class="regular-text" type="text" id="ns2" name="ns2" value="<?php echo $default_values['ns2'] ?>">
										</td>
									</tr>

									<tr>
										<th scope="row">
											<label for="ns2"><?php _e( 'Email:', 'wpispconfig' ); ?></label>
										</th>
										<td>
											<input class="regular-text" type="text" id="email" name="email" value="<?php echo $default_values['email'] ?>">
										</td>
									</tr>
								</table>
							
							</div>
						</div>

					</div>		<!--  postbox-container-2 -->		

					<div>
						<p>
						<?php submit_button(__('Create all', 'wpispconfig'), 'primary', 'wpispconfig-save-settings2', false); ?>
						</p>
					</div>
				</div> <!-- #post-body -->
			</div> <!-- #poststuff -->
		</form>		
	</div><!-- .wrap -->
        <?php

	}
	public static function create($array_values = array()) {
		$options = WPISPConfig_Settings::get_option();


		$values = array();
		$values['client_id'] = (!empty($array_values['client_id']) ? $array_values['client_id'] : '0');
		$values['domain_id'] = (!empty($array_values['domain_id']) ? $array_values['domain_id'] : '0');
		$values['domain'] = (!empty($array_values['domain']) ? $array_values['domain'] : '');

		
		$values['server_id'] = (!empty($array_values['server']) ? $array_values['server'] : '1');
		$values['template_id'] = (!empty($array_values['dns_template_id']) ? $array_values['dns_template_id'] : '');
		$values['client_ip']  = (!empty($array_values['client_ip']) ? $array_values['client_ip'] : '');
		$values['ns1'] = (!empty($array_values['ns1']) ? $array_values['ns1'] : '');
		$values['ns2'] = (!empty($array_values['ns2']) ? $array_values['ns2'] : '');
		$values['email'] =  (!empty($array_values['email']) ? $array_values['email'] : '');
		$values['dns_email'] = str_replace('@', '.', $values['email']);

		if (empty($values['client_id']) || empty($values['domain_id']) || empty($values['domain']) ) {
			throw new Exception(__( 'Empty client or domain.', 'wpispconfig' ), 1);
		}


		try {
			$api = wpispconfig_get_current_api($options);
			//$soap = new SoapIspconfig($options);
			
			
			$new_aliasdomain = array( 
								'domain' 			=>  $values['domain'], 
								'parent_domain_id' 	=> $values['domain_id'],
							);

			$aliasdomain_id = $api->sites_web_aliasdomain_add($values['client_id'], $new_aliasdomain);

			$new_dns_templatezone = array(
					            'client_id' 	=> $values['client_id'],
					            'template_id' 	=> $values['template_id'],
					            'domain' 		=> $values['domain'],
					            'ip' 			=> $values['client_ip'],
					            'ns1' 			=> $values['ns1'],
					            'ns2' 			=> $values['ns2'],
					            'email' 		=> $values['dns_email'],
					        );
	        $dns_zone = $api->dns_templatezone_add( $new_dns_templatezone );
			
	        return array(
	        	'aliasdomain_id'	=> $aliasdomain_id,
	        	'template_id'		=> $values['template_id']

	        );
	        
	        

		} catch (Exception $e) {
			throw new Exception($e->getMessage(), 1);	
		}
		return false;



	}

	public static function save() {
		if ( ! wp_verify_nonce($_POST['_wpnonce'], 'ispconfig_domain_alias_save' ) ) {
		    wp_die(__( 'Security check', 'wpispconfig' )); 
		}
		$notices_success = array();
		try {

			$created_values = self::create($_POST);

			$notices_success[] = '<strong>' . __( 'Aliasdomain ID:', 'wpispconfig' ) .'</strong> ' . $created_values['aliasdomain_id'] . '<br/>';
			$notices_success[] = '<strong>' . __( 'DNS Zones added from DNS template:', 'wpispconfig' ) .'</strong> '. $created_values['template_id'] .'<br/>';


		} catch (Exception $e) {
			WPISPConfig_notices::add( array('text' => $e->getMessage(), 'error' => true) );
			wp_redirect($_POST['_wp_http_referer']);
			die();
		}

		// Trying refresh list of dashboard without intervention in the  process of  new website
		try {
			WPISPConfig_Dashboard::refresh_website_list();
		} catch (Exception $e) {
			WPISPConfig_notices::add( array('text' => $e->getMessage(), 'error' => true) );
		}
		
		if (empty($notices_success)) {
			WPISPConfig_notices::add(__( 'Created domain alias', 'wpispconfig' ));
		} else {
			$sucess_notice = implode('', $notices_success);
			WPISPConfig_notices::add( $sucess_notice );
		}
		wp_redirect($_POST['_wp_http_referer']);
		die();

	}

}

endif;
WPISPConfig_Domain_Alias::hooks();

?>