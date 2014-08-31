<?php

namespace Feeds;

abstract class Feed{
	const CACHE_ON = FALSE;
	//                                       __  _          
	//     ____  _________  ____  ___  _____/ /_(_)__  _____
	//    / __ \/ ___/ __ \/ __ \/ _ \/ ___/ __/ / _ \/ ___/
	//   / /_/ / /  / /_/ / /_/ /  __/ /  / /_/ /  __(__  ) 
	//  / .___/_/   \____/ .___/\___/_/   \__/_/\___/____/  
	// /_/              /_/                                 
	protected $options;
	private $obj;

	//                    __  __              __    
	//    ____ ___  ___  / /_/ /_  ____  ____/ /____
	//   / __ `__ \/ _ \/ __/ __ \/ __ \/ __  / ___/
	//  / / / / / /  __/ /_/ / / / /_/ / /_/ (__  ) 
	// /_/ /_/ /_/\___/\__/_/ /_/\____/\__,_/____/  
	public function __construct($options)
	{
		$this->options = $options;
	}

	/**
	 * Set Cache
	 * @param string  $key    
	 * @param string  $val    
	 * @param integer $time   
	 * @param string  $prefix 
	 */
	public function setCache($key, $val, $time = 3600, $prefix = 'cheched-')
	{		
		set_transient($prefix.$key, $val, $time);
	}

	/**
	 * Get Cache
	 * @param  string $key    
	 * @param  string $prefix 
	 * @return mixed
	 */
	public function getCache($key, $prefix = 'cheched-')
	{		
		if(self::CACHE_ON)
		{
			$cached   = get_transient($prefix.$key);
			if (false !== $cached) return $cached;	
		}
		return false;
	}

	/**
	 * Get class name
	 * @return string --- class name
	 */
	public function getName()
	{
		$name = get_called_class();	
		$name = explode('\\', $name);	
		return strtolower(end($name));
	}

	/**
	 * Get formatted messages
	 * @param  integer $count  --- messages to return
	 * @param  integer $offset --- offset messages
	 * @return array           --- messages array
	 */
	abstract public function getMessages($count = 5, $offset = 0);	

}