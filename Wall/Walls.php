<?php
namespace Wall;

class Walls{

	//                          __              __      
	//   _________  ____  _____/ /_____ _____  / /______
	//  / ___/ __ \/ __ \/ ___/ __/ __ `/ __ \/ __/ ___/
	// / /__/ /_/ / / / (__  ) /_/ /_/ / / / / /_(__  ) 
	// \___/\____/_/ /_/____/\__/\__,_/_/ /_/\__/____/  
	const INSTAGRAM_POPULAR_ITEMS     = 0;
	const INSTAGRAM_SEARCH_BY_TAG     = 1;
	const INSTAGRAM_LOCATION_ID       = 2;
	const INSTAGRAM_USER_FEED         = 3;

	/**
	 * Get facebook wall
	 * @return Wall --- facebook object
	 */
	public static function getFacebook()
	{
		return new Wall(
			'Facebook',
			'Facebook wall',
			array(
				new \Controls\Text(
					'Max symbols per post', 
					array( 'default-value' => '200' ),
					array( 'class' => 'widefat' )
				),
				new \Controls\Text(
					'Posts per load', 
					array( 'default-value' => '5' ),
					array( 'class' => 'widefat' )
				),
				new \Controls\Text(
					'Facebook page',
					array( 'default-value' => 'whitehouse' ),
					array( 'class' => 'widefat' )
				),
				new \Controls\Text(
					'App id',
					array( 'default-value' => '802383316448078' ),
					array( 'class' => 'widefat' )
				),
				new \Controls\Text(
					'App key',
					array( 'default-value' => '970b61246640d52ac45bfa8bf596e6d5' ),
					array( 'class' => 'widefat' )
				),
				new \Controls\Text(
					'Icon',
					array( 
						'default-value' => 'fa-facebook',
						'description' => 'You can select the desired icon from here <a target="_blank" href="http://fortawesome.github.io/Font-Awesome/icons/">Font Awesome</a> and paste in the field or register your own icon in "YourTheme/style.css".'
					),
					array( 'class' => 'widefat' )
				),
				new \Controls\Checkbox(
					'Show author panel',
					array(
						'default-value' => 'on',
						'description' => 'Show author panel'
					)
				),
				new \Controls\Checkbox(
					'Show counters',
					array(
						'default-value' => 'on',
						'description' => 'Show counters'
					)
				),
				new \Controls\Checkbox(
					'Auto load',
					array(
						'default-value' => 'on',
						'description' => 'Auto load'
					)
				),
				new \Controls\Checkbox(
					'Show button',
					array(
						'default-value' => 'on',
						'description' => 'Show switcher button'
					)
				),
			),
			'fb_'
		);
	}

	/**
	 * Get twitter wall
	 * @return Wall --- twitter object
	 */
	public static function getTwitter()
	{
		return new Wall(
			'Twitter',
			'Twitter page wall',
			array(
				new \Controls\Text(
					'Max symbols per post', 
					array( 'default-value' => '200' ),
					array( 'class' => 'widefat' )
				),
				new \Controls\Text(
					'Posts per load', 
					array( 'default-value' => '5' ),
					array( 'class' => 'widefat' )
				),
				new \Controls\Text(
					'Twitter page',
					array( 'default-value' => 'whitehouse' ),
					array( 'class' => 'widefat' )
				),
				new \Controls\Text(
					'Cunsumer key',
					array( 'default-value' => 'aMY4Zsnn2KYi5TZkTCr9NlMuF' ),
					array( 'class' => 'widefat' )
				),
				new \Controls\Text(
					'Consumer secret',
					array( 'default-value' => 'vxkz9T7QQWUmqnJbkf7Eg8aHvFOCdcSMVMZrfbUPdNbw7nuYx9' ),
					array( 'class' => 'widefat' )
				),
				new \Controls\Text(
					'OAuth token',
					array( 'default-value' => '2717095358-aRUmevpNvioRb52xkFYls0Q7ldf9cIo2PjJzsqG' ),
					array( 'class' => 'widefat' )
				),
				new \Controls\Text(
					'OAuth token secret',
					array( 'default-value' => 'woklRm4IAnMK5dEkXCAlSboirK4qlUmYcYNkRVddPIbl4' ),
					array( 'class' => 'widefat' )
				),
				new \Controls\Text(
					'Icon',
					array( 
						'default-value' => 'fa-twitter',
						'description' => 'You can select the desired icon from here <a target="_blank" href="http://fortawesome.github.io/Font-Awesome/icons/">Font Awesome</a> and paste in the field or register your own icon in "YourTheme/style.css".'
					),
					array( 'class' => 'widefat' )
				),
				new \Controls\Checkbox(
					'Show author panel',
					array(
						'default-value' => 'on',
						'description' => 'Show author panel'
					)
				),
				new \Controls\Checkbox(
					'Show counters',
					array(
						'default-value' => 'on',
						'description' => 'Show counters'
					)
				),
				new \Controls\Checkbox(
					'Auto load',
					array(
						'default-value' => 'on',
						'description' => 'Auto load'
					)
				),
				new \Controls\Checkbox(
					'Show button',
					array(
						'default-value' => 'on',
						'description' => 'Show switcher button'
					)
				),
			),
			'tw_'
		);
	}

