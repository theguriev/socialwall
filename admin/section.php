<?php
namespace Admin;

class Section extends BaseWithControls{
	//                                       __  _          
	//     ____  _________  ____  ___  _____/ /_(_)__  _____
	//    / __ \/ ___/ __ \/ __ \/ _ \/ ___/ __/ / _ \/ ___/
	//   / /_/ / /  / /_/ / /_/ /  __/ /  / /_/ /  __(__  ) 
	//  / .___/_/   \____/ .___/\___/_/   \__/_/\___/____/  
	// /_/              /_/                                 
	private $prefix;		

	//                    __  __              __    
	//    ____ ___  ___  / /_/ /_  ____  ____/ /____
	//   / __ `__ \/ _ \/ __/ __ \/ __ \/ __  / ___/
	//  / / / / / /  __/ /_/ / / / /_/ / /_/ (__  ) 
	// /_/ /_/ /_/\___/\__/_/ /_/\____/\__,_/____/  
	public function __construct($title, $args = array(), \Controls\ControlsCollection $controls = null)
	{
		$args = array_merge(array('class' => '', 'tab_icon' => ''), $args);
		parent::__construct($title, $args, $controls);
		$this->prefix = isset($this->options['prefix']) ? $this->options['prefix'] : $this->name.'_';
		$this->setDefaults();
	}

	/**
	 * Get tab icon.
	 * You can choose your icon here http://fortawesome.github.io/Font-Awesome/icons/
	 * @return string --- HTML code
	 */
	public function getTabIcon()
	{
		if(strlen($this->options['tab_icon']))
		{
			ob_start();
			?>
			<span class="fa-stack">
			  <i class="fa fa-stop fa-stack-2x"></i>
			  <i class="fa <?php echo $this->options['tab_icon']; ?> fa-stack-1x fa-inverse"></i>
			</span>
			<?php
			$var = ob_get_contents();
			ob_end_clean();
			return $var;
		}
		return '';
	}

	/**
	 * Get section HTML
	 * @return string --- HTML code
	 */
	public function getHTML()
	{
		$title  = sprintf('<h3>%s</h3>', $this->title);
		$values = $this->getSectionValues();
		return sprintf(
			'<div class="group basicsettings" id="options-group-%s">%s</div>',
			$this->name,
			sprintf(
				'%s <div class="section-wrapper">%s</div>',
				$title,
				$this->controls->getHTML($values)		
			)
		);
	}

	/**
	 * Get all saved values
	 * @return mixed --- if success function return array | null if not
	 */
	private function getSectionValues()
	{
		if(!$this->controls) return null;

		$values = array();
		$tmp    = $this->controls->getControls();			
		foreach ($tmp as &$ctrl) 
		{
			$values[$ctrl->getName()] = get_option($ctrl->getName());
		}				
		return $values;
	}

	/**
	 * Set defaults
	 */
	public function setDefaults()
	{
		if($this->controls->getCount() > 0)
		{
			$controls = $this->controls->getControls();
			foreach ($controls as $ctrl) 
			{
				if(get_option($ctrl->getName()) === false) update_option($ctrl->getName(), $ctrl->getDefaultValue());
			}
		}
	}

	/**
	 * Get section prefix
	 * @return string --- prefix
	 */
	public function getPrefix()
	{
		return $this->prefix;
	}

}