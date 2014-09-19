<?php

namespace Feeds;

abstract class Feed{
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
		if(!count($options)) $options = static::getOptions();
		$this->options = $options;
	}

	/**
	 * Format text in the MD5 key cache
	 * @param  array  $properties --- key pieces
	 * @return string
	 */
	public function formatCacheKey($properties)
	{
		if(is_array($properties)) $properties = implode('_', $properties);
		return md5($properties);
	}

	/**
	 * Get hash (md5) request
	 * @return string --- md5
	 */
	public function getHashRequestOptions($options = array())
	{
		$options         = array_merge($this->options, $options);
		$options['name'] = $this->getName();
		$options['icon'] = $this->getIcon(); // Will be update after changing icon

		$options = \__::joinArray($options);

		return $this->formatCacheKey($options);
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
	 * Get feed message/button icon
	 * @return string
	 */
	public function getIcon()
	{
		$options = array_merge(array('icon' => ''), $this->options);
		$icon = (string) $options['icon'];
		if(strlen($icon)) return $icon;
		return static::getDefaultIcon();
	}

	/**
	 * Get formatted messages
	 * @param  integer $count  --- messages to return
	 * @param  integer $offset --- offset messages
	 * @return array           --- messages array
	 */
	abstract public function getMessages($count = 5, $offset = 0);	

	/**
	 * Get feed message/button icon
	 * @return string
	 */
	abstract public static function getDefaultIcon();

	/**
	 * Get options from database
	 * @return array --- options collection
	 */
	abstract public static function getOptions();

}