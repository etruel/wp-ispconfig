<?php 
/**
* @package         etruel\ISPConfig
* @subpackage      RestApi
* @author          Esteban Truelsegaard <esteban@netmdp.com>
*/
// Exit if accessed directly
if ( !defined('ABSPATH') ) {
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );
    exit();
}
class RestApiISPConfig {

    private $rest_api_url;
    private $session_id;
    private $sslverify = true;
    public function __construct($options){
       
        if($options['skip_ssl']) {
            // apply stream context to disable ssl checks
            $this->sslverify = false;
        }
        $this->rest_api_url = $options['restapi_location'];
        $this->session_id = $this->request('login', array('username' => $options['soapusername'], 'password' => $options['soappassword']) );
        return $this;
    }

    public function request($method, $params) {

        $new_request = $this->rest_api_url . '?' .$method;
        $response = wp_remote_post($new_request, array(
                'method' => 'POST',
                'timeout' => 45,
                'redirection' => 5,
                'httpversion' => '1.0',
                'blocking' => true,
                'headers' => array(),
                'body' => json_encode($params),
                'cookies' => array(),
                'sslverify' => $this->sslverify,
            )
        );
        if ( is_wp_error( $response ) ) {
            throw new Exception($response->get_error_message(), 1);
        } 
        $res = json_decode($response['body'], true);

        if (isset($res['code'])) {
            if ($res['code'] != 'ok') {
                $error_message = (!empty($res['message']) ? $res['message'] : 'An error has been ocurred!');
                throw new Exception($error_message, 1);
            }
        }
        return $res['response'];
    }
    
   
    public function get_client_by_user($username){
        $params_api = array(
            'session_id'    => $this->session_id,
            'username'      => $username,
        );

        return $this->request('client_get_by_username', $params_api);
        
    }

    public function get_function_list() {
        $params_api = array(
            'session_id'    => $this->session_id,
        );

        return $this->request('get_function_list', $params_api);
    }

    public function server_get_all() {
        $params_api = array(
            'session_id'    => $this->session_id,
        );

        return $this->request('server_get_all', $params_api);
    }

    public function dns_templatezone_get_all() {
        $ret = array();
        if (in_array('dns_templatezone_get_all', $this->get_function_list())) {
            
            $params_api = array(
                'session_id'    => $this->session_id,
            );

            return $this->request('dns_templatezone_get_all', $params_api);
        }
        return $ret;
    }

    public function add_client($options = array(), $reseller_id = 0){
        $default_options = array(
            'company_name' => '',
            'contact_name' => '',
            'customer_no' => '',
            'vat_id' => '',
            'street' => '',
            'zip' => '',
            'city' => '',
            'state' => '',
            'country' => 'EN',
            'telephone' => '',
            'mobile' => '',
            'fax' => '',
            'email' => '',
            'internet' => '',
            'icq' => '',
            'notes' => '',
            'default_mailserver' => 1,
            'limit_maildomain' => -1,
            'limit_mailbox' => -1,
            'limit_mailalias' => -1,
            'limit_mailaliasdomain' => -1,
            'limit_mailforward' => -1,
            'limit_mailcatchall' => -1,
            'limit_mailrouting' => 0,
            'limit_mailfilter' => -1,
            'limit_fetchmail' => -1,
            'limit_mailquota' => -1,
            'limit_spamfilter_wblist' => 0,
            'limit_spamfilter_user' => 0,
            'limit_spamfilter_policy' => 1,
            'default_webserver' => 1,
            'limit_web_ip' => '',
            'limit_web_domain' => -1,
            'limit_web_quota' => -1,
            'web_php_options' => 'no,fast-cgi,cgi,mod,suphp',
            'limit_web_subdomain' => -1,
            'limit_web_aliasdomain' => -1,
            'limit_ftp_user' => -1,
            'limit_shell_user' => 0,
            'ssh_chroot' => 'no,jailkit,ssh-chroot',
            'limit_webdav_user' => 0,
            'default_dnsserver' => 1,
            'limit_dns_zone' => -1,
            'limit_dns_slave_zone' => -1,
            'limit_dns_record' => -1,
            'default_dbserver' => 1,
            'limit_database' => -1,
            'limit_cron' => 0,
            'limit_cron_type' => 'url',
            'limit_cron_frequency' => 5,
            'limit_traffic_quota' => -1,
            'username' => '',
            'password' => '',
            'language' => 'en',
            'usertheme' => 'default',
            'template_master' => 0,
            'template_additional' => '',
            'created_at' => 0
        );
      
        $new_options = wp_parse_args($options, $default_options);

        if (empty($new_options['username'])) {
            throw new Exception("Error missing or invalid username");
        }
        if (empty($new_options['password'])) {
            throw new Exception("Error missing or invalid username");
        }
        if (empty($new_options['password'])) {
            throw new Exception("Error missing email");
        }

        if (!is_email($new_options['email'])) {
            throw new Exception("Error invalid email");
        }
        $params_api = array(
            'session_id'    => $this->session_id,
            'reseller_id'   => $reseller_id,
            'params'        => $new_options,
        );
        return $this->request('client_add', $params_api);

    }

