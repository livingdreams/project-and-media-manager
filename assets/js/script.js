jQuery(document).ready(function ($) {

    $('form.pmm-form').submit(function (event) {
        //console.log($(this).attr('method'));
        $('#loading').show();
        $(this).find('button').attr('disabled', 'disabled');

        jQuery.ajax({
            type: "POST",
            url: pmm_ajax.url,
            dataType: 'json',
            data: {action: 'pmm_ajax_action', method: $(this).attr('method'), data: $(this).serialize()}, // serializes the form's elements.
            success: function (data) {
                // show response from the php script.
                //console.log(data);                
                $('.error-message').html();
                if (data.status) {
                    if (data.message == 'reload')
                        location.reload();
                    $('.success-message').html(data.message);
                } else {
                    $('.error-message').html(data.message);
                }
            }, error: function (response) {
                $('.error-message').html(response);
            }
        });

        event.preventDefault();
        $('#loading').hide();
        $(this).find('button').removeAttr('disabled');
        return false;
    });

    $('#log-out').on('click', function(){
        jQuery.ajax({
            type: "POST",
            url: pmm_ajax.url,
            dataType: 'json',
            data: {action: 'pmm_ajax_action', method: 'logout', }, // serializes the form's elements.
            success: function (data) {
                // show response from the php script.
                //console.log(data);
                $('.error-message').html();
                if (data.status) {
                    if (data.message == 'reload')
                        location.reload();
                } else {
                    $('.error-message').html(data.message);
                }
            }, error: function (response) {
                $('.error-message').html(response);
            }
        });
    })
});


/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */