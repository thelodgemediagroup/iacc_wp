<?php


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

// hides wordpress bar for everyone but admin
if ( ! current_user_can('manage_options') )
{
	add_filter( 'show_admin_bar', '__return_false' );
}


// Disallows access to wp-admin.php and wp-login.php
add_action( 'init', 'blockusers_init' );
function blockusers_init() {
    if ( is_admin() && ! current_user_can( 'administrator' ) &&
       ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
        wp_redirect( home_url() );
        exit;
    }
}

add_action( 'wp_head', 'move_login_link');
function move_login_link()
{
	$move_login_link = '<style type="text/css"> #wpadminbar #wp-admin-bar-login_menu{float:right}</style>';
	echo $move_login_link;
}


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

$force_admin_bar = new force_admin_bar();

?>