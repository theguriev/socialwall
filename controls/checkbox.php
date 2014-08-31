<?php
namespace Controls;

class Checkbox extends Control{	
	//                    __  __              __    
	//    ____ ___  ___  / /_/ /_  ____  ____/ /____
	//   / __ `__ \/ _ \/ __/ __ \/ __ \/ __  / ___/
	//  / / / / / /  __/ /_/ / / / /_/ / /_/ (__  ) 
	// /_/ /_/ /_/\___/\__/_/ /_/\____/\__,_/____/  
	public function __construct($title, $meta_hidden = array(), $meta_visible = array())
	{	
		$meta_visible = array_merge(
			array(	
				'class'       => 'control-checkbox',				
				'type'        => 'checkbox'	
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
		$meta_visible = $this->getMetaVisible();		
		$meta_hidden  = $this->getMetaHidden();	
		$meta_visible['id'] = isset($meta_visible['id']) ? $meta_visible['id'] : $meta_visible['name'];

		$label = new Label(
			$meta_hidden['label'], 
			array(
				'container' => 'label', 
				'for'       => $meta_visible['id'],
				'class'     => 'control-checkbox-label'
			)
		);

		$control = sprintf(
			'<div class="control-checkbox-wrap"><input %s %s>%s</div>',
			\__::joinArray($meta_visible),
			checked('on', $value, false),
			$label->getHTML()
		);

		return $this->getTitleHTML().$control.$this->getDescriptionHTML();
	}

	/**
	 * Get column preview to WP Grid
	 * @param  mixed $value --- column value
	 * @return string       --- HTML code
	 */
	public function getColumn($value)
	{
		$value = (string) $value;		

		return $value == '' ? '<i class="fa fa-circle-thin"></i>' : '<i class="fa fa-circle"></i>';		
	}
}