<?php
namespace Controls;

abstract class Control{
	//                                       __  _          
	//     ____  _________  ____  ___  _____/ /_(_)__  _____
	//    / __ \/ ___/ __ \/ __ \/ _ \/ ___/ __/ / _ \/ ___/
	//   / /_/ / /  / /_/ / /_/ /  __/ /  / /_/ /  __(__  ) 
	//  / .___/_/   \____/ .___/\___/_/   \__/_/\___/____/  
	// /_/              /_/                                 		
	protected $meta_hidden;
	protected $meta_visible;
	public $title;	
	private $name;	

	//                    __  __              __    
	//    ____ ___  ___  / /_/ /_  ____  ____/ /____
	//   / __ `__ \/ _ \/ __/ __ \/ __ \/ __  / ___/
	//  / / / / / /  __/ /_/ / / / /_/ / /_/ (__  ) 
	// /_/ /_/ /_/\___/\__/_/ /_/\____/\__,_/____/  
	public function __construct($title, $meta_hidden = array(), $meta_visible = array())
	{
		$meta_hidden = array_merge(
			array(
				'description'      => '',			
				'default-value'    => '',
				'show_title'       => true,
				'show_description' => true
			), 
			$meta_hidden
		);

		$meta_visible = array_merge(
			array(
				'name' => null							
			), 
			$meta_visible
		);
		

		$this->meta_hidden  = $meta_hidden;
		$this->meta_visible = $meta_visible;
		
		$this->title = $title;		
		$this->setName($this->meta_visible['name']);
	} 	

	/**
	 * Return all meta
	 * @return array --- all meta
	 */
	public function getMeta()
	{
		return array_merge($this->meta_visible, $this->meta_hidden);
	}

	/**
	 * Return visible properties
	 * @return array --- visible properties
	 */
	public function getMetaVisible()
	{
		return (array) $this->meta_visible;
	}

	/**
	 * Return hidden properties
	 * @return array --- hidden properties
	 */
	public function getMetaHidden()
	{
		return (array) $this->meta_hidden;
	}

	/**
	 * Set name ( slug ) object
	 * @param string $name --- name to set
	 */
	public function setName($name)
	{
		$name = (string) $name;
		$this->name = ($name != '') ? $name : \__::formatName($this->title);
		$this->meta_visible['name'] = $this->name;
	}

	/**
	 * Get controls name
	 * @return stromg --- control name
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Set value property
	 * @param mixed $value --- field value
	 */
	public function setValue($value = null)
	{		
		$this->meta_visible['value'] = $value ? $value : '';
	}

	/**
	 * Get default value
	 * @return mixed --- default value
	 */
	public function getDefaultValue()
	{
		return $this->meta_hidden['default-value'];
	}

	/**
	 * Get title
	 * @return string --- title HTML if show_title allow | empty if not
	 */
	public function getTitleHTML()
	{
		if(!$this->meta_hidden['show_title']) return '';
		$label = new Label($this->title);
		return $label->getHTML();
	}

	/**
	 * Get description
	 * @return --- description HTML if show_title allow | empty if not
	 */
	public function getDescriptionHTML()
	{		
		if(!$this->meta_hidden['show_description']) return '';
		$label = new Label(
			$this->meta_hidden['description'], array(
				'container' => 'label',
				'class'     => 'description-block',
				'for'       => $this->getName()
			)
		);
		return $label->getHTML();
	}

	/**
	 * Get called class [TYPE]
	 * @return string --- type in lower case
	 */
	public function getType()
	{
		$type = get_called_class();
		$type = explode('\\', $type);            
        $type = end($type);      
        $type = strtolower($type); 
        return $type;
	}

	/**
	 * Get column preview to WP Grid
	 * @param  mixed $value --- column value
	 * @return string       --- HTML code
	 */
	public function getColumn($value)
	{
		return $value;
	}
   
	/**
	 * This function must be return control html code
	 * @return string --- html code
	 */
	abstract public function getHTML($value = null);
}