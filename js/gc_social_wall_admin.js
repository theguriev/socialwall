/**
 * Clean your cache
 */
function cleanCache()
{
	if(confirm('Do you realy want clean your cache?'))
	{
		jQuery.ajax({
	        url: gc_social_wall.ajax_url,
	        type: 'POST', 
	        dataType: 'json',
	        data: {
	            action:'cleanCache',
	            count: gc_social_wall.count
	        }
	    });
	}
}