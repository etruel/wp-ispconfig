jQuery(document).ready(function ($) {

    $('#wpispconfig-save-settings2').click(function () {
        if ($('#new_domain').val() == '') {
            $('#new_domain_description').css('color', 'red');
            $('#new_domain').focus();
            return false;
        }
    });

    $('#new_domain').blur(function () {
        if ($('#ftpdb_user').val() == '') {
            $('#ftpdb_user').val( $(this).val().split('.')[0] );
        }
    });

    $('#exist_client').change(function () {
        if ($(this).is(':checked')) {
            $('#client_table').fadeOut();
            $('#tr_client_select').fadeIn();
            $('#tr_ftpdb_user').fadeIn();

            $('#td_select_client').html(wpcispconfig_new_website.txt_loading);
            var data = {
                action: 'wpispconfig_select_client',
                nonce: wpcispconfig_new_website.select_client_nonce
            }
            $.post(wpcispconfig_new_website.ajax_url, data, function (data) {
                $("#td_select_client").html(data);
            });

        } else {
            $('#client_table').fadeIn();
            $('#tr_client_select').fadeOut();
            $('#tr_ftpdb_user').fadeOut();

        }

    });

});