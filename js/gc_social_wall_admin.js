var Walls = new Object();

/**
 * Initialize walls
 */
Walls.init = function(){
	var rem, the_id,
		self = this,
		chooser = jQuery('.widgets-chooser'),
		selectSidebar = chooser.find('.widgets-chooser-sidebars'),
		sidebars = jQuery('div.widgets-sortables'),
		isRTL = !! ( 'undefined' !== typeof isRtl && isRtl );

	jQuery('#widgets-left .sidebar-name').click( function() {
		jQuery(this).closest('.widgets-holder-wrap').toggleClass('closed');
	});

	jQuery(document.body).bind('click.widgets-toggle', function(e) {
		var target = jQuery(e.target),
			css = { 'z-index': 100 },
			widget, inside, targetWidth, widgetWidth, margin;

		if ( target.parents('.widget-top').length && ! target.parents('#available-widgets').length ) 
		{
			widget = target.closest('div.widget');
			inside = widget.children('.widget-inside');
			targetWidth = parseInt( widget.find('input.widget-width').val(), 10 ),
			widgetWidth = widget.parent().width();

			if ( inside.is(':hidden') ) 
			{
				if ( targetWidth > 250 && ( targetWidth + 30 > widgetWidth ) && widget.closest('div.widgets-sortables').length ) 
				{
					if ( widget.closest('div.widget-liquid-right').length ) 
					{
						margin = isRTL ? 'margin-right' : 'margin-left';
					} 
					else 
					{
						margin = isRTL ? 'margin-left' : 'margin-right';
					}

					css[ margin ] = widgetWidth - ( targetWidth + 30 ) + 'px';
					widget.css( css );
				}
				widget.addClass( 'open' );
				inside.slideDown('fast');
			} 
			else 
			{
				inside.slideUp('fast', function() {
					widget.attr( 'style', '' );
					widget.removeClass( 'open' );
				});
			}
			e.preventDefault();
		} 
		else if ( target.hasClass('widget-control-save') ) 
		{
			Walls.save();
			e.preventDefault();
		} 
		else if ( target.hasClass('widget-control-remove') ) 
		{
			Walls.removeWall(target.closest('div.widget'));
			Walls.save();
			e.preventDefault();
		} 
		else if ( target.hasClass('widget-control-close') ) 
		{
			Walls.close( target.closest('div.widget') );
			e.preventDefault();
		}
	});

	sidebars.children('.widget').each( function() {
		var $this = jQuery(this);

		Walls.appendTitle( this );

		if ( $this.find( 'p.widget-error' ).length ) 
		{
			$this.find( 'a.widget-action' ).trigger('click');
		}
	});

	jQuery('#widget-list').children('.widget').draggable({
		connectToSortable: 'div.widgets-sortables',
		handle: '> .widget-top > .widget-title',
		distance: 2,
		helper: 'clone',
		zIndex: 100,
		containment: 'document',
		start: function( event, ui ) {
			var chooser = jQuery(this).find('.widgets-chooser');

			ui.helper.find('div.widget-description').hide();
			the_id = this.id;

			if ( chooser.length ) {
				// Hide the chooser and move it out of the widget
				jQuery( '#wpbody-content' ).append( chooser.hide() );
				// Delete the cloned chooser from the drag helper
				ui.helper.find('.widgets-chooser').remove();
	
			}
		},
		stop: function() {
			if ( rem ) {
				jQuery(rem).hide();
			}

			rem = '';
		}
	});

	sidebars.sortable({
		placeholder: 'widget-placeholder',
		items: '> .widget',
		handle: '> .widget-top > .widget-title',
		cursor: 'move',
		distance: 2,
		containment: 'document',
		start: function( event, ui ) {
			var height, $this = jQuery(this),
				$wrap = $this.parent(),
				inside = ui.item.children('.widget-inside');

			if ( inside.css('display') === 'block' ) {
				inside.hide();
				jQuery(this).sortable('refreshPositions');
			}

			if ( ! $wrap.hasClass('closed') ) {
				// Lock all open sidebars min-height when starting to drag.
				// Prevents jumping when dragging a widget from an open sidebar to a closed sidebar below.
				height = ui.item.hasClass('ui-draggable') ? $this.height() : 1 + $this.height();
				$this.css( 'min-height', height + 'px' );
			}
		},

		stop: function( event, ui ) {
			var $sidebar, $children, child, item,
				$widget = ui.item,
				id = the_id;

			if ( $widget.hasClass('deleting') ) {
				Walls.save(); // delete widget
				$widget.remove();
				return;
			}

			$widget.attr( 'style', '' ).removeClass('ui-draggable');
			the_id = '';

			Walls.save();

			$sidebar = $widget.parent();

			if ( $sidebar.parent().hasClass('closed') ) 
			{
				$sidebar.parent().removeClass('closed');
				$children = $sidebar.children('.widget');

				// Make sure the dropped widget is at the top
				if ( $children.length > 1 ) 
				{
					child = $children.get(0);
					item = $widget.get(0);

					if ( child.id && item.id && child.id !== item.id ) 
					{
						jQuery( child ).before( $widget );
					}
				}
			}
		},

		activate: function() {
			jQuery(this).parent().addClass( 'widget-hover' );
		},

		deactivate: function() {
			// Remove all min-height added on "start"
			jQuery(this).css( 'min-height', '' ).parent().removeClass( 'widget-hover' );
		},

		receive: function( event, ui ) {
			
		}
	}).sortable( 'option', 'connectWith', 'div.widgets-sortables' );

	jQuery('#available-widgets').droppable({
		tolerance: 'pointer',
		accept: function(o){
			return jQuery(o).parent().attr('id') !== 'widget-list';
		},
		drop: function(e,ui) {
			ui.draggable.addClass('deleting');
			jQuery('#removing-widget').hide().children('span').html('');
		},
		over: function(e,ui) {
			ui.draggable.addClass('deleting');
			jQuery('div.widget-placeholder').hide();

			if ( ui.draggable.hasClass('ui-sortable-helper') ) {
				jQuery('#removing-widget').show().children('span')
				.html( ui.draggable.find('div.widget-title').children('h4').html() );
			}
		},
		out: function(e,ui) {
			ui.draggable.removeClass('deleting');
			jQuery('div.widget-placeholder').show();
			jQuery('#removing-widget').hide().children('span').html('');
		}
	});

	// Area Chooser
	jQuery( '#widgets-right .widgets-holder-wrap' ).each( function( index, element ) {
		var $element = jQuery( element ),
			name = $element.find( '.sidebar-name h3' ).text(),
			id = $element.find( '.widgets-sortables' ).attr( 'id' ),
			li = jQuery('<li tabindex="0">').text( jQuery.trim( name ) );

		if ( index === 0 ) {
			li.addClass( 'widgets-chooser-selected' );
		}

		selectSidebar.append( li );
		li.data( 'sidebarId', id );
	});

	jQuery( '#available-widgets .widget .widget-title' ).on( 'click.widgets-chooser', function() {
		var $widget = jQuery(this).closest( '.widget' );

		if ( $widget.hasClass( 'widget-in-question' ) || jQuery( '#widgets-left' ).hasClass( 'chooser' ) ) {
			
		} else {
			// Open the chooser

			jQuery( '#widgets-left' ).addClass( 'chooser' );
			$widget.addClass( 'widget-in-question' ).children( '.widget-description' ).after( chooser );

			chooser.slideDown( 300, function() {
				selectSidebar.find('.widgets-chooser-selected').focus();
			});

			selectSidebar.find( 'li' ).on( 'focusin.widgets-chooser', function() {
				selectSidebar.find('.widgets-chooser-selected').removeClass( 'widgets-chooser-selected' );
				jQuery(this).addClass( 'widgets-chooser-selected' );
			} );
		}
	});

	// Add event handlers
	chooser.on( 'click.widgets-chooser', function( event ) {
		var $target = jQuery( event.target );

		if ( $target.hasClass('button-primary') ) {
			self.addWidget( chooser );
			
		} else if ( $target.hasClass('button-secondary') ) {
			
		}
	}).on( 'keyup.widgets-chooser', function( event ) {
		if ( event.which === $.ui.keyCode.ENTER ) {
			if ( jQuery( event.target ).hasClass('button-secondary') ) {
				// Close instead of adding when pressing Enter on the Cancel button
				
			} else {
				self.addWidget( chooser );
				
			}
		} else if ( event.which === $.ui.keyCode.ESCAPE ) {
			
		}
	});
};

