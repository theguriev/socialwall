<?php

namespace Feeds;

class Agregator{
	//                          __              __      
	//   _________  ____  _____/ /_____ _____  / /______
	//  / ___/ __ \/ __ \/ ___/ __/ __ `/ __ \/ __/ ___/
	// / /__/ /_/ / / / (__  ) /_/ /_/ / / / / /_(__  ) 
	// \___/\____/_/ /_/____/\__/\__,_/_/ /_/\__/____/  
	const CACHE_ON = true;	
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
	private $feeds;
	//                    __  __              __    
	//    ____ ___  ___  / /_/ /_  ____  ____/ /____
	//   / __ `__ \/ _ \/ __/ __ \/ __ \/ __  / ___/
	//  / / / / / /  __/ /_/ / / / /_/ / /_/ (__  ) 
	// /_/ /_/ /_/\___/\__/_/ /_/\____/\__,_/____/  
	public function __construct($feeds = array())
	{
		$this->feeds = $feeds;

		add_action('wp_ajax_getMessages', array(&$this, 'getMessage'));
		add_action('wp_ajax_nopriv_getMessages', array(&$this, 'getMessage'));
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
	 * Get all posts
	 * @param  integer $count  --- msg per feed
	 * @param  integer $offset --- offset msg
	 * @return array           --- all feeds with msg
	 */
	public function getMessages($count = 5, $offset = 0)
	{		
		foreach ($this->feeds as $key => $feed) 
		{
			$hash  = $feed->getHashRequestOptions(
				array(
					'count'  => $count,
					'offset' => $offset
				)
			);
			$cache = $this->getCache($hash);
			if($cache !== false)
			{
				$messages = $cache;
			}
			else
			{	
				$messages = (array) $feed->getMessages($count, $offset);
				$this->setCache($hash, $messages);
			}
			if(is_array($messages) AND count($messages))
			{
				foreach ($messages as &$msg) 
				{
					$time           = strtotime($msg->date);
					$posts[$time][] = $msg;
				}
			}
		}
		krsort($posts);
		var_dump($posts);
		die();
		return $posts;
	}

	public function getMessage()
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
				$hash  = $this->feeds[$feed]->getHashRequestOptions(
					array(
						'count'  => $count,
						'offset' => $offset
					)
				);
				$cache = $this->getCache($hash);
				if($cache !== false)
				{
					$messages = $cache;
				}
				else
				{	
					$messages = (array) $this->feeds[$feed]->getMessages($count, $offset);
					$this->setCache($hash, $messages);
				}

				foreach ($messages as $msg) 
				{
					$data['html'] .= $this->wrapBrick($msg);
				}
				$data['result'] = true;
				echo json_encode($data);
			}
		}
		die();
	}

	private function sortMessagesByTime($messages)
	{
		if(!is_array($messages) AND !count($messages)) return $messages;
		if(is_array($messages) AND count($messages))
		{
			foreach ($messages as &$msg) 
			{
				$time           = strtotime($msg->date);
				$posts[$time][] = $msg;
			}
		}
		krsort($posts);
		return $posts;
	}

	/**
	 * Wrap single brick to HTML code
	 * @param  object $obj --- [Message] object - to wrap HTML code
	 * @return string      --- HTML code
	 */
	private function wrapBrick($obj)
	{
		$twitter_account = Twitter::getOptions();
		$twitter_account = isset($twitter_account['account']) ? $twitter_account['account'] : '';

		$img = $obj->picture != '' ? sprintf('<img src="%s">', $obj->picture) : '';
		$link_text = sprintf(
			'%s<br><small>posted %s</small>',
			$obj->author,
			$this->getElapsedTime(strtotime($obj->date))
		);

		$text = $obj->text == '' ? '' : sprintf('<section><div class="text">%s</div></section>', \__::cutText($obj->text, 200)); 
		ob_start();

		?>
		<div class="brick <?php echo $obj->type; ?>" data-time="<?php echo strtotime($obj->date); ?>" data-time-no="<?php echo $obj->date; ?>">
			<?php
			echo '<pre>';
			var_dump($obj, mb_strlen($obj->text), $this->global_settings);
			echo '</pre>';
			?>
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
	 * Set Cache
	 * @param string  $key    
	 * @param string  $val    
	 * @param integer $time   
	 * @param string  $prefix 
	 */
	public function setCache($key, $val, $time = 36000)
	{		
		$val = is_array($val) ? serialize($val) : $val;
		$val = base64_encode($val);
		set_transient($key, $val, $time);
	}

	/**
	 * Get Cache
	 * @param  string $key    
	 * @param  string $prefix 
	 * @return mixed
	 */
	public function getCache($key)
	{	
		$cached = false;	
		if(self::CACHE_ON)
		{
			$cached = get_transient($key);
			if($cached !== false)
			{
				$cached     = base64_decode($cached);
				$cached_arr = unserialize($cached);

				if(is_array($cached_arr))
				{
					$cached = $cached_arr;
				}
			} 
		}
		return false !== $cached ? $cached : false;
	}
}