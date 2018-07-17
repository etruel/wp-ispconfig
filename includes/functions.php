<?php
/**
* @package         etruel\ISPConfig
* @subpackage      Functions
* @author          Esteban Truelsegaard <esteban@netmdp.com>
*/
function wpispconfig_get_current_api($options) {
	if ($options['remote_type'] == 'soap' ) {
		return new SoapIspconfig($options);
	} else {
		return new RestApiISPConfig($options);
	}
}
?>