Walls.save = function(){
	var short_code = new Array();
	jQuery('#short-code-sortable form').each(function(){
		short_code.push({
			type: jQuery(this).data('type'),
			values: jQuery(this).serializeArray()
		});
	});
	jQuery('.shortcode code').text(Walls.getShortCode(short_code));
	jQuery.ajax({
		type: "POST",
		dataType: 'json',
		url: defaults.ajax_url+'?action=saveShortCode',
		data: { request:short_code }
	});
};

/**
 * Load walls from Data Base
 */
Walls.load = function(){
	jQuery.ajax({
		type: "POST",
		dataType: 'json',
		url: defaults.ajax_url+'?action=loadShortCode',
		success: function(response){
			if(response.result)
			{
				for(var i = 0; i < response.shortcode.length; i++)
				{
					jQuery('#widget-list .widget').each(function(){
						if( jQuery(this).data('type') ==  response.shortcode[i].type)
						{
							wall = jQuery(this).clone();
							for(var x = 0; x < response.shortcode[i].values.length; x++)
							{
								wall.find('[name="' + response.shortcode[i].values[x].name + '"]').val(response.shortcode[i].values[x].value);
							}
							jQuery('#short-code-sortable').append(wall);
						}
					});
				}
				jQuery('.shortcode code').text(Walls.getShortCode(response.shortcode));
			}
		}
	});
};

