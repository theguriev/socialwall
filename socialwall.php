<?php
/*
Plugin Name: GC Social wall
Plugin URI: http://wordpress.org/plugins/gc_social_wall/
Description: This plugin helps to export your records from social networks in WordPress blog. To use it, simply insert this short code [gc_social_wall] in the right place.
Author: Guriev Eugen
Version: 1.02
Author URI: http://gurievcreative.com
*/

require_once '__.php';

class GCSocialWall{
	//                          __              __      
	//   _________  ____  _____/ /_____ _____  / /______
	//  / ___/ __ \/ __ \/ ___/ __/ __ `/ __ \/ __/ ___/
	// / /__/ /_/ / / / (__  ) /_/ /_/ / / / / /_(__  ) 
	// \___/\____/_/ /_/____/\__/\__,_/_/ /_/\__/____/  
	const FIELD_FEEDS           = 'feeds';	
	const SHARE_URL_FACEBOOK    = 'http://www.facebook.com/sharer.php?u=%s';
	const SHARE_URL_TWITTER     = 'https://twitter.com/share?url=%s&via=%s';
	const SHARE_URL_GOOGLE_PLUS = 'https://plus.google.com/share?url=%s';
	const SHARE_URL_LINKEDIN    = 'http://www.linkedin.com/shareArticle?mini=true&url=%s';

	//                                       __  _          
	//     ____  _________  ____  ___  _____/ /_(_)__  _____
	//    / __ \/ ___/ __ \/ __ \/ _ \/ ___/ __/ / _ \/ ___/
	//   / /_/ / /  / /_/ / /_/ /  __/ /  / /_/ /  __(__  ) 
	//  / .___/_/   \____/ .___/\___/_/   \__/_/\___/____/  
	// /_/              /_/                                 
	private $page_settings;
	private $agregator;	
	private $global_settings;

	//                    __  __              __    
	//    ____ ___  ___  / /_/ /_  ____  ____/ /____
	//   / __ `__ \/ _ \/ __/ __ \/ __ \/ __  / ___/
	//  / / / / / /  __/ /_/ / / / /_/ / /_/ (__  ) 
	// /_/ /_/ /_/\___/\__/_/ /_/\____/\__,_/____/  
	public function __construct()
	{
		$this->pageSettingsInit();	
		$this->agregatorInit();	
		$this->global_settings = $this->getGlobalSettingsOptions();
		// =========================================================
		// HOOKS
		// =========================================================
		add_action('wp_enqueue_scripts', array(&$this, 'scriptsAndStyles'));
		add_action('admin_enqueue_scripts', array(&$this, 'adminScriptsAndStyles'));
		add_action('admin_head', array(&$this, 'addTinyButton'));
		// =========================================================
		// SHORTCODE
		// =========================================================
		add_shortcode('gc_social_wall', array(&$this, 'updateFeed'));
	}

	public function adminScriptsAndStyles()
	{
		wp_enqueue_style('default-styles', GCLIB_URL.'css/admin.css');
	}

	public function addTinyButton()
	{
	    if (!current_user_can('edit_posts') && !current_user_can('edit_pages')) return;
		if (get_user_option('rich_editing') == 'true') 
		{
			add_filter('mce_external_plugins', array(&$this, 'addTinyPlugin'));
			add_filter('mce_buttons', array(&$this, 'registerTinyButton'));
		}
	}

	public function addTinyPlugin($plugin_array)
	{
		$plugin_array['gc_social_wall_button'] = GCLIB_URL.'js/tinymce_button.js';
		return $plugin_array;
	}

	public function registerTinyButton($buttons)
	{
		array_push($buttons, 'gc_social_wall_button');
   		return $buttons;
	}

	/**
	 * Add some scripts, styles and localizations
	 */
	public function scriptsAndStyles()
	{
		wp_enqueue_style('font-awesome', \__::FONT_AWESOME_CSS);
		wp_enqueue_style('gc_social_wall', GCLIB_URL.'css/gc_social_wall.css');

		wp_enqueue_script('jquery');
		wp_enqueue_script('masonry', GCLIB_URL.'js/masonry.js', array('jquery'));
		wp_enqueue_script('gc_social_wall', GCLIB_URL.'js/gc_social_wall.js', array('jquery'));
		wp_localize_script('gc_social_wall', 'gc_social_wall', array(
			'container'     => '.bricks-content',
			'item_selector' => '.brick'
			)
		);
	}

	/**
	 * Initialize agregator
	 */
	private function agregatorInit()
	{		
		$this->agregator = new Feeds\Agregator();
		$this->agregator->registerFeed(new Feeds\Twitter());
		$this->agregator->registerFeed(new Feeds\Facebook());
		$this->agregator->registerFeed(new Feeds\Post());
		$this->agregator->registerFeed(new Feeds\YouTube());	
		$this->agregator->registerFeed(new Feeds\Vimeo());
		$this->agregator->registerFeed(new Feeds\Instagram());
	}

