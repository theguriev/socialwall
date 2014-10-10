<?php
namespace Feeds;

class Facebook extends Feed{
	//                          __              __      
	//   _________  ____  _____/ /_____ _____  / /______
	//  / ___/ __ \/ __ \/ ___/ __/ __ `/ __ \/ __/ ___/
	// / /__/ /_/ / / / (__  ) /_/ /_/ / / / / /_(__  ) 
	// \___/\____/_/ /_/____/\__/\__,_/_/ /_/\__/____/  
	const URL             = 'https://graph.facebook.com/v2.1/%s/posts?%s';	
	const PICTURE         = 'https://graph.facebook.com/v2.1/%s/picture?%s';	
	const AUTHOR_URL      = 'https://facebook.com/%s';	
	const HASHTAG_PATTERN = 'https://www.facebook.com/hashtag/%s';
	const LINK            = 'https://www.facebook.com/permalink.php?story_fbid=%s&id=%s';

	//                                       __  _          
	//     ____  _________  ____  ___  _____/ /_(_)__  _____
	//    / __ \/ ___/ __ \/ __ \/ _ \/ ___/ __/ / _ \/ ___/
	//   / /_/ / /  / /_/ / /_/ /  __/ /  / /_/ /  __(__  ) 
	//  / .___/_/   \____/ .___/\___/_/   \__/_/\___/____/  
	// /_/              /_/                                 
	private $user_picture;                                                
	//                    __  __              __    
	//    ____ ___  ___  / /_/ /_  ____  ____/ /____
	//   / __ `__ \/ _ \/ __/ __ \/ __ \/ __  / ___/
	//  / / / / / /  __/ /_/ / / / /_/ / /_/ (__  ) 
	// /_/ /_/ /_/\___/\__/_/ /_/\____/\__,_/____/  
	public function __construct($options = array())
	{
		parent::__construct($options);
	}

	public function getMessages($count = 5, $offset = 0)
	{
		$args = array(
			'access_token' => sprintf('%s|%s', $this->options['app_id'], $this->options['app_key']),
			'fields' => 'type,link,picture,from,icon,message,created_time,comments.limit(1).summary(true),likes.limit(1).summary(true),shares'
		);
		
		$url = sprintf(
			self::URL, 
			$this->options['account'],
			http_build_query($args)
		);
		$this->user_picture = $this->getUserPicture();
		$result = \__::fileGetContentsCurl($url);
		$result = json_decode($result);
		$feed = $this->convert($result->data);
		$feed = array_slice($feed, $offset, $count);
		
		return $feed;
	}

	/**
	 * Get user picture
	 * @return string --- user picture if succes | empty if not
	 */
	private function getUserPicture()
	{
		$args_picture = array(
			'access_token' => sprintf('%s|%s', $this->options['app_id'], $this->options['app_key']),
			'redirect' => 'false',
			'fields' => 'url'
		);

		$url_picture = sprintf(
			self::PICTURE,
			$this->options['account'],
			http_build_query($args_picture)
		);
		$result_picture = \__::fileGetContentsCurl($url_picture);
		$result_picture = json_decode($result_picture);

		if(isset($result_picture->data->url))
			return $result_picture->data->url;
		return '';
	}

	/**
	 * Convert from twitter type to messages array objects
	 * @param  array $arr --- twitter type array objects
	 * @return array      --- message type array objects
	 */
	private function convert($arr)
	{		
		$messages = array();	

		foreach ($arr as $el) 
		{
			$ids  = explode('_', $el->id); 
			$link = sprintf(self::LINK, $ids[1], $ids[0]);
			$msg  = $this->getMessage($el);

			if($msg == '' AND ($el->type == 'status' OR $el->type == 'link')) continue;
			// ==============================================================
			// Agregator
			// ==============================================================
			$agregator = new \Feeds\Panels\PanelAgregator(
				array(
					'middle' => array($this->getAuthorPanel($el)),
					'below_text' => array($this->getCounters($el))
				)
			);

			array_push($messages, new Message(
					$msg,
					$link,
					$el->created_time,
					$el->from->name,
					$this->getImage($el->picture),
					$this->getName(),
					$this->getIcon(),
					$agregator,
					self::HASHTAG_PATTERN
				));
		}			
		return $messages;
	}

	/**
	 * Get message from facebook object
	 * @param  object $el --- facebook object
	 * @return string --- message
	 */
	private function getMessage($el)
	{
		$str = '';
		if(isset($el->story)) $str.= $el->story;
		if(isset($el->message)) $str.= $el->message;
		return $str;
	}
	
	/**
	 * Get image from array
	 * @return string
	 */
	private function getImage($src)
	{		
		$src = str_replace('/v/', '/', $src);
		$src = str_replace('/s130x130/', '/s/', $src);
		return $src;
	}        

	/**
	 * Get author PanelLink
	 * @param  object $el --- author object
	 * @return mixed --- PanelLink if succes | null if not
	 */
	private function getAuthorPanel($el)
	{
		if(!$this->showAuthorPanel()) return null;
		$author_link = sprintf(self::AUTHOR_URL, $this->options['account']);
		return new \Feeds\Panels\PanelLink(
			array(
				sprintf(
					'<img width="%s" class="%s" alt="%s" src="%s">',
					30,
					'circle', 
					$el->from->name,
					$this->user_picture
				),
				sprintf(
					'<div class="txt"><b class="title">%s</b><br><small>%s</small></div>',
					$el->from->name,
					$el->from->category
				)
			), 
			'panel', 
			$author_link
		);
	}

	/**
	 * Get counter Panel
	 * @param  object $el --- post object
	 * @return mixed --- Panel if success | null if not
	 */
	private function getCounters($el)
	{
		if(!$this->showCounters()) return null;
		return new \Feeds\Panels\Panel(
			array(
				'<ul>',
				sprintf(
					'<li><i class="fa %s"></i> %s</li>',
					'fa-share-alt',
					intval($el->shares->count)
				),
				sprintf(
					'<li><i class="fa %s"></i> %s</li>',
					'fa-heart',
					intval($el->likes->summary->total_count)
				),
				sprintf(
					'<li><i class="fa %s"></i> %s</li>',
					'fa-comments',
					intval($el->comments->summary->total_count)
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
		return 'fa-facebook';
	}      

	/**
	 * Get options from database
	 * @return array --- options collection
	 */
	public static function getOptions()
	{
		return array(
			'account'      => get_option('gc_fb_account'),
			'app_id'       => get_option('gc_fb_app_id'),
			'app_key'      => get_option('gc_fb_app_key'),
			'author_panel' => get_option('gc_fb_author_panel'),
			'counters'     => get_option('gc_fb_counters'),
			'icon'         => get_option('gc_fb_custom_icon')
		);
	}  
}