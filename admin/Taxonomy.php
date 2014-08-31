<?php
namespace Admin;

class Taxonomy extends BaseWithControls{
	//                    __  __              __    
	//    ____ ___  ___  / /_/ /_  ____  ____/ /____
	//   / __ `__ \/ _ \/ __/ __ \/ __ \/ __  / ___/
	//  / / / / / /  __/ /_/ / / / /_/ / /_/ (__  ) 
	// /_/ /_/ /_/\___/\__/_/ /_/\____/\__,_/____/  
	public function __construct($title, $args = array(),  \Controls\ControlsCollection $controls = null)
	{	        	
		parent::__construct($title, $args, $controls);

        $name        = ucwords($this->title);
        $name_plural = isset($this->options['plural']) ? $this->options['plural'] : $name.'s';
        $name_plural = ucwords($name_plural);
        
        $labels = array(
            'name'                       => __($name_plural),
            'singular_name'              => __($name),
            'search_items'               => __('Search '.$name_plural),
            'popular_items'              => __('Popular '.$name_plural),
            'all_items'                  => __('All '.$name_plural),
            'parent_item'                => null,
            'parent_item_colon'          => null,
            'edit_item'                  => __('Edit '.$name),
            'update_item'                => __('Update '.$name),
            'add_new_item'               => __('Add New '.$name),
            'new_item_name'              => __('New '.$name.' Name' ),
            'separate_items_with_commas' => __('Separate '.$name_plural.' with commas' ),
            'add_or_remove_items'        => __('Add or remove '.$name_plural ),
            'choose_from_most_used'      => __('Choose from the most used '.$name_plural ),
            'not_found'                  => __('No '.$name_plural.' found.' ),
            'menu_name'                  => __($name_plural),
        );

        $defaults = array(
            'hierarchical'          => true,
            'labels'                => $labels,
            'show_ui'               => true,
            'show_admin_column'     => true,
            'query_var'             => true,
            'rewrite'               => array( 'slug' => $this->name ),
            'post_type'             => 'post'
        );

        $this->options = array_merge($defaults, $args);    

        // =========================================================
        // HOOKS
        // =========================================================
        add_action('init', array(&$this, 'registerTaxonomy'));
        if($this->controls->getCount() > 0)
        {           
            add_action($this->name.'_edit_form_fields', array(&$this, 'editFormFields')); 
            add_action($this->name.'_add_form_fields', array(&$this, 'addFormFields'));
            add_action('edited_'.$this->name, array(&$this, 'save'));
            add_action('created_'.$this->name, array(&$this, 'save'));
            add_filter('deleted_term_taxonomy', array(&$this, 'delete'));
        }    
	}

    /**
     * Register new taxonomie
     */
    public function registerTaxonomy()
    {
        register_taxonomy($this->name, $this->options['post_type'], $this->options);
    }

    /**
     * Add form fields
     * @param object $term --- term object
     */
    public function addFormFields($term)
    {
        if($this->controls->getCount() > 0)
        {
            $controls = $this->controls->getControls();
            foreach ($controls as $ctrl)
            {
                echo $ctrl->getHTML();
            }    
        }        
    }

    /**
    * Edit form fields
    * @param object $term --- term object
    */
    public function editFormFields($term)
    {
        $rows = '';
        if($this->controls->getCount() > 0)
        {
            $controls = $this->controls->getControls();
            foreach ($controls as $ctrl)
            {
                $value = get_option(sprintf('tax_%s_%s', $term->term_id, $ctrl->getName()));

                $tmp                           = clone $ctrl;
                $title                         = $tmp->getTitleHTML();
                $description                   = $tmp->getDescriptionHTML();
                $tmp->meta['show_title']       = false;
                $tmp->meta['show_description'] = false;
                $control                       = $tmp->getHTML($value);
                $rows                         .= $this->wrapRow($title, $control.$description);
            }    
        } 
        echo $this->wrapTable($rows);
    }

    /**
     * Save taxonomy
     * @param  integer $term_id --- term id
     */
    public function save($term_id) 
    {   
        if($this->controls->getCount() > 0)
        {
            $controls = $this->controls->getControls();
            foreach ($controls as $ctrl)
            {
                if(isset($_POST[$ctrl->getName()])) update_option(sprintf('tax_%s_%s', $term_id, $ctrl->getName()), $_POST[$ctrl->getName()]);
                else delete_option(sprintf('tax_%s_%s', $term_id, $ctrl->getName()));
            }    
        } 
    }

    /**
     * Delete taxonomy
     * @param  integer $term_id --- term id
     */
    public function delete($term_id) 
    {
        if($_POST['taxonomy'] == $this->name)
        {
            if($this->controls->getCount() > 0)
            {
                $controls = $this->controls->getControls();
                foreach ($controls as $ctrl)
                {
                    if(get_option(sprintf('tax_%s_%s', $term_id, $ctrl->getName()))) delete_option(sprintf('tax_%s_%s', $term_id, $ctrl->getName()));                    
                }    
            } 
        }
    }

    /**
     * Wrap controls to table
     * @param  string $html --- rows with controls
     * @return string       --- HTML code
     */
    private function wrapTable($html)
    {
        ob_start();
        ?>
        <table class="form-table">
            <tbody>
                <?php echo $html; ?>
            </tbody>
        </table>
        <?php
        $var = ob_get_contents();
        ob_end_clean();
        return $var;
    }


    /**
     * Wrap controls to row
     * @param  string $title   --- HTML label
     * @param  string $control --- HTML control and maybe his description
     * @return string          --- HTML table row
     */
    private function wrapRow($title, $control)
    {
        ob_start();
        ?>
        <tr class="form-field">
            <th scope="row">
                <?php echo $title; ?>
            </th>
            <td>
                <?php echo $control; ?>
            </td>
        </tr>
        <?php
        $var = ob_get_contents();
        ob_end_clean();
        return $var;
    }
}