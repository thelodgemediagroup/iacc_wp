<?php
/**
* A single event.  This displays the event title, description, meta, and 
* optionally, the Google map for the event.
*
* You can customize this view by putting a replacement file of the same name (single.php) in the events/ directory of your theme.
*/

if (isset($_POST['submit']) && $_POST['submit'] == 'Purchase Tickets')
			{

				// gather _POST data
				
				$event_id = get_the_ID();
				$ticket_prices = get_post_meta($event_id, 'ticket_price');
				$selected_ticket = $_POST['event_cost'];

				// check that selection is valid
				if (!empty($ticket_prices))
				{
					foreach ($ticket_prices as $price)
					{
						if ($selected_ticket == $price)
						{
							$ticket_details = explode(':', $price);
							$ticket_type_desc = $ticket_details[0];
							$ticket_type_price = $ticket_details[1];
						}
					}
				}

				// throw error for invalid selection
				if (!isset($ticket_type_desc) || empty($ticket_type_price))
				{
					echo '<div class="notice">Please re-enter the purchase information</div>';
				}
				elseif (!is_numeric($_POST['ticket_quantity']))
				{
					echo '<div class="notice">Please enter a numeric value for ticket quantity</div>';
				}
				else if ($ticket_type_price == 0)
				{
					$user_id = get_current_user_id();
					$user_info = get_userdata($user_id);
					if (empty($user_info->user_firstname))
					{
						$first_name = 'NA';
					}
					else
					{
						$first_name = $user_info->user_firstname;	
					}
					if (empty($user_info->user_lastname))
					{
						$last_name = 'NA';
					}
					else
					{
						$last_name = $user_info->user_lastname;	
					}
					
					$email = $user_info->user_email;
					$ticket_quantity = intval($_POST['ticket_quantity']);
					$event_title = get_the_title();
					$event_venue = tribe_get_venue();
					$event_date = strtotime(tribe_get_start_date());
					$timestamp = time();
					$tx_state = 1;

					$event_details = array(
						'event_title' => $event_title,
						'event_venue' => $event_venue,
						'event_date' => $event_date,
						'quantity' => $ticket_quantity,
						'amt' => '0.00'
						);

					global $wpdb;
					$sql = $wpdb->prepare(
						"INSERT INTO `event_paypal` (`user_id`, `quantity`, `event_id`, `ticket_desc`, `event_title`, `event_venue`, `event_date`, `tx_state`, `first_name`, `last_name`, `email`, `timestamp`) VALUES (%d,%d,%d,%s,%s,%s,%d,%d,%s,%s,%s,%d)", $user_id, $ticket_quantity, $event_id, $ticket_type_desc, $event_title, $event_venue, $event_date, $tx_state, $first_name, $last_name, $email, $timestamp);
					
					$query = $wpdb->query($sql);

					// email confirmation to the user
					email_event_confirmation($email, $event_details);

					//redirect users to their profile
					$redirect_to = site_url().'/member-admin/';
					header('Location: '.$redirect_to);
					exit;
				}
				else
				{
					
					$ticket_quantity = intval($_POST['ticket_quantity']);

					$user_id = get_current_user_id();
					$event_title = get_the_title();
					$event_venue = tribe_get_venue();
					$event_cost = number_format($ticket_type_price, 2, '.', '');
					$event_date = tribe_get_start_date();

					$paypal_name = 'Tickets for '.$event_title.' '.$event_date;
					$paypal_total = $event_cost * $ticket_quantity;
					$paypal_total_cost = number_format($paypal_total, 2, '.', '');
					$paypal_desc = $event_title;
					$paypal_custom = array();

					$paypal_custom['event_id'] = $event_id;
					$paypal_custom['event_venue'] = $event_venue;
					$paypal_custom['event_title'] = $event_title;
					$paypal_custom['event_date'] = $event_date;
					$paypal_custom['ticket_desc'] = $ticket_type_desc;			

					$paypal_custom_json = json_encode($paypal_custom);

					// set paypal constants
					$paypal_user = 'admin_api1.iaccusa.org';
					$paypal_pwd = 'M6HTJENSVREJY86N';
					$paypal_signature = 'AVZGhB1VUnsI8UmFV0o9yMLggCMKA.uRTpoAgNe9M8CIgbrQ635nmKvN';
					$paypal_target = 'https://api-3t.paypal.com/nvp';

					$paypal_fields = array(
						'USER' => urlencode($paypal_user),
						'PWD' => urlencode($paypal_pwd),
						'SIGNATURE' => urlencode($paypal_signature),
						'METHOD' => urlencode('SetExpressCheckout'),
						'VERSION' => urlencode('72.0'),
						'PAYMENTREQUEST_0_PAYMENTACTION' => urlencode('SALE'),
						'PAYMENTREQUEST_0_AMT' => urlencode($paypal_total_cost),
						'PAYMENTREQUEST_0_AMT0' => urlencode($paypal_total_cost),
						'PAYMENTREQUEST_0_ITEMAMT' => urlencode($paypal_total_cost),
						'L_PAYMENTREQUEST_0_NAME0' => urlencode($paypal_name),
						'L_PAYMENTREQUEST_0_DESC0' => urlencode($paypal_desc),
						'L_PAYMENTREQUEST_0_AMT0' => urlencode($event_cost),
						'L_PAYMENTREQUEST_0_QTY0' => urlencode($ticket_quantity),
						'ITEMAMT' => urlencode($ticket_quantity),
						'PAYMENTREQUEST_0_CUSTOM' => urlencode($paypal_custom_json),
						'PAYMENTREQUEST_0_DESC' => urlencode($paypal_desc),
						'PAYMENTREQUEST_0_CURRENCYCODE' => urlencode('USD'),
						'PAYMENTREQUEST_0_SHIPPINGAMT' => urlencode('0.00'),
						'PAYMENTREQUEST_0_TAXAMT' => urlencode('0.00'),
						'CANCELURL' => urlencode('http://www.iaccusa.org/events/'),
						'RETURNURL' => urlencode('http://www.iaccusa.org/event-confirm/')					
						);
					
					$fields_string = '';

					foreach ($paypal_fields as $key => $value)
					{
						$fields_string .= $key.'='.$value.'&';
					}

					
					rtrim($fields_string,'&');
					
					$ch = curl_init();

					curl_setopt($ch, CURLOPT_URL, $paypal_target);
					curl_setopt($ch, CURLOPT_POST, count($paypal_fields));
					curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
					curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
					curl_setopt($ch, CURLOPT_VERBOSE, 1);

					
					$result = curl_exec($ch);

					curl_close($ch);
					

					
					parse_str($result, $result);

					if ( $result['ACK'] == 'Success')
					{
						$response = urldecode($result['TOKEN']);
						header('Location: https://www.paypal.com/webscr?cmd=_express-checkout&token='.$response);
						exit;
					}
					else
					{
						echo '<div class="notice">The transaction did not initialize, please try again.</div>';
					}
				}
			}

