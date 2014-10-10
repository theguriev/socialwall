<?php
namespace Feeds;

class Post extends Feed{
	//                    __  __              __    
	//    ____ ___  ___  / /_/ /_  ____  ____/ /____
	//   / __ `__ \/ _ \/ __/ __ \/ __ \/ __  / ___/
	//  / / / / / /  __/ /_/ / / / /_/ / /_/ (__  ) 
	// /_/ /_/ /_/\___/\__/_/ /_/\____/\__,_/____/  
	public function __construct($options = array())
	{
		parent::__construct($options);
	}

	public function getMessages($count = 5, $offset = 0)
	{
		$args = array(
			'posts_per_page'   => $count,
			'offset'           => $offset,
			'category'         => '',
			'orderby'          => 'post_date',
			'order'            => 'DESC',
			'include'          => '',
			'exclude'          => '',
			'meta_key'         => '',
			'meta_value'       => '',
			'post_type'        => 'post',
			'post_mime_type'   => '',
			'post_parent'      => '',
			'post_status'      => 'publish',
			'suppress_filters' => true 
		);
		$args  = array_merge($args, $this->options);
		$posts = get_posts($args);
		return $this->convert($posts);
	}

	public function convert($arr)
	{
		$messages = array();				
		if(is_array($arr))
		{
			foreach ($arr as $p) 
			{	
				$user = get_user_by('id', $p->post_author); 				
				array_push($messages, new Message(
						strip_tags($p->post_content),
						get_permalink($p->ID),
						$p->post_date,
						$user->data->user_login,
						\__::getThumbnailURL($p->ID),
						$this->getName(),
						$this->getIcon(),
						Null,
						''
					));
			}	
		}
		return $messages;
	}

	/**
	 * Get feed message/button default icon
	 * @return string
	 */
	public static function getDefaultIcon()
	{
		return 'fa-wordpress';
	}

	/**
	 * Get options from database
	 * @return array --- options collection
	 */
	public static function getOptions()
	{
		return array(
			'post_type' => get_option('gc_pt_post_type'),
			'category'  => get_option('gc_pt_include_categories'),
			'icon'      => get_option('gc_pt_custom_icon')
		);
	}
}