<?php
namespace Controls;

class Select extends Control{	
	//                    __  __              __    
	//    ____ ___  ___  / /_/ /_  ____  ____/ /____
	//   / __ `__ \/ _ \/ __/ __ \/ __ \/ __  / ___/
	//  / / / / / /  __/ /_/ / / / /_/ / /_/ (__  ) 
	// /_/ /_/ /_/\___/\__/_/ /_/\____/\__,_/____/  
	public function __construct($title, $meta_hidden = array(), $meta_visible = array())
	{			
		parent::__construct($title, $meta_hidden, $meta_visible);							
	}

	/**
	 * Get html code
	 * @param  string $value --- value
	 * @return string        --- HTML code
	 */
	public function getHTML($value = null)
	{	
		$this->setValue($value);

		$options      = '';
		$meta_hidden  = $this->getMetaHidden();
		$meta_visible = $this->getMetaVisible();

		if(is_array($meta_hidden['values']))
		{
			foreach ($meta_hidden['values'] as $item) 
			{				
				if(is_array($item))
				{
					$options.= sprintf(
						'<option value="%1$s" %3$s>%2$s</option>', 
						$item[0], 
						$item[1],
						selected( $item[0], $value, false)
					);
				}
				else
				{
					$options.= sprintf(
						'<option value="%1$s" %2$s>%1$s</option>', 
						$item,
						selected( $item, $value, false)
					);
				}
			}
		}
		
		$control = sprintf(
			'<select %s>%s</select>', 
			\__::joinArray($meta_visible),
			$options
		);

		return $this->getTitleHTML().$control.$this->getDescriptionHTML();
	}
}