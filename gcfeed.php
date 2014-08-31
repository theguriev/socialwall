<?php
/*
Plugin Name: GC feed
Plugin URI: http://wordpress.org/plugins/gcfeed/
Description: This plugin helps to export your records from social networks in WordPress blog.
Author: Guriev Eugen
Version: 1.0
Author URI: http://gurievcreative.com
*/

require_once '__.php';

class GCFeed{
	//                                       __  _          
	//     ____  _________  ____  ___  _____/ /_(_)__  _____
	//    / __ \/ ___/ __ \/ __ \/ _ \/ ___/ __/ / _ \/ ___/
	//   / /_/ / /  / /_/ / /_/ /  __/ /  / /_/ /  __(__  ) 
	//  / .___/_/   \____/ .___/\___/_/   \__/_/\___/____/  
	// /_/              /_/                                 
	private $page_settings;
	private $agregator;	

	//                    __  __              __    
	//    ____ ___  ___  / /_/ /_  ____  ____/ /____
	//   / __ `__ \/ _ \/ __/ __ \/ __ \/ __  / ___/
	//  / / / / / /  __/ /_/ / / / /_/ / /_/ (__  ) 
	// /_/ /_/ /_/\___/\__/_/ /_/\____/\__,_/____/  
	public function __construct()
	{
		$this->pageSettingsInit();	
		$this->agregatorInit();	
		// =========================================================
		// HOOKS
		// =========================================================
		add_action('wp_enqueue_scripts', array(&$this, 'scriptsAndStyles'));
		// =========================================================
		// SHORTCODE
		// =========================================================
		add_shortcode('gcfeed', array(&$this, 'updateFeed'));
	}

	/**
	 * Add some scripts or styles
	 */
	public function scriptsAndStyles()
	{
		wp_enqueue_style('font-awesome', \__::FONT_AWESOME_CSS);
		wp_enqueue_style('gcfeed', GCLIB_URL.'css/gcfeed.css');

		wp_enqueue_script('jquery');
		wp_enqueue_script('masonry', GCLIB_URL.'js/masonry.pkgd.min.js', array('jquery'));
		wp_enqueue_script('gcfeed', GCLIB_URL.'js/gcfeed.js', array('jquery'));
	}

	/**
	 * Initialize agregator
	 */
	private function agregatorInit()
	{		
		$this->agregator = new Feeds\Agregator();
		$this->agregator->registerFeed(new Feeds\Twitter($this->getTwitterOptions()));
		$this->agregator->registerFeed(new Feeds\Facebook($this->getFacebookOptions()));
		$this->agregator->registerFeed(new Feeds\Post($this->getPostTypeOptions()));	
		
	}

	public function updateFeed()
	{		
		$feeds = $this->agregator->getFeeds();
		
		?>
		<div class="bricks-content">
		<?php
		foreach ($feeds as $messages) 
		{
			if(is_array($messages))
			{
				foreach ($messages as $msg) 
				{
					$this->wrapBrick($msg);
				}
			}
		}
		?>
		</div>
		<?php
	}

	/**
	 * Get "FONT AWESOME" icon from type
	 * @param  string $type --- social media type
	 * @return string       --- font awesome icon
	 */
	public function getIconByType($type)
	{
		$icons = array(
			'facebook' => 'fa-facebook',
			'twitter'  => 'fa-twitter',
			'post'     => 'fa-wordpress'
		);
		if(isset($icons[$type])) return $icons[$type];
		return '';
	}

	private function wrapBrick($obj)
	{

		$img = $obj->picture != '' ? sprintf('<img src="%s">', $obj->picture) : '';
		$link = sprintf(
			'<a href="%s" class="link">%s</a><br><small>posted %s</small>',
			$obj->link,
			$obj->author,
			$this->getElapsedTime(strtotime($obj->date))
		);
		?>
		<div class="brick <?php echo $obj->type; ?>">
			<div class="brick-type">
				<i class="fa <?php echo $this->getIconByType($obj->type); ?>"></i>
			</div>
			<header>
				<?php echo $img; ?>
			</header>
			<section>
				<div class="text"><?php echo $obj->text; ?></div>				
			</section>
			<footer>
				<?php echo $link; ?>
			</footer>
		</div>
		<?php
	}