    public function dns_templatezone_add($options = array() ) {
         $default_options = array(
            'client_id' => '',
            'template_id' => '1',
            'domain' => '',
            'ip' => '',
            'ns1' => '',
            'ns2' => '',
            'email' => '',
        );
        $new_options = wp_parse_args($options, $default_options);

        $params_api = array(
            'session_id'    => $this->session_id,
        );
        $params_api = wp_parse_args($params_api, $new_options);
        return $this->request('dns_templatezone_add', $params_api);
    }

    public function add_website( $client_id = 0, $options = array() ){
        $default_options = array(
            'server_id' => '1',
            'domain' => '',
            'ip_address' => '*',
            'http_port' => '80',
            'https_port' => '443',
            'type' => 'vhost',
            'parent_domain_id' => 0,
            'vhost_type' => '',
            'hd_quota' => -1,
            'traffic_quota' => -1,
            'cgi' => 'n',
            'ssi' => 'n',
            'suexec' => 'n',
            'errordocs' => 1,
            'is_subdomainwww' => 1,
            'subdomain' => 'www',
            'php' => 'php-fpm', 
            'php_fpm_use_socket' => 'y',
            'ruby' => 'n', 
            'redirect_type' => '',
            'redirect_path' => '',
            'ssl' => 'n',
            'ssl_state' => '',
            'ssl_locality' => '',
            'ssl_organisation' => '',
            'ssl_organisation_unit' => '',
            'ssl_country' => '',
            'ssl_domain' => '',
            'ssl_request' => '',
            'ssl_cert' => '',
            'ssl_bundle' => '',
            'ssl_action' => '',
            'stats_password' => '',
            'stats_type' => 'webalizer',
            'allow_override' => 'All',
            'apache_directives' => '',
            'php_open_basedir' => '/', 
            'custom_php_ini' => '', 
            'backup_interval' => '',
            'backup_copies' => 1,
            'active' => 'y',
            'traffic_quota_lock' => 'n',
            'pm' => 'dynamic',
            'pm_process_idle_timeout'=>10,
            'pm_max_requests' => 0,
            'read_only' => false
        );
        
        $new_options = wp_parse_args($options, $default_options);
        $params_api = array(
            'session_id'    => $this->session_id,
            'client_id'     => $client_id,
            'params'        => $new_options,
            'readonly'      => $new_options['read_only'],
        );
        return $this->request('sites_web_domain_add', $params_api);

    }

    public function sites_ftp_user_add( $client_id = 0, $domain_id = 0, $options = array() ){
        $default_options = array(
            'server_id'         => '1',
            'parent_domain_id'  => $domain_id,
            'username'          => '',
            'password'          => '',
            'quota_size'        => -1,
            'active'            => 'y',
            'uid'               => 'web'.$domain_id,
            'gid'               => 'client'.$client_id,
            'dir'               => '/var/www/clients/client'. $client_id .'/web' . $domain_id,
            'quota_files'       => -1,
            'ul_ratio'          => -1,
            'dl_ratio'          => -1,
            'ul_bandwidth'      => -1,
            'dl_bandwidth'      => -1
        );
        
       $new_options = wp_parse_args($options, $default_options);
       $params_api = array(
            'session_id'    => $this->session_id,
            'client_id'     => $client_id,
            'params'        => $new_options,
        );
        return $this->request('sites_ftp_user_add', $params_api);

    }
    
