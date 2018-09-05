jQuery(document).ready(function($) {
	
	$('#exist_client').change(function() {
		if (jQuery(this).is(':checked')) {
			jQuery('#client_table').fadeOut();
			jQuery('#tr_client_select').fadeIn();

			jQuery('#td_select_client').html(wpcispconfig_new_website.txt_loading);
			var data = {
				action: 'wpispconfig_select_client',
				nonce: wpcispconfig_new_website.select_client_nonce
			}
			jQuery.post(wpcispconfig_new_website.ajax_url, data, function( data ) {
				jQuery("#td_select_client").html(data);
			});

		} else {
			jQuery('#client_table').fadeIn();
			jQuery('#tr_client_select').fadeOut();
			
		}

	});

});