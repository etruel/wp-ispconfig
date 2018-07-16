<?php 
/**
* @package         etruel\ISPConfig
* @subpackage      SoapISPConfig
* @author          The Team Mate at etruel.com
* @copyright       Copyright (c) 2018
*/
// Exit if accessed directly
if ( !defined('ABSPATH') ) {
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );
    exit();
}
class SoapIspconfig {


    private $soap;
    private $session_id;



    public function __construct($options){
        $soap_options = array('location' => $options['soap_location'], 'uri' => $options['soap_uri'], 'trace' => 1, 'exceptions' => 1);
       
        
        if($options['skip_ssl']) {
            // apply stream context to disable ssl checks
            $soap_options['stream_context'] = stream_context_create(array(
                'ssl' => array(
                    'verify_peer'       => false,
                    'verify_peer_name'  => false,
                    'allow_self_signed' => true
                )
            ));
        }

        if (!class_exists('SoapClient')) {
            throw new Exception('SoapClient class does not exist. Please add it to your PHP.ini', 1);
            return false;
        }

        $this->soap = new SoapClient(null, $soap_options);
        $this->session_id = $this->soap->login($options['soapusername'], $options['soappassword']);
        return $this;
    }
    
   
    public function get_client_by_user($username){
       
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
        if (in_array('dns_templatezone_get_all', $this->get_function_list())) {
            $ret = $this->soap->dns_templatezone_get_all($this->session_id);
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
     
        return $this->soap->client_add($this->session_id, $reseller_id, $new_options);

    }

    public function dns_templatezone_add($options = array() ) {
         $default_options = array(
            'client_id' => '',
            'template_id' => '1',
            'domain' => '',
            'ip' => '',
            'ns1' => '',
            'ns2' => '',
            'dns_email' => '',
        );
        $new_options = wp_parse_args($options, $default_options);
        extract($new_options);
        return $this->soap->dns_templatezone_add($this->session_id, $client_id, $template_id, $domain, $ip, $ns1, $ns2, $dns_email);
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
        
       return $this->soap->sites_web_domain_add($this->session_id, $client_id, $new_options, $new_options['read_only']);

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
       return $this->soap->sites_ftp_user_add($this->session_id, $client_id, $new_options);

    }
    
    public function sites_database_user_add( $client_id = 0, $options = array() ){
        $default_options = array(
            'server_id'         => '1',
            'database_user'     => '',
            'database_password' => '',
        );
        
       $new_options = wp_parse_args($options, $default_options);        
       return $this->soap->sites_database_user_add($this->session_id, $client_id, $new_options);

    }

    public function mail_domain_add( $client_id = 0, $options = array() ){
        $default_options = array(
            'server_id' => '1',
            'domain' => '',
            'active' => 'y'
        );
        
       $new_options = wp_parse_args($options, $default_options);        
       return $this->soap->mail_domain_add($this->session_id, $client_id, $new_options);

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
       return $this->soap->sites_web_aliasdomain_add($this->session_id, $client_id, $new_options);
    }

}
?>