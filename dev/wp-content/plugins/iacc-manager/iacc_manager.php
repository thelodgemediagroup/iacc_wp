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
define('SPONSOR_PATH', '../wp-content/plugins/iacc-manager/sponsors/');
require_once(ABSPATH . 'wp-config.php');
require_once(ABSPATH . 'wp-load.php');

require_once(ABSPATH . "wp-admin" . '/includes/image.php');
require_once(ABSPATH . "wp-admin" . '/includes/file.php');
require_once(ABSPATH . "wp-admin" . '/includes/media.php');

add_action('admin_menu', 'imm_create_menu');
add_action('admin_init', 'imm_load_scripts');
add_action('init', 'create_csv');

// Load datepicker scripts
function imm_load_scripts()
{
	$load_jquery_ui  = plugins_url( '/js/jquery-ui-1.10.3.custom.min.js', __FILE__);
	$load_datepicker = plugins_url( '/js/datepicker.js', __FILE__);
	$load_calendar_style = plugins_url( '/css/jquery-ui-1.10.3.custom.min.css', __FILE__);
	wp_enqueue_script('load_datepicker', $load_datepicker, array('jquery', 'load_jquery_ui'));
	wp_enqueue_script('load_jquery_ui', $load_jquery_ui, array('jquery'));
	wp_enqueue_style('load_calendar_style', $load_calendar_style);
}

// Create the dashboard links

function imm_create_menu()
{
	$imm_icon_location = plugin_dir_url( __FILE__ ).'/images/plugin_logo.png';
	$imm_create_settings = add_menu_page('IACC Manager', 'IACC', 'administrator', __FILE__,'imm_member_action',$imm_icon_location,'30');
	$imm_append_events = add_submenu_page(__FILE__, 'Purchased Events', 'Purchased Events', 'administrator', 'purchased_events', 'imm_event_action');
	$imm_append_members = add_submenu_page(__FILE__, 'Membership Upgrades', 'Membership Upgrades', 'administrator', 'membership_upgrades', 'imm_get_membership_upgrades');
	$imm_append_sponsors = add_submenu_page(__FILE__, 'Sponsors Slider', 'Sponsors Slider', 'administrator', 'sponsors_slider', 'imm_sponsor_action');
}

