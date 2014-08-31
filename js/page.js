jQuery(document).ready(function($) {	
	// Loads tabbed sections if they exist
	if ( jQuery('.nav-tab-wrapper').length > 0 ) 
	{
		options_framework_tabs();
	}
});

function options_framework_tabs() 
{

	var group      = jQuery('.group');
	var navtabs    = jQuery('.nav-tab-wrapper a');
	var active_tab = '';

	// Hides all the .group sections to start
	group.hide();

	// Find if a selected tab is saved in localStorage
	if ( typeof(localStorage) != 'undefined' ) {
		active_tab = localStorage.getItem('active_tab');
	}

	// If active tab is saved and exists, load it's .group
	if ( active_tab != '' && jQuery(active_tab).length ) 
	{
		jQuery(active_tab).fadeIn();
		jQuery(active_tab + '-tab').addClass('nav-tab-active');
	} 
	else 
	{
		jQuery('.group:first').fadeIn();
		jQuery('.nav-tab-wrapper a:first').addClass('nav-tab-active');
	}

	// Bind tabs clicks
	navtabs.click(function(e) {
		e.preventDefault();
		// Remove active class from all tabs
		navtabs.removeClass('nav-tab-active');

		jQuery(this).addClass('nav-tab-active').blur();

		if (typeof(localStorage) != 'undefined' ) 
		{
			localStorage.setItem('active_tab', jQuery(this).attr('href') );
		}

		group.hide();
		jQuery(jQuery(this).attr('href')).fadeIn();
	});
}