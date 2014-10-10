<?php

namespace Feeds;

class Agregator{                                 
	//                                       __  _          
	//     ____  _________  ____  ___  _____/ /_(_)__  _____
	//    / __ \/ ___/ __ \/ __ \/ _ \/ ___/ __/ / _ \/ ___/
	//   / /_/ / /  / /_/ / /_/ /  __/ /  / /_/ /  __(__  ) 
	//  / .___/_/   \____/ .___/\___/_/   \__/_/\___/____/  
	// /_/              /_/                                 
	private $feeds;
	//                    __  __              __    
	//    ____ ___  ___  / /_/ /_  ____  ____/ /____
	//   / __ `__ \/ _ \/ __/ __ \/ __ \/ __  / ___/
	//  / / / / / /  __/ /_/ / / / /_/ / /_/ (__  ) 
	// /_/ /_/ /_/\___/\__/_/ /_/\____/\__,_/____/  
	public function __construct($feeds = array())
	{
		$this->feeds = $feeds;

		// ==============================================================
		// AJAX methods initialization
		// ==============================================================
		add_action('wp_ajax_getMessages', array(&$this, 'getMessages'));
		add_action('wp_ajax_nopriv_getMessages', array(&$this, 'getMessages'));
		add_action('wp_ajax_cleanCache', array(&$this, 'cleanCache'));
		add_action('wp_ajax_nopriv_cleanCache', array(&$this, 'cleanCache'));
		// =========================================================
		// SHORTCODE
		// =========================================================
		add_shortcode('gc_social_wall', array(&$this, 'displayFeeds'));
	}

	/**
	 * Add feed 
	 * @param  iFeed  $feed --- feed object
	 */
	public function registerFeed(Feed $feed)
	{
		$this->feeds[$feed->getName()] = $feed;
	}

	/**
	 * Remove feed from collection
	 * @param  iFeed  $feed --- feed object
	 */
	public function removeFeed(Feed $feed)
	{
		unset($this->feeds[$feed->getName()]);
	}

	/**
	 * Get all registered feeds
	 * @return array --- feeds collection
	 */
	public function getFeeds()
	{
		return (array) $this->feeds;
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
	 * Wrap feed navigate buttons
	 * @param  string $hide_buttons --- social wall option
	 * @param  string $buttons --- buttons HTML code
	 * @return string --- buttons wrapped HTML code
	 */
	private function wrapFeedButtons($hide_buttons, $buttons)
	{
		if($hide_buttons == 'on') return '';
		return sprintf('<nav><ul class="bricks-buttons">%s</ul></nav>', $buttons);
	}

	/**
	 * Display all feeds with buttons panel
	 * @return string --- HTML code
	 */
	public function displayFeeds()
	{		
		$feeds           = $this->getFeeds();
		$bricks          = '';
		$feed_buttons    = '';
		$feed_to_show    = '';
		$global_settings = \GCSocialWall::getGlobalSettingsOptions();

		foreach ($feeds as $feed) 
		{
			if($global_settings[\GCSocialWall::FIELD_FEEDS][$feed->getName()] == 'on')
			{
				$feed_to_show .= sprintf('getMessages("%s"); ', $feed->getName());
				$feed_buttons .= $this->wrapFeedButton($feed);
			}
		}
		ob_start();
		?>
		<div class="bricks">
			<?php echo $this->wrapFeedButtons($global_settings['hide_buttons'], $feed_buttons); ?>
			<div class="bricks-content">
				<?php echo $bricks; ?>
			</div>
		</div>
		<script>
			jQuery(document).ready(function(){
				isotopeInit();
				<?php echo $feed_to_show; ?>
				setTimeout(sortPosts, 3000);
				setTimeout(sortPosts, 6000);
				setTimeout(sortPosts, 16000);
			});
		</script>
		<?php
		
		$var = ob_get_contents();
		ob_end_clean();
		return $var;
	}

	/**
	 * Get messages from Feed. [AJAX]
	 */
	public function getMessages()
	{
		$count    = 10;
		$offset   = 0;
		$messages = array();
		$data     = array();

		if(isset($_POST['feed']) AND strlen($_POST['feed']))
		{
			$count = intval($_POST['count']);
			$feed  = $_POST['feed'];
			if(isset($this->feeds[$feed]))
			{
				$f = $this->feeds[$feed];
				$hash  = $f->getHashRequestOptions(
					array(
						'count'  => $count,
						'offset' => $offset
					)
				);
				
				$messages = \__::getSetCache($hash, function() use($count, $offset, $f) { return (array) $f->getMessages($count, $offset); });

				foreach ($messages as $msg) 
				{
					$data['html'] .= $msg->getHTML();
				}
				$data['html'] = @mb_convert_encoding($data['html'], 'utf-8', mb_detect_encoding($data['html']));
				$data['result'] = true;
				$data['hash'] = $hash;
				echo json_encode($data);
			}
		}
		die();
	}

	/**
	 * Clean all cache
	 */
	public function cleanCache()
	{
		$arr = array();

		foreach ($this->feeds as $f) 
		{
			$count = intval($_POST['count']);
			$hash  = $f->getHashRequestOptions(
				array(
					'count'  => $count,
					'offset' => 0
				)
			);
			
			$arr[] = array(
				delete_transient($hash),
				$hash
			);
		}
		echo json_encode($arr);
		die();
	}

}