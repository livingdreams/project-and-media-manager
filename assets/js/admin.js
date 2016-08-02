jQuery(document).ready(function ($) {

    btn_upload = $('<button/>', {
        id: 'upload',
        text: 'Select',
        click: function (e) {
            e.preventDefault();
            var image = wp.media({
                title: 'Upload Image',
                // mutiple: true if you want to upload multiple files at once
                multiple: false
            }).open().on('select', function () {
                //  return the selected image object from the Media Uploader,
                uploaded_image = image.state().get('selection').first();
                // convert uploaded_image to a JSON object to make accessing it easier
                image_url = uploaded_image.toJSON().url;
                // Let's assign the url value to the input field
                $(e.target).prev('.upload-img-url').val(image_url);
            });
        }
    });
    $('.upload-img-url').after(btn_upload);
    
    $('form').submit(function (event) {

        $('#loading').show();
        $(this).find('button').attr('disabled', 'disabled');
        //return false;
        $.ajax({
            type: "POST",
            url: ajaxurl,
            dataType: 'json',
            data: {action: 'pmm_ajax_action', method: $(this).attr('method'), data: $(this).serialize()}, // serializes the form's elements.
            success: function (data) {
                // show response from the php script.
                //console.log(data);
                $('.errorMessage').html('');
                $('.successMessage').html('');
                $('#loading').hide();
                if (data.status) {
                    if (data.message == 'reload')
                        window.location.reload(true);
                    $('.successMessage').html(data.message);
                } else {
                    $('.errorMessage').html(data.message);
                }
            }, error: function (response) {
                $('.errorMessage').html(response);
            }
        });

        event.preventDefault();

        $(this).find('button').removeAttr('disabled');
        return false;
    });

    $('#btnNPSubmit').click(function () {
        user_id = $.trim($('#user_id').val());
        new_pass = $.trim($('#new_password').val());

        if (user_id == '') {
            $('.errorMessage').html('Please choose a user.');
        } else if (new_pass == '' || new_pass == null) {
            $('.errorMessage').html("New Password is required.");
        } else {
            $.ajax({
                type: 'POST',
                url: ajaxurl,
                dataType: 'json',
                data: {action: 'pmm_ajax_action', method: 'reset_password', data: {id: user_id, password: new_pass}},
                success: function (data) {
                    $('.errorMessage').html('');
                    if (data.status) {
                        $('.successMessage').html('Password has been changed successfully.');
                        $('#user_id').val('');
                        $('#new_password').val('');
                    }
                }, error: function (response) {
                    $('.errorMessage').html(response);
                }
            });
        }
        return false;
    });

    $('#btnNPCancel').click(function () {
        window.location = window.location.href;
    });

});


/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */