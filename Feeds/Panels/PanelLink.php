<?php
namespace Feeds\Panels;

class PanelLink extends Base{
	//                                       __  _          
	//     ____  _________  ____  ___  _____/ /_(_)__  _____
	//    / __ \/ ___/ __ \/ __ \/ _ \/ ___/ __/ / _ \/ ___/
	//   / /_/ / /  / /_/ / /_/ /  __/ /  / /_/ /  __(__  ) 
	//  / .___/_/   \____/ .___/\___/_/   \__/_/\___/____/  
	// /_/              /_/                                 
	private $link;
	private $link_target;

	//                    __  __              __    
	//    ____ ___  ___  / /_/ /_  ____  ____/ /____
	//   / __ `__ \/ _ \/ __/ __ \/ __ \/ __  / ___/
	//  / / / / / /  __/ /_/ / / / /_/ / /_/ (__  ) 
	// /_/ /_/ /_/\___/\__/_/ /_/\____/\__,_/____/  
	public function __construct($items = array(), $class = 'panel', $link = '', $link_target = '_blank')
	{
		$this->link = $link;
		$this->link_target = $link_target;
		parent::__construct($items, $class);
	}

	/**
	 * Get panel HTML code
	 * @return string --- HTML code
	 */
	public function getHTML()
	{
		return sprintf(
				'<a href="%s" target="%s" class="%s">%s</a>',
				$this->link,
				$this->link_target,
				$this->class,
				$this->getItemsHTML()
			);
	}
}