    public function sites_database_user_add( $client_id = 0, $options = array() ){
        $default_options = array(
            'server_id'         => '1',
            'database_user'     => '',
            'database_password' => '',
        );
        
       $new_options = wp_parse_args($options, $default_options);
       $params_api = array(
            'session_id'    => $this->session_id,
            'client_id'     => $client_id,
            'params'        => $new_options,
        );
        return $this->request('sites_database_user_add', $params_api);        

    }

    public function mail_domain_add( $client_id = 0, $options = array() ){
        $default_options = array(
            'server_id' => '1',
            'domain' => '',
            'active' => 'y'
        );
        
       $new_options = wp_parse_args($options, $default_options);
       $params_api = array(
            'session_id'    => $this->session_id,
            'client_id'     => $client_id,
            'params'        => $new_options,
        );
        return $this->request('mail_domain_add', $params_api);         

    }

     public function mail_user_add( $client_id = 0, $options = array() ){
        $default_options = array(
            'server_id' => '1',
            'email' => '',
            'login' => '',
            'password' => '',
            'name' => '',
            'uid' => 5000,
            'gid' => 5000,
            'maildir' => '/var/vmail/'. time(true) . '-'. rand(1, 1234) .'/'. time(true) . '-'. rand(1, 1234),
            'quota' => 524288000,
            'cc' => '',
            'homedir' => '/var/vmail',
            'autoresponder' => 'n',
            'autoresponder_start_date' => '',
            'autoresponder_end_date' => '',
            'autoresponder_text' => '',
            'move_junk' => 'n',
            'custom_mailfilter' => '',
            'postfix' => 'y',
            'access' => 'n',
            'disableimap' => 'n',
            'disablepop3' => 'n',
            'disabledeliver' => 'n',
            'disablesmtp' => 'n',
        );
         
       $new_options = wp_parse_args($options, $default_options); 
       $params_api = array(
            'session_id'    => $this->session_id,
            'client_id'     => $client_id,
            'params'        => $new_options,
        );
        return $this->request('mail_user_add', $params_api);        

    }

    public function client_get_all() {
        $params_api = array(
            'session_id'    => $this->session_id,
        );

        return $this->request('client_get_all', $params_api);
    }
    public function client_get($client_id) {
        
        $params_api = array(
            'session_id'    => $this->session_id,
            'client_id'     => $client_id,
        );

        return $this->request('client_get', $params_api);
    }


    public function client_get_groupid($primary_id) {
        $params_api = array(
            'session_id'    => $this->session_id,
            'client_id'     => $primary_id,
        );
        return $this->request('client_get_groupid', $params_api);     
    }

    public function sites_web_domain_get($primary_id) {
        $params_api = array(
            'session_id'    => $this->session_id,
            'primary_id'     => $primary_id,
        );
        return $this->request('sites_web_domain_get', $params_api);      
    }
        
    public function sites_web_aliasdomain_add($client_id, $options = array()) {
        $default_options = array(
            'server_id' => '1',
            'ip_address' => '*',
            'domain' => '',
            'type' => 'alias',
            'parent_domain_id' => '',
            'vhost_type' => '',
            'document_root' => NULL,
            'system_user' => NULL,
            'system_group' => NULL,
            'hd_quota' => 0,
            'traffic_quota' => -1,
            'cgi' => 'n',
            'ssi' => 'n',
            'suexec' => 'n',
            'errordocs' => 1,
            'is_subdomainwww' => 1,
            'subdomain' => '',
            'php' => 'mod',
            'ruby' => 'n',
            'redirect_type' => '',
            'redirect_path' => '',
            'ssl' => 'n',
            'ssl_state' => '',
            'ssl_locality' => '',
            'ssl_organisation' => '',
            'ssl_organisation_unit' => '',
            'ssl_country' => '',
            'ssl_domain' => '',
            'ssl_request' => '',
            'ssl_cert' => '',
            'ssl_bundle' => '',
            'ssl_action' => '',
            'stats_password' => '',
            'stats_type' => 'webalizer',
            'allow_override' => 'All',
            'apache_directives' => '',
            'php_open_basedir' => '/',
            'custom_php_ini' => '',
            'backup_interval' => '',
            'backup_copies' => 1,
            'active' => 'y',
            'traffic_quota_lock' => 'n'
        );
         
       $new_options = wp_parse_args($options, $default_options);
       $params_api = array(
            'session_id'    => $this->session_id,
            'client_id'     => $client_id,
            'params'        => $new_options,
        );
        return $this->request('sites_web_aliasdomain_add', $params_api);        
    }

}
?>