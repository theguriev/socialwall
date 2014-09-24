<?php

namespace Feeds;

class VK extends Feed{
	//                          __              __      
	//   _________  ____  _____/ /_____ _____  / /______
	//  / ___/ __ \/ __ \/ ___/ __/ __ `/ __ \/ __/ ___/
	// / /__/ /_/ / / / (__  ) /_/ /_/ / / / / /_(__  ) 
	// \___/\____/_/ /_/____/\__/\__,_/_/ /_/\__/____/  
	const POPULAR_ITEMS = 0;
	const SEARCH_BY_TAG = 1;
	const LOCATION_ID   = 2;
	const USER_FEED     = 3;

	//                    __  __              __    
	//    ____ ___  ___  / /_/ /_  ____  ____/ /____
	//   / __ `__ \/ _ \/ __/ __ \/ __ \/ __  / ___/
	//  / / / / / /  __/ /_/ / / / /_/ / /_/ (__  ) 
	// /_/ /_/ /_/\___/\__/_/ /_/\____/\__,_/____/  
	public function __construct($options = array())
	{
		parent::__construct($options);
		$this->obj = new \sdk\VK\VkPhpSdk();

		
		$result = $this->obj->api('getProfiles', 
			array(
				'uids'   => '60840761',
				'fields' => 'uid, first_name, last_name, nickname, screen_name, photo_big',
			)
		);
		
		echo '<pre>';
		var_dump($result);
		echo '</pre>';
	}

	public function getMessages($count = 5, $offset = 0)
	{
		
		return NULL;
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
			foreach ($arr as $p) 
			{		
				array_push($messages, new Message(
						$p->caption->text,
						$p->link,
						$p->created_time,
						$p->caption->from->full_name,
						$p->images->standard_resolution->url,
						$this->getName(),
						$this->getIcon()
					));
			}	
		}
		return (array) $messages;
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
			'search_type'   => intval(get_option('gc_ins_search_type')),
			'query'         => (string) get_option('gc_ins_query'),
			'client_id'     => (string) get_option('gc_ins_client_id'),
			'client_secret' => (string) get_option('gc_ins_client_secret'),
			'icon'          => (string) get_option('gc_ins_custom_icon')
		);
	}
	                                             
}