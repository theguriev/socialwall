<?php
namespace Controls;

class Label{	
	//                                       __  _          
	//     ____  _________  ____  ___  _____/ /_(_)__  _____
	//    / __ \/ ___/ __ \/ __ \/ _ \/ ___/ __/ / _ \/ ___/
	//   / /_/ / /  / /_/ / /_/ /  __/ /  / /_/ /  __(__  ) 
	//  / .___/_/   \____/ .___/\___/_/   \__/_/\___/____/  
	// /_/              /_/                                 
	public $title;
	public $meta;

	//                    __  __              __    
	//    ____ ___  ___  / /_/ /_  ____  ____/ /____
	//   / __ `__ \/ _ \/ __/ __ \/ __ \/ __  / ___/
	//  / / / / / /  __/ /_/ / / / /_/ / /_/ (__  ) 
	// /_/ /_/ /_/\___/\__/_/ /_/\____/\__,_/____/  
	public function __construct($title, $args = array())
	{	
		$this->meta = array_merge(
			array(
				'container' => 'h4',
				'class'     => 'heading'
			), 
			$args
		);			
		$this->title = $title;				
	}

	/**
	 * Get html code
	 * @param  string $value --- value
	 * @return string        --- HTML code
	 */
	public function getHTML($value = null)
	{		
		$meta = \__::unsetKeys(array('container'), $this->meta);
				
		return sprintf(
			'<%1$s %3$s>%2$s</%1$s>', 
			$this->meta['container'], 
			$this->title,
			\__::joinArray($meta)
		);
	}
}