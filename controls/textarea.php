<?php
namespace Controls;

class Textarea extends Control{	
	//                    __  __              __    
	//    ____ ___  ___  / /_/ /_  ____  ____/ /____
	//   / __ `__ \/ _ \/ __/ __ \/ __ \/ __  / ___/
	//  / / / / / /  __/ /_/ / / / /_/ / /_/ (__  ) 
	// /_/ /_/ /_/\___/\__/_/ /_/\____/\__,_/____/  
	public function __construct($title, $meta_hidden = array(), $meta_visible = array())
	{	
		$meta_visible = array_merge(
			array(								
				'rows'			   => 8,
				'class'            => 'widefat'				
			), 
			$meta_visible
		);
		parent::__construct($title, $meta_hidden, $meta_visible);							
	}

	/**
	 * Get html code
	 * @param  string $value --- value
	 * @return string        --- HTML code
	 */
	public function getHTML($value = null)
	{			
		$control  = sprintf('<textarea %s>%s</textarea>', \__::joinArray($this->getMetaVisible()), (string) $value);
		return $this->getTitleHTML().$control.$this->getDescriptionHTML();
	}
}