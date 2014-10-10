<?php
namespace Feeds;

class Vimeo extends Feed{
	//                     __             __      
	//   _________  ____  / /____  ____  / /______
	//  / ___/ __ \/ __ \/ __/ _ \/ __ \/ __/ ___/
	// / /__/ /_/ / / / / /_/  __/ / / / /_(__  ) 
	// \___/\____/_/ /_/\__/\___/_/ /_/\__/____/  
	const VIDEOS_URL    = 'http://vimeo.com/api/v2/%s/videos.json';	

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
		$json = \__::fileGetContentsCurl(sprintf(self::VIDEOS_URL, $this->options['account']));
		$json = (array) json_decode($json);
		$json = array_splice($json, $offset, $count);

		return $this->convert($json);
	}

	public function convert($arr)
	{
		$messages = array();				
		if(is_array($arr))
		{
			foreach ($arr as $el) 
			{	
				// ==============================================================
				// Agregator
				// ==============================================================
				$agregator = new \Feeds\Panels\PanelAgregator(
					array(
						'middle' => array($this->getAuthorPanel($el)),
						'below_text' => array($this->getCounters($el))
					)
				);

				array_push($messages, new Message(
						strip_tags($el->description),
						$el->url,
						$el->upload_date,
						$el->title,
						$el->thumbnail_large,
						$this->getName(),
						$this->getIcon(),
						$agregator,
						''
					));
			}	
		}
		return $messages;
	}

	/**
	 * Get author PanelLink
	 * @param  object $el --- author object
	 * @return mixed --- PanelLink if succes | null if not
	 */
	private function getAuthorPanel($el)
	{
		if(!$this->showAuthorPanel()) return null;
		return new \Feeds\Panels\PanelLink(
			array(
				sprintf(
					'<img width="%s" class="%s" alt="%s" src="%s">',
					30,
					'circle', 
					$el->user_name,
					$el->user_portrait_small
				),
				sprintf(
					'<div class="txt"><b class="title">%s</b><br></div>',
					$el->user_name
				)
			), 
			'panel', 
			$el->user_url
		);
	}

	/**
	 * Get counter Panel
	 * @param  object $el --- post object
	 * @return mixed --- Panel if success | null if not
	 */
	private function getCounters($el)
	{
		if(!$this->showCounters()) return null;
		return new \Feeds\Panels\Panel(
			array(
				'<ul>',
				sprintf(
					'<li><i class="fa %s"></i> %s</li>',
					'fa-heart',
					intval($el->stats_number_of_likes)
				),
				sprintf(
					'<li><i class="fa %s"></i> %s</li>',
					'fa-comments',
					intval($el->stats_number_of_comments)
				),
				sprintf(
					'<li><i class="fa %s"></i> %s</li>',
					'fa-eye',
					intval($el->stats_number_of_plays)
				),
				'</ul>'
			),
			'counts'
		);
	}

	/**
	 * Get feed message/button default icon
	 * @return string
	 */
	public static function getDefaultIcon()
	{
		return 'fa-vimeo-square';
	}

	/**
	 * Get options from database
	 * @return array --- options collection
	 */
	public static function getOptions()
	{
		return array(
			'account'      => get_option('gc_v_account'),
			'author_panel' => (string) get_option('gc_v_author_panel'),
			'counters'     => (string) get_option('gc_v_counters'),
			'icon'         => get_option('gc_v_custom_icon')
		);
	}
}