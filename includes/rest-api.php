<?php
/**
 * @package         etruel\ISPConfig
 * @subpackage      RestApi
 * @author          Esteban Truelsegaard <esteban@netmdp.com>
 */
// Exit if accessed directly
if(!defined('ABSPATH')) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}

class RestApiISPConfig {

	private $rest_api_url;
	private $session_id;
	private $sslverify = true;

	public function __construct($options) {

		if($options['skip_ssl']) {
			// apply stream context to disable ssl checks
			$this->sslverify = false;
		}
		$this->rest_api_url	 = $options['restapi_location'];
		$this->session_id	 = $this->request('login', array('username' => $options['soapusername'], 'password' => $options['soappassword']));
		return $this;
	}

	public function request($method, $params) {

		$new_request = $this->rest_api_url . '?' . $method;
		$response	 = wp_remote_post($new_request, array(
			'method'		 => 'POST',
			'timeout'		 => 45,
			'redirection'	 => 5,
			'httpversion'	 => '1.1',
			'blocking'		 => true,
			'headers'		 => array(),
			'body'			 => json_encode($params),
			'cookies'		 => array(),
			'sslverify'		 => $this->sslverify,
			)
		);
		if(is_wp_error($response)) {
			throw new Exception($response->get_error_message(), 1);
		}

		$res = json_decode($response['body'], true);

		if(isset($res['code'])) {
			if($res['code'] != 'ok') {
				$error_message = (!empty($res['message']) ? $res['message'] : 'An error has been ocurred!');
				throw new Exception($method . ': ' . $error_message, 1);
			}
		}
		return $res['response'];
	}

	public function get_client_by_user($username) {
		$params_api = array(
			'session_id' => $this->session_id,
			'username'	 => $username,
		);

		return $this->request('client_get_by_username', $params_api);
	}

	public function get_function_list() {
		$params_api = array(
			'session_id' => $this->session_id,
		);

		return $this->request('get_function_list', $params_api);
	}

	public function server_get_all() {
		$params_api = array(
			'session_id' => $this->session_id,
		);

		return $this->request('server_get_all', $params_api);
	}

	public function dns_templatezone_get_all() {
		$ret = array();
		if(in_array('dns_templatezone_get_all', $this->get_function_list())) {

			$params_api = array(
				'session_id' => $this->session_id,
			);

			return $this->request('dns_templatezone_get_all', $params_api);
		}
		return $ret;
	}

	public function add_client($options = array(), $reseller_id = 0) {

		$default_options = wpispconfig_default_options_add_client();

		$new_options = wp_parse_args($options, $default_options);

		if(empty($new_options['username'])) {
			throw new Exception("add_client: Error missing or invalid username");
		}
		if(empty($new_options['password'])) {
			throw new Exception("add_client: Error missing or invalid username");
		}
		if(empty($new_options['password'])) {
			throw new Exception("add_client: Error missing email");
		}

		if(!is_email($new_options['email'])) {
			throw new Exception("add_client: Error invalid email");
		}
		$params_api = array(
			'session_id'	 => $this->session_id,
			'reseller_id'	 => $reseller_id,
			'params'		 => $new_options,
		);
		return $this->request('client_add', $params_api);
	}

	public function dns_templatezone_add($options = array()) {

		$default_options = wpispconfig_default_options_dns_templatezone_add();
		$new_options	 = wp_parse_args($options, $default_options);

		$params_api	 = array(
			'session_id' => $this->session_id,
		);
		$params_api	 = wp_parse_args($params_api, $new_options);
		return $this->request('dns_templatezone_add', $params_api);
	}

	public function add_website($client_id = 0, $options = array()) {

		$default_options = wpispconfig_default_options_add_website();

		$new_options = wp_parse_args($options, $default_options);
		$params_api	 = array(
			'session_id' => $this->session_id,
			'client_id'	 => $client_id,
			'params'	 => $new_options,
			'readonly'	 => $new_options['read_only'],
		);
		return $this->request('sites_web_domain_add', $params_api);
	}