if (isset($_POST['submit']) && $_POST['submit'] == 'Reserve Seats')
{
				// gather _POST data
				
				$event_id = get_the_ID();
				$free_tickets = get_post_meta($event_id, 'free_tickets');
				$selected_ticket = $_POST['event_cost'];
				
				// check that selection is valid
				if (!empty($free_tickets))
				{
					foreach ($free_tickets as $ticket_info)
					{
						if ($selected_ticket == $ticket_info)
						{
							$ticket_details = explode(':', $ticket_info);
							$ticket_type_desc = $ticket_details[0];
							$ticket_type_price = $ticket_details[1];
						}
					}
				}

				// throw error for invalid selection
				if (!isset($ticket_type_desc))
				{
					echo '<div class="notice">Please re-enter the purchase information</div>';
				}
				elseif (!is_numeric($_POST['ticket_quantity']))
				{
					echo '<div class="notice">Please enter a numeric value for ticket quantity</div>';
				}
				
					$user_id = get_current_user_id();
					$user_info = get_userdata($user_id);
					if (empty($user_info->user_firstname))
					{
						$first_name = 'NA';
					}
					else
					{
						$first_name = $user_info->user_firstname;	
					}
					if (empty($user_info->user_lastname))
					{
						$last_name = 'NA';
					}
					else
					{
						$last_name = $user_info->user_lastname;	
					}
					
					$email = $user_info->user_email;
					$ticket_quantity = intval($_POST['ticket_quantity']);
					$event_title = get_the_title();
					$event_venue = tribe_get_venue();
					$event_date = strtotime(tribe_get_start_date());
					$timestamp = time();
					$tx_state = 1;

					$event_details = array(
						'event_title' => $event_title,
						'event_venue' => $event_venue,
						'event_date' => $event_date,
						'quantity' => $ticket_quantity,
						'amt' => '0.00'
						);

					global $wpdb;
					$sql = $wpdb->prepare(
						"INSERT INTO `event_paypal` (`user_id`, `quantity`, `event_id`, `ticket_desc`, `event_title`, `event_venue`, `event_date`, `tx_state`, `first_name`, `last_name`, `email`, `timestamp`) VALUES (%d,%d,%d,%s,%s,%s,%d,%d,%s,%s,%s,%d)", $user_id, $ticket_quantity, $event_id, $ticket_type_desc, $event_title, $event_venue, $event_date, $tx_state, $first_name, $last_name, $email, $timestamp);
					
					$query = $wpdb->query($sql);

					// email the confirmation to user
					email_event_confirmation($email, $event_details);

					//redirect users to their profile
					$redirect_to = site_url().'/member-admin/';
					header('Location: '.$redirect_to);
					exit;
				
}

