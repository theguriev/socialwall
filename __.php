<?php


define('GCLIB_URL', plugin_dir_url(__FILE__));
define('GCLIB_DIR', plugin_dir_path(__FILE__));

class __{
	//                          __              __      
	//   _________  ____  _____/ /_____ _____  / /______
	//  / ___/ __ \/ __ \/ ___/ __/ __ `/ __ \/ __/ ___/
	// / /__/ /_/ / / / (__  ) /_/ /_/ / / / / /_(__  ) 
	// \___/\____/_/ /_/____/\__/\__,_/_/ /_/\__/____/  
	const FONT_AWESOME_CSS = '//netdna.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css';	
	const BASE_CSS         = '/css/base.css';
	const CACHE_ON 		   = true;	
	                                                 
	//                    __  __              __    
	//    ____ ___  ___  / /_/ /_  ____  ____/ /____
	//   / __ `__ \/ _ \/ __/ __ \/ __ \/ __  / ___/
	//  / / / / / /  __/ /_/ / / / /_/ / /_/ (__  ) 
	// /_/ /_/ /_/\___/\__/_/ /_/\____/\__,_/____/
	public function __construct()
	{
		// =========================================================
		// Load all php files
		// =========================================================
		spl_autoload_register(array(&$this, 'autoloader'));
	}  
	
	/**
	 * Auto load all Factory classes
	 */
	public static function autoloader($class) 
	{			
		$path = sprintf('%s%s.php', GCLIB_DIR, $class);	
		$path = str_replace('\\', '/', $path);	
		if (file_exists($path))
		{
			require_once $path;
	    }	
	}     

	/**
	 * Join arra to one string
	 * @param  array $arr          --- items to join
	 * @param  string $join_symbol --- joint sybol between $key and $value
	 * @param  string $separator   --- items separator
	 * @return string              --- joined string or empty
	 */
	public static function joinArray($arr, $join_symbol = "=", $separator = ' ')
	{
		$str = '';
		if(is_array($arr)) 
		{
			foreach ($arr as $key => $value) 
			{
				$str.= sprintf(
					'%1$s%2$s%3$s%4$s',
					$key,
					$join_symbol,
					'"'.$value.'"',
					$separator
				);
			}	
		}
		return $str;
	}    

	/**
	 * Unset some keys from array
	 * @param  array  $keys --- keys array
	 * @param  array  $arr  --- array where need unset keys
	 * @return array        --- array without unseated keys
	 */
	public static function unsetKeys(array $keys, array $arr)
	{
		foreach ($keys as $key) 
		{
			if(isset($arr[$key])) unset($arr[$key]);
		}
		return $arr;
	}

	/**
     * Format name.
     * From: Some name
     * To:   some_name
     * @param  string $name --- entry name
     * @return string       --- exit name
     */
    public static function formatName($name)
    {
        return strtolower(str_replace(' ', '_', $name));
    }       

    /**
     * Start a session if is not running
     */
    public static function sessionStart()
    {
    	if(session_id() == '') session_start();
    }                             

    /**
     * Get attachment ID from src
     * @param  string $image_src --- image url
     * @return integer           --- attachment ID
     */
    public static function getAttachmentIDFromSrc($image_src) 
    {
		global $wpdb;
		$query = "SELECT ID FROM {$wpdb->posts} WHERE guid='$image_src'";
		$id    = $wpdb->get_var($query);
		return intval($id);
	}

	/**
	 * Get thumbnail url
	 * @param  integer $id  --- post id
	 * @param  string $size --- image seize. Default: full
	 * @return string       --- URL
	 */
	public static function getThumbnailURL($id, $size = 'full')
	{
		if(!has_post_thumbnail($id)) return false;
		$thumb = wp_get_attachment_image_src( get_post_thumbnail_id($id), $size);
		return $thumb['0'];
	}

	/**
	 * Get contents 
	 * @param  string $url
	 * @return string
	 */
	public static function fileGetContentsCurl($url) 
	{
	    $ch = curl_init();

	    curl_setopt($ch, CURLOPT_HEADER, 0);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	    curl_setopt($ch, CURLOPT_URL, $url);

	    $data = curl_exec($ch);
	    curl_close($ch);

	    return $data;
	}

	/**
	 * Cut text to a certain length
	 * EXAMPLE __::cutText('Hellow world!', 8); 
	 * Before: Hellow world!
	 * After: Hellow...
	 * @param  string $str --- string to cut
	 * @param  int $max --- count symbols
	 * @return string      --- cutted string
	 */
	public static function cutText($str, $max)
	{
		$str = substr($str, 0, $max+1);
		if (mb_strlen($str) > $max)
		{
			$pieces = explode(' ', $str);
			unset($pieces[count($pieces)-1]);
			return implode(' ', $pieces).'...';
		}
		return $str;
	}

	/**
	 * Get and Set cache
	 * @param  string  $key  --- key cache
	 * @param  mixed  $func  --- get value function if cache is empty
	 * @param  integer $time --- how mutch time cache is live
	 * @return mixed         --- value
	 */
	public static function getSetCache($key, $func, $time = 3600)
	{
		$val = self::getCache($key);
		if($val === false)
		{
			$val = $func();
			self::setCache($key, $val, $time);
			return $val;
		}
		else
		{
			return $val;
		}
	}

	/**
	 * Set Cache
	 * @param string  $key    
	 * @param string  $val    
	 * @param integer $time   
	 * @param string  $prefix 
	 */
	public static function setCache($key, $val, $time = 3600)
	{		
		$val = is_array($val) ? serialize($val) : $val;
		$val = base64_encode($val);
		set_transient($key, $val, $time);
	}

	/**
	 * Get Cache
	 * @param  string $key    
	 * @param  string $prefix 
	 * @return mixed
	 */
	public static function getCache($key)
	{	
		$cached = false;	
		if(self::CACHE_ON)
		{
			$cached = get_transient($key);
			if($cached !== false)
			{
				$cached     = base64_decode($cached);
				$cached_arr = unserialize($cached);

				if(is_array($cached_arr))
				{
					$cached = $cached_arr;
				}
			} 
		}
		return false !== $cached ? $cached : false;
	}

	/**
	 * Get all hashtags from string
	 * @param  string $str --- some text with hashtags
	 * @return array       --- hashtags
	 */
	public static function getAllHastags($str)
	{
		$result = array();
		preg_match_all('/#[^\s]*/i', $str, $result);
		return $result;
	}

	/**
	 * Wrap hastag to link
	 * @param  string $str --- some text with hashtags
	 * @param  string $link_pattern --- link patter like: https://twitter.com/hashtag/%s?src=hash
	 * @return string --- some text with wrapped hashtags
	 */
	public static function wrapHashtags($str, $link_pattern = '%s')
	{
		if(!strlen($link_pattern)) return $str;
		$hashtags = self::getAllHastags($str);
		if(isset($hashtags[0]) AND count($hashtags[0]))
		{
			foreach ($hashtags[0] as $tag) 
			{
				$tag_clean   = str_replace('#', '', $tag);
				$tag_url     = sprintf($link_pattern, $tag_clean);
				$tag_wrapped = sprintf('<a href="%s" target="_blank">%s</a>', $tag_url, $tag);
				$str         = str_replace($tag, $tag_wrapped, $str);
			}
		}
		return $str;
	}

}

// =========================================================
// LAUNCH
// =========================================================
$helper = new __();