function imm_member_action()
{

	$action  = !empty($_REQUEST['action']) ? $_REQUEST['action'] : 'view';

	if ( $action == 'view' )
	{
		imm_view_ending_memberships();
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

function imm_sponsor_action()
{
	$action  = !empty($_REQUEST['action']) ? $_REQUEST['action'] : 'add';

	if ( $action == 'add' )
	{
		imm_add_sponsor();
		imm_get_all_sponsors();
	}
	else if ( $action == 'edit' )
	{
		imm_edit_sponsor();
	}
	else if ( $action == 'delete' )
	{
		imm_delete_sponsor();
	}
}

function imm_view_ending_memberships()
{
	global $wpdb;

	$row_style = '';
	$membership_length = 31536000;
	$membership_length_days = 365;
	$day = 86400;
	$three_days = 259200;
	$seven_days = 604800;
	$two_weeks = 1209600;
	$one_month = 2592000;
	$warning_period = $membership_length - $one_month;

	$sql = $wpdb->prepare("SELECT * FROM member_paypal WHERE UNIX_TIMESTAMP() > `timestamp` AND (UNIX_TIMESTAMP() - `timestamp`) > %d AND tx_state = 1;", $warning_period);
	$ends = $wpdb->get_results($sql);
	$ends_count = count($ends);

	if (!empty($ends))
	{

		echo '<div class="updated"><h2>Memberships Ending Soon ('.$ends_count.')</h2>';
		echo '<table class="widefat page fixed" style="margin-bottom:6px;"><thead><tr>';
		echo '<th>Member</th>';
		echo '<th>Membership Type</th>';
		echo '<th>Email</th>';
		echo '<th>Ending In</th>';
		echo '<th>End Date</th>';
		echo '<th>Edit</th>';
		echo '</tr></thead>';

		foreach ($ends as $end)
		{
			$ending_in = intval($membership_length_days - intval((time() - $end->timestamp) / $day));

			switch($ending_in)
			{
				case $ending_in <= 0:
					$row_style = ' style="background-color: #DC7868;"';
					$ending_message = 'ENDED';
					break;

				case $ending_in == 1:
					$row_style = ' style="background-color: #EFEFB6;"';
					$ending_message = $ending_in . ' day';
					break;

				case $ending_in <= 3:
					$row_style = ' style="background-color: #EFEFB6;"';					
					$ending_message = $ending_in . ' days';
					break;

				default:
					$row_style = '';
					$ending_message = $ending_in . ' days';
					break;
			}
				

			$end_date_math = $end->timestamp + $membership_length;
			$end_date = date("m/d/y", $end_date_math);

			echo '<tr'.$row_style.'>';
			echo '<td>'.$end->nickname.'</td>';
			echo '<td>'.$end->description.'</td>';
			echo '<td>'.$end->email.'</td>';
			echo '<td>'.$ending_message.'</td>';
			echo '<td>'.$end_date.'</td>';
			echo '<td><a href="admin.php?page=iacc-manager/iacc_manager.php&action=edit&user_id='.$end->user_id.'" title="Edit Membership Details">Edit</a></td>';
			echo '</tr>';
		}

		echo '</table>';
		echo '<br />';
		echo '<p><a href="admin.php?page=iacc-manager/iacc_manager.php&action=create_expiring_csv" title="Create CSV of Expiring Memberships" class="button-primary">Create Expiring Memberships CSV</a></p>';		
		echo '</div>';

	}
	

}

function imm_view_all_members()
{
	global $wpdb;

	$order_by = !empty($_GET['order_by']) ? $_GET['order_by'] : 'ID';

	switch($order_by)
	{
		case 'member':
			$order_members_by = 'nickname';
			break;
		case 'email':
			$order_members_by = 'email';
			break;
		case 'membership_type':
			$order_members_by = 'member_permissions';
			break;
		default:
			$order_members_by = 'ID';
			break;
	}

	$user_args = array(
		'meta_key' => 'nickname',
		'orderby' => $order_members_by
		);
	$iacc_users = get_users($user_args);
	$user_count = count($iacc_users);

	echo '<h2>Current Members ('.$user_count.')</h2>';
	echo '<table class="widefat page fixed"><thead><tr>';
	echo '<th><a href="admin.php?page=iacc-manager/iacc_manager.php&order_by=member">Member</a></th>';
	echo '<th><a href="admin.php?page=iacc-manager/iacc_manager.php&order_by=email">Email</a></th>';
	echo '<th><a href="admin.php?page=iacc-manager/iacc_manager.php&order_by=membership_type">Membership Type</a></th>';
	echo '<th>Edit</th>';
	echo '</tr></thead>';
	
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
	echo '<br />';
	echo '<p><a href="admin.php?page=iacc-manager/iacc_manager.php&action=create_members_csv&order_by='.$order_by.'" title="Create CSV of Member Emails" class="button-primary">Create Email CSV</a></p>';
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
	$member_meta = get_user_by('id', $user_id);
	$user_email = $member_meta->user_email;

	global $wpdb;

	$sql = "SELECT * FROM ".MEMBER_PAYPAL." WHERE user_id = ".$user_id." AND tx_state = 1 ORDER BY timestamp DESC, amt DESC LIMIT 1;";
	$purchase = $wpdb->get_results($sql);

	// select the most recent record in membership transactions
	$purchase_date = !empty($purchase[0]->timestamp) ? $purchase[0]->timestamp : '';
	$purchase_amt = !empty($purchase[0]->amt) ? $purchase[0]->amt : 0.00;
	$transaction_state = !empty($purchase) ? 1 : 0;
	$purchase_print_date = !empty($purchase[0]->timestamp) ? date("m/d/y", $purchase_date) : date("m/d/y", time());
	$tx_id = !empty($purchase[0]->tx_id) ? $purchase[0]->tx_id : '';

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

	if (isset($_POST['submit']) && $_POST['submit'] == 'Save Member Changes')
	{		
		$user_id = $_POST['user_id'];
		$member_permissions = $_POST['membership_type'];
		$membership_payment = number_format($_POST['membership_payment'], 2, '.', '');
		$membership_date = strtotime($_POST['membership_date']);
		$transaction_state = $_POST['transaction_state'];
		$tx_id = $_POST['tx_id'];
		$first_name = $_POST['first_name'];
		$last_name = $_POST['last_name'];
		$email = $_POST['email'];
		$nickname = $_POST['nickname'];

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
				$member_permissions = 1;
				$membership_type = "IACC Attendee";
				$member_prettyprint = 'Attendee';
				break;
		}

		if (isset($membership_type) && isset($member_prettyprint) && isset($member_permissions) && is_numeric($membership_payment))
		{
			global $wpdb;
			
			if ($member_permissions > 1)
			{
				// insert a new record
				$sql = $wpdb->prepare("INSERT INTO `".MEMBER_PAYPAL."` (`timestamp`, `amt`, `description`, `pretty_print`, `permissions`, `user_id`, `nickname`, `first_name`, `last_name`, `email`, `tx_state`) VALUES (%d,%d,%s,%s,%d,%d,%s,%s,%s,%s,%d)", $membership_date, $membership_payment, $membership_type, $member_prettyprint, $member_permissions, $user_id, $nickname, $first_name, $last_name, $email, 1);
				$query = $wpdb->query($sql);
			}

			if ($transaction_state == 1)
			{
				// expire the old record
				$sql = $wpdb->prepare("UPDATE `".MEMBER_PAYPAL."` SET `tx_state` = %d WHERE `tx_id` = %d", 2, $tx_id);
				$query = $wpdb->query($sql);				
			}
			
			// Upate the wordpress user meta values
			update_user_meta($user_id, 'membership_type', $membership_type);
			update_user_meta($user_id, 'member_permissions', $member_permissions);
			update_user_meta($user_id, 'member_prettyprint', $member_prettyprint);

			$redirect_path = '/wp-admin/admin.php?page=iacc-manager/iacc_manager.php';
			header('Location: '.site_url().$redirect_path);
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
			<tr>
				<td>Membership Payment</td>
				<td>$<input type="text" id="payment" name="membership_payment" value="<?php echo $purchase_amt; ?>"/></td>
			</tr>
			<tr>
				<td>Membership Date:</td>
				<td><input type="text" id="datepicker" name="membership_date" value="<?php echo $purchase_print_date; ?>"/></td>
			</tr>
		</table>
		<br />
		<input type="hidden" name="user_id" value="<?php echo $user_id; ?>"/>
		<input type="hidden" name="nickname" value="<?php echo $user['nickname'][0]; ?>"/>
		<input type="hidden" name="email" value="<?php echo $user_email; ?>"/>
		<input type="hidden" name="first_name" value="<?php echo $user['first_name'][0]; ?>"/>
		<input type="hidden" name="last_name" value="<?php echo $user['last_name'][0]; ?>"/>
		<input type="hidden" name="transaction_state" value="<?php echo $transaction_state; ?>"/>
		<input type="hidden" name="tx_id" value="<?php echo $tx_id; ?>"/>
		<input type="submit" name="submit" value="Save Member Changes" class="button-primary"/>

	</form>		
<?php
}

function imm_get_purchased_events()
{
	global $wpdb;

	$order_by = !empty($_GET['order_by']) ? $_GET['order_by'] : 'event_id ASC, timestamp ASC';

	$sql = "SELECT * FROM ".EVENT_PAYPAL." WHERE tx_state = 1 ORDER BY ".$order_by.";";
	$purchases = $wpdb->get_results($sql);

	echo '<h2>Purchased Events</h2>';

	if (!empty($purchases))
	{
		echo '<table class="widefat page fixed"><thead><tr>';
		echo '<th><a href="admin.php?page=purchased_events&order_by=event_id">Event</a></th>';
		echo '<th><a href="admin.php?page=purchased_events&order_by=event_date">Event Date</a></th>';
		echo '<th><a href="admin.php?page=purchased_events&order_by=quantity">Ticket Quantity</a></th>';
		echo '<th><a href="admin.php?page=purchased_events&order_by=ticket_desc">Ticket Type</a></th>';
		echo '<th><a href="admin.php?page=purchased_events&order_by=first_name">Member</a></th>';
		echo '<th><a href="admin.php?page=purchased_events&order_by=email">Email</a></th>';
		echo '<th><a href="admin.php?page=purchased_events&order_by=amt">Price</a></th>';
		echo '<th><a href="admin.php?page=purchased_events&order_by=timestamp">Purchase Date</a></th>';
		echo '</tr></thead>';

		foreach ($purchases as $purchase)
		{

			if ($purchase->first_name == 'NA' || $purchase->last_name == 'NA')
			{
				$full_name = 'N/A';
			}
			else
			{
				$full_name = $purchase->first_name.' '.$purchase->last_name;
			}
			echo '<tr>';
			echo '<td><a href="admin.php?page=purchased_events&action=event_view&event_id='.$purchase->event_id.'" title="View by Event">'.$purchase->event_title.'</a></td>';
			echo '<td>'.date("F j, Y, g:i a", $purchase->event_date).'</td>';
			echo '<td>'.$purchase->quantity.'</td>';
			echo '<td>'.$purchase->ticket_desc.'</td>';
			echo '<td>'.$full_name.'</td>';
			echo '<td>'.$purchase->email.'</td>';
			echo '<td>'.$purchase->amt.'</td>';
			echo '<td>'.date("F j, Y, g:i a", $purchase->timestamp).'</td>';
			echo '</tr>';
		}

		echo '</table>';
		echo '<br />';
		echo '<p><a href="admin.php?page=purchased_events&action=create_event_purchases_csv&order_by='.$order_by.'" title="Create CSV of Event Purchase Emails" class="button-primary">Create Event Purchase CSV</a></p>';
	}
	else
	{
		echo '<p>No events purchased to display</p>';
	}
}

function imm_get_registers_by_event()
{
	global $wpdb;

	$order_by = !empty($_GET['order_by']) ? $_GET['order_by'] : 'timestamp ASC';

	$sql = "SELECT * FROM ".EVENT_PAYPAL." WHERE tx_state = 1 AND event_id = ".$_GET['event_id']." ORDER BY ".$order_by.";";
	$purchases = $wpdb->get_results($sql);

	$event_title = $purchases[0]->event_title;

	echo '<h2>'.$event_title.'</h2>';
		echo '<table class="widefat page fixed"><thead><tr>';
		echo '<th><a href="admin.php?page=purchased_events&action=event_view&event_id='.$_GET['event_id'].'&order_by=event_id">Event</a></th>';
		echo '<th><a href="admin.php?page=purchased_events&action=event_view&event_id='.$_GET['event_id'].'&order_by=event_date">Event Date</a></th>';
		echo '<th><a href="admin.php?page=purchased_events&action=event_view&event_id='.$_GET['event_id'].'&order_by=quantity">Ticket Quantity</a></th>';
		echo '<th><a href="admin.php?page=purchased_events&action=event_view&event_id='.$_GET['event_id'].'&order_by=ticket_desc">Ticket Type</a></th>';
		echo '<th><a href="admin.php?page=purchased_events&action=event_view&event_id='.$_GET['event_id'].'&order_by=first_name">Member</a></th>';
		echo '<th><a href="admin.php?page=purchased_events&action=event_view&event_id='.$_GET['event_id'].'&order_by=email">Email</a></th>';
		echo '<th><a href="admin.php?page=purchased_events&action=event_view&event_id='.$_GET['event_id'].'&order_by=amt">Price</a></th>';
		echo '<th><a href="admin.php?page=purchased_events&action=event_view&event_id='.$_GET['event_id'].'&order_by=timestamp">Purchase Date</a></th>';
		echo '</tr></thead>';


	foreach ($purchases as $purchase)
	{
		if ($purchase->first_name == 'NA' || $purchase->last_name == 'NA')
		{
			$full_name = 'N/A';
		}
		else
		{
			$full_name = $purchase->first_name.' '.$purchase->last_name;
		}

		echo '<tr>';
		echo '<td><a href="admin.php?page=purchased_events&action=event_view&event_id='.$purchase->event_id.'" title="View by Event">'.$purchase->event_title.'</a></td>';
		echo '<td>'.date("F j, Y, g:i a", $purchase->event_date).'</td>';
		echo '<td>'.$purchase->quantity.'</td>';
		echo '<td>'.$purchase->ticket_desc.'</td>';
		echo '<td>'.$full_name.'</td>';
		echo '<td>'.$purchase->email.'</td>';
		echo '<td>'.$purchase->amt.'</td>';
		echo '<td>'.date("F j, Y, g:i a", $purchase->timestamp).'</td>';
		echo '</tr>';
	}

	echo '</table>';
	echo '<br />';
	echo '<p><a href="admin.php?page=purchased_events&action=create_registers_by_event_csv&event_id='.$_GET['event_id'].'&order_by='.$order_by.'" title="Create CSV of Emails for Purchases of This Event" class="button-primary">Create Event Purchase CSV</a></p>';
}

function imm_get_membership_upgrades()
{
	$order_by = !empty($_GET['order_by']) ? $_GET['order_by'] : 'timestamp DESC';

	global $wpdb;
	$sql = "SELECT * FROM ".MEMBER_PAYPAL." WHERE tx_state >= 1 ORDER BY ".$order_by.";";
	$members = $wpdb->get_results($sql);

	echo '<h2>Membership Upgrades</h2>';

	if (!empty($members))
	{
		echo '<table class="widefat page fixed"><thead><tr>';
		echo '<th><a href="admin.php?page=membership_upgrades&order_by=first_name">Member Name</a></th>';
		echo '<th><a href="admin.php?page=membership_upgrades&order_by=nickname">Nickname</a></th>';
		echo '<th><a href="admin.php?page=membership_upgrades&order_by=email">Email</a></th>';
		echo '<th><a href="admin.php?page=membership_upgrades&order_by=description">Membership Type</a></th>';
		echo '<th><a href="admin.php?page=membership_upgrades&order_by=amt">Price</a></th>';
		echo '<th><a href="admin.php?page=membership_upgrades&order_by=timestamp">Purchase Date</a></th>';
		echo '<th><a href="admin.php?page=membership_upgrades&order_by=tx_state">Status</a></th>';
		echo '</tr></thead>';

		foreach ($members as $member)
		{
			if ($member->tx_state == 1)
			{
				$status = 'Active';
			}
			else if ($member->tx_state == 2)
			{
				$status = 'Expired';
			}
			echo '<tr>';
			echo '<td>'.$member->first_name.' '.$member->last_name.'</td>';
			echo '<td>'.$member->nickname.'</td>';
			echo '<td>'.$member->email.'</td>';
			echo '<td>'.$member->description.'</td>';
			echo '<td>'.$member->amt.'</td>';
			echo '<td>'.date("jS F, Y", $member->timestamp).'</td>';
			echo '<td>'.$status.'</td>';
			echo '</tr>';
		}

		echo '</table>';
		echo '<br />';
		echo '<p><a href="admin.php?page=purchased_events&action=create_member_upgrade_csv&order_by='.$order_by.'" title="Create CSV of Membership Upgrades" class="button-primary">Create Membership Upgrade CSV</a></p>';
	}
	else
	{
		echo '<div class="updated"><p>No membership purchases to display</p></div>';
	}
}

function imm_add_sponsor()
{
	if (isset($_POST['submit']) && $_POST['submit'] == 'Add Sponsor')
	{
		$sponsor_link = $_POST['sponsor_link'];

		$file_name = $_FILES['img_file']['name'];
		$file_size = $_FILES['img_file']['size'];
		$file_error = $_FILES['img_file']['error'];
		$file_tmp = $_FILES['img_file']['tmp_name'];

		$file_split = explode('.', $file_name);
		$split_length = count($file_split);
		$file_ext = intval($split_length - 1);
		$file_type = trim($file_split[$file_ext]);

		
		if ($file_size > 20480)
		{
			echo '<div class="updated"><p>The uploaded file is too large. Please submit a file less than 20kb in size</p></div>';
		}
		else if ($file_error === 1)
		{
			echo '<div class="updated"><p>An error occurred during processing. Please try again.</p></div>';
		}
		else if ($file_type == 'jpg' || $file_type == 'jpeg' || $file_type == 'png' or $file_type == 'gif')
		{
			$move_file = move_uploaded_file($file_tmp, SPONSOR_PATH.$file_name);

			if ($move_file != true)
			{
				echo '<div class="update"><p>The file transfer was not successful. Please try the upload again.</p></div>';
			}
			else
			{
				$sponsors = get_option('imm_sponsors');
				if (!$sponsors)
				{
					$sponsors = array();
					$sponsors[$file_name] = $sponsor_link;

					update_option( 'imm_sponsors', $sponsors);	
				}
				else
				{
					$sponsors[$file_name] = $sponsor_link;
					update_option( 'imm_sponsors', $sponsors);	
				}
				
			}			
			
		}		
		else
		{
			echo '<div class="updated"><p>The uploaded image file is not of a valid type. Please submit a JPG, PNG, or GIF file.</p></div>';
		}
	}

	?>
	<h2>Add Sponsor</h2>
	<form enctype="multipart/form-data" method="post" action="">
		<div class="postbox">
			<table>
				<tr>
					<td>Sponsor Link (Must begin with http://)</td>
					<td><input type="text" size="60" name="sponsor_link"></td>
				</tr>
				<tr>
					<td>Sponsor Image (210px width x 75px height)</td>
					<td><input type="file" name="img_file"></td>
				</tr>
			</table>
		</div>
		<input type="submit" name="submit" class="button-primary" value="Add Sponsor">
	</form>

	<?php
}

function imm_get_all_sponsors()
{
	$imm_sponsors = get_option('imm_sponsors');
	
	?>
	<br />
	<br />
	<br />
	<h2>Sponsors</h2>

	<table class="widefat page fixed">
		<thead>
			<tr><th>Image</th><th>Link</th><!--<th>Edit</th>--><th>Delete</th></tr>
		</thead>
	<?php

	foreach ($imm_sponsors as $key => $value)
	{
		$spons_url = stripslashes($value);
		echo '<tr>';
		echo '<td><img src="'.SPONSOR_PATH.$key.'" height="75px" width="210px"></td>';
		echo '<td><a href="'.$value.'">'.$value.'</a></td>';
		echo '<td><a href="'.site_url().'/wp-admin/admin.php?page=sponsors_slider&action=delete&img='.$key.'&spons_url='.$spons_url.'" onclick="return confirm(\'Are you sure you want to delete this sponsor?\')">Delete</a></td>';
		echo '</tr>';
	}

	echo '</table>';
}

function imm_delete_sponsor()
{
	$sponsor_img = $_GET['img'];
	$sponsor_url = stripslashes($_GET['spons_url']);

	$sponsors = get_option('imm_sponsors');
	$sponsor_reset = array();

	foreach ($sponsors as $key => $value)
	{
		if ($key != $sponsor_img && $value != $sponsor_url)
		{
			$sponsor_reset[$key] = $value;
		}
	}

	update_option('imm_sponsors', $sponsor_reset);
	unlink(SPONSOR_PATH.$sponsor_img);
	header('Location: '.site_url().'/wp-admin/admin.php?page=sponsors_slider');
}

function imm_display_sponsor_slider()
{
$sponsors = get_option('imm_sponsors');

if (!empty($sponsors))
{	
?>
	<script src="<?php echo site_url(); ?>/wp-content/plugins/iacc-manager/js/sponsors.js"></script>
		<script src="<?php echo site_url(); ?>/wp-content/plugins/iacc-manager/js/jcarousellite_1.0.1.js"></script>
		<link href="<?php echo site_url(); ?>/wp-content/plugins/iacc-manager/css/style.css" rel="stylesheet">
		<div class="wpic_container">
			<div class="wpic_navigation">
				<button style="float:right; background: url('<?php echo site_url(); ?>/wp-content/plugins/iacc-manager/images/next.png') no-repeat;" class="wpic_next"></button>
				<button style="float:left; background: url('<?php echo site_url(); ?>/wp-content/plugins/iacc-manager/images/prev.png') no-repeat;" class="wpic_prev"></button>
			</div>
			
			<div class="wpic_content">
				<ul class="wpic_gallery">
					<?php 
						foreach ($sponsors as $key => $value)
						{
							$spons_url = stripslashes($value);
		
							echo '<li style="height: 100px; width: 210px;"><a href="'.$spons_url.'" target="_blank"><img src="'.site_url().'/wp-content/plugins/iacc-manager/sponsors/'.$key.'"></a></li>';

						}
					?>
				</ul>
			</div>	
		</div>
		<?php
}
else
{
	return;
}
}

function create_csv()
{
	if ( (isset($_GET['action'])) && ($_GET['action'] == 'create_members_csv') )
	{
		$filename = 'ALL_IACC_Members_' . date('Y-m-d-H-i-s') . '.csv';
		header( 'Content-Description: File Transfer' );
		header( 'Content-Disposition: attachment; filename=' . $filename );
		header( 'Content-Type: text/csv; charset=' . get_option( 'blog_charset' ), true );

		global $wpdb;

		$order_by = !empty($_GET['order_by']) ? $_GET['order_by'] : 'ID';

		switch($order_by)
		{
			case 'member':
				$order_members_by = 'nickname';
				break;
			case 'email':
				$order_members_by = 'email';
				break;
			case 'membership_type':
				$order_members_by = 'member_permissions';
				break;
			default:
				$order_members_by = 'ID';
				break;
		}

		$user_args = array(
			'meta_key' => 'nickname',
			'orderby' => $order_members_by
			);
		$iacc_users = get_users($user_args);


		foreach ($iacc_users as $user)
		{
			$iacc_members = array();
			$member_format = '"'.$user->user_email.'"';
			$iacc_members[] = $member_format;
			echo implode(',', $iacc_members)."\n";
		}
		exit;
	}

	if ( (isset($_GET['action'])) && ($_GET['action'] == 'create_event_purchases_csv') )
	{
		$filename = 'All_Event_Purchases_' . date('Y-m-d-H-i-s') . '.csv';
		header( 'Content-Description: File Transfer' );
		header( 'Content-Disposition: attachment; filename=' . $filename );
		header( 'Content-Type: text/csv; charset=' . get_option( 'blog_charset' ), true );	

		global $wpdb;

		$order_by = !empty($_GET['order_by']) ? $_GET['order_by'] : 'event_id ASC, timestamp ASC';

		$sql = "SELECT * FROM ".EVENT_PAYPAL." WHERE tx_state = 1 ORDER BY ".$order_by.";";
		$purchases = $wpdb->get_results($sql);		

		foreach ($purchases as $purchase)
		{
			$event_purchases = array();
			$purchase_format = '"'.$purchase->email.'"';
			$event_purchases[] = $purchase_format;
			echo implode(',', $event_purchases)."\n";
		}
		exit;
	}

	if ( (isset($_GET['action'])) && ($_GET['action'] == 'create_registers_by_event_csv') )
	{
		$file_string = 'Event_ID_' . $_GET['event_id'] . '_';
		$filename = $file_string . date('Y-m-d-H-i-s') . '.csv';
		header( 'Content-Description: File Transfer' );
		header( 'Content-Disposition: attachment; filename=' . $filename );
		header( 'Content-Type: text/csv; charset=' . get_option( 'blog_charset' ), true );		

		global $wpdb;

		$order_by = !empty($_GET['order_by']) ? $_GET['order_by'] : 'timestamp ASC';

		$sql = "SELECT * FROM ".EVENT_PAYPAL." WHERE tx_state = 1 AND event_id = ".$_GET['event_id']." ORDER BY ".$order_by.";";
		$purchases = $wpdb->get_results($sql);

		foreach ($purchases as $purchase)
		{
			$event_purchases = array();
			$purchase_format = '"'.$purchase->email.'"';
			$event_purchases[] = $purchase_format;
			echo implode(',', $event_purchases)."\n";			
		}
		exit;
	}

	if ( (isset($_GET['action'])) && ($_GET['action'] == 'create_member_upgrade_csv') )
	{
		$filename = 'Membership_Upgrades_' . date('Y-m-d-H-i-s') . '.csv';
		header( 'Content-Description: File Transfer' );
		header( 'Content-Disposition: attachment; filename=' . $filename );
		header( 'Content-Type: text/csv; charset=' . get_option( 'blog_charset' ), true );			

		$order_by = !empty($_GET['order_by']) ? $_GET['order_by'] : 'timestamp DESC';

		global $wpdb;
		$sql = "SELECT * FROM ".MEMBER_PAYPAL." WHERE tx_state = 1 ORDER BY ".$order_by.";";
		$members = $wpdb->get_results($sql);

		foreach ($members as $member)
		{
			$membership_purchases = array();
			$purchase_format = '"'.$member->email.'"';
			$membership_purchases[] = $purchase_format;
			echo implode(',', $membership_purchases)."\n";			
		}
		exit;
	}

	if ( (isset($_GET['action'])) && ($_GET['action'] == 'create_expiring_csv') )
	{
		$filename = 'Expiring_Memberships_' . date('Y-m-d-H-i-s') . '.csv';
		header( 'Content-Description: File Transfer' );
		header( 'Content-Disposition: attachment; filename=' . $filename );
		header( 'Content-Type: text/csv; charset=' . get_option( 'blog_charset' ), true );		

		global $wpdb;

		$membership_length = 31536000;
		$membership_length_days = 365;
		$day = 86400;
		$three_days = 259200;
		$seven_days = 604800;
		$two_weeks = 1209600;
		$one_month = 2592000;
		$warning_period = $membership_length - $one_month;

		$sql = $wpdb->prepare("SELECT * FROM member_paypal WHERE UNIX_TIMESTAMP() > `timestamp` AND (UNIX_TIMESTAMP() - `timestamp`) > %d AND tx_state = 1;", $warning_period);
		$ends = $wpdb->get_results($sql);

		foreach ($ends as $end)
		{
			$member_expirations = array();
			$expiration_format = '"'.$end->email.'"';
			$member_expirations[] = $expiration_format;
			echo implode(',', $member_expirations)."\n";			
		}
		exit;
	}
}
?>