	/**
	 * Get posttype wall
	 * @return Wall --- posttype object
	 */
	public static function getPost()
	{
		$post_types = get_post_types(array('public' => true));
		$post_types = \__::unsetKeys(array('attachment'), $post_types);
		return new Wall(
			'Post type',
			'Post type wall',
			array(
				new \Controls\Text(
					'Max symbols per post', 
					array( 'default-value' => '200' ),
					array( 'class' => 'widefat' )
				),
				new \Controls\Text(
					'Posts per load', 
					array( 'default-value' => '5' ),
					array( 'class' => 'widefat' )
				),
				new \Controls\Select(
					'Post type', 
					array(
						'values' => $post_types, 
						'description' => 'You can select your own post type'
					)
				),
				new \Controls\Text(
					'Include categories', 
					array('description' => 'You need type category ids separated by commas. Like this: 1,2,3,4'), 
					array('placeholder' => 'Categories')
				),
				new \Controls\Text(
					'Icon',
					array( 
						'default-value' => 'fa-wordpress',
						'description' => 'You can select the desired icon from here <a target="_blank" href="http://fortawesome.github.io/Font-Awesome/icons/">Font Awesome</a> and paste in the field or register your own icon in "YourTheme/style.css".'
					),
					array( 'class' => 'widefat' )
				),
				new \Controls\Checkbox(
					'Auto load',
					array(
						'default-value' => 'on',
						'description' => 'Auto load'
					)
				),
				new \Controls\Checkbox(
					'Show button',
					array(
						'default-value' => 'on',
						'description' => 'Show switcher button'
					)
				),
			),
			'pt_'
		);
	}

	/**
	 * Get YouTube wall
	 * @return Wall --- YouTube object
	 */
	public static function getYouTube()
	{
		return new Wall(
			'YouTube',
			'YouTube page wall',
			array(
				new \Controls\Text(
					'Max symbols per post', 
					array( 'default-value' => '200' ),
					array( 'class' => 'widefat' )
				),
				new \Controls\Text(
					'Posts per load', 
					array( 'default-value' => '5' ),
					array( 'class' => 'widefat' )
				),
				new \Controls\Text(
					'YouTube account',
					array( 'default-value' => 'vevo' ),
					array( 'class' => 'widefat' )
				),
				new \Controls\Text(
					'Icon',
					array( 
						'default-value' => 'fa-youtube',
						'description' => 'You can select the desired icon from here <a target="_blank" href="http://fortawesome.github.io/Font-Awesome/icons/">Font Awesome</a> and paste in the field or register your own icon in "YourTheme/style.css".'
					),
					array( 'class' => 'widefat' )
				),
				new \Controls\Checkbox(
					'Show author panel',
					array(
						'default-value' => 'on',
						'description' => 'Show author panel'
					)
				),
				new \Controls\Checkbox(
					'Show counters',
					array(
						'default-value' => 'on',
						'description' => 'Show counters'
					)
				),
				new \Controls\Checkbox(
					'Auto load',
					array(
						'default-value' => 'on',
						'description' => 'Auto load'
					)
				),
				new \Controls\Checkbox(
					'Show button',
					array(
						'default-value' => 'on',
						'description' => 'Show switcher button'
					)
				),
			),
			'yt_'
		);
	}

	/**
	 * Get Vimeo wall
	 * @return Wall --- Vimeo object
	 */
	public static function getVimeo()
	{
		return new Wall(
			'Vimeo',
			'Vimeo page wall',
			array(
				new \Controls\Text(
					'Max symbols per post', 
					array( 'default-value' => '200' ),
					array( 'class' => 'widefat' )
				),
				new \Controls\Text(
					'Posts per load', 
					array( 'default-value' => '5' ),
					array( 'class' => 'widefat' )
				),
				new \Controls\Text(
					'Vimeo account',
					array( 'default-value' => 'user4075991' ),
					array( 'class' => 'widefat' )
				),
				new \Controls\Text(
					'Icon',
					array( 
						'default-value' => 'fa-vimeo-square',
						'description' => 'You can select the desired icon from here <a target="_blank" href="http://fortawesome.github.io/Font-Awesome/icons/">Font Awesome</a> and paste in the field or register your own icon in "YourTheme/style.css".'
					),
					array( 'class' => 'widefat' )
				),
				new \Controls\Checkbox(
					'Show author panel',
					array(
						'default-value' => 'on',
						'description' => 'Show author panel'
					)
				),
				new \Controls\Checkbox(
					'Show counters',
					array(
						'default-value' => 'on',
						'description' => 'Show counters'
					)
				),
				new \Controls\Checkbox(
					'Auto load',
					array(
						'default-value' => 'on',
						'description' => 'Auto load'
					)
				),
				new \Controls\Checkbox(
					'Show button',
					array(
						'default-value' => 'on',
						'description' => 'Show switcher button'
					)
				),
			),
			'v_'
		);
	}

