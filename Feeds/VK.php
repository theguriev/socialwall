<?php

namespace Feeds;

class VK extends Feed{
	//                          __              __      
	//   _________  ____  _____/ /_____ _____  / /______
	//  / ___/ __ \/ __ \/ ___/ __/ __ `/ __ \/ __/ ___/
	// / /__/ /_/ / / / (__  ) /_/ /_/ / / / / /_(__  ) 
	// \___/\____/_/ /_/____/\__/\__,_/_/ /_/\__/____/  
    const SITE       = 'https://vk.com/';
    const AUTHOR_URL = 'https://vk.com/%s';
    const USER_URL   = 'https://api.vk.com/method/users.get?uids=%s&fields=site,uid,first_name,last_name,nickname,screen_name,photo_big';
    const WALL_URL   = 'https://api.vk.com/method/wall.get?domain=%s&count=%s&offset=%s';
    //                                       __  _          
    //     ____  _________  ____  ___  _____/ /_(_)__  _____
    //    / __ \/ ___/ __ \/ __ \/ _ \/ ___/ __/ / _ \/ ___/
    //   / /_/ / /  / /_/ / /_/ /  __/ /  / /_/ /  __(__  ) 
    //  / .___/_/   \____/ .___/\___/_/   \__/_/\___/____/  
    // /_/              /_/                                 
    private $user;

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
		$user = $this->options['account'];
		$wall_url = sprintf(
			self::WALL_URL,
			$user,
			$count,
			$offset
		);

		$wall = \__::fileGetContentsCurl($wall_url);
		$wall = json_decode($wall);

		$user_url   = sprintf(self::USER_URL, $user);
		$this->user = \__::fileGetContentsCurl($user_url);
		$this->user = json_decode($this->user);

		if(is_array($wall->response) AND count($wall->response) > 1)
		{
			$wall = array_slice($wall->response, 1);
		}

		return $this->convert($wall);
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
				$link = sprintf(
					'%1$sid%2$s?w=wall%2$s_%3$s', 
					self::SITE,
					$el->from_id,
					$el->id
				);
				$image = isset($el->attachment->photo) ? $el->attachment->photo->src_xxbig : '';
				$name  = isset($this->user->response[0]->first_name) ? $this->user->response[0]->first_name.' '.$this->user->response[0]->last_name : '';

				// ==============================================================
				// Agregator
				// ==============================================================
				$agregator = new \Feeds\Panels\PanelAgregator(
					array(
						'middle' => array($this->getAuthorPanel($name)),
						'below_text' => array($this->getCounters($el))
					)
				);

				array_push($messages, new Message(
						$el->text,
						$link,
						date('Y-m-d H:i:s', intval($el->date)),
						$name,
						$image,
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
	 * @param string $name --- author name
	 * @return mixed --- PanelLink if succes | null if not
	 */
	private function getAuthorPanel($name)
	{
		if(!$this->showAuthorPanel()) return null;
		$author_link      = sprintf(self::AUTHOR_URL, $this->user->response[0]->screen_name);
		return new \Feeds\Panels\PanelLink(
			array(
				sprintf(
					'<img width="%s" class="%s" alt="%s" src="%s">',
					30,
					'circle', 
					$name,
					$this->user->response[0]->photo_big
				),
				sprintf(
					'<div class="txt"><b class="title">%s</b><br><small>%s</small></div>',
					$name,
					$this->user->response[0]->site
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
				sprintf(
					'<li><i class="fa %s"></i> %s</li>',
					'fa fa-bullhorn',
					intval($el->reposts->count)
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
		return 'fa-vk';
	}

	/**
	 * Get options from database
	 * @return array --- options collection
	 */
	public static function getOptions()
	{
		return array(
			'account'      => (string) get_option('gc_vk_account'),
			'author_panel' => (string) get_option('gc_vk_author_panel'),
			'counters'     => (string) get_option('gc_vk_counters'),
			'icon'         => (string) get_option('gc_vk_custom_icon')
		);
	}
	                                             
}