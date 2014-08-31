<?php


define('GCLIB_URL', plugin_dir_url(__FILE__));
define('GCLIB_DIR', plugin_dir_path(__FILE__));

class __{
	//                          __              __      
	//   _________  ____  _____/ /_____ _____  / /______
	//  / ___/ __ \/ __ \/ ___/ __/ __ `/ __ \/ __/ ___/
	// / /__/ /_/ / / / (__  ) /_/ /_/ / / / / /_(__  ) 
	// \___/\____/_/ /_/____/\__/\__,_/_/ /_/\__/____/  
	const FONT_AWESOME_CSS = '//netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css';	
	const BASE_CSS         = '/css/base.css';
	                                                 
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
		$path = strtolower($path);
		//var_dump($path);
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

}

// =========================================================
// LAUNCH
// =========================================================
$helper = new __();