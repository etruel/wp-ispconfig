<?php
/**
 * @package         etruel\ISPConfig
 * @subpackage      Functions
 * @author          Esteban Truelsegaard <esteban@netmdp.com>
 */
function wpispconfig_get_settings_tabs() {
	$current_tab = (isset($_GET['tab']) ) ? $_GET['tab'] : 'general';
	$tabs		 = array(
		'general'		 => array(
			'text'	 => 'General',
			'url'	 => admin_url('admin.php?page=ispconfig_settings'),
		),
		'default-values' => array(
			'text'	 => 'Default Values',
			'url'	 => admin_url('admin.php?page=ispconfig_defaultvalues'),
		),
	);

	echo '<h2 class="nav-tab-wrapper">';
	foreach($tabs as $tab_id => $tab_value) {
		$tab_name = $tab_value['text'];

		$tab_url = add_query_arg(array('tab' => $tab_id), $tab_value['url']);

		$active = $current_tab == $tab_id ? ' nav-tab-active' : '';
		echo '<a href="' . esc_url($tab_url) . '" title="' . esc_attr(sanitize_text_field($tab_name)) . '" class="nav-tab' . $active . '">' . ( $tab_name ) . '</a>';
	}
	echo '</h2>';
}

function wpispconfig_get_current_api($options) {
	if($options['remote_type'] == 'soap') {
		return new SoapIspconfig($options);
	}else {
		return new RestApiISPConfig($options);
	}
}

function wpispconfig_default_options_add_client() {
	$default_options = array(
		'company_name'				 => '',
		'contact_name'				 => '',
		'customer_no'				 => '',
		'vat_id'					 => '1',
		'street'					 => '',
		'zip'						 => '',
		'city'						 => '',
		'state'						 => '',
		'country'					 => 'EN',
		'telephone'					 => '',
		'mobile'					 => '',
		'fax'						 => '',
		'email'						 => '',
		'internet'					 => '',
		'icq'						 => '',
		'notes'						 => '',
		'default_mailserver'		 => 1,
		'limit_maildomain'			 => -1,
		'limit_mailbox'				 => -1,
		'limit_mailalias'			 => -1,
		'limit_mailaliasdomain'		 => -1,
		'limit_mailforward'			 => -1,
		'limit_mailcatchall'		 => -1,
		'limit_mailrouting'			 => 0,
		'limit_mailfilter'			 => -1,
		'limit_fetchmail'			 => -1,
		'limit_mailquota'			 => -1,
		'limit_spamfilter_wblist'	 => 0,
		'limit_spamfilter_user'		 => 0,
		'limit_spamfilter_policy'	 => 1,
		'default_webserver'			 => 1,
		'limit_web_ip'				 => '',
		'limit_web_domain'			 => -1,
		'limit_web_quota'			 => -1,
		'web_php_options'			 => 'no,fast-cgi,cgi,mod,suphp',
		'limit_web_subdomain'		 => -1,
		'limit_web_aliasdomain'		 => -1,
		'limit_ftp_user'			 => -1,
		'limit_shell_user'			 => 0,
		'ssh_chroot'				 => 'no,jailkit,ssh-chroot',
		'limit_webdav_user'			 => 0,
		'default_dnsserver'			 => 1,
		'limit_dns_zone'			 => -1,
		'limit_dns_slave_zone'		 => -1,
		'limit_dns_record'			 => -1,
		'default_dbserver'			 => 1,
		'limit_database'			 => -1,
		'limit_cron'				 => 0,
		'limit_cron_type'			 => 'url',
		'limit_cron_frequency'		 => 5,
		'limit_traffic_quota'		 => -1,
		'username'					 => '',
		'password'					 => '',
		'language'					 => 'en',
		'usertheme'					 => 'default',
		'template_master'			 => 0,
		'template_additional'		 => '',
		'created_at'				 => 0
	);
	$default_options = apply_filters('wpispconfig_default_options_add_client', $default_options);
	return $default_options;
}

