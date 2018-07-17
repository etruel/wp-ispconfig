jQuery(document).ready(function($) {
	
	onchange_type();
	jQuery('.settings-api-type').change(function() {
		onchange_type();
	});

	jQuery('#btn-test-connection').click(function(e) {
		jQuery(this).prop('disabled', true);
		jQuery('#test-connection-div').fadeIn();
		jQuery('#test-connection-div .spinner').addClass('is-active');
		jQuery('#test-connection-message').html(settings_obj.text_loading);
		jQuery('#test-connection-message').removeClass('error');
		jQuery('#test-connection-message').removeClass('success');
		
		var data = {
			action: 'ispconfig_testconnection',
			_wpnonce: settings_obj.nonce_test_con,
			options: $("input[name*='WPISPConfig_Options']").serialize()
		};
		jQuery.post(settings_obj.ajax_url, data, function(response) {  //si todo ok devuelve LOG sino 0
			jQuery('#test-connection-div .spinner').removeClass('is-active');
			jQuery('#test-connection-message').removeClass('success');
			jQuery('#test-connection-message').removeClass('error');

			if (response != 'connection-success') {
				jQuery('#test-connection-message').addClass('error');
				jQuery('#test-connection-message').html(response);
			} else {
				jQuery('#test-connection-message').addClass('success');
				jQuery('#test-connection-message').html(settings_obj.text_success);
				
			}
			jQuery('#btn-test-connection').prop('disabled', false);

		}).fail(function() {
			jQuery('#test-connection-div .spinner').removeClass('is-active');
			jQuery('#test-connection-message').removeClass('success');
			jQuery('#test-connection-message').addClass('error');
			jQuery('#test-connection-message').html(settings_obj.text_error_fail);
			jQuery('#btn-test-connection').prop('disabled', false);
			
		});
	});
});

function onchange_type() {
	if (jQuery('.settings-api-type:checked').val() == 'soap') {
		jQuery('#WPISPConfig_Options_restapi_location').parent().parent().fadeOut();
		jQuery('#WPISPConfig_Options_soap_location').parent().parent().fadeIn();
	} else {
		jQuery('#WPISPConfig_Options_restapi_location').parent().parent().fadeIn();
		jQuery('#WPISPConfig_Options_soap_location').parent().parent().fadeOut();
	}
}