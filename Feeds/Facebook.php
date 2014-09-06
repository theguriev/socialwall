<?php

namespace Feeds;

use sdk\Facebook\FacebookSession;
use sdk\Facebook\FacebookRequest;
use sdk\Facebook\FacebookResponse;
use sdk\Facebook\FacebookSDKException;
use sdk\Facebook\FacebookRequestException;
use sdk\Facebook\FacebookAuthorizationException;
use sdk\Facebook\GraphObject;

class Facebook extends Feed{
	//                    __  __              __    
	//    ____ ___  ___  / /_/ /_  ____  ____/ /____
	//   / __ `__ \/ _ \/ __/ __ \/ __ \/ __  / ___/
	//  / / / / / /  __/ /_/ / / / /_/ / /_/ (__  ) 
	// /_/ /_/ /_/\___/\__/_/ /_/\____/\__,_/____/  
	public function __construct($options = array())
	{
		parent::__construct($options);
		FacebookSession::setDefaultApplication(
			$this->options['app_id'],
			$this->options['app_key']
		);
		$this->obj = FacebookSession::newAppSession();

	}

	public function getMessages($count = 5, $offset = 0)
	{
		$cache_key = $this->formatCacheKey(
			array($this->getName(), $count, $offset)
		);
		$ids       = $this->getIDs($count);
		$objects   = array();
		$user      = $this->options['account'];
		$cache     = $this->getCache($cache_key);	
		if($cache)
		{			
			return $cache;
		}	
	
		if(!is_array($ids)) return false;
		foreach ($ids as $id) 
		{
			$request = new FacebookRequest(
				$this->obj,
				'GET',
				sprintf('/%s', $id)
			);
			$response  = $request->execute();
			$objects[] = $response->getGraphObject()->asArray();			
		}
		$feed = $this->convert($objects);

		$this->setCache($cache_key, $feed);
		return $feed;
	}

	private function getIDs($count)
	{
		$request = new FacebookRequest(
			$this->obj, 'GET',
			sprintf('/%s/posts?limit=%d&fields=object_id', $this->options['account'], $count)
		);
		$response = $request->execute();		
		$ids      = $response->getGraphObject()->asArray();
		$res      = array();
		
		if(is_array($ids['data']))
		{
			foreach ($ids['data'] as $std) 
			{
				if(isset($std->object_id)) $res[] = $std->object_id;
			}
		}
		return $res;
	}

	/**
	 * Convert from twitter type to messages array objects
	 * @param  array $arr --- twitter type array objects
	 * @return array      --- message type array objects
	 */
	private function convert($arr)
	{		
		$messages = array();	
		$defaults = array(
			'message'      => '',
			'link'         => '',
			'created_time' => '',
			'picture'      => ''
		);	

		foreach ($arr as $el) 
		{	
			$el = array_merge($defaults, $el);

			array_push($messages, new Message(
					$this->getMsg($el),
					$el['link'],
					$el['created_time'],
					$el['from']->name,
					$this->getImage($el),
					$this->getName(),
					$this->getIcon()
				));
		}			
		return $messages;
	}
	
	/**
	 * Get image from array
	 * @return string
	 */
	private function getImage($obj)
	{		
		if(isset($obj['format']) and is_array($obj['format']))
		{
			$last = end($obj['format']);
			return $last->picture;
		}
		if(isset($obj['images']) and is_array($obj['images']))
		{
			return $obj['images'][0]->source;
		}		
		return $obj->picture;
	}        

	/**
	 * Get message from array
	 * @param  array $obj --- facebook post
	 * @return string     --- message from facebook post
	 */
	private function getMsg($obj)
	{
		if(isset($obj['name'])) return $obj['name'];
		if(isset($obj['message'])) return $obj['message'];
		return '';
	}         

	/**
	 * Get feed message/button icon
	 * @return string
	 */
	public function getIcon()
	{
		return 'fa-facebook';
	}                    
}