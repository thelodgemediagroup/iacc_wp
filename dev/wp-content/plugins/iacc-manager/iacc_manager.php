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
add_action('init', 'create_csv_members');

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
	/*else if ( $action == 'create_csv' )
	{
		
		#create_csv_members();

	} */

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

	echo '<br />';
	echo '<p><a href="admin.php?page=iacc-manager/iacc_manager.php&action=create_members_csv" title="Create CSV of Member Emails" class="button-primary">Create Email CSV</a></p>';
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

	if (!empty($purchases))
	{
		echo '<table class="widefat page fixed"><thead><tr><th>Event</th><th>Event Date</th><th>Ticket Quantity</th><th>Ticket Type</th><th>Member</th><th>Email</th><th>Price</th><th>Purchase Date</th></tr></thead>';

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
	}
	else
	{
		echo '<p>No events purchased to display</p>';
	}
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
}

function imm_get_membership_upgrades()
{
	global $wpdb;
	$sql = "SELECT * FROM ".MEMBER_PAYPAL." WHERE tx_state = 1 ORDER BY timestamp DESC;";
	$members = $wpdb->get_results($sql);

	echo '<h2>Membership Upgrades</h2>';

	if (!empty($members))
	{
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
		//echo '<td><a href="'.site_url().'/wp-admin/admin.php?page=sponsors_slider&action=edit&img='.$key.'&spons_url='.$spons_url.'">Edit</a></td>';
		echo '<td><a href="'.site_url().'/wp-admin/admin.php?page=sponsors_slider&action=delete&img='.$key.'&spons_url='.$spons_url.'" onclick="return confirm(\'Are you sure you want to delete this sponsor?\')">Delete</a></td>';
		echo '</tr>';
	}

	echo '</table>';
}
/*
function imm_edit_sponsor()
{
	$sponsor_img = $_GET['img'];
	$sponsor_url = stripslashes($_GET['spons_url']);

	if (isset($_POST['submit']) && $_POST['submit'] == 'Edit Sponsor')
	{
		if (!empty($_POST['sponsor_link']))
		{
			$up_sponsor_link = $_POST['sponsor_link'];
		}
		if (!empty($_FILES['img_file']))
		{
			$file_name = $_FILES['img_file']['name'];
			$file_size = $_FILES['img_file']['size'];
			$file_error = $_FILES['img_file']['error'];
			$file_tmp = $_FILES['img_file']['tmp_name'];

			$file_split = explode('.', $file_name);
			$split_length = count($file_split);
			$file_ext = intval($split_length - 1);
			$file_type = $file_split[$file_ext];	
		}
		if (!isset($up_sponsor_link) && !isset($file_name))
		{
			echo '<div class="updated"><p>Please enter a New URL, a new image file, or both.</p></div>';
		}
		else
		{
			if ()
		}

	}
?>
	<h2>Edit Sponsor</h2>
	<div class="postbox">
		<table>
			<tr>
				<td><b>Current Image:</b></td>
				<td><img src="<?php echo SPONSOR_PATH.$sponsor_img; ?>"></td>
			</tr>
			<tr>
				<td><b>Current Link URL:</b></td>
				<td><a href="<?php echo $sponsor_url; ?>"><?php echo $sponsor_url; ?></a></td>
			</tr>
		</table>
	</div>

	<h2>Update</h2>

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
		<input type="submit" name="submit" class="button-primary" value="Edit Sponsor">
	</form>

<?php
}
*/
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
		
							echo '<li style="height: 100px; width: 210px;"><a href="'.$spons_url.'"><img src="'.site_url().'/wp-content/plugins/iacc-manager/sponsors/'.$key.'"></a></li>';

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

function create_csv_members()
{
	if ( (isset($_GET['action'])) && ($_GET['action'] == 'create_members_csv') )
	{
		$filename = 'ALL_IACC_Members_' . date('Y-m-d-H-i-s') . '.csv';
		header( 'Content-Description: File Transfer' );
		header( 'Content-Disposition: attachment; filename=' . $filename );
		header( 'Content-Type: text/csv; charset=' . get_option( 'blog_charset' ), true );

		global $wpdb;

		$attendee_args = array('key' => 'membership_type', 'value' => 'attendee');
		$member_args = array('key' => 'membership_type', 'value' => 'member');
		$corp_member_args = array('key' => 'membership_type', 'value' => 'corporate_member');
		$user_args = array('relation' => 'OR', $attendee_args, $member_args, $corp_member_args);
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
}
?>