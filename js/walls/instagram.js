function Instagram(options){
	//                                       __  _          
	//     ____  _________  ____  ___  _____/ /_(_)__  _____
	//    / __ \/ ___/ __ \/ __ \/ _ \/ ___/ __/ / _ \/ ___/
	//   / /_/ / /  / /_/ / /_/ /  __/ /  / /_/ /  __(__  ) 
	//  / .___/_/   \____/ .___/\___/_/   \__/_/\___/____/  
	// /_/              /_/                                 
	var $this = this;
	this.options  = options;
	this.url_next = '';
	this.urls     = {
		0 : 'https://api.instagram.com/v1/media/popular?client_id={0}&count={1}', 				// POPULAR_ITEMS
		1 : 'https://api.instagram.com/v1/tags/{0}/media/recent?client_id={1}&count={2}',       // SEARCH_BY_TAG
		2 : 'https://api.instagram.com/v1/locations/{0}/media/recent?client_id={1}&count={2}',  // LOCATION_ID
		3 : 'https://api.instagram.com/v1/users/{0}/media/recent?client_id={1}&count={2}',      // USER_FEED
		4 : 'http://instagram.com/{0}'															// AUTHOR
	};
	console.log(options);
	//                    __  __              __    
	//    ____ ___  ___  / /_/ /_  ____  ____/ /____
	//   / __ `__ \/ _ \/ __/ __ \/ __ \/ __  / ___/
	//  / / / / / /  __/ /_/ / / / /_/ / /_/ (__  ) 
	// /_/ /_/ /_/\___/\__/_/ /_/\____/\__,_/____/  
	/**
	 * Get all items
	 */
	this.getItems = function(){
		var url = String.Format(
			'https://api.instagram.com/v1/users/{0}/media/recent?client_id={1}&count={2}&callback=?',
			this.options.i_query,
			this.options.i_client_id,
			this.options.i_posts_per_load
		);
		this.load(url);
	}    

	/**
	 * Get next items
	 */
	this.getNext = function(){
		this.load(this.url_next);
	};

	/**
	 * Load items
	 * @param  stirng url --- ajax url
	 */
	this.load = function(url){
		jQuery.ajax({
			type: 'GET',
			dataType: 'jsonp',
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
					$this.url_next = response.pagination.next_url;
				}
			}
		});
	};      

	/**
	 * Get class type
	 * @return string --- class type
	 */
	this.getType = function(){
		return 'instagram';
	}; 

	/**
	 * Get instagram switcher button
	 */
	this.getButton = function(){
		return Wall.getButton(this.options.i_show_button, this.getType(), this.options.i_icon);
	};                      

	/**
	 * Wrap one item to HTML code
	 * @param  object item --- instagram object
	 * @return string --- HTML code
	 */
	this.wrapItem = function(el){
		
		var brick = [];

		if(el.caption == null) 
		{
			el.caption = {text : ''};
			console.log(el.caption);
		}
		else
		{
			console.log(el.caption);
		}


		brick.push(
			String.Format(
				'<div class="brick {0}" data-time="{1}" data-time-no="{2}">',
				this.getType(),
				el.created_time,
				el.created_time
			)
		);
		brick.push(Wall.getSharePanel(el.link));
		brick.push(this.getImageHeader(el.images.standard_resolution.url));
		brick.push(this.getTextSection(el.caption.text, el));
		brick.push(this.getAuthorPanel(el));
		brick.push(this.getFooter(el));
		brick.push('</div>');

		return brick.join('');
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
		footer.push('<i class="fa ' + this.options.i_icon + '"></i>');
		footer.push('</div>');
		footer.push('<div class="txt">');
		footer.push(el.user.full_name + '<br><small>posted ' + Wall.getElapsedTime(Date.parse(el.created_time)) + '</small>');
		footer.push('</div>');
		footer.push('</a>');
		footer.push('</footer>');
		return footer.join(' ');
	};

	/**
	 * Get message author
	 * @param  object el --- facebook item
	 * @return string --- HTML code
	 */
	this.getAuthorPanel = function(el){
		if(this.options.i_show_author_panel != 'on') return '';
		
		var user_picture = $this.user_picture != '' ? $this.user_picture : 'http://placehold.it/30x30';

		var link = String.Format(
			'http://instagram.com/{0}', 
			el.user.username
		);
		var img = String.Format(
			'<img width="30" src="{0}" alt="{1}" class="circle">',
			el.user.profile_picture,
			el.user.full_name
		);
		var text = String.Format(
			'<div class="txt"><b class="title">{0}</b><br><small>{1}</small></div>',
			el.user.full_name,
			el.user.website
		);
		return String.Format(
			'<a class="panel" target="_blank" href="{0}">{1}</a>',
			link,
			img + text
		);
	};

	/**
	 * Get text section
	 * @param  string text --- long text
	 * @param  object el --- instagram element
	 * @return string --- HTML code
	 */
	this.getTextSection = function(text, el){
		text = Wall.cutText(text, this.options.i_max_symbols_per_post);
		return String.Format(
			'<section><div class="text">{0}</div>{1}</section>',
			text,
			this.getCounters(el)
		);
	};

	/**
	 * Get message counters
	 * @param  object el --- instagram item
	 * @return string --- HTML code
	 */
	this.getCounters = function(el){
		if(this.options.i_show_counters != 'on') return '';

		var counters = [];
		var pattern  = '<li><i class="fa {0}"></i> {1}</li>';

		counters.push('<div class="counts"><ul>');
		counters.push(
			String.Format(
				pattern, 
				'fa-heart',
				parseInt(el.likes.count)
			)
		);
		counters.push(
			String.Format(
				pattern,
				'fa-comments',
				parseInt(el.comments.count)
			)
		);
		counters.push('</ul></div>');
		return counters.join(' ');
	};

	/**
	 * Get image header
	 * @param  string src --- image url
	 * @return string --- HTML code
	 */
	this.getImageHeader = function(src){
		if(src != '')
		{
			return '<header>' + '<img src="' + src + '" alt="Image">' + '</header>';
		}
		return '';
	};
}