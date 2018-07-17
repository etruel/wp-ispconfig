jQuery(document).ready(function($) {

	$('#client_id').change(function(e){
		$('#email').val($(this).find(':selected').data('email'));
		e.preventDefault();
		var client = $(this).val();
		$('#domain_id').fadeOut();
		jQuery.ajax({
			type : "post",
			dataType : "json",
			url : myAjax.ajaxurl,
			data : {action: "cmb_domain", client : client, nonce: myAjax.nonce},
			success: function(response) {
				if(response.type == "success") {
					//alert(response.data);
					$('#domain_id').hide().html(response.data).fadeIn();
				}
				else {
					alert(response.message);
					$('#domain_id').fadeIn();
				}
			}
		});
	});

});
