<?php
namespace Feeds;

class Message{
	//                                       __  _          
	//     ____  _________  ____  ___  _____/ /_(_)__  _____
	//    / __ \/ ___/ __ \/ __ \/ _ \/ ___/ __/ / _ \/ ___/
	//   / /_/ / /  / /_/ / /_/ /  __/ /  / /_/ /  __(__  ) 
	//  / .___/_/   \____/ .___/\___/_/   \__/_/\___/____/  
	// /_/              /_/                                 
	private $text;  
	private $link; 
	private $date; 
	private $author;
	private $picture;
	private $type; 

	//                    __  __              __    
	//    ____ ___  ___  / /_/ /_  ____  ____/ /____
	//   / __ `__ \/ _ \/ __/ __ \/ __ \/ __  / ___/
	//  / / / / / /  __/ /_/ / / / /_/ / /_/ (__  ) 
	// /_/ /_/ /_/\___/\__/_/ /_/\____/\__,_/____/  
	public function __construct($text = '', $link = '', $date = '', $author = '', $picture = '', $type = '')
	{
		$this->text    = $text;
		$this->link    = $link;
		$this->date    = $date;
		$this->author  = $author;
		$this->picture = $picture;
		$this->type    = $type;
	}

	/**
	 * Get properties
	 * @param  string $key --- property name
	 * @return mixed       --- property value
	 */
	public function __get($key)
	{
		if(property_exists($this, $key))
		{
			return $this->$key;
		}
	}
}