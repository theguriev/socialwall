<?php
namespace Feeds\Panels;

abstract class Base{
	//                                       __  _          
	//     ____  _________  ____  ___  _____/ /_(_)__  _____
	//    / __ \/ ___/ __ \/ __ \/ _ \/ ___/ __/ / _ \/ ___/
	//   / /_/ / /  / /_/ / /_/ /  __/ /  / /_/ /  __(__  ) 
	//  / .___/_/   \____/ .___/\___/_/   \__/_/\___/____/  
	// /_/              /_/                                 
	public $items;
	protected $class;

	//                    __  __              __    
	//    ____ ___  ___  / /_/ /_  ____  ____/ /____
	//   / __ `__ \/ _ \/ __/ __ \/ __ \/ __  / ___/
	//  / / / / / /  __/ /_/ / / / /_/ / /_/ (__  ) 
	// /_/ /_/ /_/\___/\__/_/ /_/\____/\__,_/____/  
	public function __construct($items = array(), $class = '')
	{
		$this->items = $items;
		$this->class = $class;
	}

	/**
	 * Get items HTML
	 * @return string --- HTML code
	 */
	public function getItemsHTML()
	{
		return implode('', $this->items);
	}
	
	/**
	 * Get panel HTML code
	 * @return string --- HTML code
	 */
	abstract public function getHTML();
}