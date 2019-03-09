jQuery(document).ready(function($) {
    $('#yearly-report-form').validate();

    $('#yearly-report-form').on('submit', function(e) {
        $('#notification-box').text('Updating please wait....');
        e.preventDefault();
        jQuery.post(
            ajaxurl, {
                'action': 'dbn_update_yearly_record',
                'date': $('#date').val(),
                'title': $('#title').val(),
                'document_id': $('#document_id').val(),
                'category': $('#category').val(),
                'nonce': $('#nonce').val(),
            },
            function(response) {
                $('#notification-box').text('');
                location.reload();
            });

    });

});

function open_gallery(record) {
    var mediaUploader;

    // If the uploader object has already been created, reopen the dialog
    if (mediaUploader) {
        //mediaUploader.open();
        return;
    }
    // Extend the wp.media object
    mediaUploader = wp.media.frames.file_frame = wp.media({
        title: 'Upload Report Document',
        button: {
            text: 'Insert document'
        },
        multiple: false
    });
    // Open the uploader dialog
    mediaUploader.open();
    // When a file is selected, grab the URL and set it as the text field's value
    mediaUploader.on('select', function() {
        var attachment = mediaUploader.state().get('selection').first().toJSON();
        $('#' + record).val(attachment.id);
        var link = attachment.url;
        $('span[data-filename=' + record + ']').text(link.substring(link.lastIndexOf('/') + 1));
        mediaUploader.close();
    });

}
$('#upload').click(function(e) {
    e.preventDefault();
    var record = $(this).data('id');
    open_gallery(record);
});

$('a.delete_record').on('click', function(e) {
    e.preventDefault();
    var id = jQuery(this).data('id');
    jQuery.post(ajaxurl, {
            'action': 'dbn_delete_record',
            'row': id,
        },
        function(response) {
            location.reload();
        })
})