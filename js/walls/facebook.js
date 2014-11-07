function Facebook(options){
	//                                       __  _          
	//     ____  _________  ____  ___  _____/ /_(_)__  _____
	//    / __ \/ ___/ __ \/ __ \/ _ \/ ___/ __/ / _ \/ ___/
	//   / /_/ / /  / /_/ / /_/ /  __/ /  / /_/ /  __(__  ) 
	//  / .___/_/   \____/ .___/\___/_/   \__/_/\___/____/  
	// /_/              /_/                                 
	this.options      = options;
	this.url          = '';
	this.url_user     = '';
	this.url_next     = '';
	this.user_picture = '';
	var $this         = this;
	
	//                    __  __              __    
	//    ____ ___  ___  / /_/ /_  ____  ____/ /____
	//   / __ `__ \/ _ \/ __/ __ \/ __ \/ __  / ___/
	//  / / / / / /  __/ /_/ / / / /_/ / /_/ (__  ) 
	// /_/ /_/ /_/\___/\__/_/ /_/\____/\__,_/____/  
		                                             
	/**
	 * Get all items
	 */
	this.getItems = function(){
		var args = {
			access_token: options.fb_app_id + '|' + options.fb_app_key,
			fields: 'type,link,picture,from,message,created_time,comments.limit(1).summary(true),likes.limit(1).summary(true),shares',
			limit: options.fb_posts_per_load
		};
		this.url = String.Format(
			'https://graph.facebook.com/v2.1/{0}/posts?{1}',
			options.fb_facebook_page,
			jQuery.param(args)
		);
		// ==============================================================
		// Load User picture before load wall posts
		// ==============================================================
		this.setUserPicture();
		// ==============================================================
		// Load wall posts
		// ==============================================================
		this.load(this.url);
	};

	/**
	 * Get next items
	 */
	this.getNext = function(){
		this.load(this.url_next);
	};

	/**
	 * Get class type
	 * @return string --- class type
	 */
	this.getType = function(){
		return 'facebook';
	};

	/**
	 * Load items
	 * @param  stirng url --- ajax url
	 */
	this.load = function(url){
		jQuery.ajax({
			type: "GET",
			dataType: 'json',
			url: url,
			success: function(response){
				if(typeof(response.data) != 'undefined')
				{
					var elements = [];
					for(var i = 0; i < response.data.length; i++)
					{
						elements.push( $this.wrapItem( response.data[i] ) );
					}
					elements = jQuery(elements.join('')).css({opacity: 0});
					elements.imagesLoaded(function(){
						jQuery(gc_social_wall.container).append(elements);
						jQuery(gc_social_wall.container).masonry( 'appended', elements, true );
						elements.animate({opacity: 1});
					});
					jQuery(gc_social_wall.container).masonry('layout');
					$this.url_next = response.paging.next;
				}
			}
		});
	};

	/**
	 * Get facebook switcher button
	 */
	this.getButton = function(){
		return Wall.getButton(this.options.fb_show_button, this.getType(), this.options.fb_icon);
	};

	/**
	 * Wrap one item to HTML code
	 * @param  object item --- facebook object
	 * @return string --- HTML code
	 */
	this.wrapItem = function(el){
		var brick = [];
		var msg   = this.getText(el);
		var ids = el.id.split('_');

		if(msg == '' && (el.type == 'status' || el.type == 'link')) return '';
		Wall.getAllHashTags(msg);
		el.link = 'https://www.facebook.com/permalink.php?story_fbid=' + ids[1] + '&id=' + ids[0];

		brick.push('<div class="brick facebook" data-time="' + Date.parse(el.created_time) + '" data-time-no="' + el.created_time + '">');
		brick.push(Wall.getSharePanel(el.link));
		brick.push(this.getImageHeader(el.picture));
		brick.push(this.getTextSection(msg, el));
		brick.push(this.getAuthorPanel(el));
		brick.push(this.getFooter(el));
		brick.push('</div>');

		return brick.join('');
	};	

	/**
	 * Get user picture
	 * @return string --- image src
	 */
	this.setUserPicture = function(){
		var args = {
			access_token: this.options.fb_app_id + '|' + this.options.fb_app_key,
			fields: 'url',
			redirect: 'false'
		};
		this.url_user = String.Format(
			'https://graph.facebook.com/v2.1/{0}/picture?{1}',
			this.options.fb_facebook_page,
			jQuery.param(args)
		);

		jQuery.ajax({
			type: "GET",
			dataType: 'json',
			url: this.url_user,
			data: this.options.fb_facebook_page,
			success: function(response){
				if(Wall.isset(response.data.url))
				{
					$this.user_picture = response.data.url;
				}
			}
		});
	};

	/**
	 * Get message author
	 * @param  object el --- facebook item
	 * @return string --- HTML code
	 */
	this.getAuthorPanel = function(el){
		if(this.options.fb_show_author_panel != 'on') return '';
		
		var user_picture = $this.user_picture != '' ? $this.user_picture : 'http://placehold.it/30x30';

		var link = String.Format(
			'https://facebook.com/{0}', 
			this.options.fb_facebook_page
		);
		var img = String.Format(
			'<img width="30" src="{0}" alt="{1}" class="circle">',
			user_picture,
			el.from.name
		);
		var text = String.Format(
			'<div class="txt"><b class="title">{0}</b><br><small>{1}</small></div>',
			el.from.name,
			el.from.category
		);
		return String.Format(
			'<a class="panel" target="_blank" href="{0}">{1}</a>',
			link,
			img + text
		);
	};

	/**
	 * Get message counters
	 * @param  object el --- facebook item
	 * @return string --- HTML code
	 */
	this.getCounters = function(el){
		if(this.options.fb_show_counters != 'on') return '';

		var counters = [];
		var pattern  = '<li><i class="fa {0}"></i> {1}</li>';

		counters.push('<div class="counts"><ul>');
		counters.push(
			String.Format(
				pattern, 
				'fa-share-alt',
				parseInt(el.shares.count)
			)
		);
		counters.push(
			String.Format(
				pattern,
				'fa-heart',
				parseInt(el.likes.summary.total_count)
			)
		);
		counters.push(
			String.Format(
				pattern,
				'fa-comments',
				parseInt(el.comments.summary.total_count)
			)
		);
		counters.push('</ul></div>');
		return counters.join(' ');
	};

	/**
	 * Get footer block
	 * @param  object el --- facebook item
	 * @return string --- HTML code
	 */
	this.getFooter = function(el){
		var footer = [];

		footer.push('<footer>');
		footer.push('<a href="' + el.link + '" target="_blank">');
		footer.push('<div class="brick-type">');
		footer.push('<i class="fa ' + this.options.fb_icon + '"></i>');
		footer.push('</div>');
		footer.push('<div class="txt">');
		footer.push(el.from.name + '<br><small>posted ' + Wall.getElapsedTime(Date.parse(el.created_time)) + '</small>');
		footer.push('</div>');
		footer.push('</a>');
		footer.push('</footer>');
		return footer.join(' ');
	};

	/**
	 * Get text section
	 * @param  string text --- long text
	 * @param  object el --- facebook element
	 * @return string --- HTML code
	 */
	this.getTextSection = function(text, el){
		text = Wall.cutText(text, this.options.fb_max_symbols_per_post);
		text = Wall.wrapHashTags(text, 'https://www.facebook.com/hashtag/{0}');
		return String.Format(
			'<section><div class="text">{0}</div>{1}</section>',
			text,
			this.getCounters(el)
		);
	};

	/**
	 * Get text from facebook element
	 * @param  object el --- facebook element
	 * @return string --- text
	 */
	this.getText = function(el){
		var str = '';
		if(Wall.isset(el.story)) str += el.story;
		if(Wall.isset(el.message)) str += el.message;
		return str;
	};

	/**
	 * Get image header
	 * @param  string src --- image url
	 * @return string --- HTML code
	 */
	this.getImageHeader = function(src){
		src = this.getImage(src);
		if(src != '')
		{
			return '<header>' + '<img src="' + src + '" alt="Image">' + '</header>';
		}
		return '';
	};

	/**
	 * Get image from source
	 * @return string --- image url
	 */
	this.getImage = function(src){
		return src.replace('/v/', '/').replace('/s130x130/', '/s/');
	};
}



