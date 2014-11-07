var Wall = new Object();

/**
 * Get all hash tags from text
 * @param  string text --- text with hash tags
 * @return array --- hashtags
 */
Wall.getAllHashTags = function(text){
	return text.match(/#[^\s]*/i);
};

/**
 * Wrap text hash tags
 * @param  string text --- some text with hash tags
 * @param  string pattern --- url pattern
 * @return string --- text with hash tags url's
 */
Wall.wrapHashTags = function(text, pattern){
	var match = Wall.getAllHashTags(text);
	var tag   = '';

	if(match != null)
	{
		for(var i = 0; i < match.length; i++)
		{
			tag = match[i].replace('#', '').replace(',', '');
			tag = String.Format(pattern, tag);
			tag = String.Format('<a href="{0}">{1}</a>', tag, match[i]);
			text = text.replace(match[i], tag);
		}	
	}
	return text;
};

/**
 * Get share panel to each brick
 * @param  string url --- link to share
 * @return string --- HTML code
 */
Wall.getSharePanel = function(url){
	var panel           = [];	
	var facebook_url    = 'http://www.facebook.com/sharer.php?u=' + url;
	var twitter_url     = 'https://twitter.com/share?url=' + url;
	var google_plus_url = 'https://plus.google.com/share?url=' + url;
	var linkedin_url    = 'http://www.linkedin.com/shareArticle?mini=true&url=' + url;

	panel.push('<ul class="share-panel">');
	// ==============================================================
	// Facebook
	// ==============================================================
	panel.push('<li class="facebook">');
	panel.push('<a href="' + facebook_url + '" onclick="sharePopup(this, event)">');
	panel.push('<i class="fa fa-facebook"></i>');
	panel.push('</a>');
	panel.push('</li>');
	// ==============================================================
	// Twitter
	// ==============================================================	
	panel.push('<li class="twitter">');
	panel.push('<a href="' + twitter_url + '" onclick="sharePopup(this, event)">');
	panel.push('<i class="fa fa-twitter"></i>');
	panel.push('</a>');
	panel.push('</li>');
	// ==============================================================
	// Google plus
	// ==============================================================
	panel.push('<li class="google-plus">');
	panel.push('<a href="' + google_plus_url + '" onclick="sharePopup(this, event)">');
	panel.push('<i class="fa fa-google-plus"></i>');
	panel.push('</a>');
	panel.push('</li>');
	// ==============================================================
	// Linked in
	// ==============================================================
	panel.push('<li class="linkedin">');
	panel.push('<a href="' + linkedin_url + '" onclick="sharePopup(this, event)">');
	panel.push('<i class="fa fa-linkedin"></i>');
	panel.push('</a>');
	panel.push('</li>');

	panel.push('</ul>');
	return panel.join(' ');
};

/**
 * Get elapsed time
 * @param  integer time --- unix timestamp
 * @return string elapsed time
 */
Wall.getElapsedTime = function(time){
	var str   = '';
	var items = [];
	var date  = new Date();
	var d     = new Date();
	var time  = date.getTime() - time;
	var text  = '';
	var unit  = 0;
	var res   = {
		year   : 0,
		month  : 0,
		day    : 0,
		hour   : 0,
		minute : 0,
		second : 0
	};

    var tokens = {
        '31536000000' : 'year',
        '2592000000'  : 'month',        
        '86400000'    : 'day',
        '3600000'     : 'hour',
        '60000'       : 'minute',
        '1000'        : 'second'
    };

    for(var key in tokens)
    {
    	text = tokens[key];
    	unit = parseInt(key);
    	if(time < unit) continue;
    	
    	res[text] = Math.floor(time/unit);
    	time = time-(res[text]*unit);
    }
    for(var key in res)
    {
    	if(!parseInt(res[key])) continue;
    	items.push(res[key] + ' ' + key + ' ');
    }
    items = items.slice(0, 2);
    return items.join(' ') + ' ago';
}

/**
 * Check variable to isset
 * @param  mixed variable --- variable to check
 * @return boolean --- true if succes | false if not
 */
Wall.isset = function(variable){
	return typeof(variable) != 'undefined';
};

/**
 * Make long text smaller
 * @param  string text --- long text
 * @param  integer symbols --- limit symbols
 * @return string --- small text aka limited
 */
Wall.cutText = function(text, symbols){
	var pieces = [];
	text = text.substr(0, symbols+1);
	if(text.length > symbols)
	{
		pieces = text.split(' ');
		delete pieces[pieces.length-1];
		return pieces.join(' ') + '...';
	}
	return text;
};

/**
 * Sort bricks by time
 */
Wall.sortBricks = function(){
	var list = jQuery(gc_social_wall.container + ' .brick').toArray();
	list.sort(function(a, b){
		if(jQuery(a).data('time') > jQuery(b).data('time')) return -1;
		return 1;
	});

	jQuery(list).appendTo(gc_social_wall.container);
	jQuery(gc_social_wall.container).masonry('reloadItems');
	jQuery(gc_social_wall.container).masonry('layout');
}

/**
 * Get facebook switcher button
 */
Wall.getButton = function(on_off, type, icon){
	if(on_off != 'on') return '';

	return String.Format(
		'<li class="{0}"><a href="{0}" onclick="filterToggle(event, this)"><i class="fa fa-2x {1}"></i></a></li>',
		type,
		icon
	);
};