<?php
/**
* A single event.  This displays the event title, description, meta, and 
* optionally, the Google map for the event.
*
* You can customize this view by putting a replacement file of the same name (single.php) in the events/ directory of your theme.
*/
ob_start();
if (isset($_POST['submit']) && $_POST['submit'] == 'Purchase Tickets')
			{

				// gather _POST data
				$user_id = $_POST['user_id'];
				$ticket_quantity = $_POST['ticket_quantity'];
				$event_id = $_POST['event_id'];
				$event_title = $_POST['event_title'];
				$event_venue = $_POST['event_venue'];
				$event_cost = number_format($_POST['event_cost'], 2, '.', '');
				$event_date = $_POST['event_date'];

				$paypal_name = 'Tickets for '.$event_title.' '.$event_date;
				$paypal_total = $event_cost * $ticket_quantity;
				$paypal_total_cost = number_format($paypal_total, 2, '.', '');
				$paypal_desc = $event_title;
				$paypal_custom = array();

				$paypal_custom['event_id'] = $event_id;
				$paypal_custom['event_venue'] = $event_venue;
				$paypal_custom['event_title'] = $event_title;
				$paypal_custom['event_date'] = $event_date;				

				$paypal_custom_json = json_encode($paypal_custom);

				// set paypal constants
				$paypal_user = 'iacctest_api1.iacc.org';
				$paypal_pwd = '1364059762';
				$paypal_signature = 'A7TqZwXuy-wkefzg6ZJOzSRN4BT0AMAkmbyUdSVB.gp7RG-kMNotJJ-O';
				$paypal_target = 'https://api-3t.sandbox.paypal.com/nvp';

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
					'CANCELURL' => urlencode('http://iacc.thelodgemediagroup.com/events/'),
					'RETURNURL' => urlencode('http://iacc.thelodgemediagroup.com/event-confirm/')					
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
					header('Location: https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&token='.$response);
					exit;
				}
				else
				{
					echo '<b>The transaction did not initialize, please try again.</b>';
				}
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
			echo '<b>Login to purchase tickets</b>';
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
<?php 
$user_id = get_current_user_id();
if (is_user_logged_in())
{ ?>
	<dl class="column">
		<form method="post" action="<?php the_permalink(); ?>">
	
			<dt class="event-label">Ticket Quantity:</dt>
			<dd><input type="text" size="10" name="ticket_quantity"></dd>
				<input type="hidden" name="event_id" value="<?php the_ID(); ?>">
				<input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
				<input type="hidden" name="event_cost" value="<?php echo tribe_get_cost(); ?>">
				<input type="hidden" name="event_venue" value="<?php echo tribe_get_venue(); ?>">
				<input type="hidden" name="event_date" value="<?php echo tribe_get_start_date(); ?>">
				<input type="hidden" name="event_title" value="<?php the_title(); ?>">
			<dt class"event-label"></dt>
			<dd><input type="submit" name="submit" value="Purchase Tickets"></dd>
		
		</form>
	</dl>
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

<div class="navlink tribe-previous"><?php tribe_previous_event_link(); ?></div>

<div class="navlink tribe-next"><?php tribe_next_event_link(); ?></div>
<div style="clear:both"></div>