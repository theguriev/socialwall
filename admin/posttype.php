<?php
namespace Admin;

class PostType extends Base{
	//                    __  __              __    
	//    ____ ___  ___  / /_/ /_  ____  ____/ /____
	//   / __ `__ \/ _ \/ __/ __ \/ __ \/ __  / ___/
	//  / / / / / /  __/ /_/ / / / /_/ / /_/ (__  ) 
	// /_/ /_/ /_/\___/\__/_/ /_/\____/\__,_/____/  
	public function __construct($title, $args = array())
	{        		
		$n = ucwords($title);

        $defaults = array(
            'label'              => $n.'s',
            'singular_name'      => $n,
            'public'             => true,
            'publicly_queryable' => true,
            'query_var'          => true,            
            'rewrite'            => true,
            'capability_type'    => 'post',
            'hierarchical'       => true,
            'menu_position'      => null,
            'supports'           => array('title', 'editor', 'thumbnail'),
            'has_archive'        => true
        );

		$args = array_merge($defaults, $args);
        parent::__construct($title, $args);

		if(!post_type_exists($this->name))
		{            
            add_action('init', array(&$this, 'registerPostType'));
		}
	}

    /**
     * Registers a new post type in the WP db.
     */
    public function registerPostType()
    {        
        register_post_type($this->name, $this->options);
        
        if(isset($this->options['icon_code'])) add_action('admin_enqueue_scripts', array(&$this, 'addMenuIcon'));
    }

    /**
     * Add menu icon
     */
    public function addMenuIcon()
    {       
        ?>
        <style>
            #adminmenu #menu-posts-<?php echo $this->name; ?> .wp-menu-image:before {
                content: "\<?php echo $this->options['icon_code']; ?>";  
                font-family: 'FontAwesome' !important;
                font-size: 18px !important;
                text-shadow: 0 1px 0 rgba(0, 0, 0, 0.7) !important;
            }
        </style>
        <?php
    }
}