<?php 
namespace Admin;

abstract class BaseWithControls extends Base{
	//                                       __  _          
	//     ____  _________  ____  ___  _____/ /_(_)__  _____
	//    / __ \/ ___/ __ \/ __ \/ _ \/ ___/ __/ / _ \/ ___/
	//   / /_/ / /  / /_/ / /_/ /  __/ /  / /_/ /  __(__  ) 
	//  / .___/_/   \____/ .___/\___/_/   \__/_/\___/____/  
	// /_/              /_/                                 
	private $prefix;
	public $controls;
	//                    __  __              __    
	//    ____ ___  ___  / /_/ /_  ____  ____/ /____
	//   / __ `__ \/ _ \/ __/ __ \/ __ \/ __  / ___/
	//  / / / / / /  __/ /_/ / / / /_/ / /_/ (__  ) 
	// /_/ /_/ /_/\___/\__/_/ /_/\____/\__,_/____/  
	public function __construct($title, $args = array(), \Controls\ControlsCollection $controls = null)
	{		
		parent::__construct($title, $args);
		$this->controls = $controls;
		$this->prefix   = isset($this->options['prefix']) ? $this->options['prefix'] : $this->name.'_';

		// =========================================================
		// SECTION INITIALIZATION
		// =========================================================
		if($this->controls)
		{
			$tmp = $this->controls->getControls();			
			foreach ($tmp as &$ctrl) 
			{
				$ctrl->setName($this->prefix.$ctrl->getName());
			}	$this->controls->init();					
		}		
	}
}