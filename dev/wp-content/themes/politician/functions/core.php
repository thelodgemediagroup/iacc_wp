<?php
if (!defined('TEMPLATENAME')) {
	define('TEMPLATENAME', get_option('template'));
}

 /** Tell WordPress to run theme_custom_setup() when the 'after_setup_theme' hook is run. */
add_action('after_setup_theme', 'theme_custom_setup');

if ( ! isset( $content_width ) ) $content_width = 960;

if (!function_exists('theme_custom_setup')) {
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support post thumbnails.
 *
 * @uses add_theme_support() To add support for post thumbnails and automatic feed links.
 * @uses register_nav_menus() To add support for navigation menus.
 * @uses add_custom_background() To add support for a custom background.
 * @uses add_editor_style() To style the visual editor.
 * @uses load_theme_textdomain() For translation/localization support.
 * @uses add_custom_image_header() To add support for a custom header.
 * @uses register_default_headers() To register the default custom header images provided with the theme.
 * @uses set_post_thumbnail_size() To set a custom post thumbnail size.
 *
 */
	function theme_custom_setup() {

		// This theme styles the visual editor with editor-style.css to match the theme style.
//		add_editor_style();

		// This theme uses post thumbnails
		add_theme_support('post-thumbnails');

		// Add default posts and comments RSS feed links to head
		add_theme_support( 'automatic-feed-links');

		// Make theme available for translation
		// Translations can be filed in the /languages/ directory
		load_theme_textdomain(TEMPLATENAME, get_template_directory() . '/languages');

		// This theme uses wp_nav_menu() in one location.
		register_nav_menus(
			array(
				'primary' => __('Primary Navigation', TEMPLATENAME),
			)
		);

	}
}

// styles & scripts
if ( !is_admin() ) {
	function init_styles_and_scripts() {
		wp_deregister_script('jquery');
		wp_register_script('jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js');

		wp_register_style('css_flexslider', get_template_directory_uri() . '/sliders/flexslider/flexslider.css');
		wp_register_style('css_custom', get_template_directory_uri() . '/stylesheets/custom.php');

		wp_register_script('js_easing', get_template_directory_uri() . '/js/jquery.easing.1.3.js', array('jquery'));
		wp_register_script('js_cycle', get_template_directory_uri() . '/js/jquery.cycle.all.min.js', array('jquery'));
		wp_register_script('js_respond', get_template_directory_uri() . '/js/respond.min.js', array('jquery'));
		wp_register_script('js_autoAlign', get_template_directory_uri() . '/js/jquery.flexibleColumns.min.js', array('jquery'));
		wp_register_script('js_isotope', get_template_directory_uri() . '/js/jquery.isotope.min.js', array('jquery'));
		wp_register_script('js_fancybox', get_template_directory_uri() . '/fancybox/jquery.fancybox.pack.js', array('jquery'));
		wp_register_script('js_twitter', get_template_directory_uri() . '/js/jquery.tweet.js', array('jquery'));
		wp_register_script('js_flexslider', get_template_directory_uri() . '/sliders/flexslider/jquery.flexslider-min.js', array('jquery'));
		

		wp_register_script('js_custom', get_template_directory_uri() . '/js/custom.js', array('jquery'), '1.0.0');
	}
	add_action('init', 'init_styles_and_scripts');
} else {
	function init_admin_styles_and_scripts() {
		wp_register_style( 'css_admin_options', get_template_directory_uri() . '/functions/admin_options/stylesheets/admin_options.css');
		wp_register_script( 'jquery-cooke', get_template_directory_uri() . '/functions/admin_options/js/jquery.cookie.js');
		wp_register_script( 'admin_common', get_template_directory_uri() . '/functions/admin_options/js/common.js');
		wp_enqueue_style('css_admin_options');
		wp_enqueue_script('jquery-cooke');
		wp_enqueue_script('jquery-ui-tabs');
		add_thickbox();
		wp_enqueue_script('admin_common');
//		remove_meta_box('postcustom', 'post', 'normal');
//		remove_meta_box('postcustom', 'page', 'normal');
	}
	add_action('admin_init', 'init_admin_styles_and_scripts');
}

/**
 * add a default-gravatar to options
 */
function theme_addgravatar( $avatar_defaults ) {
	$myavatar = get_template_directory_uri() . '/images/gravatar.png';
	$avatar_defaults[$myavatar] = 'people';
	return $avatar_defaults;
}
add_filter( 'avatar_defaults', 'theme_addgravatar' );

function theme_post_limit($limit) {
		if ( is_admin() ) return $limit;
		$old_limit = $limit;
		if ( is_home() ) {
			$limit = get_option('front_page_limit');
		} elseif ( is_search() ) {
			$limit = get_option('searches_limit');
		} elseif ( is_category() ) {
			$limit = get_option('categories_limit');
		} elseif ( is_tag() ) {
			$limit = get_option('tags_limit');
		} elseif ( is_author() ) {
			$limit = get_option('authors_limit');
		} elseif ( is_year() ) {
			$limit = get_option('year_archives_limit') ? get_option('year_archives_limit') : get_option('archives_limit');
		} elseif ( is_month() ) {
			$limit = get_option('month_archives_limit') ? get_option('month_archives_limit') : get_option('archives_limit');
		} elseif ( is_day() ) {
			$limit = get_option('day_archives_limit') ? get_option('day_archives_limit') : get_option('archives_limit');
		} elseif ( is_archive() ) {
			$limit = get_option('archives_limit');
		}

		if ( !$limit )
			$limit = $old_limit;
		elseif ( $limit == '-1' )
			$limit = '18446744073709551615';
		return $limit;
}
add_action('option_posts_per_page', 'theme_post_limit');

