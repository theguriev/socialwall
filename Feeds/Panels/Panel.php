<?php
namespace Feeds\Panels;

class Panel extends Base{
	//                    __  __              __    
	//    ____ ___  ___  / /_/ /_  ____  ____/ /____
	//   / __ `__ \/ _ \/ __/ __ \/ __ \/ __  / ___/
	//  / / / / / /  __/ /_/ / / / /_/ / /_/ (__  ) 
	// /_/ /_/ /_/\___/\__/_/ /_/\____/\__,_/____/  
	public function __construct($items = array(), $class = 'panel')
	{
		parent::__construct($items, $class);
	}

	/**
	 * Get panel HTML code
	 * @return string --- HTML code
	 */
	public function getHTML()
	{
		return sprintf(
				'<div class="%s">%s</div>',
				$this->class,
				$this->getItemsHTML()
			);
	}
}