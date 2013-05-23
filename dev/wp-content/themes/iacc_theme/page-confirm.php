<?php
/*
Template Name: Upgrade Confirm
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


			<?php // logic for paypal upgrade

			function confirm_paypal()
			{
				$paypal_user = 'admin_api1.iaccusa.org';
				$paypal_pwd = 'M6HTJENSVREJY86N';
				$paypal_signature = 'AVZGhB1VUnsI8UmFV0o9yMLggCMKA.uRTpoAgNe9M8CIgbrQ635nmKvN';
				$paypal_target = 'https://api-3t.paypal.com/nvp';

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
					$token = $result['TOKEN'];
					$timestamp = strtotime($result['TIMESTAMP']);
					$amt = $result['AMT'];
					$user_id = get_current_user_id();
					$nickname = get_user_meta($user_id, 'nickname', true);
					$membership_type = $result['DESC'];
					$member_permissions = $result['CUSTOM'];
					$email = $result['EMAIL'];
					$first_name = $result['FIRSTNAME'];
					$last_name = $result['LASTNAME'];
					$payer_id = $result['PAYERID'];
					$tx_state = 0;

					switch($member_permissions)
						{
							case 2:
								$member_prettyprint = 'Member';
								break;
							case 3:
								$member_prettyprint = 'Corporate Member';
								break;
							case 4:
								$member_prettyprint = 'Corporate Member';
								break;
							case 5:
								$member_prettyprint = 'Corporate Member';
								break;
							case 6:
								$member_prettyprint = 'Corporate Member';
								break;
							case 7:
								$member_prettyprint = 'Silver Donor';
								break;
							case 8:
								$member_prettyprint = 'Gold Donor';
								break;
							case 9:
								$member_prettyprint = 'Platinum Donor';
								break;
						}

					//insert data into database
					global $wpdb;
					$sql = $wpdb->prepare(
						"INSERT INTO `member_paypal` (`user_id`, `nickname`, `description`, `permissions`, `pretty_print`, `amt`, `email`, `first_name`, `last_name`, `token`, `timestamp`, `payer_id`, `tx_state`) VALUES (%d,%s,%s,%d,%s,%d,%s,%s,%s,%s,%d,%s,%d)", $user_id, $nickname, $membership_type, $member_permissions, $member_prettyprint, $amt, $email, $first_name, $last_name, $token, $timestamp, $payer_id, $tx_state);

					$query = $wpdb->query($sql);

					return $result;
				}
				else
				{
					echo '<div class="notice">The transaction did not complete, please try again.</div>';	
				}
			}


			if (isset($_POST['submit']) && $_POST['submit'] == 'Confirm Purchase')
				{
					global $wpdb;
					$sql = $wpdb->prepare(
						"SELECT * FROM `member_paypal` WHERE `token` = %s AND `timestamp` = %d", $_POST['chk_val'], $_POST['chk_val2']);

					$query = $wpdb->get_results($sql);

					$payer_id = $query[0]->payer_id;
					$token = $query[0]->token;
					$timestamp = $query[0]->timestamp;
					$amt = $query[0]->amt;
					$membership_type = $query[0]->description;
					$member_prettyprint = $query[0]->pretty_print;
					$member_permissions = $query[0]->permissions;
					$user_id = $query[0]->user_id;
					
					$paypal_user = 'admin_api1.iaccusa.org';
					$paypal_pwd = 'M6HTJENSVREJY86N';
					$paypal_signature = 'AVZGhB1VUnsI8UmFV0o9yMLggCMKA.uRTpoAgNe9M8CIgbrQ635nmKvN';
					$paypal_target = 'https://api-3t.paypal.com/nvp';

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
						$tx_state = 1;

						//update wp tables with member data
						update_user_meta($user_id, 'membership_type', $membership_type);
						update_user_meta($user_id, 'member_permissions', $member_permissions);
						update_user_meta($user_id, 'member_prettyprint', $member_prettyprint);

						//insert data into database
							global $wpdb;
							$sql = $wpdb->prepare(
								"UPDATE `member_paypal` SET `transaction_id`=%s, `fee`=%d, `tx_state`=%d WHERE `token`=%s AND `timestamp`=%d", $transaction_id, $fee, $tx_state, $token, $timestamp);

						$query = $wpdb->query($sql);

						$redirect_to = site_url().'/member-admin/';

						//redirect users to their profile

						wp_redirect($redirect_to);
						exit;

					}
					else
					{
						echo '<div class="notice">Payment did not complete. Please try again</div>';
					}
				}
			?>


			<?php //Get info for building the confirmation form

				$result = confirm_paypal();

				// get info from DB about user
				$user_id = get_current_user_id();
				$user = get_user_meta($user_id, 'nickname', true);

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
						<td>Upgrade Type:</td>
						<td><?php echo $result['L_NAME0']; ?></td>
					</tr>
					<tr>
						<td>Billing Total:</td>
						<td><?php echo '$'.$result['AMT'].' '.$result['CURRENCYCODE']; ?></td>
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

				</table>

				<input type="hidden" name="chk_val" value="<?php echo $result['TOKEN']; ?>">

				<input type="hidden" name="chk_val2" value="<?php echo strtotime($result['TIMESTAMP']); ?>">

				<input type="submit" name="submit" value="Confirm Purchase">

			</form>

			<form method="get" action="<?php echo site_url(); ?>/upgrade/">
				<input type="submit" name="confirm-cancel" value="Cancel">
			</form>



		</section><!--/ #content -->

		

	</section><!--/ .container -->

<?php get_footer(); ?>

