<?php
/**
* @package         etruel\ISPConfig
* @subpackage 	   Add2Client
* @author          Esteban Truelsegaard <esteban@netmdp.com>
*/ 
if ( !defined('ABSPATH') ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}
if (!class_exists('WPISPConfig_Add2Client')) :

class WPISPConfig_Add2Client {

	private static $current_client_data = array();

	public static function hooks() {
		add_action('wpispconfig_all_in_one_before_table', array(__CLASS__, 'form_inputs_before'), 10, 1);
		add_action('wpispconfig_all_in_one_add_scripts', array(__CLASS__, 'add_scripts'));


		add_filter('wpispconfig_all_in_one_success_notices', array(__CLASS__, 'notices_success'), 10, 2);
		add_filter('wpispconfig_values_all_in_one_before_create', array(__CLASS__, 'before_create'), 10, 3);
		
	}
	public static function notices_success($notices_success, $created_values) {
		if (!empty(self::$current_client_data)) {
			$notices_success[0] = '<strong>' . __( 'Using an existing client ID:', 'wpispconfig' ) .'</strong> ' . $created_values['client_id'] . '<br/>';
			$notices_success[1] = '<strong>' . __( 'Username:', 'wpispconfig' ) .'</strong> '. self::$current_client_data['username'] .'<br/>';
			unset($notices_success[2]);
		}
		return $notices_success;
	}
	public static function before_create($values, $input_values, $soap) {
		if (!empty($input_values['exist_client'])) {
			if (!empty($input_values['client_id'])) {
				$client_id = intval($input_values['client_id']);
				try {
					self::$current_client_data = $soap->client_get($client_id);
					$values['client_id'] = $client_id;
					$values['client_name'] = self::$current_client_data['contact_name'];
					$values['company_name'] = self::$current_client_data['company_name'];
					$values['email'] = self::$current_client_data['email'];
					$values['username'] = self::$current_client_data['username'] . wp_generate_password(3, false, false);
					$values['password'] = wp_generate_password(12, false, false);
					$values['dns_email'] = str_replace('@', '.', $values['email']);

				} catch (Exception $e) {
					throw new Exception("Client does not exist with ID:" . $client_id, 1);
				}
			}
		}
		return $values;
	}

	public static function add_scripts() {
		wp_enqueue_script( 'wpcispconfig-all-in-one-add2client', WPISPCONFIG_PLUGIN_URL . 'assets/js/all_in_one.js', array( 'jquery' ), WPISPCONFIG_VERSION, true );
	}

	public static function form_inputs_before($soap) { 
		$clients = array();
		$client_data = array();
		if (!empty($soap)) {
			try {
				$clients = $soap->client_get_all();
				foreach ($clients as $key => $client_id) {
					$client_data[] = $soap->client_get($client_id);
				}
			} catch (Exception $e) {
				echo '<div class="notice notice-error">' .$e->getMessage() . '</div>';
			}

		}
		
		?>
		<table class="form-table">
			<tr>
				<th scope="row">
					<label for="exist_client"><?php _e( 'Existing client:', 'wpispconfig' ); ?></label>
				</th>
				<td>
					<input type="checkbox" id="exist_client" name="exist_client" value="1">
				</td>
			</tr>
			<tr id="tr_client_select" style="display: none;">
				<th scope="row">
					<label for="exist_client"><?php _e( 'Select Client:', 'wpispconfig' ); ?></label>
				</th>
				<td>
					<select id="client_id" name="client_id">
					<?php 
						foreach ($client_data as $key => $values) {
							echo '<option value="' . $values['client_id'] . '">' . $values['client_id'] . ' | ' . $values['contact_name'] . ' | ' . $values['company_name'] . '</option>';
						}
					?>
					</select>
					
				</td>
			</tr>
		</table>
	<?php
	}
}

endif;

WPISPConfig_Add2Client::hooks();

?>