function wpispconfig_default_options_dns_templatezone_add() {
	$default_options = array(
		'template_id'	 => '1',
		'domain'		 => '',
		'ip'			 => '',
		'ns1'			 => '',
		'ns2'			 => '',
		'email'			 => '',
		'dnssec'		 => 'n',
	);
	$default_options = apply_filters('wpispconfig_default_options_dns_templatezone_add', $default_options);
	return $default_options;
}

function wpispconfig_default_options_add_website() {
	$default_options = array(
		'server_id'					 => '1',
		'domain'					 => '',
		'ip_address'				 => '*',
		'http_port'					 => '80',
		'https_port'				 => '443',
		'type'						 => 'vhost',
		'parent_domain_id'			 => 0,
		'vhost_type'				 => '',
		'hd_quota'					 => -1,
		'traffic_quota'				 => -1,
		'cgi'						 => 'n',
		'ssi'						 => 'n',
		'suexec'					 => 'n',
		'errordocs'					 => 1,
		'is_subdomainwww'			 => 1,
		'subdomain'					 => 'www',
		'php'						 => 'php-fpm',
		'php_fpm_use_socket'		 => 'y',
		'ruby'						 => 'n',
		'redirect_type'				 => '',
		'redirect_path'				 => '',
		'ssl'						 => 'n',
		'ssl_state'					 => '',
		'ssl_locality'				 => '',
		'ssl_organisation'			 => '',
		'ssl_organisation_unit'		 => '',
		'ssl_country'				 => '',
		'ssl_domain'				 => '',
		'ssl_request'				 => '',
		'ssl_cert'					 => '',
		'ssl_bundle'				 => '',
		'ssl_action'				 => '',
		'stats_password'			 => '',
		'stats_type'				 => 'webalizer',
		'allow_override'			 => 'All',
		'apache_directives'			 => '',
		'php_open_basedir'			 => '/',
		'custom_php_ini'			 => '',
		'backup_interval'			 => '',
		'backup_copies'				 => 1,
		'active'					 => 'y',
		'traffic_quota_lock'		 => 'n',
		'pm'						 => 'dynamic',
		'pm_process_idle_timeout'	 => 10,
		'pm_max_requests'			 => 0,
		'read_only'					 => false
	);
	$default_options = apply_filters('wpispconfig_default_options_add_website', $default_options);
	return $default_options;
}

function wpispconfig_default_options_sites_ftp_user_add($client_id, $domain_id) {
	$default_options = array(
		'server_id'			 => '1',
		'parent_domain_id'	 => $domain_id,
		'username'			 => '',
		'password'			 => '',
		'quota_size'		 => -1,
		'active'			 => 'y',
		'uid'				 => 'web' . $domain_id,
		'gid'				 => 'client' . $client_id,
		'dir'				 => '/var/www/clients/client' . $client_id . '/web' . $domain_id,
		'quota_files'		 => -1,
		'ul_ratio'			 => -1,
		'dl_ratio'			 => -1,
		'ul_bandwidth'		 => -1,
		'dl_bandwidth'		 => -1
	);
	$default_options = apply_filters('wpispconfig_default_options_sites_ftp_user_add', $default_options, $client_id, $domain_id);
	return $default_options;
}

function wpispconfig_default_options_sites_database_user_add() {
	$default_options = array(
		'server_id'			 => '1',
		'database_user'		 => '',
		'database_password'	 => '',
	);
	$default_options = apply_filters('wpispconfig_default_options_sites_database_user_add', $default_options);
	return $default_options;
}

function wpispconfig_default_options_mail_domain_add() {
	$default_options = array(
		'server_id'	 => '1',
		'domain'	 => '',
		'active'	 => 'y'
	);
	$default_options = apply_filters('wpispconfig_default_options_mail_domain_add', $default_options);
	return $default_options;
}

