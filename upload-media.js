jQuery(document).ready(function($){
	$(document).on("click", ".upload_image_button", function(e) {
		var custom_uploader;

		var inputElem = $(this).prev(".image_input");
		var preview = $(this).next(".rhd_lovelyimage_preview");
		e.preventDefault();

		// If the uploader object has already been created, reopen the dialog
		if (custom_uploader) {
			custom_uploader.open();
			return;
		}

		// Extend the wp.media object
		custom_uploader = wp.media.frames.file_frame = wp.media({
			title: 'Choose Image',
			button: {
				text: 'Choose Image'
			},
			multiple: false
		});

		// When a file is selected, grab the URL and set it as the text field's value
		custom_uploader.on('select', function() {
			attachment = custom_uploader.state().get('selection').first().toJSON();
			inputElem.val(attachment.url);
			preview.attr("src", attachment.url);
			preview.show();
		});

		// Open the uploader dialog
		custom_uploader.open();
	});
});