<?php

namespace Feeds;

class Agregator{
	//                          __              __      
	//   _________  ____  _____/ /_____ _____  / /______
	//  / ___/ __ \/ __ \/ ___/ __/ __ `/ __ \/ __/ ___/
	// / /__/ /_/ / / / (__  ) /_/ /_/ / / / / /_(__  ) 
	// \___/\____/_/ /_/____/\__/\__,_/_/ /_/\__/____/  
	const CACHE_ON = true;	                                                 
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
		
		return $posts;
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