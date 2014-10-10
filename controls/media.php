<?php
namespace Controls;

class Media extends Control{	
	//                    __  __              __    
	//    ____ ___  ___  / /_/ /_  ____  ____/ /____
	//   / __ `__ \/ _ \/ __/ __ \/ __ \/ __  / ___/
	//  / / / / / /  __/ /_/ / / / /_/ / /_/ (__  ) 
	// /_/ /_/ /_/\___/\__/_/ /_/\____/\__,_/____/  
	public function __construct($title, $meta_hidden = array(), $meta_visible = array())
	{			
		parent::__construct($title, $meta_hidden, $meta_visible);	
		// =========================================================
		// 	Add small thu
		// =========================================================
		add_image_size('thumbnail-small', 50, 50, true);					
	}

	/**
	 * Get html code
	 * @param  string $value --- value
	 * @return string        --- HTML code
	 */
	public function getHTML($value = null)
	{	
		$text = new Text(
			$this->getName(), 
			array(
				'show_title'       => false,
				'show_description' => false				
			)
		);

		$button  = '<button type="button" class="button button-upload" onclick="uploadMedia(event, this)">Upload</button>';		
		$control = sprintf('<div class="control-media">%s</div>', $text->getHTML($value).$button.$this->getScreenshot($value));
		
		return $this->getTitleHTML().$control.$this->getDescriptionHTML();
	}

	/**
	 * Get screenshot HTML
	 * @param  string $value --- value
	 * @return string        --- HTML code
	 */
	private function getScreenshot($value = '')
	{
		$screenshot = '';
		if((string)$value != '')
		{
			$screenshot = sprintf('<img src="%s"><a class="remove-image" href="#" onclick="removeMedia(event, this)"><i class="fa fa-trash-o fa-2x"></i></a>', $value);
		}
		return sprintf('<div class="control-media-screenshot"><div class="screenshot">%s</div></div>', $screenshot);
	}

	/**
	 * Get column preview to WP Grid
	 * @param  mixed $value --- column value
	 * @return string       --- HTML code
	 */
	public function getColumn($value)
	{		
		if($value)
		{
			$id = \__::getAttachmentIDFromSrc($value);
			if($id)
			{
				$img = wp_get_attachment_image_src($id, 'thumbnail-small');
				if(isset($img[0]))
				{
					return sprintf(
						'<img src="%1$s" alt="image" height="%3$s" width="%2$s">',
						$img[0],
						$img[1],
						$img[2]
					);
				}
			}	
		}
		return '';		
	}
}