function wpispconfig_default_options_mail_user_add() {
	$default_options = array(
		'server_id'					 => '1',
		'email'						 => '',
		'login'						 => '',
		'password'					 => '',
		'name'						 => '',
		'uid'						 => 5000,
		'gid'						 => 5000,
		'maildir'					 => '/var/vmail/' . time(true) . '-' . rand(1, 1234) . '/' . time(true) . '-' . rand(1, 1234),
		'quota'						 => 524288000,
		'cc'						 => '',
		'homedir'					 => '/var/vmail',
		'autoresponder'				 => 'n',
		'autoresponder_start_date'	 => '',
		'autoresponder_end_date'	 => '',
		'autoresponder_text'		 => '',
		'move_junk'					 => 'n',
		'custom_mailfilter'			 => '',
		'postfix'					 => 'y',
		'access'					 => 'n',
		'disableimap'				 => 'n',
		'disablepop3'				 => 'n',
		'disabledeliver'			 => 'n',
		'disablesmtp'				 => 'n',
	);
	$default_options = apply_filters('wpispconfig_default_options_mail_user_add', $default_options);
	return $default_options;
}

function wpispconfig_default_options_sites_web_aliasdomain_add() {

	$default_options = array(
		'server_id'				 => '1',
		'ip_address'			 => '*',
		'domain'				 => '',
		'type'					 => 'alias',
		'parent_domain_id'		 => '',
		'vhost_type'			 => '',
		'document_root'			 => NULL,
		'system_user'			 => NULL,
		'system_group'			 => NULL,
		'hd_quota'				 => 0,
		'traffic_quota'			 => -1,
		'cgi'					 => 'n',
		'ssi'					 => 'n',
		'suexec'				 => 'n',
		'errordocs'				 => 1,
		'is_subdomainwww'		 => 1,
		'subdomain'				 => '',
		'php'					 => 'mod',
		'ruby'					 => 'n',
		'redirect_type'			 => '',
		'redirect_path'			 => '',
		'ssl'					 => 'n',
		'ssl_state'				 => '',
		'ssl_locality'			 => '',
		'ssl_organisation'		 => '',
		'ssl_organisation_unit'	 => '',
		'ssl_country'			 => '',
		'ssl_domain'			 => '',
		'ssl_request'			 => '',
		'ssl_cert'				 => '',
		'ssl_bundle'			 => '',
		'ssl_action'			 => '',
		'stats_password'		 => '',
		'stats_type'			 => 'webalizer',
		'allow_override'		 => 'All',
		'apache_directives'		 => '',
		'php_open_basedir'		 => '/',
		'custom_php_ini'		 => '',
		'backup_interval'		 => '',
		'backup_copies'			 => 1,
		'active'				 => 'y',
		'traffic_quota_lock'	 => 'n'
	);
	$default_options = apply_filters('wpispconfig_default_options_sites_web_aliasdomain_add', $default_options);
	return $default_options;
}

function wpispconfig_default_options_dns_zone_add() {
	$default_options = array(
		'server_id'		 => '1',
		'origin'		 => 'test.intt.',
		'ns'			 => 'one',
		'mbox'			 => 'zonemaster.test.tld.',
		'serial'		 => time(true) . rand(1, 9999),
		'refresh'		 => '7200',
		'retry'			 => '540',
		'expire'		 => '604800',
		'minimum'		 => '3600',
		'ttl'			 => '3600',
		'active'		 => 'y',
		'xfer'			 => '',
		'also_notify'	 => '',
		'update_acl'	 => '',
	);
	$default_options = apply_filters('wpispconfig_default_options_dns_zone_add', $default_options);
	return $default_options;
}

function wpispconfig_default_options_dns_alias_add() {
	$default_options = array(
		'server_id'	 => '1',
		'zone'		 => '1',
		'name'		 => 'alias',
		'type'		 => 'alias',
		'data'		 => 'hostmachine',
		'aux'		 => '0',
		'ttl'		 => '3600',
		'active'	 => 'y',
		'stamp'		 => 'CURRENT_TIMESTAMP',
		'serial'	 => '1',
	);
	$default_options = apply_filters('wpispconfig_default_options_dns_alias_add', $default_options);
	return $default_options;
}
