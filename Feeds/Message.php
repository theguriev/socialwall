<?php
namespace Feeds;

class Message{
	//                          __              __      
	//   _________  ____  _____/ /_____ _____  / /______
	//  / ___/ __ \/ __ \/ ___/ __/ __ `/ __ \/ __/ ___/
	// / /__/ /_/ / / / (__  ) /_/ /_/ / / / / /_(__  ) 
	// \___/\____/_/ /_/____/\__/\__,_/_/ /_/\__/____/  
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
	private $text;  
	private $link; 
	private $date; 
	private $author;
	private $picture;
	private $type; 
	private $icon;
	private $panel;
	private $hashtag_pattern;

	//                    __  __              __    
	//    ____ ___  ___  / /_/ /_  ____  ____/ /____
	//   / __ `__ \/ _ \/ __/ __ \/ __ \/ __  / ___/
	//  / / / / / /  __/ /_/ / / / /_/ / /_/ (__  ) 
	// /_/ /_/ /_/\___/\__/_/ /_/\____/\__,_/____/  
	public function __construct(
		$text = '', $link = '', $date = '', 
		$author = '', $picture = '', $type = '', 
		$icon = '', \Feeds\Panels\PanelAgregator $panel = null,
		$hashtag_pattern = '%s'
	)
	{
		$this->text    = strip_tags($text);
		$this->link    = $link;
		$this->date    = $date;
		$this->author  = $author;
		$this->picture = $picture;
		$this->type    = $type;
		$this->icon    = (string) $icon;
		$this->panel   = $panel;
		$this->hashtag_pattern = $hashtag_pattern;
	}

	/**
	 * Get properties
	 * @param  string $key --- property name
	 * @return mixed       --- property value
	 */
	public function __get($key)
	{
		if(property_exists($this, $key))
		{
			return $this->$key;
		}
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
	 * Get message brick HTML code
	 * @return HTML code
	 */
	public function getHTML()
	{
		$global_settings = \GCSocialWall::getGlobalSettingsOptions();
		$img = $this->picture != '' ? sprintf('<img src="%s">', $this->picture) : '';
		$link_text = sprintf(
			'%s<br><small>posted %s</small>',
			$this->author,
			$this->getElapsedTime(strtotime($this->date))
		);
		$target = $this->type != 'post' ? '_blank' : '_top';

		$text = '';
		$check_empty_text = $this->text.$this->getLocationHTML('above_text').$this->getLocationHTML('below_text');

		if(strlen($check_empty_text))
		{
			$cutted = \__::cutText((string) $this->text, $global_settings['max_symbols']);
			$cutted = \__::wrapHashtags($cutted, $this->hashtag_pattern);

			$text = sprintf(
				'<section>%s<div class="text">%s</div>%s</section>', 
				$this->getLocationHTML('above_text'),
				$cutted,
				$this->getLocationHTML('below_text')
			); 
		}
		
		ob_start();

		?>
		<div class="brick <?php echo $this->type; ?>" data-time="<?php echo strtotime($this->date); ?>" data-time-no="<?php echo $this->date; ?>">
			<?php echo $this->getSharePanelHTML(); ?>
			<header>
				<?php echo $img; ?>
			</header>
			<?php $this->theLocationHTML('top'); ?>
			<?php echo $text; ?>
			<?php $this->theLocationHTML('middle'); ?>
			<footer>
				<a href="<?php echo $this->link; ?>" target="<?php echo $target; ?>">
					<div class="brick-type">
						<i class="fa <?php echo $this->icon; ?>"></i>
					</div>
					<div class="txt">
						<?php echo $link_text; ?>		
					</div>
				</a>
				<?php $this->theLocationHTML('bottom'); ?>
			</footer>
		</div>
		<?php
		$var = ob_get_contents();
		ob_end_clean();
		return $var;
	}

	/**
	 * Display panel location
	 * @param  string $location --- location name
	 */
	private function theLocationHTML($location = 'top')
	{
		if($this->panel != null)
		{
			echo $this->panel->getLocationHTML($location);
		}
	}

	/**
	 * Get panel location
	 * @param  string $location --- location name
	 */
	private function getLocationHTML($location = 'top')
	{
		if($this->panel != null)
		{
			return $this->panel->getLocationHTML($location);
		}
		return '';
	}

	/**
	 * Get share panel HTML code
	 * @return string --- HTML code
	 */
	public function getSharePanelHTML()
	{
		$twitter_account = Twitter::getOptions();
		$twitter_account = isset($twitter_account['account']) ? $twitter_account['account'] : '';

		ob_start();
		?>
		<ul class="share-panel">
			<li class="facebook">
				<a href="<?php printf(self::SHARE_URL_FACEBOOK, urlencode($this->link)); ?>" onclick="sharePopup(this, event)">
					<i class="fa fa-facebook"></i>
				</a>
			</li>
			<li class="twitter">
				<a href="<?php printf(self::SHARE_URL_TWITTER, urlencode($this->link), $twitter_account); ?>" onclick="sharePopup(this, event)">
					<i class="fa fa-twitter"></i>
				</a>
			</li>
			<li class="google-plus">
				<a href="<?php printf(self::SHARE_URL_GOOGLE_PLUS, urlencode($this->link)); ?>" onclick="sharePopup(this, event)">
					<i class="fa fa-google-plus"></i>
				</a>
			</li>
			<li class="linkedin">
				<a href="<?php printf(self::SHARE_URL_LINKEDIN, urlencode($this->link)); ?>" onclick="sharePopup(this, event)">
					<i class="fa fa-linkedin"></i>
				</a>
			</li>
		</ul>
		<?php
		
		$var = ob_get_contents();
		ob_end_clean();
		return $var;
	}
}