<?php
/*
Plugin Name: GC Social wall
Plugin URI: http://wordpress.org/plugins/gc_social_wall/
Description: This plugin helps to export your records from social networks in WordPress blog. To use it, simply insert this short code [gc_social_wall] in the right place.
Author: Guriev Eugen
Version: 1.11
Author URI: http://gurievcreative.com
*/

require_once '__.php';

class GCSocialWall{
	//                          __              __      
	//   _________  ____  _____/ /_____ _____  / /______
	//  / ___/ __ \/ __ \/ ___/ __/ __ `/ __ \/ __/ ___/
	// / /__/ /_/ / / / (__  ) /_/ /_/ / / / / /_(__  ) 
	// \___/\____/_/ /_/____/\__/\__,_/_/ /_/\__/____/  
	const FIELD_SHORT_CODE = 'gc_social_wall_short_code';                                                 
	//                                       __  _          
	//     ____  _________  ____  ___  _____/ /_(_)__  _____
	//    / __ \/ ___/ __ \/ __ \/ _ \/ ___/ __/ / _ \/ ___/
	//   / /_/ / /  / /_/ / /_/ /  __/ /  / /_/ /  __(__  ) 
	//  / .___/_/   \____/ .___/\___/_/   \__/_/\___/____/  
	// /_/              /_/                                 
	private $page;
	//                    __  __              __    
	//    ____ ___  ___  / /_/ /_  ____  ____/ /____
	//   / __ `__ \/ _ \/ __/ __ \/ __ \/ __  / ___/
	//  / / / / / /  __/ /_/ / / / /_/ / /_/ (__  ) 
	// /_/ /_/ /_/\___/\__/_/ /_/\____/\__,_/____/  
	public function __construct()
	{
		$this->page = new Wall\Page();
		// ==============================================================
		// Actions & Filters
		// ==============================================================
		add_action('wp_ajax_saveShortCode', array(&$this, 'saveShortCodeAJAX'));
		add_action('wp_ajax_nopriv_saveShortCode', array(&$this, 'saveShortCodeAJAX'));
		add_action('wp_ajax_loadShortCode', array(&$this, 'loadShortCodeAJAX'));
		add_action('wp_ajax_nopriv_loadShortCode', array(&$this, 'loadShortCodeAJAX'));
		add_action('wp_enqueue_scripts', array(&$this, 'scriptsAndStyles'));
		// ==============================================================
		// Shortcodes
		// ==============================================================
		add_shortcode( 'gc_social_wall', array(&$this, 'initializeWall') );

	}

	/**
	 * Add scripts and styles to theme
	 */
	public function scriptsAndStyles()
	{
		// ==============================================================
		// Scripts
		// ==============================================================
		wp_enqueue_script('string-format', GCLIB_URL.'/js/string.format.js', array('jquery'));
		wp_enqueue_script('wall', GCLIB_URL.'/js/walls/wall.js', array('jquery'));
		wp_enqueue_script('facebook_wall', GCLIB_URL.'/js/walls/facebook.js', array('jquery'));
		wp_enqueue_script('instagram_wall', GCLIB_URL.'/js/walls/instagram.js', array('jquery'));
		wp_enqueue_script('gc_social_wall', GCLIB_URL.'/js/gc_social_wall.js', array('jquery'));
		wp_localize_script('gc_social_wall', 'gc_social_wall', array(
				'container' => '#bricks .bricks-content',
				'container_buttons' => '#bricks nav .bricks-buttons',
			) 
		);
		wp_enqueue_script('masonry', GCLIB_URL.'/js/masonry.js');
		wp_enqueue_script('imagesloaded', GCLIB_URL.'/js/imagesloaded.js');
		// ==============================================================
		// Styles
		// ==============================================================
		wp_enqueue_style('gc_social_wall', GCLIB_URL.'/css/gc_social_wall.css');
		wp_enqueue_style('font-aweseome', \__::FONT_AWESOME_CSS);
	}

	public function initializeWall($atts, $content)
	{
		foreach ($atts as &$el) 
		{
			$el = preg_replace('/^wall-[0-9]*?="/', '', $el);
			$el = preg_replace('/"$/', '', $el);
			$el = base64_decode($el);
			$el = json_decode($el);
		}
		ob_start();
		?>
		<div id="bricks">
			<nav><ul class="bricks-buttons"></ul></nav>
			<div class="bricks-content">
			</div>
		</div>
		<script>
		var walls_to_load = <?php echo json_encode($atts); ?>;
		</script>
		<?php
		
		$var = ob_get_contents();
		ob_end_clean();
		return $var;
		
	}

	/**
	 * Save short code to data base [AJAX]
	 */
	public function saveShortCodeAJAX()
	{
		$json['result'] = false;
		if(isset($_POST['request']) AND is_array($_POST['request']))
		{
			$json['result'] = update_option( self::FIELD_SHORT_CODE, $_POST['request'] );
		}
		echo json_encode($json);
		die();
	}

	/**
	 * Load shortcode [AJAX]
	 */
	public function loadShortCodeAJAX()
	{
		$json = array(
			'result'    => false,
			'shortcode' => ''
		);
		
		$shortcode = get_option(self::FIELD_SHORT_CODE);
		if($shortcode)
		{
			$json['result'] = true;
			$json['shortcode'] = $shortcode;
		}
		echo json_encode($json);
		die();
	}
}
// =========================================================
// LAUNCH
// =========================================================
$GLOBALS['gc_social_wall'] = new GCSocialWall();





