<?php
/**
 * @package         etruel\ISPConfig
 * @subpackage      SoapISPConfig
 * @author          Esteban Truelsegaard <esteban@netmdp.com>
 */
// Exit if accessed directly
if(!defined('ABSPATH')) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}

class SoapIspconfig {

	private $soap;
	private $session_id;

	public function __construct($options) {
		$soap_options = array('location' => $options['soap_location'], 'uri' => $options['soap_uri'], 'trace' => 1, 'exceptions' => 1);


		if($options['skip_ssl']) {
			// apply stream context to disable ssl checks
			$soap_options['stream_context'] = stream_context_create(array(
				'ssl' => array(
					'verify_peer'		 => false,
					'verify_peer_name'	 => false,
					'allow_self_signed'	 => true
				)
			));
		}

		if(!class_exists('SoapClient')) {
			throw new Exception('SoapClient class does not exist. Please add it to your PHP.ini', 1);
			return false;
		}

		$this->soap			 = new SoapClient(null, $soap_options);
		$this->session_id	 = $this->soap->login($options['soapusername'], $options['soappassword']);
		return $this;
	}

	public function get_client_by_user($username) {

		return $this->soap->client_get_by_username($this->session_id, $username);
	}

	public function get_function_list() {
		return $this->soap->get_function_list($this->session_id);
	}

	public function server_get_all() {
		return $this->soap->server_get_all($this->session_id);
	}

	public function dns_templatezone_get_all() {
		$ret = array();
		if(in_array('dns_templatezone_get_all', $this->get_function_list())) {
			$ret = $this->soap->dns_templatezone_get_all($this->session_id);
		}
		return $ret;
	}

	public function add_client($options = array(), $reseller_id = 0) {

		$default_options = wpispconfig_default_options_add_client();

		$new_options = wp_parse_args($options, $default_options);

		if(empty($new_options['username'])) {
			throw new Exception("Error missing or invalid username");
		}
		if(empty($new_options['password'])) {
			throw new Exception("Error missing or invalid username");
		}
		if(empty($new_options['password'])) {
			throw new Exception("Error missing email");
		}

		if(!is_email($new_options['email'])) {
			throw new Exception("Error invalid email");
		}

		return $this->soap->client_add($this->session_id, $reseller_id, $new_options);
	}

	public function dns_templatezone_add($options = array()) {

		$default_options = wpispconfig_default_options_dns_templatezone_add();

		$new_options = wp_parse_args($options, $default_options);
		extract($new_options);
		return $this->soap->dns_templatezone_add($this->session_id, $client_id, $template_id, $domain, $ip, $ns1, $ns2, $dns_email);
	}

	public function add_website($client_id = 0, $options = array()) {

		$default_options = wpispconfig_default_options_add_website();

		$new_options = wp_parse_args($options, $default_options);

		return $this->soap->sites_web_domain_add($this->session_id, $client_id, $new_options, $new_options['read_only']);
	}

	public function sites_ftp_user_add($client_id = 0, $domain_id = 0, $options = array()) {

		$default_options = wpispconfig_default_options_sites_ftp_user_add($client_id, $domain_id);

		$new_options = wp_parse_args($options, $default_options);
		return $this->soap->sites_ftp_user_add($this->session_id, $client_id, $new_options);
	}

	public function sites_database_user_add($client_id = 0, $options = array()) {

		$default_options = wpispconfig_default_options_sites_database_user_add();

		$new_options = wp_parse_args($options, $default_options);
		return $this->soap->sites_database_user_add($this->session_id, $client_id, $new_options);
	}

	public function mail_domain_add($client_id = 0, $options = array()) {

		$default_options = wpispconfig_default_options_mail_domain_add();

		$new_options = wp_parse_args($options, $default_options);
		return $this->soap->mail_domain_add($this->session_id, $client_id, $new_options);
	}

	public function mail_user_add($client_id = 0, $options = array()) {

		$default_options = wpispconfig_default_options_mail_user_add();
		$new_options	 = wp_parse_args($options, $default_options);
		return $this->soap->mail_user_add($this->session_id, $client_id, $new_options);
	}

	public function client_get_all() {
		return $this->soap->client_get_all($this->session_id);
	}

	public function client_get($client_id) {
		return $this->soap->client_get($this->session_id, $client_id);
	}

	public function client_get_groupid($primary_id) {
		return $this->soap->client_get_groupid($this->session_id, $primary_id);
	}

	public function sites_web_domain_get($primary_id) {
		return $this->soap->sites_web_domain_get($this->session_id, $primary_id);
	}

	public function sites_web_aliasdomain_add($client_id, $options = array()) {

		$default_options = wpispconfig_default_options_sites_web_aliasdomain_add();

		$new_options = wp_parse_args($options, $default_options);
		return $this->soap->sites_web_aliasdomain_add($this->session_id, $client_id, $new_options);
	}

	public function dns_zone_get_id($primary_id) {
		return $this->soap->dns_zone_get_id($this->session_id, $primary_id);
	}

	public function dns_zone_add($client_id, $options = array()) {

		$default_options = wpispconfig_default_options_dns_zone_add();

		$new_options = wp_parse_args($options, $default_options);
		return $this->soap->dns_zone_add($this->session_id, $client_id, $new_options);
	}

	public function dns_alias_add($client_id, $options = array()) {

		$default_options = wpispconfig_default_options_dns_alias_add();

		$new_options = wp_parse_args($options, $default_options);
		return $this->soap->dns_alias_add($this->session_id, $client_id, $new_options);
	}

}
