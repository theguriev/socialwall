<?php

namespace Feeds;

class Agregator{
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
			$messages = (array) $feed->getMessages($count, $offset);
			if(count($messages))
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
}