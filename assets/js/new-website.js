jQuery(document).ready(function($) {
	
	$('#exist_client').change(function() {
		if (jQuery(this).is(':checked')) {
			jQuery('#client_table').fadeOut();
			jQuery('#tr_client_select').fadeIn();
		} else {
			jQuery('#client_table').fadeIn();
			jQuery('#tr_client_select').fadeOut();
			
		}

	});
});