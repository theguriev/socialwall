var Walls = new Object();

/**
 * Initialize walls
 */
Walls.init = function(){
	Walls.collection = {
		facebook: 'new Facebook(options)',
		instagram: 'new Instagram(options)',
	};

	Walls.autoload_busy = 0;
	jQuery.ajaxSetup({
		beforeSend: function(){
			Walls.autoload_busy++;
		},
		complete: function(){
			Walls.autoload_busy--;
		}
	});

	Walls.masonryInit();
	Walls.autoLoad();
};

Walls.appendButton = function(type, html){
	if(html == '' || type == '') return '';
	if(!Walls.buttonAlreadyExists(type))
	{
		jQuery(gc_social_wall.container_buttons).append(html);	
	}
};

/**
 * Check button already exists?
 * @param  string type --- wall type
 * @return boolean --- true if yes | false if not
 */
Walls.buttonAlreadyExists = function(type){
	var is_exists = false;
	jQuery(gc_social_wall.container_buttons).find('li').each(function(){
		if(jQuery(this).attr('class') == type) is_exists = true
	});
	return is_exists;
};

/**
 * Convert values to key => value type
 * @param  array arr --- objects array
 * @return json
 */
Walls.getValues = function(arr){
	var js = {};
	for(var i = 0; i < arr.length; i++)
	{
		js[arr[i].name] = arr[i].value;
	}
	return js;
};

/**
 * Autoload social wall items
 */
Walls.autoLoad = function(){
	var bricks_level = 0;
	jQuery(document).scroll(function() {
		bricks_level = jQuery('#bricks').offset().top + jQuery('#bricks').height() - 700;
		if(jQuery(this).scrollTop() > bricks_level && Walls.autoload_busy <= 0)
		{
			Walls.autoload_busy = 0;
			for(var i = 0; i < walls_to_load.length; i++)
			{
				if(Wall.isset(walls_to_load[i].wall))
				{
					walls_to_load[i].wall.getNext();	
				}
			}
		}
	});
};

/**
 * Initialize masonry bricks
 */
Walls.masonryInit = function(){
	jQuery(gc_social_wall.container).imagesLoaded(function(){
		jQuery(gc_social_wall.container).masonry({
			itemSelector: gc_social_wall.item_selector,
			gutter: 10,
			columnWidth: 236,
			isInitLayout: true,
			isResizeBound: true,
			animationOptions: {
		        duration: 300,
		        easing: 'linear',
		        queue: false
		    }
		});
	});
	
};

jQuery(document).ready(function(){
	var wall_type = '';
	var options   = {};

	Walls.init();
	for(var i = 0; i < walls_to_load.length; i++)
	{
		wall_type = walls_to_load[i].type;
		options   = Walls.getValues(walls_to_load[i].values);
		
		if(typeof(Walls.collection[wall_type]) != 'undefined')
		{
			walls_to_load[i].wall = eval(Walls.collection[wall_type])
			Walls.appendButton(walls_to_load[i].wall.getType(), walls_to_load[i].wall.getButton());
			walls_to_load[i].wall.getItems();
		}
	}
});