	public function updateFeed()
	{		
		$feeds        = $this->agregator->getFeeds();
		$msgs_by_time = $this->agregator->getMessages($this->global_settings['count']);
		$bricks       = '';
		$feed_buttons = '';

		foreach ($feeds as $feed) 
		{
			if($this->global_settings[self::FIELD_FEEDS][$feed->getName()] == 'on')
				$feed_buttons.= $this->wrapFeedButton($feed);
		}

		foreach ($msgs_by_time as $messages) 
		{
			if(is_array($messages))
			{
				foreach ($messages as $msg) 
				{
					if($this->global_settings[self::FIELD_FEEDS][$msg->type] == 'on')
						$bricks.= $this->wrapBrick($msg);
				}
			}
		}
		?>
		<div class="bricks">
			<nav>
				<ul class="bricks-buttons">
					<?php echo $feed_buttons; ?>
				</ul>
			</nav>
			<div class="bricks-content">
				<?php echo $bricks; ?>
			</div>
		</div>
		
		<?php
	}

	/**
	 * Wrap single brick to HTML code
	 * @param  object $obj --- [Message] object - to wrap HTML code
	 * @return string      --- HTML code
	 */
	private function wrapBrick($obj)
	{
		$twitter_account = Feeds\Twitter::getOptions();
		$twitter_account = isset($twitter_account['account']) ? $twitter_account['account'] : '';

		$img = $obj->picture != '' ? sprintf('<img src="%s">', $obj->picture) : '';
		$link_text = sprintf(
			'%s<br><small>posted %s</small>',
			$obj->author,
			$this->getElapsedTime(strtotime($obj->date))
		);

		$text = $obj->text == '' ? '' : sprintf('<section><div class="text">%s</div></section>', \__::cutText($obj->text, $this->global_settings['max_symbols'])); 
		ob_start();
		?>
		<div class="brick <?php echo $obj->type; ?>">
			<ul class="share-panel">
				<li class="facebook">
					<a href="<?php printf(self::SHARE_URL_FACEBOOK, urlencode($obj->link)); ?>" onclick="sharePopup(this, event)">
						<i class="fa fa-facebook"></i>
					</a>
				</li>
				<li class="twitter">
					<a href="<?php printf(self::SHARE_URL_TWITTER, urlencode($obj->link), $twitter_account); ?>" onclick="sharePopup(this, event)">
						<i class="fa fa-twitter"></i>
					</a>
				</li>
				<li class="google-plus">
					<a href="<?php printf(self::SHARE_URL_GOOGLE_PLUS, urlencode($obj->link)); ?>" onclick="sharePopup(this, event)">
						<i class="fa fa-google-plus"></i>
					</a>
				</li>
				<li class="linkedin">
					<a href="<?php printf(self::SHARE_URL_LINKEDIN, urlencode($obj->link)); ?>" onclick="sharePopup(this, event)">
						<i class="fa fa-linkedin"></i>
					</a>
				</li>
			</ul>
			<header>
				<?php echo $img; ?>
			</header>
			<?php echo $text; ?>
			<footer>
				<a href="<?php echo $obj->link; ?>" target="_blank">
					<div class="brick-type">
						<i class="fa <?php echo $obj->icon; ?>"></i>
					</div>
					<div class="txt">
						<?php echo $link_text; ?>		
					</div>
				</a>
			</footer>
		</div>
		<?php
		$var = ob_get_contents();
		ob_end_clean();
		return $var;
	}

	/**
	 * Wrap feed switcher button 
	 * @param  object $feed --- [Feed] object
	 * @return string       --- HTML code
	 */
	private function wrapFeedButton($feed)
	{
		return sprintf(
			'<li class="%1$s"><a href="%1$s" onclick="filterToggle(event, this)"><i class="fa fa-2x %2$s"></i></a></li>', 
			$feed->getName(), $feed->getIcon()
		);
	}

	/**
	 * Get elapsed time array
	 * @return array --- elapsed time
	 */
	public function getElapsedTime($time)
	{
		$str   = '';
		$items = array();
		$time  = time() - $time;
		$res   = array(
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
	    	$items[] = $value.' '.$key.' ';
	    }    
	    $items = array_slice($items, 0, 2);
	    return implode(' ', $items).' ago';
	}

