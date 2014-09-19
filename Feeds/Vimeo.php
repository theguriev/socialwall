<?php
namespace Feeds;

class Vimeo extends Feed{
	//                     __             __      
	//   _________  ____  / /____  ____  / /______
	//  / ___/ __ \/ __ \/ __/ _ \/ __ \/ __/ ___/
	// / /__/ /_/ / / / / /_/  __/ / / / /_(__  ) 
	// \___/\____/_/ /_/\__/\___/_/ /_/\__/____/  
	const VIDEOS_URL    = 'http://vimeo.com/api/v2/%s/videos.json';	

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
		$json = \__::fileGetContentsCurl(sprintf(self::VIDEOS_URL, $this->options['account']));
		$json = (array) json_decode($json);
		$json = array_splice($json, $offset, $count);
		
		return $this->convert($json);
	}

	public function convert($arr)
	{
		$messages = array();				
		if(is_array($arr))
		{
			foreach ($arr as $el) 
			{	
				array_push($messages, new Message(
						strip_tags($el->description),
						$el->url,
						$el->upload_date,
						$el->title,
						$el->thumbnail_large,
						$this->getName(),
						$this->getIcon()
					));
			}	
		}
		return $messages;
	}

	/**
	 * Get feed message/button default icon
	 * @return string
	 */
	public static function getDefaultIcon()
	{
		return 'fa-vimeo-square';
	}

	/**
	 * Get options from database
	 * @return array --- options collection
	 */
	public static function getOptions()
	{
		return array(
			'account' => get_option('gc_v_account'),
			'icon'    => get_option('gc_v_custom_icon')
		);
	}
}