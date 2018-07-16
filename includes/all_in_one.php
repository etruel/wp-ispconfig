<?php
/**
* @package         etruel\ISPConfig
* @subpackage 	   All in one
* @author          The Team Mate at etruel.com
* @copyright       Copyright (c) 2018
*/
// Exit if accessed directly
if ( !defined('ABSPATH') ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

if (!class_exists('WPISPConfig_all_in_one')) :
class WPISPConfig_all_in_one {

	public static function hooks() {
		if ( is_admin() ){ // admin actions
			add_action('admin_menu', array(__CLASS__, 'menu') );
		}
		add_action( 'admin_post_ispconfig_allinone_save', array(__CLASS__, 'save'));
	}

	public static function menu() {
		
	    $page = add_submenu_page( 
	    	'ispconfig_settings', 
	    	'All in one', 
	    	'All in one',
    		'manage_options', 
    		'wpispconfig_allinone',
    		array(__CLASS__, 'page')
    	);
	}

	public static function page() {
		 if(!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
       	
       	$options = WPISPConfig_Settings::get_option(); 
       	$template_dns = array();
       	$servers = array();
        ?>

        <div class="wrap">
		<h2><?php _e('All in one', 'wpispconfig'); ?></h2>
		<form name="wpispconfig-allinone" method="post" autocomplete="off" action="<?php echo admin_url( 'admin-post.php' ); ?>">
			<input type="hidden" name="action" value="ispconfig_allinone_save"/>
			<?php
				wp_nonce_field('ispconfig_allinone_save');
			?>
			<div id="poststuff">
				<div id="post-body" class="metabox-holder columns-1">
					
					<div id="postbox-container-1" class="postbox-container">

						<div id="all_in_one" class="postbox">
							
							
							<div class="inside">
								<p></p>
								
								<?php

									try {
										$soap = new SoapIspconfig($options);
										//print_r($soap->server_get_all());
										$template_dns = $soap->dns_templatezone_get_all();
										$servers = $soap->server_get_all();
									} catch (Exception $e) {
										echo '<div class="notice notice-error">' .$e->getMessage() . '</div>';
									}
								?>

								<table class="form-table">
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
									<tr>
										<th scope="row">
											<label for="new_domain"><?php _e( 'New Domain:', 'wpispconfig' ); ?></label>
										</th>
										<td>
											<input class="regular-text" type="text" id="new_domain" name="new_domain" value="">
										</td>
									</tr>
									<tr>
										<th scope="row">
											<label for="client_name"><?php _e( 'Client Name:', 'wpispconfig' ); ?></label>
										</th>
										<td>
											<input class="regular-text" type="text" id="client_name" name="client_name" value="">
										</td>
									</tr>

									<tr>
										<th scope="row">
											<label for="company_name"><?php _e( 'Company Name:', 'wpispconfig' ); ?></label>
										</th>
										<td>
											<input class="regular-text" type="text" id="company_name" name="company_name" value="">
										</td>
									</tr>
									<tr>
										<th scope="row">
											<label for="email"><?php _e( 'Email:', 'wpispconfig' ); ?></label>
										</th>
										<td>
											<input class="regular-text" type="text" id="email" name="email" value="">
										</td>
									</tr>
									<tr>
										<th scope="row">
											<label for="username"><?php _e( 'Client Username:', 'wpispconfig' ); ?></label>
										</th>
										<td>
											<input class="regular-text" type="text" id="username" name="username" value="">
										</td>
									</tr>
									<tr>
										<th scope="row">
											<label for="password"><?php _e( 'Client Password:', 'wpispconfig' ); ?></label>
										</th>
										<td>
											<input class="regular-text" type="text" id="password" name="password" value="<?php echo wp_generate_password(12, false, false); ?>">
										</td>
									</tr>
									<tr>
										<th scope="row">
											<label for="client_ip"><?php _e( 'Client IP:', 'wpispconfig' ); ?></label>
										</th>
										<td>
											<input class="regular-text" type="text" id="client_ip" name="client_ip" value="">
										</td>
									</tr>	
									<tr>
										<th scope="row">
											<label for="ns1"><?php _e( 'NameServer 1:', 'wpispconfig' ); ?></label>
										</th>
										<td>
											<input class="regular-text" type="text" id="ns1" name="ns1" value="">
										</td>
									</tr>
									<tr>
										<th scope="row">
											<label for="ns2"><?php _e( 'NameServer 2:', 'wpispconfig' ); ?></label>
										</th>
										<td>
											<input class="regular-text" type="text" id="ns2" name="ns2" value="">
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

		$server_id = (!empty($array_values['server']) ? $array_values['server'] : '1');
		$client_name = (!empty($array_values['client_name']) ? $array_values['client_name'] : '');
		$company_name = (!empty($array_values['company_name']) ? $array_values['company_name'] : '');
		$email =  (!empty($array_values['email']) ? $array_values['email'] : '');
		$username = (!empty($array_values['username']) ? $array_values['username'] : '');
		$password = (!empty($array_values['password']) ? $array_values['password'] : '');

		$template_id = (!empty($array_values['dns_template_id']) ? $array_values['dns_template_id'] : '');
		$new_domain = (!empty($array_values['new_domain']) ? $array_values['new_domain'] : '');
		$client_ip  = (!empty($array_values['client_ip']) ? $array_values['client_ip'] : '');
		$client_ip  = (!empty($array_values['client_ip']) ? $array_values['client_ip'] : '');
		$ns1 = (!empty($array_values['ns1']) ? $array_values['ns1'] : '');
		$ns2 = (!empty($array_values['ns2']) ? $array_values['ns2'] : '');
		$dns_email = str_replace('@', '.', $email);


		try {

			$soap = new SoapIspconfig($options);
			$new_client =  array(
								'company_name' 	=> $company_name,
								'contact_name' 	=> $client_name,
								'email' 		=> $email,
								'username' 		=> $username,
								'password' 		=> $password,

							);

			$client_id = $soap->add_client( $new_client);

			$new_dns_templatezone = array(
					            'client_id' 	=> $client_id,
					            'template_id' 	=> $template_id,
					            'domain' 		=> $new_domain,
					            'ip' 			=> $client_ip,
					            'ns1' 			=> $ns1,
					            'ns2' 			=> $ns2,
					            'dns_email' 	=> $dns_email,
					        );
	        $dns_zone = $soap->dns_templatezone_add( $new_dns_templatezone );

	        $new_website = array(
	        					'server_id'		 => $server_id,
					            'domain' 		 => $new_domain,
					            'stats_password' => $password,
					        );

	        $domain_id = $soap->add_website($client_id, $new_website );

	        $ftp_dir = '/var/www/clients/client'. $client_id .'/web' . $domain_id;
	        $new_ftp = array(
	        					'server_id'		=> $server_id,
					            'username' 		=> $username,
					            'password' 		=> $password,
					            'dir'           => $ftp_dir,
					        );

	        $ftp_user_id = $soap->sites_ftp_user_add($client_id, $domain_id, $new_ftp );

	        $new_database = array(
	        					'server_id'				=> $server_id,
					            'database_user' 		=> $username,
					            'database_password' 	=> $password,
					        );

	        $database_user_id = $soap->sites_database_user_add($client_id, $new_database );

	        $soap->mail_domain_add($client_id, array( 'domain' => $new_domain) );
	        
	        $new_email_address = $username . '@' . $new_domain;
	        $new_email_options = array(
	        					'server_id'		=> $server_id,
					            'email' 		=> $new_email_address,
					            'login' 		=> $new_email_address,
					            'password' 		=> $password,
					            'name' 			=> $client_name,
					            'maildir'		=>  '/var/vmail/'.$new_domain.'/'.$username,
					        );
	       $email_id = $soap->mail_user_add($client_id, $new_email_options);
	        
	        return array(
	        	'server_id'			=> $server_id,
	        	'client_id' 		=> $client_id, 
	        	'company_name' 		=> $company_name, 
	        	'contact_name' 		=> $contact_name, 
	        	'email' 			=> $email, 
	        	'username' 			=> $username, 
	        	'password' 			=> $password,
	        	'template_id'		=> $template_id,
	        	'domain_id'			=> $domain_id,
	        	'domain'			=> $new_domain,
	        	'ftp_user_id'		=> $ftp_user_id,
	        	'ftp_dir'			=> $ftp_dir,
	        	'database_user_id'	=> $database_user_id,
	        	'email_id'			=> $email_id,
	        	'email_address'		=> $new_email_address,

	        );
	        

		} catch (Exception $e) {
			throw new Exception($e->getMessage(), 1);	
		}
		return false;

	}
	public static function save() {
		if ( ! wp_verify_nonce($_POST['_wpnonce'], 'ispconfig_allinone_save' ) ) {
		    wp_die(__( 'Security check', 'wpispconfig' )); 
		}
		
		try {

			$created_values = self::create($_POST);

			$notice_success = '<strong>' . __( 'Client ID:', 'wpispconfig' ) .'</strong> ' . $created_values['client_id'] . '<br/>';
			$notice_success .= '<strong>' . __( 'Username:', 'wpispconfig' ) .'</strong> '. $created_values['username'] .'<br/>';
			$notice_success .= '<strong>' . __( 'Password:', 'wpispconfig' ) .'</strong> '. $created_values['password'] .'<br/>';
			$notice_success .= '<strong>' . __( 'Comapany name:', 'wpispconfig' ) .'</strong> '. $created_values['company_name'] .'<br/>';
			$notice_success .= '<strong>' . __( 'Contact name:', 'wpispconfig' ) .'</strong> '. $created_values['contact_name'] .'<br/><br/>';
			$notice_success .= '<strong>' . __( 'Email:', 'wpispconfig' ) .'</strong> '. $created_values['email']. '<br/><br/>';


			$notice_success .= '<strong>' . __( 'Server ID:', 'wpispconfig' ) .'</strong> '. $created_values['server_id'] .'<br/>';
			$notice_success .= '<strong>' . __( 'DNS Zones added from DNS template:', 'wpispconfig' ) .'</strong> '. $created_values['template_id'] .'<br/><br/>';

			$notice_success .= '<strong>' . __( 'Web Domain ID:', 'wpispconfig' ) .'</strong> '. $created_values['domain_id'] .'<br/>';
			$notice_success .= '<strong>' . __( 'Domain:', 'wpispconfig' ) .'</strong> '. $created_values['domain'] .'<br/><br/>';

			$notice_success .= '<strong>' . __( 'FTP User ID:', 'wpispconfig' ) .'</strong> '. $created_values['ftp_user_id'] .'<br/>';
			$notice_success .= '<strong>' . __( 'FTP domain:', 'wpispconfig' ) .'</strong> '. $created_values['domain'] .'<br/>';
			$notice_success .= '<strong>' . __( 'FTP User:', 'wpispconfig' ) .'</strong> '. $created_values['username'] .'<br/>';
			$notice_success .= '<strong>' . __( 'FTP Pass:', 'wpispconfig' ) .'</strong> '. $created_values['password'] .'<br/>';
			$notice_success .= '<strong>' . __( 'FTP dir:', 'wpispconfig' ) .'</strong> '. $created_values['ftp_dir'] .'<br/><br/>';

			$notice_success .= '<strong>' . __( 'Database User ID:', 'wpispconfig' ) .'</strong> '. $created_values['database_user_id'].'<br/>';
			$notice_success .= '<strong>' . __( 'Database User:', 'wpispconfig' ) .'</strong> '. $created_values['username'] .'<br/>';
			$notice_success .= '<strong>' . __( 'Database Pass:', 'wpispconfig' ) .'</strong> '. $created_values['password'] .'<br/>';
			$notice_success .= '<strong>' . __( 'You must create Databases', 'wpispconfig' ) .'</strong> <br/><br/>';

			$notice_success .= '<strong>' . __( 'New email ID:', 'wpispconfig' ) .'</strong> '. $created_values['email_id'] .'<br/>';
			$notice_success .= '<strong>' . __( 'New e-mail account:', 'wpispconfig' ) .'</strong> '. $created_values['email_address'] .'<br/>';
			$notice_success .= '<strong>' . __( 'Password:', 'wpispconfig' ) .'</strong> '. $created_values['password'] .'<br/>';

			WPISPConfig_notices::add( $notice_success );
			wp_redirect($_POST['_wp_http_referer']);
			die();

		} catch (Exception $e) {
			WPISPConfig_notices::add( array('text' => $e->getMessage(), 'error' => true) );
			wp_redirect($_POST['_wp_http_referer']);
			die();
		}

		WPISPConfig_notices::add(__( 'Created all in one', 'wpispconfig' ));
		wp_redirect($_POST['_wp_http_referer']);

	}

}
endif;

WPISPConfig_all_in_one::hooks();

?>