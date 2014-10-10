<?php

namespace Feeds;

class Instagram extends Feed{
	//                          __              __      
	//   _________  ____  _____/ /_____ _____  / /______
	//  / ___/ __ \/ __ \/ ___/ __/ __ `/ __ \/ __/ ___/
	// / /__/ /_/ / / / (__  ) /_/ /_/ / / / / /_(__  ) 
	// \___/\____/_/ /_/____/\__/\__,_/_/ /_/\__/____/  
	const POPULAR_ITEMS     = 0;
	const SEARCH_BY_TAG     = 1;
	const LOCATION_ID       = 2;
	const USER_FEED         = 3;
	const POPULAR_ITEMS_URL = 'https://api.instagram.com/v1/media/popular?client_id=%s&count=%s';
	const SEARCH_BY_TAG_URL = 'https://api.instagram.com/v1/tags/%s/media/recent?client_id=%s&count=%s';
	const LOCATION_ID_URL   = 'https://api.instagram.com/v1/locations/%s/media/recent?client_id=%s&count=%s';
	const USER_FEED_URL     = 'https://api.instagram.com/v1/users/%s/media/recent?client_id=%s&count=%s';
	const AUTHOR_URL        = 'http://instagram.com/%s';      

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
		$URL  = $this->getURL($this->options['search_type'], $this->options['query'], $count);
		$data = \__::fileGetContentsCurl($URL);
		$data = json_decode($data);

		if($data)
		{
			return $this->convert($data->data);	
		}
		return NULL;
	}

	/**
	 * Get request URL
	 * @param  integer $type  --- request type
	 * @param  string  $query --- query
	 * @param  integer $count --- limit items
	 * @return string --- URL 
	 */
	public function getURL($type = 0, $query = '', $count = 5)
	{
		$client_ID = $this->options['client_id'];
		$types     = array(
			self::SEARCH_BY_TAG => self::SEARCH_BY_TAG_URL,
			self::LOCATION_ID => self::LOCATION_ID_URL,
			self::USER_FEED => self::USER_FEED_URL
		);

		if($type)
		{
			return sprintf(
				$types[$type],
				$query,
				$client_ID,
				$count
			);
		}
		return sprintf(
			self::POPULAR_ITEMS_URL,
			$client_ID, $count
		);
	}

	/**
	 * Convert from twitter type to messages array objects
	 * @param  array $arr --- twitter type array objects
	 * @return array      --- message type array objects
	 */
	private function convert($arr)
	{
		$messages = array();			
		if(is_array($arr) AND count($arr))
		{
			foreach ($arr as $el) 
			{	
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
						$el->caption->text,
						$el->link,
						date('Y-m-d H:i:s', intval($el->created_time)),
						$el->caption->from->full_name,
						$el->images->standard_resolution->url,
						$this->getName(),
						$this->getIcon(),
						$agregator,
						''
					));
			}	
		}
		return (array) $messages;
	}

	/**
	 * Get author PanelLink
	 * @param  object $el --- author object
	 * @return mixed --- PanelLink if succes | null if not
	 */
	private function getAuthorPanel($el)
	{
		if(!$this->showAuthorPanel()) return null;
		$author_link  = sprintf(self::AUTHOR_URL, $el->user->username);
		return new \Feeds\Panels\PanelLink(
			array(
				sprintf(
					'<img width="%s" class="%s" alt="%s" src="%s">',
					30,
					'circle', 
					$el->user->full_name,
					$el->user->profile_picture
				),
				sprintf(
					'<div class="txt"><b class="title">%s</b><br><small>%s</small></div>',
					$el->user->full_name,
					$el->user->website
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
					'fa-heart',
					intval($el->likes->count)
				),
				sprintf(
					'<li><i class="fa %s"></i> %s</li>',
					'fa-comments',
					intval($el->comments->count)
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
		return 'fa-instagram';
	}

	/**
	 * Get options from database
	 * @return array --- options collection
	 */
	public static function getOptions()
	{
		return array(
			'search_type'   => intval(get_option('gc_ins_search_type')),
			'query'         => (string) get_option('gc_ins_query'),
			'client_id'     => (string) get_option('gc_ins_client_id'),
			'author_panel'  => (string) get_option('gc_ins_author_panel'),
			'counters'      => (string) get_option('gc_ins_counters'),
			'icon'          => (string) get_option('gc_ins_custom_icon')
		);
	}
	                                             
}