//require_once('paypal_event.php');
// Don't load directly
if ( !defined('ABSPATH') ) { die('-1'); }
?>
<span class="back"><a href="<?php echo tribe_get_events_link(); ?>"><?php _e('&laquo; Back to Events', 'tribe-events-calendar'); ?></a></span>				

<?php
	$gmt_offset = (get_option('gmt_offset') >= '0' ) ? ' +' . get_option('gmt_offset') : " " . get_option('gmt_offset');
 	$gmt_offset = str_replace( array( '.25', '.5', '.75' ), array( ':15', ':30', ':45' ), $gmt_offset );
 	if (strtotime( tribe_get_end_date(get_the_ID(), false, 'Y-m-d G:i') . $gmt_offset ) <= time() ) { ?><div class="event-passed"><?php  _e('This event has passed.', 'tribe-events-calendar'); ?></div><?php } ?>
<div id="tribe-events-event-meta" itemscope itemtype="http://schema.org/Event">
	<?php
		if (!is_user_logged_in())
		{
			echo '<div class="info"><a href="'.site_url().'/login/" title="Member Login">Login</a> to purchase tickets</div>';
		}
	?>
	<dl class="column">
		<dt class="event-label event-label-name"><?php _e('Event:', 'tribe-events-calendar'); ?></dt>
		<dd itemprop="name" class="event-meta event-meta-name"><span class="summary"><?php the_title(); ?></span></dd>
		<?php if (tribe_get_start_date() !== tribe_get_end_date() ) { ?>
			<dt class="event-label event-label-start"><?php _e('Start:', 'tribe-events-calendar'); ?></dt> 
			<dd class="event-meta event-meta-start"><meta itemprop="startDate" content="<?php echo tribe_get_start_date( null, false, 'Y-m-d-h:i:s' ); ?>"/><?php echo tribe_get_start_date(); ?></dd>
			<dt class="event-label event-label-end"><?php _e('End:', 'tribe-events-calendar'); ?></dt>
			<dd class="event-meta event-meta-end"><meta itemprop="endDate" content="<?php echo tribe_get_end_date( null, false, 'Y-m-d-h:i:s' ); ?>"/><?php echo tribe_get_end_date(); ?></dd>						
		<?php } else { ?>
			<dt class="event-label event-label-date"><?php _e('Date:', 'tribe-events-calendar'); ?></dt> 
			<dd class="event-meta event-meta-date"><meta itemprop="startDate" content="<?php echo tribe_get_start_date( null, false, 'Y-m-d-h:i:s' ); ?>"/><?php echo tribe_get_start_date(); ?></dd>
		<?php } ?>
		<?php if ( tribe_get_cost() ) : ?>
			<dt class="event-label event-label-cost"><?php _e('Cost:', 'tribe-events-calendar'); ?></dt>
			<dd itemprop="price" class="event-meta event-meta-cost"><?php echo tribe_get_cost(); ?></dd>
		<?php endif; ?>
		<?php tribe_meta_event_cats(); ?>
		<?php if ( tribe_get_organizer_link( get_the_ID(), false, false ) ) : ?>
			<dt class="event-label event-label-organizer"><?php _e('Organizer:', 'tribe-events-calendar'); ?></dt>
			<dd class="vcard author event-meta event-meta-author"><span class="fn url"><?php echo tribe_get_organizer_link(); ?></span></dd>
      <?php elseif (tribe_get_organizer()): ?>
			<dt class="event-label event-label-organizer"><?php _e('Organizer:', 'tribe-events-calendar'); ?></dt>
			<dd class="vcard author event-meta event-meta-author"><span class="fn url"><?php echo tribe_get_organizer(); ?></span></dd>
		<?php endif; ?>
		<?php if ( tribe_get_organizer_phone() ) : ?>
			<dt class="event-label event-label-organizer-phone"><?php _e('Phone:', 'tribe-events-calendar'); ?></dt>
			<dd itemprop="telephone" class="event-meta event-meta-phone"><?php echo tribe_get_organizer_phone(); ?></dd>
		<?php endif; ?>
		<?php if ( tribe_get_organizer_email() ) : ?>
			<dt class="event-label event-label-email"><?php _e('Email:', 'tribe-events-calendar'); ?></dt>
			<dd itemprop="email" class="event-meta event-meta-email"><a href="mailto:<?php echo tribe_get_organizer_email(); ?>"><?php echo tribe_get_organizer_email(); ?></a></dd>
		<?php endif; ?>
		<dt class="event-label event-label-updated"><?php _e('Updated:', 'tribe-events-calendar'); ?></dt>
		<dd class="event-meta event-meta-updated"><span class="date updated"><?php the_date(); ?></span></dd>
		<?php if ( class_exists('TribeEventsRecurrenceMeta') && function_exists('tribe_get_recurrence_text') && tribe_is_recurring_event() ) : ?>
			<dt class="event-label event-label-schedule"><?php _e('Schedule:', 'tribe-events-calendar'); ?></dt>
         <dd class="event-meta event-meta-schedule"><?php echo tribe_get_recurrence_text(); ?> 
            <?php if( class_exists('TribeEventsRecurrenceMeta') && function_exists('tribe_all_occurences_link')): ?>(<a href='<?php tribe_all_occurences_link(); ?>'>See all</a>)<?php endif; ?>
         </dd>
		<?php endif; ?>
	</dl>
	<dl class="column" itemprop="location" itemscope itemtype="http://schema.org/Place">
		<?php if(tribe_get_venue()) : ?>
		<dt class="event-label event-label-venue"><?php _e('Venue:', 'tribe-events-calendar'); ?></dt> 
		<dd itemprop="name" class="event-meta event-meta-venue">
			<?php if( class_exists( 'TribeEventsPro' ) ): ?>
				<?php tribe_get_venue_link( get_the_ID(), class_exists( 'TribeEventsPro' ) ); ?>
			<?php else: ?>
				<?php echo tribe_get_venue( get_the_ID() ); ?>
			<?php endif; ?>
		</dd>
		<?php endif; ?>
		<?php if(tribe_get_phone()) : ?>
		<dt class="event-label event-label-venue-phone"><?php _e('Phone:', 'tribe-events-calendar'); ?></dt> 
			<dd itemprop="telephone" class="event-meta event-meta-venue-phone"><?php echo tribe_get_phone(); ?></dd>
		<?php endif; ?>
		<?php if( tribe_address_exists( get_the_ID() ) ) : ?>
		<dt class="event-label event-label-address">
			<?php _e('Address:', 'tribe-events-calendar') ?><br />
			<?php if( tribe_show_google_map_link( get_the_ID() ) ) : ?>
				<a class="gmap" itemprop="maps" href="<?php echo tribe_get_map_link(); ?>" title="<?php _e('Click to view a Google Map', 'tribe-events-calendar'); ?>" target="_blank"><?php _e('Google Map', 'tribe-events-calendar' ); ?></a>
			<?php endif; ?>
		</dt>
			<dd class="event-meta event-meta-address">
			<?php echo tribe_get_full_address( get_the_ID() ); ?>
			</dd>
		<?php endif; ?>

	</dl>
  
   	<?php if( function_exists('tribe_the_custom_fields') && tribe_get_custom_fields( get_the_ID() ) ): ?>
	  	<?php tribe_the_custom_fields( get_the_ID() ); ?>
	<?php endif; ?>

