jQuery(document).ready(function(){
	// =========================================================
	// Initialize color picker
	// =========================================================
    jQuery('.color-picker').wpColorPicker();
});

/**
 * Upload media to server
 * @param  object event --- probably click event
 * @param  object obj   --- clicked object
 */
function uploadMedia(event, obj)
{
	var el = jQuery(obj);
	if (window.fraemwork_upload) 
	{
		window.fraemwork_upload.open();
	} 
	else 
	{
		window.fraemwork_upload = wp.media.frames.framework_upload = wp.media({			
			title: el.data('choose'),
			button: {
				text: el.data('update'),
				close: false
			}
		});

		window.fraemwork_upload.on( 'select', function() {			
			var attachment = window.fraemwork_upload.state().get('selection').first();
			window.fraemwork_upload.close();
			el.parent().find('input[type="text"]').val(attachment.attributes.url);			
			if ( attachment.attributes.type == 'image' ) 
			{
				el.parent().find('.screenshot').empty().hide().append('<img src="' + attachment.attributes.url + '"><a class="remove-image" href="#" onclick="removeMedia(event, this)"><i class="fa fa-trash-o fa-2x"></i></a>').slideDown('fast');
			}			
		});
	}

	window.fraemwork_upload.open();

	event.preventDefault();	
}

/**
 * Cean controls
 * @param  object event --- probably click event
 * @param  object obj   --- clicked object
 */
function removeMedia(event, obj)
{
	var el = jQuery(obj);	
	el.parent().parent().parent().find('input').val('');
	el.parent().parent().find('.screenshot').html('');
	
	event.preventDefault();	
}