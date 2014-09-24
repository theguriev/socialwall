
/**
 * Popup for share buttons
 * @param  object obj   --- button
 * @param  object event --- event
 */
function sharePopup(obj, event)
{
	var href = jQuery(obj).attr('href');
	window.open(href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');
	event.preventDefault();
}

function layout()
{
	jQuery(gc_social_wall.container).masonry('layout');
	window.timer = false;
}

/**
 * Initialize masonty bricks
 */
function isotopeInit()
{
	jQuery(gc_social_wall.container).masonry({
		itemSelector: gc_social_wall.item_selector,
		gutter: 10,
		columnWidth: 236
	});
}

/**
 * Feed filter toggle
 * @param  object event --- click
 * @param  object obj   --- button  
 */
function filterToggle(event, obj)
{
	var filter_class = 'filter';

	showAllBricks(jQuery(obj).parent().parent().parent().parent());

	jQuery(obj).parent().toggleClass(filter_class);
	jQuery(obj).parent().parent().find('li').each(function(){
		if(jQuery(this).hasClass(filter_class))
		{
			hideBricksByFeed(jQuery(this).parent().parent().parent(), jQuery(this).find('a').attr('href'));
		}
	});
	jQuery(gc_social_wall.container).masonry('layout');
	event.preventDefault();
}

/**
 * Show all bricks
 * @param  object bricks --- jQuery bricks html wrapper
 */
function showAllBricks(bricks)
{
	bricks.find('.bricks-content .brick').each(function(){
		jQuery(this).show();
	});
}

/**
 * Hide bricks by feed type
 * @param  object bricks --- jQuery bricks html wrapper
 * @param  string feed   --- feed type
 */
function hideBricksByFeed(bricks, feed)
{
	bricks.find('.bricks-content .brick.' + feed).each(function(){
		jQuery(this).hide();
	});
}

/**
 * Get messages
 */
function getMessages(feed)
{
	jQuery.ajax({
        url: gc_social_wall.ajax_url,
        type: 'POST', 
        dataType: 'json',
        async: false,
        data: {
            action:'getMessages',
            feed: feed,
            count: gc_social_wall.count
        },
        success:function(data) {
            if(data.result)
			{
				var append_html = jQuery(data.html);
				
				jQuery(gc_social_wall.container).append(append_html);
				jQuery(gc_social_wall.container).masonry('appended', append_html, true);
				layout(); 
				if(!window.timer)
				{
					window.timer = setTimeout(function() { layout() }, 1000);	
				}
			} 	
        }
    });
}

function sortPosts()
{
	var list = jQuery(gc_social_wall.container + ' .brick').toArray();
	list.sort(function(a, b){
		if(jQuery(a).data('time') > jQuery(b).data('time')) return -1;
		return 1;
	});

	jQuery(list).appendTo(gc_social_wall.container);
	jQuery(gc_social_wall.container).masonry('reloadItems');
	layout();
}
