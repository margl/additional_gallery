$(function() {
    $('.additional-gallery-upload-btn').click(function() {
        let file_input = $('#additional_gallery_files');
        let endpoint = file_input.data('upload-url');
        //check if files are present
        if(file_input.prop('files').length > 0) {
            uploadFiles(file_input.prop('files'), endpoint);
        }
    });
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

        }
    });
}