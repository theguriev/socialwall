jQuery(window).load(function(){ 
	// =========================================================
	// MASONRY BRICS
	// =========================================================
	isotopeInit();
});

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
