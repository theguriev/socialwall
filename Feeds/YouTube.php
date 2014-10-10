<?php
namespace Feeds;

class YouTube extends Feed{
	//                     __             __      
	//   _________  ____  / /____  ____  / /______
	//  / ___/ __ \/ __ \/ __/ _ \/ __ \/ __/ ___/
	// / /__/ /_/ / / / / /_/  __/ / / / /_(__  ) 
	// \___/\____/_/ /_/\__/\___/_/ /_/\__/____/  
	const VIDEOS_URL    = 'http://gdata.youtube.com/feeds/base/users/%s/uploads?alt=json&v=2&orderby=published&client=ytapi-youtube-profile&max-results=%s';	
	const SINGLE_URL    = 'http://gdata.youtube.com/feeds/api/videos/%s?v=2&alt=json';    
	const USER_URL      = 'http://gdata.youtube.com/feeds/api/users/%s?alt=json';       
	const THUMBNAIL_URL = 'http://i.ytimg.com/vi/%s/hqdefault.jpg';

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
		$json = \__::fileGetContentsCurl(sprintf(self::VIDEOS_URL, $this->options['account'], $count));
		$json = json_decode($json);
		$IDs  = $this->getIDs($json->feed->entry);

		return $this->convert($IDs);
	}

	public function convert($arr)
	{
		$messages = array();				
		if(is_array($arr))
		{
			$user = \__::fileGetContentsCurl(sprintf(self::USER_URL, $this->options['account']));
			$user = json_decode($user);

			foreach ($arr as $id) 
			{	
				$json   = \__::fileGetContentsCurl(sprintf(self::SINGLE_URL, $id));
				$el  = json_decode($json);

				// ==============================================================
				// Agregator
				// ==============================================================
				$agregator = new \Feeds\Panels\PanelAgregator(
					array(
						'middle' => array($this->getAuthorPanel($user)),
						'below_text' => array($this->getCounters($el))
					)
				);

				array_push($messages, new Message(
						$el->entry->{'media$group'}->{'media$description'}->{'$t'},
						$el->entry->link[0]->href,
						$el->entry->published->{'$t'},
						$el->entry->title->{'$t'},
						$this->getThumbnail($id),
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
	 * @param  object $user --- author object
	 * @return mixed --- PanelLink if succes | null if not
	 */
	private function getAuthorPanel($user)
	{
		if(!$this->showAuthorPanel()) return null;
		return new \Feeds\Panels\PanelLink(
			array(
				sprintf(
					'<img width="%s" class="%s" alt="%s" src="%s">',
					30,
					'circle', 
					$user->entry->author[0]->name->{'$t'},
					$user->entry->{'media$thumbnail'}->url
				),
				sprintf(
					'<div class="txt"><b class="title">%s</b><br><div class="counts"><ul><li><i class="fa fa-users"></i> %s</li><li><i class="fa fa-eye"></i> %s</li></ul></div></div>',
					$user->entry->author[0]->name->{'$t'},
					$user->entry->{'yt$statistics'}->subscriberCount,
					$user->entry->{'yt$statistics'}->totalUploadViews
				)
			), 
			'panel', 
			$user->entry->author[0]->uri->{'$t'}
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
					'fa-star',
					intval($el->entry->{'yt$statistics'}->favoriteCount)
				),
				sprintf(
					'<li><i class="fa %s"></i> %s</li>',
					'fa-eye',
					intval($el->entry->{'yt$statistics'}->viewCount)
				),
				sprintf(
					'<li><i class="fa %s"></i> %s</li>',
					'fa-thumbs-up',
					intval($el->entry->{'yt$rating'}->numLikes)
				),
				sprintf(
					'<li><i class="fa %s"></i> %s</li>',
					'fa-thumbs-down',
					intval($el->entry->{'yt$rating'}->numDislikes)
				),
				sprintf(
					'<li><i class="fa %s"></i> %s</li>',
					'fa-comments',
					intval($el->entry->{'gd$comments'}->countHint)
				),
				'</ul>'
			),
			'counts'
		);
	}

	/**
	 * Get all ID's from request
	 * @param  array $entry --- request feed array
	 * @return mixed        --- if succes ID's | false if not
	 */
	private function getIDs($entry)
	{
		$IDs = array();
		if(is_array($entry))
		{
			foreach ($entry as $video) 
			{
				$id    = $video->id->{'$t'};
				$id    = explode(':', $id);
				$IDs[] = end($id);
			}
			return $IDs;
		}
		return false;
	}

	/**
	 * Get video thumbnail
	 * @param  string $id --- video id
	 * @return string     --- image url
	 */
	private function getThumbnail($id)
	{
		return sprintf(self::THUMBNAIL_URL, $id);
	}

	/**
	 * Get feed message/button default icon
	 * @return string
	 */
	public static function getDefaultIcon()
	{
		return 'fa-youtube';
	}

	/**
	 * Get options from database
	 * @return array --- options collection
	 */
	public static function getOptions()
	{
		return array(
			'account'      => get_option('gc_yt_account'),
			'author_panel' => (string) get_option('gc_yt_author_panel'),
			'counters'     => (string) get_option('gc_yt_counters'),
			'icon'         => get_option('gc_yt_custom_icon')
		);
	}
}