<?php
/*
Plugin Name: IACC Manager
Plugin URI: http://thelodgemediagroup.com
Description: This plugin shows members' account upgrades and event purchases.
Author: The Lodge Media Group
Author URI: http://thelodgemediagroup.com
Version: 0.5.0
*/

// Register globals
global $wpdb;
define('EVENT_PAYPAL', 'event_paypal');
define('MEMBER_PAYPAL', 'member_paypal');
require_once(ABSPATH . 'wp-config.php');
require_once(ABSPATH . 'wp-load.php');

add_action('admin_menu', 'imm_create_menu');

// Create the dashboard links

function imm_create_menu()
{
	$imm_icon_location = plugin_dir_url( __FILE__ ).'/images/plugin_logo.png';
	$imm_create_settings = add_menu_page('IACC Manager', 'IACC', 'administrator', __FILE__,'imm_action',$imm_icon_location,'30');
	$imm_append_events = add_submenu_page(__FILE__, 'Purchased Events', 'Purchased Events', 'administrator', 'purchased_events', 'imm_get_purchased_events');
	$imm_append_members = add_submenu_page(__FILE__, 'Membership Upgrades', 'Membership Upgrades', 'administrator', 'membership_upgrades', 'imm_get_membership_upgrades');
}

function imm_action()
{

	$action  = !empty($_REQUEST['action']) ? $_REQUEST['action'] : 'view';

	if ( $action == 'view' )
	{
		imm_view_all_members();
	}
	else if ( $action == 'edit' )
	{
		exit;
	}
	else if ( $action == 'delete' )
	{
		exit;
	}

}

function imm_view_all_members()
{
	global $wpdb;

	$attendee_args = array('key' => 'membership_type', 'value' => 'attendee');
	$member_args = array('key' => 'membership_type', 'value' => 'member');
	$corp_member_args = array('key' => 'membership_type', 'value' => 'corporate_member');
	$user_args = array('relation' => 'OR', $attendee_args, $member_args, $corp_member_args);
	$iacc_users = get_users($user_args);
	$user_count = count($iacc_users);

	echo '<h2>Current Members ('.$user_count.')</h2>';
	echo '<table class="widefat page fixed"><thead><tr><th>Member</th><th>Email</th><th>Membership Type</th></tr></thead>';
	
	foreach ($iacc_users as $user)
	{
		
		echo '<tr>';
		echo '<td>'.$user->nickname.'</td>';
		echo '<td>'.$user->user_email.'</td>';
		echo '<td>'.$user->membership_type.'</td>';
		echo '</tr>';
	}

	echo '</table>';
}

function imm_get_purchased_events()
{
	global $wpdb;
	$sql = "SELECT * FROM ".EVENT_PAYPAL." ORDER BY event_id ASC, timestamp ASC;";
	$purchases = $wpdb->get_results($sql);
/*
	$sql = "SELECT DISTINCT event_id FROM ".EVENT_PAYPAL;
	$events = $wpdb->get_results($sql);
*/

	echo '<h2>Purchased Events</h2>';
	echo '<table class="widefat page fixed"><thead><tr><th>Event</th><th>Event Date</th><th>Ticket Quantity</th><th>Ticket Type</th><th>Member</th><th>Email</th><th>Price</th><th>Time</th></tr></thead>';

	foreach ($purchases as $purchase)
	{
		echo '<tr>';
		echo '<td>'.$purchase->event_title.'</td>';
		echo '<td>'.date("jS F, Y", $purchase->event_date).'</td>';
		echo '<td>'.$purchase->quantity.'</td>';
		echo '<td>'.$purchase->ticket_desc.'</td>';
		echo '<td>'.$purchase->first_name.' '.$purchase->last_name.'</td>';
		echo '<td>'.$purchase->email.'</td>';
		echo '<td>'.$purchase->amt.'</td>';
		echo '<td>'.date("jS F, Y", $purchase->timestamp).'</td>';
		echo '</tr>';
	}

	echo '</table>';
}

function imm_get_membership_upgrades()
{
	global $wpdb;
	$sql = "SELECT * FROM ".MEMBER_PAYPAL." ORDER BY timestamp DESC;";
	$members = $wpdb->get_results($sql);

	echo '<h2>Membership Upgrades</h2>';
	echo '<table class="widefat page fixed"><thead><tr><th>Member Name</th><th>Nickname</th><th>Email</th><th>Membership Type</th><th>Price</th><th>Purchase Date</th></tr></thead>';

	foreach ($members as $member)
	{
		echo '<tr>';
		echo '<td>'.$member->first_name.' '.$member->last_name.'</td>';
		echo '<td>'.$member->nickname.'</td>';
		echo '<td>'.$member->email.'</td>';
		echo '<td>'.$member->description.'</td>';
		echo '<td>'.$member->amt.'</td>';
		echo '<td>'.date("jS F, Y", $member->timestamp).'</td>';
		echo '</tr>';
	}

	echo '</table>';
}

?>