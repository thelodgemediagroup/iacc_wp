<?php
/*
Plugin Name: FirePHP
Plugin URI: http://inchoo.net/wordpress/wordpress-firephp-plugin/
Description: FirePHP for WordPress.
Author: Ivan Weiler
Version: 0.2
Author URI: http://inchoo.net

Changelog:
0.2 Added ob_start for theme debugging
0.1 Initial version
*/


require_once dirname(__FILE__).'/FirePHPCore/fb.php';


class WP_FirePHP{

	function activation_hook(){
		
		$firephp = plugin_basename(__FILE__);
		
		$active_plugins = get_option('active_plugins');
		
		$new_active_plugins = array();
		
		array_push($new_active_plugins, $firephp);
		
		foreach($active_plugins as $plugin)
				if($plugin!=$firephp) $new_active_plugins[] = $plugin;
		
		
		update_option('active_plugins',$new_active_plugins);
	}
	
	
	function pre_update_option_active_plugins($newvalue){
		
		$firephp = plugin_basename(__FILE__);
		
		if(!in_array($firephp,$newvalue)) return $newvalue;
		
		$new_active_plugins = array();
		
		array_push($new_active_plugins, $firephp);
		
		foreach($newvalue as $plugin)
				if($plugin!=$firephp) $new_active_plugins[] = $plugin;

		
		return $new_active_plugins;
	}
	
	function init(){ ob_start(); }

}


/* Force FirePHP to load before other plugins */
register_activation_hook(__FILE__, array('WP_FirePHP','activation_hook'));
add_filter('pre_update_option_active_plugins',array('WP_FirePHP','pre_update_option_active_plugins'));

/* Turn on output buffering */
add_action('init',array('WP_FirePHP','init'));

?>