	public function sites_ftp_user_add($client_id = 0, $domain_id = 0, $options = array()) {

		$default_options = wpispconfig_default_options_sites_ftp_user_add($client_id, $domain_id);

		$new_options = wp_parse_args($options, $default_options);
		$params_api	 = array(
			'session_id' => $this->session_id,
			'client_id'	 => $client_id,
			'params'	 => $new_options,
		);
		return $this->request('sites_ftp_user_add', $params_api);
	}

	public function sites_database_user_add($client_id = 0, $options = array()) {

		$default_options = wpispconfig_default_options_sites_database_user_add();

		$new_options = wp_parse_args($options, $default_options);
		$params_api	 = array(
			'session_id' => $this->session_id,
			'client_id'	 => $client_id,
			'params'	 => $new_options,
		);
		return $this->request('sites_database_user_add', $params_api);
	}

	public function mail_domain_add($client_id = 0, $options = array()) {

		$default_options = wpispconfig_default_options_mail_domain_add();

		$new_options = wp_parse_args($options, $default_options);
		$params_api	 = array(
			'session_id' => $this->session_id,
			'client_id'	 => $client_id,
			'params'	 => $new_options,
		);
		return $this->request('mail_domain_add', $params_api);
	}

	public function mail_user_add($client_id = 0, $options = array()) {

		$default_options = wpispconfig_default_options_mail_user_add();

		$new_options = wp_parse_args($options, $default_options);
		$params_api	 = array(
			'session_id' => $this->session_id,
			'client_id'	 => $client_id,
			'params'	 => $new_options,
		);
		return $this->request('mail_user_add', $params_api);
	}

	public function client_get_all() {
		$params_api = array(
			'session_id' => $this->session_id,
		);

		return $this->request('client_get_all', $params_api);
	}

	public function client_get($client_id) {

		$params_api = array(
			'session_id' => $this->session_id,
			'client_id'	 => $client_id,
		);

		return $this->request('client_get', $params_api);
	}

	public function client_get_groupid($primary_id) {
		$params_api = array(
			'session_id' => $this->session_id,
			'client_id'	 => $primary_id,
		);
		return $this->request('client_get_groupid', $params_api);
	}

	public function sites_web_domain_get($primary_id) {
		$params_api = array(
			'session_id' => $this->session_id,
			'primary_id' => $primary_id,
		);
		return $this->request('sites_web_domain_get', $params_api);
	}

	public function sites_web_aliasdomain_add($client_id, $options = array()) {

		$default_options = wpispconfig_default_options_sites_web_aliasdomain_add();

		$new_options = wp_parse_args($options, $default_options);
		$params_api	 = array(
			'session_id' => $this->session_id,
			'client_id'	 => $client_id,
			'params'	 => $new_options,
		);
		return $this->request('sites_web_aliasdomain_add', $params_api);
	}

	public function dns_zone_get_id($primary_id) {
		$params_api = array(
			'session_id' => $this->session_id,
			'origin'	 => $primary_id,
		);
		return $this->request('dns_zone_get_id', $params_api);
	}

	public function dns_zone_add($client_id, $options = array()) {

		$default_options = wpispconfig_default_options_dns_zone_add();

		$new_options = wp_parse_args($options, $default_options);
		$params_api	 = array(
			'session_id' => $this->session_id,
			'client_id'	 => $client_id,
			'params'	 => $new_options,
		);
		return $this->request('dns_zone_add', $params_api);
	}

	public function dns_alias_add($client_id, $options = array()) {

		$default_options = wpispconfig_default_options_dns_alias_add();

		$new_options = wp_parse_args($options, $default_options);
		$params_api	 = array(
			'session_id' => $this->session_id,
			'client_id'	 => $client_id,
			'params'	 => $new_options,
		);
		return $this->request('dns_alias_add', $params_api);
	}

}
