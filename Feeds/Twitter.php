<?php

namespace Feeds;

class Twitter extends Feed{
	//                          __              __      
	//   _________  ____  _____/ /_____ _____  / /______
	//  / ___/ __ \/ __ \/ ___/ __/ __ `/ __ \/ __/ ___/
	// / /__/ /_/ / / / (__  ) /_/ /_/ / / / / /_(__  ) 
	// \___/\____/_/ /_/____/\__/\__,_/_/ /_/\__/____/  
	const HASHTAG_PATTERN = 'https://twitter.com/hashtag/%s?src=hash';	                                                 
	//                    __  __              __    
	//    ____ ___  ___  / /_/ /_  ____  ____/ /____
	//   / __ `__ \/ _ \/ __/ __ \/ __ \/ __  / ___/
	//  / / / / / /  __/ /_/ / / / /_/ / /_/ (__  ) 
	// /_/ /_/ /_/\___/\__/_/ /_/\____/\__,_/____/  
	public function __construct($options = array())
	{
		parent::__construct($options);
		$this->obj = new \sdk\TwitterOAuth\TwitterOAuth(	
			$this->options['consumer_key'],
			$this->options['consumer_secret'],
			$this->options['oauth_token'],
			$this->options['oauth_token_secret']
		);
	}

	public function getMessages($count = 5, $offset = 0)
	{
		$user   = $this->options['account'];		
		$query  = sprintf('https://api.twitter.com/1.1/statuses/user_timeline.json?count=%s&screen_name=%s', $count, urlencode($user));
		$tweets = $this->obj->get($query);		
		
		return $this->convert($tweets);
	}

	/**
	 * Convert from twitter type to messages array objects
	 * @param  array $arr --- twitter type array objects
	 * @return array      --- message type array objects
	 */
	private function convert($arr)
	{
		$messages = array();			
		if(is_array($arr))
		{
			foreach ($arr as $tweet) 
			{				
				$picture = $this->getAllMediaURLs($tweet);
				$link    = sprintf(
					'https://twitter.com/%s/status/%s', 
					$tweet->user->screen_name, 
					$tweet->id_str
				);
				// ==============================================================
				// Agregator
				// ==============================================================
				$agregator = new \Feeds\Panels\PanelAgregator(
					array(
						'middle' => array($this->getAuthorPanel($tweet)),
						'below_text' => array($this->getCounters($tweet))
					)
				);
				// ==============================================================
				// Message
				// ==============================================================
				array_push($messages, new Message(
						$tweet->text,
						$link,
						$tweet->created_at,
						$tweet->user->name,
						$picture,
						$this->getName(),
						$this->getIcon(),
						$agregator,
						self::HASHTAG_PATTERN
					)
				);

			}	
		}
		return $messages;
	}

	/**
	 * Get all media URLs from tweet
	 * @param  object $tweet --- tweet object
	 * @return string        --- media URLs separated ","
	 */
	private function getAllMediaURLs($tweet)
	{
		$URLs = array();
		if(!isset($tweet->entities->media) OR !is_array($tweet->entities->media)) return '';
		foreach ($tweet->entities->media as $el) 
		{
			array_push($URLs, $el->media_url);
		}
		if(count($URLs) > 0) return implode(',', $URLs);
		return '';
	}

	/**
	 * Get author PanelLink
	 * @param  object $tweet --- author object
	 * @return mixed --- PanelLink if succes | null if not
	 */
	private function getAuthorPanel($tweet)
	{
		if(!$this->showAuthorPanel()) return null;
		$author_link = sprintf('https://twitter.com/%s', $tweet->user->screen_name);
		return new \Feeds\Panels\PanelLink(
			array(
				sprintf(
					'<img width="%s" class="%s" alt="%s" src="%s">',
					30,
					'circle', 
					$tweet->user->name,
					$tweet->user->profile_image_url
				),
				sprintf(
					'<div class="txt"><b class="title">%s</b><br><small>%s</small></div>',
					$tweet->user->name,
					$tweet->user->location
				)
			), 
			'panel', 
			$author_link
		);
	}

	/**
	 * Get counter Panel
	 * @param  object $tweet --- post object
	 * @return mixed --- Panel if success | null if not
	 */
	private function getCounters($tweet)
	{
		if(!$this->showCounters()) return null;
		return new \Feeds\Panels\Panel(
			array(
				'<ul>',
				sprintf(
					'<li><i class="fa %s"></i> %s</li>',
					'fa-retweet',
					intval($tweet->retweet_count)
				),
				sprintf(
					'<li><i class="fa %s"></i> %s</li>',
					'fa-star',
					intval($tweet->favorite_count)
				),
				'</ul>'
			),
			'counts'
		);
	}

	/**
	 * Get feed message/button default icon
	 * @return string
	 */
	public static function getDefaultIcon()
	{
		return 'fa-twitter';
	}

	/**
	 * Get options from database
	 * @return array --- options collection
	 */
	public static function getOptions()
	{
		return array(
			'account'            => get_option('gc_tw_account'),
			'consumer_key'       => get_option('gc_tw_consumer_key'),
			'consumer_secret'    => get_option('gc_tw_consumer_secret'),
			'oauth_token'        => get_option('gc_tw_oauth_token'),
			'oauth_token_secret' => get_option('gc_tw_oauth_token_secret'),
			'author_panel'       => (string) get_option('gc_tw_author_panel'),
			'counters'           => (string) get_option('gc_tw_counters'),
			'icon'    			 => get_option('gc_tw_custom_icon')
		);
	}      	                                      
}