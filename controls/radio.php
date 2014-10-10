<?php
namespace Controls;

class Radio extends Control{	
	//                    __  __              __    
	//    ____ ___  ___  / /_/ /_  ____  ____/ /____
	//   / __ `__ \/ _ \/ __/ __ \/ __ \/ __  / ___/
	//  / / / / / /  __/ /_/ / / / /_/ / /_/ (__  ) 
	// /_/ /_/ /_/\___/\__/_/ /_/\____/\__,_/____/  
	public function __construct($title, $meta_hidden = array(), $meta_visible = array())
	{	
		$meta_visible = array_merge(
			array(
				'class'       => 'control-radio',
				'type'        => 'radio'	
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
		$radios = '';
		$this->meta['value'] = $value ? $value : '';		

		if(is_array($this->meta['values']))
		{
			foreach ($this->meta['values'] as $item) 
			{				
				$radios.= $this->getRadio($item, $value);
			}
		}
		$control = $radios;

		return $this->getTitleHTML().$control.$this->getDescriptionHTML();
	}

	/**
	 * Wrap radio to html tags
	 * @param  mixed $item   --- value or value | label
	 * @param  string $value --- value
	 * @return string        --- HTML code
	 */
	private function getRadio($item, $value)
	{
		$meta = $this->getMetaVisible();

		if(!is_array($item)) $item = array($item, $item);
		
		$meta['id'] = $meta['name'].'-'.$item[0];

		$label = new Label(
			$item[1], 
			array(
				'container' => 'label', 
				'for'       => $meta['id'],
				'class'     => 'control-radio-label'
			)
		);

		$radio = sprintf(
			'<div class="control-radio-wrap"><input %1$s value="%2$s" %3$s>%4$s</div>',
			\__::joinArray($meta),
			$item[0],
			checked($item[0], $value, false),
			$label->getHTML()
		);

		return $radio;
	}
}