<!-- PURCHASE TICKETS FORM -->

<?php //prepare price data

$event_id = get_the_ID();
$ticket_prices = get_post_meta($event_id, 'ticket_price');
$free_tickets = get_post_meta($event_id, 'free_tickets');
$event_cost_form = '<select name="event_cost">';

if (!empty($ticket_prices))
{
	foreach ($ticket_prices as $price)
	{
		$ticket = explode(':', $price);
		$ticket_desc = $ticket[0];
		$ticket_price = $ticket[1];
		$event_cost_form .= '<option value="'.$price.'">'.$ticket_desc.' ($'.$ticket_price.')</option>';
	}
}

if (!empty($free_tickets))
{
	foreach($free_tickets as $free)
	{
		$ticket = explode(':', $free);
		$ticket_desc = $ticket[0];
		$ticket_price = $ticket[1];
		$event_cost_form .= '<option value="'.$free.'">'.$ticket_desc.' (Free)</option>';
	}
}

$event_cost_form .= '</select>';

?>

<?php 
$user_id = get_current_user_id();
if (is_user_logged_in())
{ 
	if (!empty($ticket_prices))
	{?>
		<dl class="column">
			<form method="post" action="<?php the_permalink(); ?>">
		
				<dt class="event-label">Ticket Type:</dt>
				<dd>
					<?php echo $event_cost_form; ?>
				</dd>
				<dt class="event-label">Ticket Quantity:</dt>
				<dd><input type="text" size="10" name="ticket_quantity"></dd>

				<dt class="event-label"></dt>
				<dd><input type="submit" name="submit" value="Purchase Tickets"></dd>
			
			</form>
		</dl>
	<?php } 
	else
	{?>
		<dl class="column">
			<form method="post" action="<?php the_permalink(); ?>">
		
				<dt class="event-label">Ticket Type:</dt>
				<dd>
					<?php echo $event_cost_form; ?>
				</dd>
				<dt class="event-label">Seat Quantity:</dt>
				<dd><input type="text" size="10" name="ticket_quantity"></dd>

				<dt class="event-label"></dt>
				<dd><input type="submit" name="submit" value="Reserve Seats"></dd>
			
			</form>
		</dl>		
	<?php } ?>	
<?php } ?>


