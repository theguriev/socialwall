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
			'orderby'          => 'post_date',
			'order'            => 'DESC',
			'post_type'        => 'post',
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
						$p->post_content,
						get_permalink($p->ID),
						$p->post_date,
						$user->data->user_login,
						\__::getThumbnailURL($p->ID),
						$this->getName()
					));
			}	
		}
		return $messages;
	}
}