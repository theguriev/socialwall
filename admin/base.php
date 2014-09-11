<?php 
namespace Admin;

abstract class Base{
	//                                       __  _          
	//     ____  _________  ____  ___  _____/ /_(_)__  _____
	//    / __ \/ ___/ __ \/ __ \/ _ \/ ___/ __/ / _ \/ ___/
	//   / /_/ / /  / /_/ / /_/ /  __/ /  / /_/ /  __(__  ) 
	//  / .___/_/   \____/ .___/\___/_/   \__/_/\___/____/  
	// /_/              /_/                                 
	protected $title;
	protected $name;	
	protected $options;

	//                    __  __              __    
	//    ____ ___  ___  / /_/ /_  ____  ____/ /____
	//   / __ `__ \/ _ \/ __/ __ \/ __ \/ __  / ___/
	//  / / / / / /  __/ /_/ / / / /_/ / /_/ (__  ) 
	// /_/ /_/ /_/\___/\__/_/ /_/\____/\__,_/____/  
	public function __construct($title, $args = array())
	{
		$this->title    = $title;		
		$this->options  = $args;	
		$this->name     = isset($this->options['name']) ? $this->options['name'] : \__::formatName($title);		
		$this->options  = $args;

		$this->options['name'] = $this->name;

		add_action('admin_enqueue_scripts', array(&$this, 'adminScriptsAndStyles'));
		add_action('admin_enqueue_scripts', array(&$this, 'adminScriptsAndScripts'));
	}

	/**
     * Add styles to admin panel
     */
    public function adminScriptsAndStyles()
    {
        wp_enqueue_style('font-awesome', \__::FONT_AWESOME_CSS);  
        wp_enqueue_style('gcpage', GCLIB_URL.\__::BASE_CSS );    
        wp_enqueue_style('wp-color-picker');  
    }

    /**
     * Add scripts to admin panel
     */
    public function adminScriptsAndScripts()
    {
    	if(function_exists('wp_enqueue_media')) wp_enqueue_media();    	
    	wp_enqueue_script('wp-color-picker');
    	wp_enqueue_script('media-uploader', GCLIB_URL.'js/media.js', array('jquery'));
    }

}