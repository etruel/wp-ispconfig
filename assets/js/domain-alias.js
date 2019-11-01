jQuery(document).ready(function ($) {
	$('#client_id').change(function(e){
		$('#email').val($(this).find(':selected').data('email'));
		e.preventDefault();
		var client = $(this).val();
		$('#domain_id').hide();
		$('#domain_id_td').append('<p id="domain_id_loading">'+ js_wpconfig_domain_alias.txt_loading +'</p>');
		jQuery.ajax({
			type : "post",
			dataType : "json",
			url : js_wpconfig_domain_alias.ajaxurl,
			data : {action: "wpispconfig_cmb_domain", client : client, nonce: js_wpconfig_domain_alias.nonce},
			success: function(response) {
				if(response.type == "success") {
					//alert(response.data);
					$('#domain_id').html(response.data).fadeIn();
					$('#domain_id_loading').remove();
				}
				else {
					alert(response.message);
					$('#domain_id').fadeIn();
					$('#domain_id_loading').remove();
				}
			}
		});
	});

});