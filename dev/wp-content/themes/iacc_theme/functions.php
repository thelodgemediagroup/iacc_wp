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

// Return content type for emails as text/html
function set_html_content_type()
{
	return 'text/html';
}

// Set up a function for mailing confirmation of event registers
function email_event_confirmation($user_email, $event_details)
{
	// recipient information
	$to = $user_email;

	// subject to display for email
	$subject = 'Registration Confirmation: ' . $event_details['event_title'];

	// message to format the event data
	$message = '<p>Thank you for your IACC event registration. Keep the details below for your records.</p>';
	$message .= '<br /><table>';
	$message .= '<tr><td>Event Name: </td><td>'.$event_details['event_title'].'</td></tr>';
	$message .= '<tr><td>Event Venue: </td><td>'.$event_details['event_venue'].'</td></tr>';
	$message .= '<tr><td>Event Date: </td><td>'.date('l F jS, Y g:i a', $event_details['event_date']).'</td></tr>';
	$message .= '<tr><td>Reserved Quantity: </td><td>'.$event_details['quantity'].'</td></tr>';
	$message .= '<tr><td>Cost: </td><td>'.$event_details['amt'].'</td></tr>';
	$message .= '</table><br />';
	$message .= '<p>This is an automated message sent from iaccusa.org.</p>';

	$headers = 'From: IACC Admin <admin@iaccusa.org>'."\r\n";

	// send the email - set html content type first, then remove when sent
	add_filter( 'wp_mail_content_type', 'set_html_content_type' );
	wp_mail($to, $subject, $message, $headers);
	remove_filter( 'wp_mail_content_type', 'set_html_content_type' );
}

?>