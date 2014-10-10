<?php
namespace Feeds\Panels;

class PanelAgregator{
	//                                       __  _          
	//     ____  _________  ____  ___  _____/ /_(_)__  _____
	//    / __ \/ ___/ __ \/ __ \/ _ \/ ___/ __/ / _ \/ ___/
	//   / /_/ / /  / /_/ / /_/ /  __/ /  / /_/ /  __(__  ) 
	//  / .___/_/   \____/ .___/\___/_/   \__/_/\___/____/  
	// /_/              /_/                                 
	private $panels;

	//                    __  __              __    
	//    ____ ___  ___  / /_/ /_  ____  ____/ /____
	//   / __ `__ \/ _ \/ __/ __ \/ __ \/ __  / ___/
	//  / / / / / /  __/ /_/ / / / /_/ / /_/ (__  ) 
	// /_/ /_/ /_/\___/\__/_/ /_/\____/\__,_/____/  
	public function __construct($panels = array())
	{
		$this->panels = array_merge($this->getDefaults(), $panels);
	}

	/**
	 * Get default panels
	 * @return array --- default
	 */
	public function getDefaults()
	{
		$defaults        = array();
		$allow_locations = $this->getAllowLocations();

		foreach ($allow_locations as $location) 
		{
			$defaults[$location] = '';
		}
		return $defaults;
	}

	/**
	 * Get allow locations
	 * @return array --- locations
	 */
	public function getAllowLocations()
	{
		return array('top', 'middle', 'bottom', 'above_text', 'below_text');
	}

	/**
	 * Allow this location or no
	 * @param  string  $location --- location name
	 * @return boolean --- true if success | false if not
	 */
	public function isAllow($location)
	{
		return in_array($location, $this->getAllowLocations());
	}

	/**
	 * Add panel 
	 * @param  iFeed  $panel --- panel object
	 */
	public function registerPanel(Base $panel, $location = 'top')
	{
		if(!$this->isAllow($location)) return false;
		$this->panels[$location][] = $panel;
		return true;
	}

	/**
	 * Remove panel from collection
	 * @param  iPanel  $panel --- panel object
	 */
	public function removePanel($location, $id)
	{
		if(!$this->isAllow($location)) return false;
		if(isset($this->panels[$location][$id]))
			unset($this->panels[$location][$id]);
		return true;
	}

	/**
	 * Get all registered panels
	 * @return array --- panels collection
	 */
	public function getPanels()
	{
		return (array) $this->panels;
	}

	/**
	 * Get all panel from location
	 * @param  string $location --- location name
	 * @return string --- HTML code
	 */
	public function getLocationHTML($location)
	{
		$html = '';
		if(!$this->isAllow($location)) return '';

		if($this->panels[$location])
		{
			foreach ($this->panels[$location] as $panel) 
			{
				if($panel instanceof Base)
				{
					$html.= $panel->getHTML();
				}
			}
		}
		return $html;
	}
}