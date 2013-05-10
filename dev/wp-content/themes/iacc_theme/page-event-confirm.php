<?php
/*
Template Name: Event Confirm
*/
?>

<?php get_header(); ?>

<?php 	get_template_part('part', 'layouts');		
		global $_theme_layout;
?>


	<section class="container sbr clearfix">

		<?php get_template_part('part', 'title'); ?>

		<section id="content">

			<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

				<?php the_content(); ?>

			<?php endwhile; endif; ?>


			<?php // logic for paypal event confirm

			function confirm_paypal()
			{
				$paypal_user = 'iacctest_api1.iacc.org';
				$paypal_pwd = '1364059762';
				$paypal_signature = 'A7TqZwXuy-wkefzg6ZJOzSRN4BT0AMAkmbyUdSVB.gp7RG-kMNotJJ-O';

				$paypal_target = 'https://api-3t.sandbox.paypal.com/nvp';

				$fields = array(
					'USER' => urlencode($paypal_user),
					'PWD' => urlencode($paypal_pwd),
					'SIGNATURE' => urlencode($paypal_signature),
					'VERSION' => urlencode('72.0'),
					'TOKEN' => urlencode($_GET['token']),
					'METHOD' => urlencode('GetExpressCheckoutDetails')
					);

				$fields_string = '';

				foreach ($fields as $key => $value)
				{
					$fields_string .= $key .'='.$value.'&';
				}

				rtrim($fields_string,'&');

				$ch = curl_init();

				curl_setopt($ch, CURLOPT_URL, $paypal_target);
				curl_setopt($ch, CURLOPT_POST, count($fields));
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

					// get the user id					
					$user_id = get_current_user_id();

					//unpack the custom field
					$paypal_json = stripslashes($result['CUSTOM']);
					$event_detail = json_decode($paypal_json, TRUE);

					// set the rest of the vars
					fb($event_detail);
					$event_id = $event_detail['event_id'];
					$event_date = strtotime($event_detail['event_date']);
					$event_venue = $event_detail['event_venue'];
					$event_title = $event_detail['event_title'];
					$ticket_desc = $event_detail['ticket_desc'];
					$email = $result['EMAIL'];
					$first_name = $result['FIRSTNAME'];
					$last_name = $result['LASTNAME'];
					$quantity = $result['L_QTY0'];
					$token = $result['TOKEN'];
					$timestamp = strtotime($result['TIMESTAMP']);
					$payerid = $result['PAYERID'];
					$amt = $result['AMT'];
					$tx_state = 0;

					global $wpdb;
					$sql = $wpdb->prepare(
						"INSERT INTO `event_paypal` (`user_id`, `event_id`, `event_title`, `event_venue`, `event_date`, `ticket_desc`, `email`, `first_name`, `last_name`, `token`, `timestamp`, `quantity`, `tx_state`, `amt`, `payer_id`) VALUES (%d,%d,%s,%s,%d,%s,%s,%s,%s,%s,%d,%d,%d,%d,%s)", $user_id, $event_id, $event_title, $event_venue, $event_date, $ticket_desc, $email, $first_name, $last_name, $token, $timestamp, $quantity, $tx_state, $amt, $payerid);

					$query = $wpdb->query($sql);

					return $result;
				}
				else
				{
					echo '<div class="notice">The transaction did not complete, please try again.</div>';	
				}
			}
			//Get info for building the confirmation form

				$result = confirm_paypal();

				// get info from DB about user
				$user_id = get_current_user_id();
				$user = get_user_meta($user_id, 'nickname', true);


			if (isset($_POST['submit']) && $_POST['submit'] == 'Confirm Purchase')
				{
					// Check token in form against token in DB
					$chk_val = $_POST['chk_val'];
					$chk_val2 = $_POST['chk_val2'];

					global $wpdb;
					$sql = $wpdb->prepare(
						"SELECT * FROM `event_paypal` WHERE `token` = '".$_POST['chk_val']."' AND `timestamp` = '".$_POST['chk_val2']."'");
					$query = $wpdb->get_results($sql);
					$token = $query[0]->token;
					$timestamp = $query[0]->timestamp;
					$payer_id = $query[0]->payer_id;
					$amt = $query[0]->amt;
					$event_title = $query[0]->event_title;
					$event_date = $query[0]->event_date;
					$event_venue = $query[0]->event_venue;
					$ticket_quantity = $query[0]->quantity;

					//get the users IACC email
					$user_id_post = get_current_user_id();
					$user_info = get_userdata($user_id_post);
					$user_email = $user_info->user_email;


					$event_details = array(
						'event_title' => $event_title,
						'event_venue' => $event_venue,
						'event_date' => $event_date,
						'quantity' => $ticket_quantity,
						'amt' => $amt
						);					

					if ($chk_val != $token || $chk_val2 != $timestamp)
					{
						echo '<div class="notice">An error occurred during processing. Please try again</div>';
					}
					else
					{

						$paypal_user = 'iacctest_api1.iacc.org';
						$paypal_pwd = '1364059762';
						$paypal_signature = 'A7TqZwXuy-wkefzg6ZJOzSRN4BT0AMAkmbyUdSVB.gp7RG-kMNotJJ-O';

						$paypal_target = 'https://api-3t.sandbox.paypal.com/nvp';

						$fields = array(
				              'USER' => urlencode($paypal_user),
				              'PWD' => urlencode($paypal_pwd),
				              'SIGNATURE' => urlencode($paypal_signature),
				              'VERSION' => urlencode('72.0'),
				              'PAYMENTREQUEST_0_PAYMENTACTION' => urlencode('Sale'),
				              'PAYERID' => urlencode($payer_id),
				              'TOKEN' => urlencode($token),
				              'PAYMENTREQUEST_0_AMT' => urlencode($amt),
				              'METHOD' => urlencode('DoExpressCheckoutPayment')
				          );
						
						$fields_string = '';
				      	foreach ( $fields as $key => $value)
				        {
				        	$fields_string .= $key.'='.$value.'&';
				        }
				     	rtrim($fields_string,'&');
				     
				     	$ch = curl_init();

						curl_setopt($ch, CURLOPT_URL, $paypal_target);
						curl_setopt($ch, CURLOPT_POST, count($fields));
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

							//prepare paypal and post data for db insert

							$transaction_id = $result['PAYMENTINFO_0_TRANSACTIONID'];
							$fee = $result['PAYMENTINFO_0_FEEAMT'];

							// Change state to success

							$tx_state = 1;


							//insert data into database
							global $wpdb;
							$sql = $wpdb->prepare(
								"UPDATE `event_paypal` SET `transaction_id`=%s, `fee`=%d, `tx_state`=%d WHERE `token`=%s AND `timestamp`=%d", $transaction_id, $fee, $tx_state, $token, $timestamp);

							$query = $wpdb->query($sql);

							// email confirmation to the user
							email_event_confirmation($user_email, $event_details);

							//redirect users to their profile
							$redirect_to = site_url().'/member-admin/';
							header('Location: '.$redirect_to);
							exit;

						}
						else
						{
							echo '<div class="notice">Payment did not complete. Please try again</div>';
						}
					}
				}
			?>

			<?php

			?>

			<form action="<?php echo the_permalink(); ?>" method="POST">

				<table>

					<tr>
						<td>IACC User:</td>
						<td><?php echo $user ?></td>
					</tr>
					<tr>
						<td>First Name:</td>
						<td><?php echo $result['FIRSTNAME']; ?></td>
					</tr>
					<tr>
						<td>Last Name:</td>
						<td><?php echo $result['LASTNAME']; ?></td>
					</tr>
					<tr>
						<td>Email:</td>
						<td><?php echo $result['EMAIL']; ?></td>
					</tr>
					<tr>
						<td>Address:</td>
						<td><?php echo $result['SHIPTOSTREET']; ?></td>
					</tr>
					<tr>
						<td>City:</td>
						<td><?php echo $result['SHIPTOCITY']; ?></td>
					</tr>
					<tr>
						<td>ZIP:</td>
						<td><?php echo $result['SHIPTOZIP']; ?></td>
					</td>
					<tr>
						<td>Country</td>
						<td><?php echo $result['SHIPTOCOUNTRYNAME']; ?></td>
					</tr>
					<tr>
						<td>Item:</td>
						<td><?php echo $result['L_NAME0']; ?></td>
					</tr>
					<tr>
						<td>Ticket Quantity:</td>
						<td><?php echo $result['L_PAYMENTREQUEST_0_QTY0']; ?></td>
					</tr>
					<tr>
						<td>Cost Per Ticket:</td>
						<td>$<?php echo $result['L_PAYMENTREQUEST_0_AMT0']; ?></td>
					</tr>
					<tr>
						<td>Billing Total:</td>
						<td><?php echo '$'.$result['AMT'].' '.$result['CURRENCYCODE']; ?></td>
					</tr>
				</table>

				<input type="submit" name="submit" value="Confirm Purchase">

				<input type="hidden" name="chk_val" value="<?php echo $result['TOKEN']; ?>">
				<input type="hidden" name="chk_val2" value="<?php echo strtotime($result['TIMESTAMP']); ?>">

			</form>

			<form method="get" action="<?php echo site_url(); ?>/events/">
				<input type="submit" name="confirm-cancel" value="Cancel">
			</form>



		</section><!--/ #content -->

		

	</section><!--/ .container -->

<?php get_footer(); ?>