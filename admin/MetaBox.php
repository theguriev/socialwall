<?php
namespace Admin;

class MetaBox extends BaseWithControls{
	//                    __  __              __    
	//    ____ ___  ___  / /_/ /_  ____  ____/ /____
	//   / __ `__ \/ _ \/ __/ __ \/ __ \/ __  / ___/
	//  / / / / / /  __/ /_/ / / / /_/ / /_/ (__  ) 
	// /_/ /_/ /_/\___/\__/_/ /_/\____/\__,_/____/  
	public function __construct($title, $args = array(),  \Controls\ControlsCollection $controls = null)
	{	        	
		parent::__construct($title, $args, $controls);

		$defaults = array(            
            'post_type' => 'post',
            'context'   => 'normal',
        );
        $this->options = array_merge($defaults, $args);            

        // =========================================================
        // METABOX INITIALIZATION
        // =========================================================
        add_action('post_edit_form_tag', array(&$this, 'postEditFormTag'));  
        add_action('admin_init', array(&$this, 'configureMetaBox'));
	}

    /**
     * Add tag to form
     */
    public function postEditFormTag()
    {
        echo ' enctype="multipart/form-data"';
    }

    /**
     * Configure meta box    
     */
    public function configureMetaBox()
    {   
        add_action('save_post', array(&$this, 'savePost'));           
        add_meta_box($this->name, $this->title, array(&$this, 'getHTML'), $this->options['post_type'], $this->options['context'], 'default');

        add_filter('manage_edit-'.$this->options['post_type'].'_columns', array(&$this, 'columnThumb'));   
        add_action('manage_'.$this->options['post_type'].'_posts_custom_column', array($this, 'columnThumbShow'), 10, 2);           
    }

    /**
     * Register new columns
     * @param  array $columns 
     * @return array
     */
    public function columnThumb($columns)
    {
        $arr = array();
        $ctrls = $this->controls->getControls();
        if($ctrls)
        {
            foreach ($ctrls as $ctrl) 
            {                
                $arr[$ctrl->getName()] = $ctrl->title;
            }
        }        
        
        return array_merge($columns, $arr);
    }

    /**
     * Display new column
     * @param  string  $column  
     * @param  integer $post_id           
     */
    public function columnThumbShow($column, $post_id)
    {         
        $ctrl = $this->controls->getControlByName($column);             
        if($ctrl)
        {            
            $meta = get_post_meta($post_id, $ctrl->getName(), true);
            printf('%s', $ctrl->getColumn($meta));
        }
    }

    /**
     * Helper for checkbox control in admin table
     * @param  string $val --- value
     * @return string      --- css class
     */
    private function circleCheckbox($val)
    {
        if(is_array($val)) return $val;
        return $val == '' ? '<i class="fa fa-circle-thin"></i>' : '<i class="fa fa-circle"></i>';
    }

    /**
     * Render meta box
     * PRINT CONTROLS HTML CODE
     * @param  object $post
     * @param  array $data
     */
    public function getHTML($post)
    {
        wp_nonce_field(plugin_basename(__FILE__), 'jw_nonce');

        $meta  = get_post_custom($post->ID);       
        $ctrls = $this->controls->getControls();        

        if(!$ctrls) return '';       
        foreach ($ctrls as $ctrl) 
        {               
            $value = isset($meta[$ctrl->getName()][0]) ? $meta[$ctrl->getName()][0] : ''; 
            echo $ctrl->getHtml($value);                                    
        }
    }    

    /**
     * When a post saved/updated in the database, this methods updates the meta box params in the db as well.
     */
    public function savePost($post_id)
    {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if ($_POST && !wp_verify_nonce($_POST['jw_nonce'], plugin_basename(__FILE__))) return;

        $controls = $this->controls->getControls();
        if($controls)
        {
            foreach ($controls as $ctrl) 
            {
                if(isset($_POST[$ctrl->getName()]))
                {
                    update_post_meta($post_id, $ctrl->getName(), $_POST[$ctrl->getName()]);
                }    
                else
                {
                    delete_post_meta($post_id, $ctrl->getName());
                }
            }
        }        
    } 
}