/**
 * Replaces "[...]" (appended to automatically generated excerpts) with an ellipsis and twentyten_continue_reading_link().
 *
 * To override this in a child theme, remove the filter and add your own
 * function tied to the excerpt_more filter hook.
 *
 * @return string An ellipsis
 */
function theme_auto_excerpt_more( $more ) {
		  return ' &hellip;';
}
add_filter( 'excerpt_more', 'theme_auto_excerpt_more' );

require_once (get_template_directory() . '/functions/metaboxes/metaboxes_generator.php');

function theme_favico() {
	$ico = get_option('favicon');
	if (empty($ico))
		$ico = get_template_directory_uri().'/images/favicon.ico';
	echo $ico;
}

function theme_logo() {
	$logo = get_option('logo');
	if (empty($logo))
		$logo = get_template_directory_uri().'/images/logo.png';
	echo $logo;
}

function theme_check_custom_background() {
	$background = get_background_image();
	$color = get_background_color();
   return (!$background && !$color) ? true : false;
}

/*-----------------------------------------------------------------------------------*/
/* TGM Plugin Activation
/*-----------------------------------------------------------------------------------*/

require_once (get_template_directory() . '/functions/class-tgm-plugin-activation.php');

add_action( 'tgmpa_register', 'politician_theme_register_required_plugins' );

function politician_theme_register_required_plugins() {

	$plugins = array(

		// WP-PageNavi
		array(
			'name' 		=> 'WP-PageNavi',
			'slug' 		=> 'wp-pagenavi',
			'required' 	=> false,
			'version'	=> '2.82'
		),
		
		// Contact Form 7
		array(
			'name'		=> 'Contact Form 7',
			'slug'		=> 'contact-form-7',
			'required'	=> false,
			'version'	=> '3.2'
		),
		
		// WP Google Maps
		array(
			'name'		=> 'WP Google Maps',
			'slug'		=> 'wp-google-maps',
			'required'	=> false,
			'version'	=> '5.0'
		),
		
		// Facebook Social Plugin Widgets
		array(
			'name'		=> 'Facebook Social Plugin Widgets',
			'slug'		=> 'facebook-social-plugin-widgets',
			'required'	=> false,
			'version'	=> '1.3'
		),
		
		// Regenerate Thumbnails
		array(
			'name'		=> 'Regenerate Thubmnails',
			'slug'		=> 'regenerate-thumbnails',
			'required'	=> false,
			'version'	=> '2.2.3'
		),
		
		// The Events Calendar
		array(
			'name'		=> 'The Events Calendar',
			'slug'		=> 'the-events-calendar',
			'required'	=> false,
			'version'	=> '2.0.7'
		)

	);

	/**
	 * Array of configuration settings. Amend each line as needed.
	 * If you want the default strings to be available under your own theme domain,
	 * leave the strings uncommented.
	 * Some of the strings are added into a sprintf, so see the comments at the
	 * end of each line for what each argument will be.
	 */
	$config = array(
		'domain'       		=> TEMPLATENAME,         	// Text domain - likely want to be the same as your theme.
		'default_path' 		=> '',                         	// Default absolute path to pre-packaged plugins
		'parent_menu_slug' 	=> 'themes.php', 				// Default parent menu slug
		'parent_url_slug' 	=> 'themes.php', 				// Default parent URL slug
		'menu'         		=> 'install-required-plugins', 	// Menu slug
		'has_notices'      	=> true,                       	// Show admin notices or not
		'is_automatic'    	=> false,					   	// Automatically activate plugins after installation or not
		'message' 			=> '',							// Message to output right before the plugins table
		'strings'      		=> array(
			'page_title'                       			=> __( 'Install Required Politician Plugins', TEMPLATENAME ),
			'menu_title'                       			=> __( 'Install Politician Plugins', TEMPLATENAME ),
			'installing'                       			=> __( 'Installing Politician Plugin: %s', TEMPLATENAME ), // %1$s = plugin name
			'oops'                             			=> __( 'Something went wrong with the plugin API.', TEMPLATENAME ),
			'notice_can_install_required'     			=> _n_noop( 'Politician requires the following plugin: %1$s.', 'Politician requires the following plugins: %1$s.' ), // %1$s = plugin name(s)
			'notice_can_install_recommended'			=> _n_noop( 'Politician recommends the following plugin: %1$s.', 'Politician recommends the following plugins: %1$s.' ), // %1$s = plugin name(s)
			'notice_cannot_install'  					=> _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.' ), // %1$s = plugin name(s)
			'notice_can_activate_required'    			=> _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s)
			'notice_can_activate_recommended'			=> _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s)
			'notice_cannot_activate' 					=> _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.' ), // %1$s = plugin name(s)
			'notice_ask_to_update' 						=> _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with Politician: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with Politician: %1$s.' ), // %1$s = plugin name(s)
			'notice_cannot_update' 						=> _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.' ), // %1$s = plugin name(s)
			'install_link' 					  			=> _n_noop( 'Begin installing plugin', 'Begin installing plugins' ),
			'activate_link' 				  			=> _n_noop( 'Activate installed plugin', 'Activate installed plugins' ),
			'return'                           			=> __( 'Return to Required Plugins Installer', TEMPLATENAME ),
			'plugin_activated'                 			=> __( 'Plugin activated successfully.', TEMPLATENAME ),
			'complete' 									=> __( 'All plugins installed and activated successfully. %s', TEMPLATENAME ) // %1$s = dashboard link
		)
	);

	tgmpa( $plugins, $config );

}

?>