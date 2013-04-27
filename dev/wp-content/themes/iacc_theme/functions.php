<?php

// Gets ridda tha events calendar admin menu
if (!current_user_can('manage_options')) {
	  define('TRIBE_DISABLE_TOOLBAR_ITEMS', true);
	}

// This removes the wordpress logo
function remove_wp_logo()
{
	if ( ! current_user_can('manage_options'))
	{
		global $wp_admin_bar;
		$wp_admin_bar->remove_menu('wp-logo');
	}
}
add_action('wp_before_admin_bar_render', 'remove_wp_logo');

// Disallows access to wp-admin.php and wp-login.php
add_action( 'init', 'blockusers_init' );
function blockusers_init() {
    if ( is_admin() && ! current_user_can( 'administrator' ) &&
       ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
        wp_redirect( home_url() );
        exit;
    }
}

// for anon users, make the login admin link float right
add_action( 'wp_head', 'move_login_link');
function move_login_link()
{
	$move_login_link = '<style type="text/css"> #wpadminbar #wp-admin-bar-login_menu{float:right}</style>';
	echo $move_login_link;
}

// for logged in users, make profile link float right
add_action( 'wp_head', 'move_profile_link');
function move_profile_link()
{
	$move_profile_link = '<style type="text/css"> #wpadminbar #wp-admin-bar-profile_menu{float:right}</style>';
	echo $move_profile_link;
}

add_action( 'wp_head', 'move_signout_link');
function move_signout_link()
{
	$move_signout_link = '<style type="text/css"> #wpadminbar #wp-admin-bar-sign_out_menu{float:right}</style>';
	echo $move_signout_link;
}

// Make an admin bar for anon users. Prompt them to login
class force_admin_bar
{

	function __construct()
	{

		if ( is_user_logged_in() ) { return false; } 

		add_action( 'wp_before_admin_bar_render', array( &$this, 'disable_bar_search' ) );

		add_filter( 'show_admin_bar', '__return_true' );

		add_action( 'admin_bar_menu', array( &$this, 'logged_out_menus'), 15 );

	}

	function logged_out_menus( $meta = FALSE )
	{
		global $wp_admin_bar, $blog_id;

		$wp_admin_bar->add_menu( array (
			'id' => 'login_menu',
			'title' => __( 'Login' ),
			'href' => get_home_url( $blog_id, '/login/')
			));
	}

	function remove_wp_logo()
	{
		global $wp_admin_bar;
		$wp_admin_bar->remove_menu('wp-logo');
	}

	function disable_bar_search()
	{
		global $wp_admin_bar;
		$wp_admin_bar->remove_menu('search');
	}

	function disable_events_menu()
	{
		global $wp_admin_bar;
		$wp_admin_bar->remove_menu('events-calendar');
	}

}

// Instantiate the anonymous user login admin bar
$force_admin_bar = new force_admin_bar();

// Regular member admin admin bar. Profile link.
class logged_in_iacc_member 
{
	function __construct()
	{
		if (current_user_can('manage_options'))
		{
			return FALSE;
		}

		if (! is_user_logged_in() )
		{
			return FALSE;
		}
		add_action( 'wp_before_admin_bar_render', array( &$this, 'disable_bar_search' ) );

		add_action( 'wp_before_admin_bar_render', array( &$this, 'disable_my_account_link' ) );

		add_action( 'wp_before_admin_bar_render', array( &$this, 'disable_my_blogs' ) );

		add_action ( 'wp_before_admin_bar_render', array( &$this, 'disable_site_name' ) );

		add_filter( 'show_admin_bar', '__return_true' );

		add_action( 'admin_bar_menu', array( &$this, 'sign_out_menus'), 15 );

		add_action( 'admin_bar_menu', array( &$this, 'logged_in_menus'), 15 );

	}

	function logged_in_menus( $meta = FALSE )
	{
		global $wp_admin_bar, $blog_id;

		$wp_admin_bar->add_menu( array (
			'id' => 'profile_menu',
			'title' => __( 'Profile' ),
			'href' => get_home_url( $blog_id, '/member-admin/')
			));
	}

	function sign_out_menus( $meta = FALSE )
	{
		global $wp_admin_bar, $blog_id;

		$wp_admin_bar->add_menu( array(
			'id' => 'sign_out_menu',
			'title' => __( 'Log Out' ),
			'href' => get_home_url( $blog_id, '/login/?a=logout')
			));
	}

	function disable_my_account_link()
	{
		global $wp_admin_bar;
		$wp_admin_bar->remove_menu('my-account');
	}

	function disable_my_blogs()
	{
		global $wp_admin_bar;
		$wp_admin_bar->remove_menu('my-blogs');
	}

	function remove_wp_logo()
	{
		global $wp_admin_bar;
		$wp_admin_bar->remove_menu('wp-logo');
	}

	function disable_bar_search()
	{
		global $wp_admin_bar;
		$wp_admin_bar->remove_menu('search');
	}

	function disable_site_name()
	{
		global $wp_admin_bar;
		$wp_admin_bar->remove_menu('site-name');
	}
}

$logged_in_iacc_member = new logged_in_iacc_member();

update_option('home', 'local.iacc');
update_option('siteurl', 'local.iacc');

?>