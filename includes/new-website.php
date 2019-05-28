<?php
/**
	* @package         etruel\ISPConfig
	* @subpackage 	   All in one
	* @author          Esteban Truelsegaard <esteban@netmdp.com>
	*/
// Exit if accessed directly
if(!defined('ABSPATH'))	{
				header('Status: 403 Forbidden');
				header('HTTP/1.1 403 Forbidden');
				exit();
}

if(!class_exists('WPISPConfig_New_Website'))	:

				class	WPISPConfig_New_Website	{

								private	static	$current_client_data	=	array();

								public	static	function	hooks()	{
												if(is_admin())	{	// admin actions
																add_action('admin_menu',	array(__CLASS__,	'menu'));
												}
												add_action('admin_post_ispconfig_allinone_save',	array(__CLASS__,	'save'));
												add_action('wpispconfig_all_in_one_before_table',	array(__CLASS__,	'form_inputs_before'),	10,	1);
												add_filter('wpispconfig_all_in_one_success_notices',	array(__CLASS__,	'notices_success'),	10,	2);
												add_filter('wpispconfig_values_all_in_one_before_create',	array(__CLASS__,	'before_create'),	10,	3);
												add_action('wp_ajax_wpispconfig_select_client',	array(__CLASS__,	'ajax_select_client'));
								}

								public	static	function	menu()	{

												$page	=	add_submenu_page(
																'ispconfig_dashboard',
																__('New Website',	'wpispconfig'),
																'<img src="'	.	WPISPCONFIG_PLUGIN_URL	.	'assets/images/pror.png'	.	'" style="margin: 0pt 2px -2px 0pt;"><span>'	.	'New Website',
																'manage_options',
																'wpispconfig_new_website',
																array(__CLASS__,	'page')
												);

												add_action('admin_print_styles-'	.	$page,	array(__CLASS__,	'add_styles'));
												add_action('admin_print_scripts-'	.	$page,	array(__CLASS__,	'add_scripts'));
								}

								public	static	function	add_styles()	{
												do_action('wpispconfig_all_in_one_add_styles');
								}

								public	static	function	add_scripts()	{
												wp_enqueue_script('wpcispconfig-new-website',	WPISPCONFIG_PLUGIN_URL	.	'assets/js/new-website.js',	array('jquery'),	WPISPCONFIG_VERSION,	true);
												wp_localize_script('wpcispconfig-new-website',	'wpcispconfig_new_website',
																array(
																				'ajax_url'												=>	admin_url('admin-ajax.php'),
																				'select_client_nonce'	=>	wp_create_nonce('new_website_nonce'),
																				'txt_loading'									=>	__('Loading...',	'wpispconfig'),
																)
												);
												do_action('wpispconfig_all_in_one_add_scripts');
								}

								public	static	function	ajax_select_client()	{
												$nonce	=	'';
												if(isset($_REQUEST['nonce']))	{
																$nonce	=	$_REQUEST['nonce'];
												}
												if(!wp_verify_nonce($nonce,	'new_website_nonce'))	{
																die('Security check');
												}
												$api					=	null;
												$options	=	WPISPConfig_Settings::get_option();
												try	{
																$soap	=	wpispconfig_get_current_api($options);
												}catch	(Exception	$e)	{
																echo	'<div class="notice notice-error">'	.	sprintf(__('Failed to connect with ISPConfig API. Please check your <a href="%s">Settings</a> and test the connection:',	'wpispconfig'),	admin_url('admin.php?page=ispconfig_settings'))	.	'<strong> '	.	$e->getMessage()	.	'</strong></div>';
												}
												$clients					=	array();
												$client_data	=	array();
												if(!empty($soap))	{
																try	{
																				$clients	=	$soap->client_get_all();
																				foreach($clients	as	$key	=>	$client_id)	{
																								$client_data[]	=	$soap->client_get($client_id);
																				}
																}catch	(Exception	$e)	{
																				echo	'<div class="notice notice-error">'	.	$e->getMessage()	.	'</div>';
																}
												}
												echo	'<select id="client_id" name="client_id">';
												echo	'<option value="-1">'	.	__('Select Client',	'wpispconfig')	.	'</option>';
												foreach($client_data	as	$key	=>	$values)	{
																echo	'<option value="'	.	$values['client_id']	.	'">'	.	$values['client_id']	.	' | '	.	$values['contact_name']	.	' | '	.	$values['company_name']	.	'</option>';
												}
												echo	'</select>';
												die();
								}

								public	static	function	page()	{
												if(!current_user_can('manage_options'))	{
																wp_die(__('You do not have sufficient permissions to access this page.'));
												}

												$options								=	WPISPConfig_Settings::get_option();
												$default_values	=	WPISPConfig_DefaultValues::get_option();
												$template_dns			=	array();
												$servers								=	array();
												?>

												<div class="wrap">
																<h2><?php	_e('New Website',	'wpispconfig');	?></h2>
																<form name="wpispconfig-allinone" method="post" autocomplete="off" action="<?php	echo	admin_url('admin-post.php');	?>">
																				<input type="hidden" name="action" value="ispconfig_allinone_save"/>
																				<?php
																				wp_nonce_field('ispconfig_allinone_save');
																				?>


																				<div id="poststuff">
																								<div id="post-body" class="metabox-holder columns-2">
																												<div id="postbox-container-1" class="postbox-container">
																																<div id="side-sortables" class="meta-box-sortables ui-sortable">
																																				<div id="wpem-about" class="postbox">
																																								<button type="button" class="handlediv button-link" aria-expanded="true">
																																												<span class="screen-reader-text"><?php	_e('Click to toggle');	?></span>
																																												<span class="toggle-indicator" aria-hidden="true"></span>
																																								</button>
																																								<h2 style="background-color: #980b13; color: white;" class="hndle"><span class="dashicons dashicons-forms"></span> <?php	_e('Create',	'wpispconfig');	?></h2>
																																								<div class="inside">
																																												<p class="description"><?php	_e('Select what you want to create on the website',	'wpispconfig');	?></p>
																																												<table class="form-table">
																																																<tr>
																																																				<th scope="row">
																																																								<label for="create_ftp"><?php	_e('FTP:',	'wpispconfig');	?></label>
																																																				</th>
																																																				<td>
																																																								<input type="checkbox" id="create_ftp" name="create[ftp]" value="1" checked="checked">
																																																				</td>
																																																</tr>
																																																<tr>
																																																				<th scope="row">
																																																								<label for="create_db"><?php	_e('DB User:',	'wpispconfig');	?></label>
																																																				</th>
																																																				<td>
																																																								<input type="checkbox" id="create_db" name="create[db]" value="1" checked="checked">
																																																				</td>
																																																</tr>
																																																<tr>
																																																				<th scope="row">
																																																								<label for="create_dns"><?php	_e('DNS:',	'wpispconfig');	?></label>
																																																				</th>
																																																				<td>
																																																								<input type="checkbox" id="create_dns" name="create[dns]" value="1" checked="checked">
																																																				</td>
																																																</tr>
																																																<tr>
																																																				<th scope="row">
																																																								<label for="create_email"><?php	_e('Email:',	'wpispconfig');	?></label>
																																																				</th>
																																																				<td>
																																																								<input type="checkbox" id="create_email" name="create[email]" value="1" checked="checked">
																																																				</td>
																																																</tr>
																																												</table>					
																																								</div>
																																				</div>

																																</div>		<!-- #side-sortables -->
																												</div>		<!--  postbox-container-1 -->	
																												<div id="postbox-container-2" class="postbox-container">

																																<div id="all_in_one" class="postbox">


																																				<div class="inside">
																																								<p></p>

																																								<?php
																																								$api												=	null;
																																								try	{
																																												$api										=	wpispconfig_get_current_api($options);
																																												//$soap = new SoapIspconfig($options);
																																												//print_r($soap->server_get_all());
																																												$template_dns	=	$api->dns_templatezone_get_all();
																																												$servers						=	$api->server_get_all();
																																								}catch	(Exception	$e)	{
																																												echo	'<div class="notice notice-error">'	.	sprintf(__('Failed to connect with ISPConfig API. Please check your <a href="%s">Settings</a> and test the connection:',	'wpispconfig'),	admin_url('admin.php?page=ispconfig_settings'))	.	'<strong> '	.	$e->getMessage()	.	'</strong></div>';
																																								}

																																								do_action('wpispconfig_all_in_one_before_table',	$api);
																																								?>

																																								<table class="form-table" id="client_table">
																																												<tr>
																																																<th scope="row">
																																																				<label for="client_name"><?php	_e('Client Name:',	'wpispconfig');	?></label>
																																																</th>
																																																<td>
																																																				<input class="regular-text" type="text" id="client_name" name="client_name" value="">
																																																</td>
																																												</tr>

																																												<tr>
																																																<th scope="row">
																																																				<label for="company_name"><?php	_e('Company Name:',	'wpispconfig');	?></label>
																																																</th>
																																																<td>
																																																				<input class="regular-text" type="text" id="company_name" name="company_name" value="">
																																																</td>
																																												</tr>
																																												<tr>
																																																<th scope="row">
																																																				<label for="email"><?php	_e('Email:',	'wpispconfig');	?></label>
																																																</th>
																																																<td>
																																																				<input class="regular-text" type="text" id="email" name="email" value="<?php	echo	$default_values['email']	?>">
																																																</td>
																																												</tr>
																																												<tr>
																																																<th scope="row">
																																																				<label for="username"><?php	_e('Client Username:',	'wpispconfig');	?></label>
																																																</th>
																																																<td>
																																																				<input class="regular-text" type="text" id="username" name="username" value="">
																																																</td>
																																												</tr>

																																								</table>
																																								<table class="form-table">
																																												<tr>
																																																<th scope="row">
																																																				<label for="password"><?php	_e('Client Password:',	'wpispconfig');	?></label>
																																																</th>
																																																<td>
																																																				<input class="regular-text" type="text" id="password" name="password" value="<?php	echo	wp_generate_password(12,	false,	false);	?>">
																																																</td>
																																												</tr>
																																												<tr>
																																																<th scope="row">
																																																				<label for="server"><?php	_e('Server:',	'wpispconfig');	?></label>
																																																</th>
																																																<td>
																																																				<select id="server" name="server">
																																																								<?php
																																																								foreach($servers	as	$key	=>	$values)	{
																																																												echo	'<option value="'	.	$values['server_id']	.	'">'	.	$values['server_name']	.	'</option>';
																																																								}
																																																								?>	
																																																				</select>
																																																</td>
																																												</tr>
																																												<tr>
																																																<th scope="row">
																																																				<label for="dns_template_id"><?php	_e('DNS Template ID:',	'wpispconfig');	?></label>
																																																</th>
																																																<td>
																																																				<?php
																																																				if(empty($template_dns))	{

																																																								echo	'<input class="regular-text" type="text" id="dns_template_id" name="dns_template_id" value="1">';
																																																				}else	{

																																																								echo	'<select id="dns_template_id" name="dns_template_id">';
																																																								foreach($template_dns	as	$key	=>	$values)	{
																																																												echo	'<option value="'	.	$values['template_id']	.	'">'	.	$values['name']	.	'</option>';
																																																								}
																																																								echo	'</select>';
																																																				}
																																																				?>	

																																																</td>
																																												</tr>
																																												<tr>
																																																<th scope="row">
																																																				<label for="new_domain"><?php	_e('New Domain:',	'wpispconfig');	?></label>
																																																</th>
																																																<td>
																																																				<input class="regular-text" type="text" id="new_domain" name="new_domain" value="">
																																																				<p id="new_domain_description" class="description"><?php	_e('Fill in the field with the new website domain or subdomain.(*)',	'wpispconfig')	?></p>
																																																</td>
																																												</tr>

																																												<tr>
																																																<th scope="row">
																																																				<label for="client_ip"><?php	_e('Client IP:',	'wpispconfig');	?></label>
																																																</th>
																																																<td>
																																																				<input class="regular-text" type="text" id="client_ip" name="client_ip" value="<?php	echo	$default_values['client_ip']	?>">
																																																</td>
																																												</tr>	
																																												<tr>
																																																<th scope="row">
																																																				<label for="ns1"><?php	_e('NameServer 1:',	'wpispconfig');	?></label>
																																																</th>
																																																<td>
																																																				<input class="regular-text" type="text" id="ns1" name="ns1" value="<?php	echo	$default_values['ns1']	?>">
																																																</td>
																																												</tr>
																																												<tr>
																																																<th scope="row">
																																																				<label for="ns2"><?php	_e('NameServer 2:',	'wpispconfig');	?></label>
																																																</th>
																																																<td>
																																																				<input class="regular-text" type="text" id="ns2" name="ns2" value="<?php	echo	$default_values['ns2']	?>">
																																																</td>
																																												</tr>
																																								</table>
																																								<table class="form-table">
																																												<tr>
																																																<th scope="row">
																																																				<label for="demomode"><?php	_e('DEMO MODE:',	'wpispconfig');	?></label>
																																																</th>
																																																<td>
																																																				<input type="checkbox" id="demomode" name="demomode" value="1">
																																																				<p class="description"><?php	_e('Select this option to see what would be created but without creating anything.',	'wpispconfig')	?></p>
																																																</td>
																																												</tr>
																																								</table>
																																				</div>
																																</div>

																												</div>		<!--  postbox-container-2 -->		

																												<div>
																																<p>
																																				<?php	submit_button(__('Create all',	'wpispconfig'),	'primary',	'wpispconfig-save-settings2',	false);	?>
																																</p>
																												</div>
																								</div> <!-- #post-body -->
																				</div> <!-- #poststuff -->
																</form>		
												</div><!-- .wrap -->
												<?php
								}

								public	static	function	form_inputs_before($soap)	{
												?>
												<table class="form-table">
																<tr>
																				<th scope="row">
																								<label for="exist_client"><?php	_e('Existing client:',	'wpispconfig');	?></label>
																				</th>
																				<td>
																								<input type="checkbox" id="exist_client" name="exist_client" value="1">
																								<p class="description"><?php	_e('Select this option to load and select an existing client.',	'wpispconfig')	?></p>
																				</td>
																</tr>
																<tr id="tr_client_select" style="display: none;">
																				<th scope="row">
																								<label for="exist_client"><?php	_e('Select Client:',	'wpispconfig');	?></label>
																				</th>
																				<td id="td_select_client">


																				</td>
																</tr>
																<tr id="tr_ftpdb_user" style="display: none;">
																				<th scope="row">
																								<label for="ftpdb_user"><?php	_e('FTP and DB Username:',	'wpispconfig');	?></label>
																				</th>
																				<td>
																								<input class="regular-text" type="text" id="ftpdb_user" name="ftpdb_user" value="">
																								<p class="description"><?php	_e('This value should be diferent for all other WebSites usernames. Left empty for autocomplete.',	'wpispconfig')	?></p>
																				</td>

																</tr>
												</table>
												<?php
								}

								public	static	function	notices_success($notices_success,	$created_values)	{
												if(!empty(self::$current_client_data))	{
																$notices_success[0]	=	'<strong>'	.	__('Using an existing client ID:',	'wpispconfig')	.	'</strong> '	.	$created_values['client_id']	.	'<br/>';
																$notices_success[1]	=	'<strong>'	.	__('Username:',	'wpispconfig')	.	'</strong> '	.	self::$current_client_data['username']	.	'<br/>';
																unset($notices_success[2]);
												}
												return	$notices_success;
								}

								public	static	function	before_create($values,	$input_values,	$soap)	{
												if(!empty($input_values['exist_client']))	{
																if(!empty($input_values['client_id']))	{
																				$client_id	=	intval($input_values['client_id']);
																				try	{
																								self::$current_client_data	=	$soap->client_get($client_id);
																								$values['client_id']							=	$client_id;
																								$values['client_name']					=	self::$current_client_data['contact_name'];
																								$values['company_name']				=	self::$current_client_data['company_name'];
																								$values['email']											=	self::$current_client_data['email'];
																								$values['emailuser']							=	self::$current_client_data['username'];
																								$values['ftpdb_user']						=	(!empty($input_values['ftpdb_user'])	?	$input_values['ftpdb_user']	:	$values['emailuser']);
																								$values['username']								=	self::$current_client_data['username']	.	wp_generate_password(3,	false,	false);
																								//$values['ftpdb_user']							=	$values['ftpdb_user'];
																								//$values['dns_email']							=	str_replace('@',	'.',	$values['email']);
																				}catch	(Exception	$e)	{
																								throw	new	Exception("Client does not exist with ID:"	.	$client_id,	1);
																				}
																}
												}
												return	$values;
								}

								public	static	function	create($array_values	=	array(),	$creating	=	array())	{
												$options		=	WPISPConfig_Settings::get_option();
												$demomode	=	(!empty($array_values['demomode'])	?	$array_values['demomode']	:	false	);

												$values	=	array();

												$values['server_id']				=	(!empty($array_values['server'])	?	$array_values['server']	:	'1');
												$values['client_name']		=	(!empty($array_values['client_name'])	?	$array_values['client_name']	:	'');
												$values['company_name']	=	(!empty($array_values['company_name'])	?	$array_values['company_name']	:	'');
												$values['email']								=	(!empty($array_values['email'])	?	$array_values['email']	:	'');
												$values['username']					=	(!empty($array_values['username'])	?	$array_values['username']	:	'');
												$values['password']					=	(!empty($array_values['password'])	?	$array_values['password']	:	'');

												$values['template_id']	=	(!empty($array_values['dns_template_id'])	?	$array_values['dns_template_id']	:	'');
												$values['new_domain']		=	(!empty($array_values['new_domain'])	?	$array_values['new_domain']	:	'');
												$values['client_ip']			=	(!empty($array_values['client_ip'])	?	$array_values['client_ip']	:	'');
												$values['ns1']									=	(!empty($array_values['ns1'])	?	$array_values['ns1']	:	'');
												$values['ns2']									=	(!empty($array_values['ns2'])	?	$array_values['ns2']	:	'');
												$values['dns_email']			=	str_replace('@',	'.',	$array_values['email']);

												$creating['ftp']			=	(!empty($creating['ftp'])	?	$creating['ftp']	:	false);
												$creating['db']				=	(!empty($creating['db'])	?	$creating['db']	:	false);
												$creating['dns']			=	(!empty($creating['dns'])	?	$creating['dns']	:	false);
												$creating['email']	=	(!empty($creating['email'])	?	$creating['email']	:	false);

												$creating	=	apply_filters('wpispconfig_new_website_creating_values',	$creating,	$values);

												try	{

																$api				=	wpispconfig_get_current_api($options);
																$values	=	apply_filters('wpispconfig_values_all_in_one_before_create',	$values,	$array_values,	$api,	$creating);

																/* create client */
																if(empty($values['client_id']))	{
																				$new_client	=	array(
																								'company_name'	=>	$values['company_name'],
																								'contact_name'	=>	$values['client_name'],
																								'email'								=>	$values['email'],
																								'username'					=>	$values['username'],
																								'password'					=>	$values['password'],
																				);
																				if(!$demomode)	{
																								$values['client_id']	=	$api->add_client($new_client);
																				}
																}

																$dns_zone	=	-1;
																if($creating['dns'])	{
																				$new_dns_templatezone	=	array(
																								'client_id'			=>	$values['client_id'],
																								'template_id'	=>	$values['template_id'],
																								'domain'						=>	$values['new_domain'],
																								'ip'										=>	$values['client_ip'],
																								'ns1'									=>	$values['ns1'],
																								'ns2'									=>	$values['ns2'],
																								'email'							=>	$values['dns_email'],
																				);
																				if(!$demomode)	{
																								$dns_zone	=	$api->dns_templatezone_add($new_dns_templatezone);
																				}
																}

																/**
																	* create Website domain and server space
																	*/
																$domain_id = -1;
																$new_website	=	array(
																				'server_id'						=>	$values['server_id'],
																				'domain'									=>	$values['new_domain'],
																				'stats_password'	=>	$values['password'],
																);
																if(!$demomode)	{
																				$domain_id	=	$api->add_website($values['client_id'],	$new_website);
																}

																$ftp_user_id	=	-1;
																$ftp_dir					=	'/var/www/clients/client'	.	$values['client_id']	.	'/web'	.	$domain_id;
																if($creating['ftp'])	{
																				$new_ftp	=	array(
																								'server_id'	=>	$values['server_id'],
																								'username'		=>	$values['ftpdb_user'],
																								'password'		=>	$values['password'],
																								'dir'							=>	$ftp_dir,
																				);

																				if(!$demomode)	{
																								$ftp_user_id	=	$api->sites_ftp_user_add($values['client_id'],	$domain_id,	$new_ftp);
																				}
																}

																$database_user_id	=	-1;
																if($creating['db'])	{
																				$new_database	=	array(
																								'server_id'									=>	$values['server_id'],
																								'database_user'					=>	$values['ftpdb_user'],
																								'database_password'	=>	$values['password'],
																				);

																				if(!$demomode)	{
																								$database_user_id	=	$api->sites_database_user_add($values['client_id'],	$new_database);
																				}
																}

																$email_id										=	-1;
																$new_email_address	=	$values['emailuser']	.	'@'	.	$values['new_domain'];
																if($creating['email'])	{

																				$new_email_options	=	array(
																								'server_id'	=>	$values['server_id'],
																								'email'					=>	$new_email_address,
																								'login'					=>	$new_email_address,
																								'password'		=>	$values['password'],
																								'name'						=>	$values['client_name'],
																								'maildir'			=>	'/var/vmail/'	.	$values['new_domain']	.	'/'	.	$values['emailuser'],
																				);
																				if(!$demomode)	{
																								$api->mail_domain_add($values['client_id'],	array('domain'	=>	$values['new_domain']));
																								$email_id	=	$api->mail_user_add($values['client_id'],	$new_email_options);
																				}
																}

																do_action('wpispconfig_new_website_finished',	$values,	$creating,	$api);
																return	array(
																				'server_id'								=>	$values['server_id'],
																				'client_id'								=>	$values['client_id'],
																				'company_name'					=>	$values['company_name'],
																				'contact_name'					=>	$values['client_name'],
																				'email'												=>	$values['email'],
																				'emailuser'								=>	$values['emailuser'],
																				'ftpdb_user'							=>	$values['ftpdb_user'],
																				'username'									=>	$values['username'],
																				'password'									=>	$values['password'],
																				'template_id'						=>	$values['template_id'],
																				'domain_id'								=>	$domain_id,
																				'domain'											=>	$values['new_domain'],
																				'ftp_user_id'						=>	$ftp_user_id,
																				'ftp_dir'										=>	$ftp_dir,
																				'database_user_id'	=>	$database_user_id,
																				'email_id'									=>	$email_id,
																				'email_address'				=>	$new_email_address,
																				'created'										=>	$creating,
																				'demomode'									=>	$demomode,
																);
												}catch	(Exception	$e)	{
																throw	new	Exception($e->getMessage(),	1);
												}
												return	false;
								}

								public	static	function	save()	{
												if(!wp_verify_nonce($_POST['_wpnonce'],	'ispconfig_allinone_save'))	{
																wp_die(__('Security check',	'wpispconfig'));
												}

												$notices_success	=	array();

												try	{

																$create	=	array();
																if(!empty($_POST['create']))	{
																				$create	=	$_POST['create'];
																}
																/* do the magic */
																$created_values	=	self::create($_POST,	$create);

																/* returned notice */
																$notices_success[]	=	($created_values['demomode'])? '<strong>'	.	__('DEMO MODE --> NOTHING CREATED',	'wpispconfig') .	'<br/>' : '';
																$notices_success[]	=	'<strong>'	.	__('Client ID:',	'wpispconfig')	.	'</strong> '	.	$created_values['client_id']	.	'<br/>';
																$notices_success[]	=	'<strong>'	.	__('Username:',	'wpispconfig')	.	'</strong> '	.	$created_values['username']	.	'<br/>';
																$notices_success[]	=	'<strong>'	.	__('Password:',	'wpispconfig')	.	'</strong> '	.	$created_values['password']	.	'<br/>';
																$notices_success[]	=	'<strong>'	.	__('Company name:',	'wpispconfig')	.	'</strong> '	.	$created_values['company_name']	.	'<br/>';
																$notices_success[]	=	'<strong>'	.	__('Contact name:',	'wpispconfig')	.	'</strong> '	.	$created_values['contact_name']	.	'<br/><br/>';
																$notices_success[]	=	'<strong>'	.	__('Email:',	'wpispconfig')	.	'</strong> '	.	$created_values['email']	.	'<br/><br/>';


																$notices_success[]	=	'<strong>'	.	__('Server ID:',	'wpispconfig')	.	'</strong> '	.	$created_values['server_id']	.	'<br/>';
																if($created_values['created']['dns'])	{
																				$notices_success[]	=	'<strong>'	.	__('DNS Zones added from DNS template:',	'wpispconfig')	.	'</strong> '	.	$created_values['template_id']	.	'<br/><br/>';
																}


																$notices_success[]	=	'<strong>'	.	__('Web Domain ID:',	'wpispconfig')	.	'</strong> '	.	$created_values['domain_id']	.	'<br/>';
																$notices_success[]	=	'<strong>'	.	__('Domain:',	'wpispconfig')	.	'</strong> '	.	$created_values['domain']	.	'<br/><br/>';

																if($created_values['created']['ftp'])	{
																				$notices_success[]	=	'<strong>'	.	__('FTP User ID:',	'wpispconfig')	.	'</strong> '	.	$created_values['ftp_user_id']	.	'<br/>';
																				$notices_success[]	=	'<strong>'	.	__('FTP domain:',	'wpispconfig')	.	'</strong> '	.	$created_values['domain']	.	'<br/>';
																				$notices_success[]	=	'<strong>'	.	__('FTP User:',	'wpispconfig')	.	'</strong> '	.	$created_values['ftpdb_user']	.	'<br/>';
																				$notices_success[]	=	'<strong>'	.	__('FTP Pass:',	'wpispconfig')	.	'</strong> '	.	$created_values['password']	.	'<br/>';
																				$notices_success[]	=	'<strong>'	.	__('FTP dir:',	'wpispconfig')	.	'</strong> '	.	$created_values['ftp_dir']	.	'<br/><br/>';
																}
																if($created_values['created']['db'])	{
																				$notices_success[]	=	'<strong>'	.	__('Database User ID:',	'wpispconfig')	.	'</strong> '	.	$created_values['database_user_id']	.	'<br/>';
																				$notices_success[]	=	'<strong>'	.	__('Database User:',	'wpispconfig')	.	'</strong> '	.	$created_values['ftpdb_user']	.	'<br/>';
																				$notices_success[]	=	'<strong>'	.	__('Database Pass:',	'wpispconfig')	.	'</strong> '	.	$created_values['password']	.	'<br/>';
																				$notices_success[]	=	'<strong>'	.	__('You must create Databases',	'wpispconfig')	.	'</strong> <br/><br/>';
																}
																if($created_values['created']['email'])	{
																				$notices_success[]	=	'<strong>'	.	__('New email ID:',	'wpispconfig')	.	'</strong> '	.	$created_values['email_id']	.	'<br/>';
																				$notices_success[]	=	'<strong>'	.	__('New e-mail account:',	'wpispconfig')	.	'</strong> '	.	$created_values['email_address']	.	'<br/>';
																				$notices_success[]	=	'<strong>'	.	__('Password:',	'wpispconfig')	.	'</strong> '	.	$created_values['password']	.	'<br/>';
																}
																$notices_success	=	apply_filters('wpispconfig_all_in_one_success_notices',	$notices_success,	$created_values);
												}catch	(Exception	$e)	{

																WPISPConfig_notices::add(array('text'	=>	$e->getMessage(),	'error'	=>	true));
																wp_redirect($_POST['_wp_http_referer']);
																die();
												}

												
												// Trying refresh list of dashboard without intervention in the  process of  new website
												try	{
																if(!$created_values['demomode'])	{
																				WPISPConfig_Dashboard::refresh_website_list();
																}
												}catch	(Exception	$e)	{
																WPISPConfig_notices::add(array('text'	=>	$e->getMessage(),	'error'	=>	true));
												}

												if(empty($notices_success))	{
																WPISPConfig_notices::add(__('Created all in one',	'wpispconfig'));
												}else	{
																$sucess_notice	=	implode('',	$notices_success);
																WPISPConfig_notices::add($sucess_notice);
												}

												wp_redirect($_POST['_wp_http_referer']);
								}

				}

				endif;

WPISPConfig_New_Website::hooks();
?>