	/**
	 * Get elapsed time array
	 * @return array --- elapsed time
	 */
	public function getElapsedTime($time)
	{
		$str  = '';
		$time = time() - $time;
		$res  = array(
			'year'   => 0,
			'month'  => 0,
			'day'    => 0,
			'hour'   => 0,
			'minute' => 0,
			'second' => 0
		);
	    $tokens = array(
	        31536000 => 'year',
	        2592000  => 'month',        
	        86400    => 'day',
	        3600     => 'hour',
	        60       => 'minute',
	        1        => 'second');

	    foreach ($tokens as $unit => $text) 
	    {
	        if ($time < $unit) continue;
	        $res[$text] = floor($time / $unit);  
	        $time = $time-($res[$text]*$unit);
	    }	
	    foreach ($res as $key => $value) 
	    {
	    	if(!intval($value)) continue;
	    	$str.= $value.' '.$key.' ';
	    }    
	    return $str.' ago';
	}

	/**
	 * Initialize page setting. Plugin setting.
	 */
	private function pageSettingsInit()
	{
		$ccollection_facebook = new Controls\ControlsCollection(
			array(		
				new Controls\Text('Account', array('default-value' => 'whitehouse'), array('placeholder' => 'Facebook page')),
				new Controls\Text('APP ID', array('default-value' => '802383316448078'), array('placeholder' => 'Application ID')),
				new Controls\Text('APP KEY', array('default-value' => '970b61246640d52ac45bfa8bf596e6d5'), array('placeholder' => 'Application key'))
			)
		);

		$ccollection_twitter = new Controls\ControlsCollection(
			array(		
				new Controls\Text('Account', array('default-value' => 'whitehouse'), array('placeholder' => 'Twitter user')),
				new Controls\Text('Consumer key', array('default-value' => 'aMY4Zsnn2KYi5TZkTCr9NlMuF'), array('placeholder' => 'Cunsumer key')),
				new Controls\Text('Consumer secret', array('default-value' => 'vxkz9T7QQWUmqnJbkf7Eg8aHvFOCdcSMVMZrfbUPdNbw7nuYx9'), array('placeholder' => 'Consumer secret')),
				new Controls\Text('OAuth token', array('default-value' => '2717095358-aRUmevpNvioRb52xkFYls0Q7ldf9cIo2PjJzsqG'), array('placeholder' => 'Token')),
				new Controls\Text('OAuth token secret', array('default-value' => 'woklRm4IAnMK5dEkXCAlSboirK4qlUmYcYNkRVddPIbl4'), array('placeholder' => 'Token secret'))
			)
		);

		$post_types = get_post_types(array('public' => true));
		$post_types = \__::unsetKeys(array('attachment'), $post_types);

		$ccollection_post_type = new Controls\ControlsCollection(
			array(						
				new Controls\Select('Post type', array('values' => $post_types, 'description' => 'You can select your own post type')),
				new Controls\Text('Include categories', array('description' => 'You need type category ids separated by commas. Like this: 1,2,3,4'))
			)
		);

		$section_facebook  = new Admin\Section('Facebook', array('prefix' => 'gc_fb_'), $ccollection_facebook);
		$section_twitter   = new Admin\Section('Twitter', array('prefix' => 'gc_tw_'), $ccollection_twitter);
		$section_post_type = new Admin\Section('Post Type', array('prefix' => 'gc_pt_'), $ccollection_post_type);

		$this->page_settings = new Admin\Page('GC Feed', array(), array($section_facebook, $section_twitter, $section_post_type));
	}

	/**
	 * Get facebook options
	 * @return array --- facebook options
	 */
	public function getFacebookOptions()
	{
		return array(
			'account' => get_option('gc_fb_account'),
			'app_id'  => get_option('gc_fb_app_id'),
			'app_key' => get_option('gc_fb_app_key')
		);
		
	}

	/**
	 * Get twitter options
	 * @return array --- twitter options
	 */
	public function getTwitterOptions()
	{
		return array(
			'account'            => get_option('gc_tw_account'),
			'consumer_key'       => get_option('gc_tw_consumer_key'),
			'consumer_secret'    => get_option('gc_tw_consumer_secret'),
			'oauth_token'        => get_option('gc_tw_oauth_token'),
			'oauth_token_secret' => get_option('gc_tw_oauth_token_secret')
		);
	}

	/**
	 * Get post type options
	 * @return array --- post type options
	 */
	public function getPostTypeOptions()
	{
		return array(
			'post_type' => get_option('gc_pt_post_type'),
			'include'   => get_option('gc_pt_include_categories')
		);
	}

}
// =========================================================
// LAUNCH
// =========================================================
$GLOBALS['gcfeed'] = new GCFeed();





