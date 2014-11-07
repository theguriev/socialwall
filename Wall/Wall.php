<?php
namespace Wall;

class Wall{
	//                                       __  _          
	//     ____  _________  ____  ___  _____/ /_(_)__  _____
	//    / __ \/ ___/ __ \/ __ \/ _ \/ ___/ __/ / _ \/ ___/
	//   / /_/ / /  / /_/ / /_/ /  __/ /  / /_/ /  __(__  ) 
	//  / .___/_/   \____/ .___/\___/_/   \__/_/\___/____/  
	// /_/              /_/                                 
	private $controls;
	private $title;
	private $description;

	//                    __  __              __    
	//    ____ ___  ___  / /_/ /_  ____  ____/ /____
	//   / __ `__ \/ _ \/ __/ __ \/ __ \/ __  / ___/
	//  / / / / / /  __/ /_/ / / / /_/ / /_/ (__  ) 
	// /_/ /_/ /_/\___/\__/_/ /_/\____/\__,_/____/  
	public function __construct($title, $description, $controls = null, $prefix = '')
	{
		$this->title       = $title;
		$this->description = $description;
		$this->controls    = new \Controls\ControlsCollection($controls);

		// =========================================================
		// SECTION INITIALIZATION
		// =========================================================
		if($this->controls)
		{
			$tmp = $this->controls->getControls();			
			foreach ($tmp as &$ctrl) 
			{
				$ctrl->setName($prefix.$ctrl->getName());
			}	$this->controls->init();					
		}	
	}

	/**
	 * Get HTML Code to Social Wall generator
	 * @return string --- HTML code
	 */
	public function getHTML()
	{
		return $this->controls->getHTML();
	}

	/**
	 * Get wall name
	 * @return string --- name
	 */
	public function getName()
	{
		return \__::formatName($this->title);
	}

	/**
	 * Get wall title
	 * @return string --- wall title
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * Get wall description
	 * @return string --- wall description
	 */
	public function getDescription()
	{
		return $this->description;
	}
}