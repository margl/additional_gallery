$(function() {
    $('.additional-gallery-upload-btn').click(function() {
        let file_input = $('#additional_gallery_files');
        let endpoint = file_input.data('upload-url');
        //check if files are present
        if(file_input.prop('files').length > 0) {
            uploadFiles(file_input.prop('files'), endpoint);
        }
    });

    $(document).on('click', '.additional-gallery-image-delete', function() {
        let parent_div = $(this).parent();
        let endpoint = $(this).data('delete-url');
        $.ajax({
            url: endpoint,
            type: 'DELETE',
            success: function (response) {
                if (response.status) {
                    $(parent_div).remove();
                }
            },
            error: function (res) {
                let response = res.responseJSON;
                if(response.error) {
                    showErrorMessage(response.error);
                }
            }
        });
    })
})

function uploadFiles(files, endpoint) {
    let formData = new FormData();
    $.each(files, function(index, file) {
        formData.append('additional_gallery_files[]', file);
    });

    $.ajax({
        url: endpoint,
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            if (response.length) {
                response.forEach(function (el) {
                    if(el.content) {
                        $('.additional-gallery-image-container').append(el.content);
                    } else if(el.error && el.image_name) {
                        $.growl.error({title: el.image_name, message: el.error});
                    }
                })
                $('#additional_gallery_files').val('');
            }
        }
    });
}