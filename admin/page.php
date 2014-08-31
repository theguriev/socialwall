<?php
namespace Admin;

class Page extends Base{
	//                                       __  _          
	//     ____  _________  ____  ___  _____/ /_(_)__  _____
	//    / __ \/ ___/ __ \/ __ \/ _ \/ ___/ __/ / _ \/ ___/
	//   / /_/ / /  / /_/ / /_/ /  __/ /  / /_/ /  __(__  ) 
	//  / .___/_/   \____/ .___/\___/_/   \__/_/\___/____/  
	// /_/              /_/                                 
	private $sections;

	//                    __  __              __    
	//    ____ ___  ___  / /_/ /_  ____  ____/ /____
	//   / __ `__ \/ _ \/ __/ __ \/ __ \/ __  / ___/
	//  / / / / / /  __/ /_/ / / / /_/ / /_/ (__  ) 
	// /_/ /_/ /_/\___/\__/_/ /_/\____/\__,_/____/  
	public function __construct($title, $args = array(),  $sections = array())
	{		
		parent::__construct($title, $args);

		$defaults = array(
            'icon_code'   => '',
            'menu_slug'   => $this->name,
            'parent_page' => '',
            'capability'  => 'administrator',
            'page_title'  => ucwords($title),
            'menu_title'  => ucwords($title)
        );
        $this->options  = array_merge($defaults, $args);
        $this->sections = $sections;

        // =========================================================
        // PAGE INITIALIZATION
        // =========================================================
        add_action('admin_menu', array(&$this, 'addPage'));
        add_action('admin_init', array(&$this, 'registerSettings'));
        
        add_action('admin_enqueue_scripts', array(&$this, 'adminScriptsAndScripts'));
	}

	/**
     * Add page to menu
     */
    public function addPage()
    {
        if($this->isParent())
        {
        	add_menu_page(
                $this->options['page_title'], 
                $this->options['menu_title'], 
                $this->options['capability'], 
                $this->options['menu_slug'], 
                array(&$this, 'getHTML')
            );            
        }
        else
        {
            add_submenu_page(
                $this->options['parent_page'], 
                $this->options['page_title'], 
                $this->options['menu_title'], 
                $this->options['capability'], 
                $this->options['menu_slug'], 
                array(&$this, 'getHTML')
            );     
        }
        if($this->options['icon_code'] != '') add_action('admin_enqueue_scripts', array(&$this, 'addMenuIcon'));
    }   

	/**
	 * Register all controls to save and sanitize
	 */
	public function registerSettings()
    {
        if(is_array($this->sections))
        {
            foreach ($this->sections as $section) 
            {    
                foreach ($section->controls->getControls() as $ctrl) 
                {                
                    register_setting($this->name, $ctrl->getName(), array(&$this, 'sanitize'));
                }
            }    
        }
    }

    /**
     * Render page
     */
    public function getHTML()
    {        
        ?>
        <div id="optionsframework-wrap" class="wrap">
        	<h2><?php echo esc_html($this->title); ?></h2>
			<h2 class="nav-tab-wrapper">
			    <?php echo $this->renderNavTab(); ?>
			</h2>
			<?php settings_errors($this->name); ?>
			<div id="optionsframework-metabox" class="metabox-holder">
			    <div id="optionsframework" class="postbox">
					<form action="options.php" method="post">
						<?php settings_fields($this->name); ?>
						<?php echo $this->renderSections(); ?>
						<div id="optionsframework-submit">
							<input type="submit" class="button-primary" name="update" value="<?php esc_attr_e( 'Save Options', 'gc' ); ?>" />
							<input type="submit" class="reset-button button-secondary" name="reset" value="<?php esc_attr_e( 'Restore Defaults', 'gc' ); ?>" onclick="return confirm( '<?php print esc_js( __( 'Click OK to reset. Any theme settings will be lost!', 'gc' ) ); ?>' );" />
							<div class="clear"></div>
						</div>
					</form>
				</div> <!-- / #container -->
			</div><!-- / #optionsframework-metabox -->
        </div> <!-- / #optionsfraemwork-wrap -->

        <?php
    }

    /**
     * Get rendered nav tab HTML
     * @return string --- HTML code
     */
    private function renderNavTab()
    {
    	$str     = '';    	
    	if(!is_array($this->sections) OR count($this->sections) <= 0) return '';
    	foreach ($this->sections as $section) 
    	{
    		$str.= sprintf(
    			'<a id="options-group-%1$s-tab" class="nav-tab %2$s" title="%3$s" href="#options-group-%1$s">%3$s</a>',
    			$section->name,
    			$section->options['class'],
    			esc_attr($section->title)
    		);    		
    	}
    	return $str;
    }

    /**
     * Render all sections
     * @return string --- HTML code
     */
    private function renderSections()
    {
    	$str = '';
    	$counter = 0;

    	if(!is_array($this->sections) OR count($this->sections) <= 0) return '';

    	foreach ($this->sections as $section) 
    	{
    		$str.= $section->getHTML();
    	}
    	return $str;
    }


    /**
     * TODO
     * @param  mixed $input 
     * @return mixed
     */
    public function sanitize($input)
    {           
        if(isset($_POST['reset']))
        {
            $key = str_replace('sanitize_option_', '', current_filter());

            add_settings_error( 'options-framework', 'restore_defaults', __( 'Default options restored.', 'gc' ), 'updated fade' );
            foreach ($this->sections as $section) 
            {
                $ctrl = $section->controls->getControlByName($key);                
                if($ctrl)
                {                    
                    $input = $ctrl->getDefaultValue();
                }                
            }           
        }     
        
        return $input;
    }

    /**
     * Thi page is Parent?
     * @return boolean true if yes | false if no
     */
    public function isParent()
    {
    	return $this->options['parent_page'] == '';
    }

    /**
     * Add scripts to admin panel
     */
    public function adminScriptsAndScripts()
    {
        wp_enqueue_script('gcpage', GCLIB_URL.'/js/page.js', array('jquery'));
    }

    /**
     * Add Font Awesome icon to menu
     */
    public function addMenuIcon()
    {       
        ?>
        <style>
            #adminmenu #toplevel_page_<?php echo $this->options['menu_slug']; ?> .wp-menu-image:before {
                content: "\<?php echo $this->options['icon_code']; ?>";  
                font-family: 'FontAwesome' !important;
                font-size: 18px !important;
            }
        </style>
        <?php
    }
}