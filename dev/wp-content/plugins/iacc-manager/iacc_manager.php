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
	$imm_create_settings = add_menu_page('IACC Manager', 'IACC', 'administrator', __FILE__,'imm_member_action',$imm_icon_location,'30');
	$imm_append_events = add_submenu_page(__FILE__, 'Purchased Events', 'Purchased Events', 'administrator', 'purchased_events', 'imm_event_action');
	$imm_append_members = add_submenu_page(__FILE__, 'Membership Upgrades', 'Membership Upgrades', 'administrator', 'membership_upgrades', 'imm_get_membership_upgrades');
}

function imm_member_action()
{

	$action  = !empty($_REQUEST['action']) ? $_REQUEST['action'] : 'view';

	if ( $action == 'view' )
	{
		imm_view_all_members();
	}
	else if ( $action == 'edit' )
	{
		imm_edit_member();
	}
	else if ( $action == 'event_view' )
	{
		imm_get_registers_by_event();
	}

}

function imm_event_action()
{
	$action  = !empty($_REQUEST['action']) ? $_REQUEST['action'] : 'view';

	if ( $action == 'view' )
	{
		imm_get_purchased_events();
	}
	else if ( $action == 'event_view' )
	{
		imm_get_registers_by_event();
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
	echo '<table class="widefat page fixed"><thead><tr><th>Member</th><th>Email</th><th>Membership Type</th><th>Edit</th></tr></thead>';
	
	foreach ($iacc_users as $user)
	{
		
		echo '<tr>';
		echo '<td>'.$user->nickname.'</td>';
		echo '<td>'.$user->user_email.'</td>';
		echo '<td>'.$user->membership_type.'</td>';
		echo '<td><a href="admin.php?page=iacc-manager/iacc_manager.php&action=edit&user_id='.$user->ID.'" title="Edit Membership Details">Edit</a></td>';
		echo '</tr>';
	}

	echo '</table>';
}

function imm_edit_member()
{
	if (is_numeric($_GET['user_id']))
	{
		$user_id = $_GET['user_id'];	
	}
	else
	{
		echo '<b>Search error, please go back and find a user to edit.</b>';
		exit;
	}

	$user = get_user_meta($user_id);
	fb($user);
	$member_meta = get_user_by('id', $user_id);
	$user_email = $member_meta->user_email;
	//verify that a membership type exists. set a default for manually entered users.
	if ( !empty($user['membership_type'][0]) )
	{
		$membership_type = $user['membership_type'][0];	
	}
	else
	{
		$membership_type = 'IACC Attendee';
		update_user_meta($user_id, 'membership_type', $membership_type);
	}
	
	//verify that membership permission exists. set a default for manually entered users.
	if ( !empty($user['member_permissions'][0]) )
	{
		$member_permissions = $user['member_permissions'][0];	
	}
	else
	{
		$member_permissions = 1;
		update_user_meta($user_id, 'member_permissions', $member_permissions);
	}
	
	//verify that membership prettyprint exists. set a default for manually entered users.
	if ( !empty($user['member_prettyprint'][0]) )
	{
		$member_prettyprint = $user['member_prettyprint'][0];	
	}
	else
	{
		$member_prettyprint = 'Attendee';
		update_user_meta($user_id, 'member_prettyprint', $member_prettyprint);
	}

// Prepare dropdown
	$membership_kinds = array(
		'IACC Attendee' 							 => 1,
		'IACC Individual Membership' 				 => 2,
		'IACC Corporate Membership 1-25 Employees'   => 3,
		'IACC Corporate Membership 26-50 Employees'  => 4,
		'IACC Corporate Membership 51-75 Employees'  => 5,
		'IACC Corporate Membership 76-100 Employees' => 6,
		'IACC Silver Donor Membership' 				 => 7,
		'IACC Gold Donor Membership' 				 => 8,
		'IACC Platinum Donor Membership' 			 => 9
		);

	$member_dropdown_form = '<select name="membership_type">';

	foreach ($membership_kinds as $key => $value)
	{
		$member_dropdown_form .= '<option value="'.$value.'"';
		if ($value == $member_permissions)
		{
			$member_dropdown_form .= ' selected="selected"';
		}
		$member_dropdown_form .= '>'.$key.'</option>';
	}	

	$member_dropdown_form .= '</select>';

	//Do the post action if it's set

	if (isset($_POST['submit']) && $_POST['submit'] == 'Edit Member')
	{
		$user_id = $_POST['user_id'];
		$member_permissions = $_POST['membership_type'];

		switch($member_permissions)
		{
			case 2: 
				$membership_type = "IACC Individual Membership";
				$member_prettyprint = 'Member';
				break;
			case 3:
				$membership_type = "IACC Corporate Membership 1-25 Employees";
				$member_prettyprint = 'Corporate Member';
				break;
			case 4:
				$membership_type = "IACC Corporate Membership 26-50 Employees";
				$member_prettyprint = 'Corporate Member';
				break;
			case 5:
				$membership_type = "IACC Corporate Membership 51-75 Employees";
				$member_prettyprint = 'Corporate Member';
				break;
			case 6:
				$membership_type = "IACC Corporate Membership 76-100 Employees";
				$member_prettyprint = 'Corporate Member';
				break;
			case 7:
				$membership_type = "Silver Donor Membership";
				$member_prettyprint = 'Silver Donor';
				break;
			case 8:
				$membership_type = "Gold Donor Membership";
				$member_prettyprint = 'Gold Donor';
				break;
			case 9:
				$membership_type = "Platinum Donor Membership";
				$member_prettyprint = 'Platinum Donor';
				break;
			default:
				$membership_type = "IACC Attendee";
				$member_prettyprint = '0.00';
				break;
		}

		if (isset($membership_type) && isset($member_prettyprint) && isset($member_permissions))
		{
			update_user_meta($user_id, 'membership_type', $membership_type);
			update_user_meta($user_id, 'member_permissions', $member_permissions);
			update_user_meta($user_id, 'member_prettyprint', $member_prettyprint);
			echo '<div class="updated"><p><b>Membership type updated.</b></p></div>';
		}
		else
		{
			echo '<div class="updated"><p><b>There was an error processing the edit. Please try again.</b></p></div>';
		}
	}

	?>

	<h2>Edit Member: <?php echo $user['nickname'][0]; ?></h2>

	<form method="POST">

		<table class="widefat page fixed">
			<thead><tr><th>Field</th><th>Value</th></tr></thead>

			<tr>
				<td>Full Name:</td>
				<td><?php echo $user['first_name'][0].' '.$user['last_name'][0]; ?></td>
			</tr>
			<tr>
				<td>Username:</td>
				<td><?php echo $user['nickname'][0]; ?></td>
			</tr>
			<tr>
				<td>Email:</td>
				<td><?php echo $user_email; ?></td>
			</tr>
			<tr>
				<td>Membership Type:</td>
				<td><?php echo $member_dropdown_form; ?></td>
			</tr>
		</table>
		<br />
		<input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
		<input type="submit" name="submit" value="Edit Member" class="button-primary">

	</form>		
<?php
}

function imm_get_purchased_events()
{
	global $wpdb;
	$sql = "SELECT * FROM ".EVENT_PAYPAL." WHERE tx_state = 1 ORDER BY event_id ASC, timestamp ASC;";
	$purchases = $wpdb->get_results($sql);

	echo '<h2>Purchased Events</h2>';
	echo '<table class="widefat page fixed"><thead><tr><th>Event</th><th>Event Date</th><th>Ticket Quantity</th><th>Ticket Type</th><th>Member</th><th>Email</th><th>Price</th><th>Purchase Date</th></tr></thead>';

	foreach ($purchases as $purchase)
	{
		echo '<tr>';
		echo '<td><a href="admin.php?page=purchased_events&action=event_view&event_id='.$purchase->event_id.'" title="View by Event">'.$purchase->event_title.'</a></td>';
		echo '<td>'.date("F j, Y, g:i a", $purchase->event_date).'</td>';
		echo '<td>'.$purchase->quantity.'</td>';
		echo '<td>'.$purchase->ticket_desc.'</td>';
		echo '<td>'.$purchase->first_name.' '.$purchase->last_name.'</td>';
		echo '<td>'.$purchase->email.'</td>';
		echo '<td>'.$purchase->amt.'</td>';
		echo '<td>'.date("F j, Y, g:i a", $purchase->timestamp).'</td>';
		echo '</tr>';
	}

	echo '</table>';
}

function imm_get_registers_by_event()
{
	global $wpdb;
	$sql = "SELECT * FROM ".EVENT_PAYPAL." WHERE tx_state = 1 AND event_id = ".$_GET['event_id']." ORDER BY timestamp ASC;";
	$purchases = $wpdb->get_results($sql);

	$event_title = $purchases[0]->event_title;

	echo '<h2>'.$event_title.'</h2>';
	echo '<table class="widefat page fixed"><thead><tr><th>Event</th><th>Event Date</th><th>Ticket Quantity</th><th>Ticket Type</th><th>Member</th><th>Email</th><th>Price</th><th>Purchase Date</th></tr></thead>';

	foreach ($purchases as $purchase)
	{
		echo '<tr>';
		echo '<td><a href="admin.php?page=purchased_events&action=event_view&event_id='.$purchase->event_id.'" title="View by Event">'.$purchase->event_title.'</a></td>';
		echo '<td>'.date("F j, Y, g:i a", $purchase->event_date).'</td>';
		echo '<td>'.$purchase->quantity.'</td>';
		echo '<td>'.$purchase->ticket_desc.'</td>';
		echo '<td>'.$purchase->first_name.' '.$purchase->last_name.'</td>';
		echo '<td>'.$purchase->email.'</td>';
		echo '<td>'.$purchase->amt.'</td>';
		echo '<td>'.date("F j, Y, g:i a", $purchase->timestamp).'</td>';
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