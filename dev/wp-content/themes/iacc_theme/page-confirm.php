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
					return $result;
				}
				else
				{
					echo '<b>The transaction did not complete, please try again.</b>';	
				}
			}


			if (isset($_POST['submit']) && $_POST['submit'] == 'Confirm Purchase')
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
			              'PAYERID' => urlencode($_POST['PAYERID']),
			              'TOKEN' => urlencode($_POST['TOKEN']),
			              'PAYMENTREQUEST_0_AMT' => urlencode($_POST['AMT']),
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
						//change membership type
						//set variables
						$membership_type = $_POST['DESC'];
						$member_permissions = $_POST['CUSTOM'];
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

						//prepare paypal and post data for db insert
						$token = $result['TOKEN'];
						$timestamp = strtotime($result['TIMESTAMP']);
						$transaction_id = $result['PAYMENTINFO_0_TRANSACTIONID'];
						$amt = $result['PAYMENTINFO_0_AMT'];
						$fee_amt = $result['PAYMENTINFO_0_FEEAMT'];
						$user_id = $_POST['user_id'];
						$nickname = $_POST['user'];
						$description = $_POST['DESC'];
						$email = $_POST['EMAIL'];
						$first_name = $_POST['FIRSTNAME'];
						$last_name = $_POST['LASTNAME'];

						//update wp tables with member data
						update_user_meta($user_id, 'membership_type', $membership_type);
						update_user_meta($user_id, 'member_permissions', $member_permissions);
						update_user_meta($user_id, 'member_prettyprint', $member_prettyprint);

						//insert data into database
						global $wpdb;
						$sql = $wpdb->prepare(
							"INSERT INTO `member_paypal` (`user_id`, `nickname`, `description`, `permissions`, `pretty_print`, `amt`, `fee`, `email`, `first_name`, `last_name`, `token`, `timestamp`, `transaction_id`) VALUES (%d,%s,%s,%d,%s,%d,%d,%s,%s,%s,%s,%d,%s)", $user_id, $nickname, $membership_type, $member_permissions, $member_prettyprint, $amt, $fee_amt, $email, $first_name, $last_name, $token, $timestamp, $transaction_id);

						$query = $wpdb->query($sql);

						$redirect_to = site_url().'/member-admin/';

						//redirect users to their profile

						wp_redirect($redirect_to);
						exit;

					}
					else
					{
						echo 'Payment did not complete. Please try again';
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

				<input type="hidden" name="user_id" value="<?php echo $user_id; ?>">

				<input type="hidden" name="user" value="<?php echo $user; ?>">

				<input type="submit" name="submit" value="Confirm Purchase">

				<?php 

					foreach($result as $key => $value)
					{
						echo '<input type="hidden" name ="'.$key.'" value="'.$value.'">';
					}

				?>

			</form>

			<form method="get" action="<?php echo site_url(); ?>/upgrade/">
				<input type="submit" name="confirm-cancel" value="Cancel">
			</form>



		</section><!--/ #content -->

		

	</section><!--/ .container -->

<?php get_footer(); ?>