/**
 * Get short code string
 * @param  array arr --- options array
 * @return string --- short code
 */
Walls.getShortCode = function(arr){
	var short_code = [];
	for(var i = 0; i < arr.length; i++)
	{
		short_code.push('wall-' + i + '="' + Base64.encode(JSON.stringify(arr[i])) + '"');
	}
	return '[gc_social_wall ' + short_code.join(' ') + ']';
};

Walls.appendTitle = function(widget){
	var title = jQuery('input[id*="-title"]', widget).val() || '';

		if ( title ) 
		{
			title = ': ' + title.replace(/<[^<>]+>/g, '').replace(/</g, '&lt;').replace(/>/g, '&gt;');
		}

		jQuery(widget).children('.widget-top').children('.widget-title').children()
				.children('.in-widget-title').html(title);
};

Walls.close  = function(widget) {
	widget.children('.widget-inside').slideUp('fast', function() {
		widget.attr( 'style', '' );
	});
};

Walls.addWidget = function( chooser ) {
	var widget, widgetId, add, n, viewportTop, viewportBottom, sidebarBounds,
		sidebarId = chooser.find( '.widgets-chooser-selected' ).data('sidebarId'),
		sidebar = jQuery( '#' + sidebarId );

	widget = jQuery('#available-widgets').find('.widget-in-question').clone();
	widgetId = widget.attr('id');
	add = widget.find( 'input.add_new' ).val();
	n = widget.find( 'input.multi_number' ).val();

	// Remove the cloned chooser from the widget
	widget.find('.widgets-chooser').remove();

	if ( 'multi' === add ) {
		widget.html(
			widget.html().replace( /<[^<>]+>/g, function(m) {
				return m.replace( /__i__|%i%/g, n );
			})
		);

		widget.attr( 'id', widgetId.replace( '__i__', n ) );
		n++;
		jQuery( '#' + widgetId ).find('input.multi_number').val(n);
	} else if ( 'single' === add ) {
		widget.attr( 'id', 'new-' + widgetId );
		jQuery( '#' + widgetId ).hide();
	}

	// Open the widgets container
	sidebar.closest( '.widgets-holder-wrap' ).removeClass('closed');

	sidebar.append( widget );
	sidebar.sortable('refresh');

	Walls.save();
	// No longer "new" widget
	widget.find( 'input.add_new' ).val('');

	jQuery( document ).trigger( 'widget-added', [ widget ] );

	/*
	 * Check if any part of the sidebar is visible in the viewport. If it is, don't scroll.
	 * Otherwise, scroll up to so the sidebar is in view.
	 *
	 * We do this by comparing the top and bottom, of the sidebar so see if they are within
	 * the bounds of the viewport.
	 */
	viewportTop = jQuery(window).scrollTop();
	viewportBottom = viewportTop + jQuery(window).height();
	sidebarBounds = sidebar.offset();

	sidebarBounds.bottom = sidebarBounds.top + sidebar.outerHeight();

	if ( viewportTop > sidebarBounds.bottom || viewportBottom < sidebarBounds.top ) {
		jQuery( 'html, body' ).animate({
			scrollTop: sidebarBounds.top - 130
		}, 200 );
	}

	window.setTimeout( function() {
		// Cannot use a callback in the animation above as it fires twice,
		// have to queue this "by hand".
		widget.find( '.widget-title' ).trigger('click');
	}, 250 );
};

/**
 * Remove wall from sortable
 * @param  object wall --- dom widget
 */
Walls.removeWall = function(wall){
	wall.remove();
	Walls.save();
};

/**	
 * Select all short code
 */
Walls.codeSelect = function(){
	Walls.selectText('shortcode-text');
};

/**
 * Select some text by ID
 * @param  string element --- DOM ID
 */
Walls.selectText = function(element) {
    var doc = document, 
    	text = doc.getElementById(element),
    	range, selection;

    if (doc.body.createTextRange) 
    {
        range = document.body.createTextRange();
        range.moveToElementText(text);
        range.select();
    } 
    else if (window.getSelection) 
    {
        selection = window.getSelection();        
        range = document.createRange();
        range.selectNodeContents(text);
        selection.removeAllRanges();
        selection.addRange(range);
    }
}

// ==============================================================
// Launch
// ==============================================================
jQuery(document).ready(function(){ 
	Walls.init(); 
	Walls.load();
});