</div>
<?php if( tribe_embed_google_map( get_the_ID() ) ) : ?>
<?php if( tribe_address_exists( get_the_ID() ) ) { echo tribe_get_embedded_map(); } ?>
<?php endif; ?>
<div class="entry-content">
	<?php
	if ( function_exists('has_post_thumbnail') && has_post_thumbnail() ) {?>
		<?php the_post_thumbnail(); ?>
	<?php } ?>
	<div class="summary"><?php the_content(); ?></div>
	<?php if (function_exists('tribe_get_ticket_form') && tribe_get_ticket_form()) { tribe_get_ticket_form(); } ?>		
</div>
<?php if( function_exists('tribe_get_single_ical_link') ): ?>
   <a class="ical single" href="<?php echo tribe_get_single_ical_link(); ?>"><?php _e('iCal Import', 'tribe-events-calendar'); ?></a>
<?php endif; ?>
<?php if( function_exists('tribe_get_gcal_link') ): ?>
   <a href="<?php echo tribe_get_gcal_link(); ?>" class="gcal-add" title="<?php _e('Add to Google Calendar', 'tribe-events-calendar'); ?>"><?php _e('+ Google Calendar', 'tribe-events-calendar'); ?></a>
<?php endif; ?>

<div style="clear:both"></div>