	/**
	 * Initialize page setting. Plugin setting.
	 */
	private function pageSettingsInit()
	{

		$ccollection_vimeo = new Controls\ControlsCollection(
			array(
				new Controls\Text(
					'Account', 
					array('default-value' => 'user4075991'),
					array('placeholder' => 'Vimeo account')
				),
				new Controls\Text(
					'Custom icon', 
					array('description' => 'You can select the desired icon from here <a href="http://fortawesome.github.io/Font-Awesome/icons/" target="_blank">Font Awesome</a> and paste in the field or register your own icon in "YourTheme/style.css".', 'default-value' => 'fa-vimeo-square'), 
					array('placeholder' => 'Icon')
				)
			)
		);

		$ccollection_facebook = new Controls\ControlsCollection(
			array(		
				new Controls\Text('Account', array('default-value' => 'whitehouse'), array('placeholder' => 'Facebook page')),
				new Controls\Text('APP ID', array('default-value' => '802383316448078'), array('placeholder' => 'Application ID')),
				new Controls\Text('APP KEY', array('default-value' => '970b61246640d52ac45bfa8bf596e6d5'), array('placeholder' => 'Application key')),
				new Controls\Text('Custom icon', array('description' => 'You can select the desired icon from here <a href="http://fortawesome.github.io/Font-Awesome/icons/" target="_blank">Font Awesome</a> and paste in the field or register your own icon in "YourTheme/style.css".', 'default-value' => 'fa-facebook'), array('placeholder' => 'Icon'))
			)
		);

		$ccollection_twitter = new Controls\ControlsCollection(
			array(		
				new Controls\Text('Account', array('default-value' => 'whitehouse'), array('placeholder' => 'Twitter user')),
				new Controls\Text('Consumer key', array('default-value' => 'aMY4Zsnn2KYi5TZkTCr9NlMuF'), array('placeholder' => 'Cunsumer key')),
				new Controls\Text('Consumer secret', array('default-value' => 'vxkz9T7QQWUmqnJbkf7Eg8aHvFOCdcSMVMZrfbUPdNbw7nuYx9'), array('placeholder' => 'Consumer secret')),
				new Controls\Text('OAuth token', array('default-value' => '2717095358-aRUmevpNvioRb52xkFYls0Q7ldf9cIo2PjJzsqG'), array('placeholder' => 'Token')),
				new Controls\Text('OAuth token secret', array('default-value' => 'woklRm4IAnMK5dEkXCAlSboirK4qlUmYcYNkRVddPIbl4'), array('placeholder' => 'Token secret')),
				new Controls\Text('Custom icon', array('description' => 'You can select the desired icon from here <a href="http://fortawesome.github.io/Font-Awesome/icons/" target="_blank">Font Awesome</a> and paste in the field or register your own icon in "YourTheme/style.css".', 'default-value' => 'fa-twitter'), array('placeholder' => 'Icon'))
			)
		);

		$post_types = get_post_types(array('public' => true));
		$post_types = \__::unsetKeys(array('attachment'), $post_types);

		$ccollection_post_type = new Controls\ControlsCollection(
			array(						
				new Controls\Select('Post type', array('values' => $post_types, 'description' => 'You can select your own post type')),
				new Controls\Text('Include categories', array('description' => 'You need type category ids separated by commas. Like this: 1,2,3,4'), array('placeholder' => 'Categories')),
				new Controls\Text('Custom icon', array('description' => 'You can select the desired icon from here <a href="http://fortawesome.github.io/Font-Awesome/icons/" target="_blank">Font Awesome</a> and paste in the field or register your own icon in "YourTheme/style.css".', 'default-value' => 'fa-wordpress'), array('placeholder' => 'Icon'))
			)
		);

		$ccollection_youtube = new Controls\ControlsCollection(
			array(		
				new Controls\Text('Account', array('default-value' => 'vevo'), array('placeholder' => 'YouTube chanel')),
				new Controls\Text('Custom icon', array('description' => 'You can select the desired icon from here <a href="http://fortawesome.github.io/Font-Awesome/icons/" target="_blank">Font Awesome</a> and paste in the field or register your own icon in "YourTheme/style.css".', 'default-value' => 'fa-youtube'), array('placeholder' => 'Icon'))
			)
		);

		$ccollection_global = new Controls\ControlsCollection(
			array(		
				new Controls\Text(
					'Max symbols', 
					array(
						'default-value' => '200',
						'description'   => 'Maximum symbols per one message. Example if maximum symbols = 8: Hello world! ( before ) | Hello... ( after )'
					), 
					array('placeholder' => 'Maximum symbols per message')
				),
				new Controls\Text(
					'Messages per feed', 
					array(
						'default-value' => '10',
						'description'   => 'Limit messages per one feed.'
					), 
					array('placeholder' => 'Limit messages per one feed.')
				),
				new Controls\Checkbox('Facebook', array('default-value' => 'on', 'label' => 'Show messages from Facebook')),
				new Controls\Checkbox('Twitter', array('default-value' => 'on', 'label' => 'Show messages from Twiiter')),
				new Controls\Checkbox('Post type', array('default-value' => 'on', 'label' => 'Show messages from WordPress Post type')),
				new Controls\Checkbox('YouTube', array('default-value' => 'on', 'label' => 'Show messages from YouTube')),
				new Controls\Checkbox('Vimeo', array('default-value' => 'on', 'label' => 'Show messages from Vimeo')),
				new Controls\Checkbox('Instagram', array('default-value' => 'on', 'label' => 'Show images from Instagram')),
			)
		);

		$ccollection_instagram = new Controls\ControlsCollection(
			array(		
				new Controls\Select(
					'Search type',
					array(
						'values' => array(
							array(Feeds\Instagram::POPULAR_ITEMS, 'Popular tems'),
							array(Feeds\Instagram::SEARCH_BY_TAG, 'Search by tag'),
							array(Feeds\Instagram::LOCATION_ID, 'Location id'),
							array(Feeds\Instagram::USER_FEED, 'User feed')
						),
						'description' => 'Select a search option.'
					)
				),
				new Controls\Text(
					'Query',
					array('description' => 'Search query.'),
					array('placeholder' => 'Search query')
				),
				new Controls\Text(
					'Client ID', 
					array('default-value' => '1515b124cf42481db64cacfb96132345'), 
					array('placeholder' => 'ID')
				),
				new Controls\Text(
					'Client secret', 
					array('default-value' => '0cd36d36a6d34f28a7dc978fbce46a35'), 
					array('placeholder' => 'Secret')
				),
				new Controls\Text(
					'Custom icon', 
					array(
						'description' => 'You can select the desired icon from here <a href="http://fortawesome.github.io/Font-Awesome/icons/" target="_blank">Font Awesome</a> and paste in the field or register your own icon in "YourTheme/style.css".', 
						'default-value' => 'fa-instagram'
					), 
					array('placeholder' => 'Icon')
				)
			)
		);
		
		$section_vimeo = new Admin\Section(
			'Vimeo',
			array(
				'prefix'   => 'gc_v_',
				'tab_icon' => 'fa-vimeo-square'
			),
			$ccollection_vimeo
		);

		$section_facebook  = new Admin\Section(
			'Facebook', 
			array(
				'prefix'   => 'gc_fb_',
				'tab_icon' => 'fa-facebook' 
			), 
			$ccollection_facebook
		);
		$section_twitter   = new Admin\Section(
			'Twitter', 
			array(
				'prefix'   => 'gc_tw_',
				'tab_icon' => 'fa-twitter' 
			), 
			$ccollection_twitter
		);
		$section_post_type = new Admin\Section(
			'Post Type', 
			array(
				'prefix'   => 'gc_pt_',
				'tab_icon' => 'fa-wordpress' 
			), 
			$ccollection_post_type
		);
		$section_youtube   = new Admin\Section(
			'YouTube', 
			array(
				'prefix'   => 'gc_yt_',
				'tab_icon' => 'fa-youtube' 
			), 
			$ccollection_youtube
		);
		$section_instagram   = new Admin\Section(
			'Instagram', 
			array(
				'prefix'   => 'gc_ins_',
				'tab_icon' => 'fa-instagram' 
			), 
			$ccollection_instagram
		);
		$section_global    = new Admin\Section(
			'Global settings', 
			array(
				'prefix'   => 'gc_gs_',
				'tab_icon' => 'fa-cog'
			), 
			$ccollection_global
		);

		$this->page_settings = new Admin\Page(
			'GC Social wall', array(), 
			array(
				$section_global, $section_facebook,
				$section_twitter, $section_post_type,
				$section_youtube, $section_vimeo,
				$section_instagram
			)
		);
	}

	/**
	 * Get Global setting
	 * @return array --- facebook options
	 */
	public function getGlobalSettingsOptions()
	{
		return array(
			'max_symbols' => (int) get_option('gc_gs_max_symbols'),
			'count'       => (int) get_option('gc_gs_messages_per_feed'),
			self::FIELD_FEEDS => array(
				'facebook'    => get_option('gc_gs_facebook'),
				'twitter'     => get_option('gc_gs_twitter'),
				'post'        => get_option('gc_gs_post_type'),
				'youtube'     => get_option('gc_gs_youtube'),
				'vimeo'       => get_option('gc_gs_vimeo'),
				'instagram'   => get_option('gc_gs_instagram')
			)
		);
		
	}
}
// =========================================================
// LAUNCH
// =========================================================
$GLOBALS['gc_social_wall'] = new GCSocialWall();