	/**
	 * Get Instagram wall
	 * @return Wall --- Instagram object
	 */
	public static function getInstagram()
	{
		return new Wall(
			'Instagram',
			'Instagram multy wall',
			array(
				new \Controls\Text(
					'Max symbols per post', 
					array( 'default-value' => '200' ),
					array( 'class' => 'widefat' )
				),
				new \Controls\Text(
					'Posts per load', 
					array( 'default-value' => '5' ),
					array( 'class' => 'widefat' )
				),
				new \Controls\Select(
					'Search type',
					array(
						'values' => array(
							array(self::INSTAGRAM_POPULAR_ITEMS, 'Popular tems'),
							array(self::INSTAGRAM_SEARCH_BY_TAG, 'Search by tag'),
							array(self::INSTAGRAM_LOCATION_ID, 'Location id'),
							array(self::INSTAGRAM_USER_FEED, 'User feed')
						),
						'description' => 'Select a search option.'
					)
				),
				new \Controls\Text(
					'Query',
					array( 'default-value' => 'love' ),
					array( 'class' => 'widefat' )
				),
				new \Controls\Text(
					'Client id',
					array( 'default-value' => '1515b124cf42481db64cacfb96132345' ),
					array( 'class' => 'widefat' )
				),
				new \Controls\Text(
					'Icon',
					array( 
						'default-value' => 'fa-instagram',
						'description' => 'You can select the desired icon from here <a target="_blank" href="http://fortawesome.github.io/Font-Awesome/icons/">Font Awesome</a> and paste in the field or register your own icon in "YourTheme/style.css".'
					),
					array( 'class' => 'widefat' )
				),
				new \Controls\Checkbox(
					'Show author panel',
					array(
						'default-value' => 'on',
						'description' => 'Show author panel'
					)
				),
				new \Controls\Checkbox(
					'Show counters',
					array(
						'default-value' => 'on',
						'description' => 'Show counters'
					)
				),
				new \Controls\Checkbox(
					'Auto load',
					array(
						'default-value' => 'on',
						'description' => 'Auto load'
					)
				),
				new \Controls\Checkbox(
					'Show button',
					array(
						'default-value' => 'on',
						'description' => 'Show switcher button'
					)
				),
			),
			'i_'
		);
	}

	/**
	 * Get VK wall
	 * @return Wall --- VK object
	 */
	public static function getVK()
	{
		return new Wall(
			'VK',
			'VK page wall',
			array(
				new \Controls\Text(
					'Max symbols per post', 
					array( 'default-value' => '200' ),
					array( 'class' => 'widefat' )
				),
				new \Controls\Text(
					'Posts per load', 
					array( 'default-value' => '5' ),
					array( 'class' => 'widefat' )
				),
				new \Controls\Text(
					'VK account',
					array( 'default-value' => 'id236993150' ),
					array( 'class' => 'widefat' )
				),
				new \Controls\Text(
					'Icon',
					array( 
						'default-value' => 'fa-vk',
						'description' => 'You can select the desired icon from here <a target="_blank" href="http://fortawesome.github.io/Font-Awesome/icons/">Font Awesome</a> and paste in the field or register your own icon in "YourTheme/style.css".'
					),
					array( 'class' => 'widefat' )
				),
				new \Controls\Checkbox(
					'Show author panel',
					array(
						'default-value' => 'on',
						'description' => 'Show author panel'
					)
				),
				new \Controls\Checkbox(
					'Show counters',
					array(
						'default-value' => 'on',
						'description' => 'Show counters'
					)
				),
				new \Controls\Checkbox(
					'Auto load',
					array(
						'default-value' => 'on',
						'description' => 'Auto load'
					)
				),
				new \Controls\Checkbox(
					'Show button',
					array(
						'default-value' => 'on',
						'description' => 'Show switcher button'
					)
				),
			),
			'vk_'
		);
	}
}