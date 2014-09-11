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
			foreach ($arr as $id) 
			{	
				$json   = \__::fileGetContentsCurl(sprintf(self::SINGLE_URL, $id));
				$video  = json_decode($json);
				
				array_push($messages, new Message(
						$video->entry->{'media$group'}->{'media$description'}->{'$t'},
						$video->entry->link[0]->href,
						$video->entry->published->{'$t'},
						$video->entry->title->{'$t'},
						$this->getThumbnail($id),
						$this->getName(),
						$this->getIcon()
					));
			}	
		}
		return $messages;
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
	 * Get feed message/button icon
	 * @return string
	 */
	public function getIcon()
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
			'account' => get_option('gc_yt_account')
		);
	}
}