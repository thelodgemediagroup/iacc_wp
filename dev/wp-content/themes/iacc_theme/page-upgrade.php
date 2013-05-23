<?php
/*
Template Name: Membership Upgrade
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

			// Set prices for the user to select from
			// # appended represents max employees
			$individual_member_price = '75.00';
			$corporate_member_25_price = '250.00';
			$corporate_member_50_price = '500.00';
			$corporate_member_75_price = '750.00';
			$corporate_member_100_price = '1000.00';
			$donor_silver_price = '1500.00';
			$donor_gold_price = '2500.00';
			$donor_platinum_price = '5000.00';

			// Pack data into form for paypal
			if (isset($_POST['submit']) && $_POST['submit'] == 'Upgrade Membership')
			{

				// gather _POST data
				$user_id = get_current_user_id();
				$membership_type = $_POST['upgrade_type'];
				$paypal_name = "IACC Membership Upgrade";

				switch($membership_type)
				{
					case 2: 
						$paypal_description = "IACC Individual Membership";
						$paypal_price = $individual_member_price;
						break;
					case 3:
						$paypal_description = "IACC Corporate Membership 1-25 Employees";
						$paypal_price = $corporate_member_25_price;
						break;
					case 4:
						$paypal_description = "IACC Corporate Membership 26-50 Employees";
						$paypal_price = $corporate_member_50_price;
						break;
					case 5:
						$paypal_description = "IACC Corporate Membership 51-75 Employees";
						$paypal_price = $corporate_member_75_price;
						break;
					case 6:
						$paypal_description = "IACC Corporate Membership 76-100 Employees";
						$paypal_price = $corporate_member_100_price;
						break;
					case 7:
						$paypal_description = "Silver Donor Membership";
						$paypal_price = $donor_silver_price;
						break;
					case 8:
						$paypal_description = "Gold Donor Membership";
						$paypal_price = $donor_gold_price;
						break;
					case 9:
						$paypal_description = "Platinum Donor Membership";
						$paypal_price = $donor_platinum_price;
						break;
					default:
						$paypal_description = "IACC Attendee";
						$paypal_price = 0.00;
						$membership_type = 1;
						echo '<div class="notice">Please re-enter the upgrade details</div>';
						break;
				}

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
					'PAYMENTREQUEST_0_AMT' => urlencode($paypal_price),
					'PAYMENTREQUEST_0_AMT0' => urlencode($paypal_price),
					'PAYMENTREQUEST_0_ITEMAMT' => urlencode($paypal_price),
					'PAYMENTREQUEST_0_CUSTOM' => urlencode($membership_type),
					'L_PAYMENTREQUEST_0_NAME0' => urlencode($paypal_name),
					'L_PAYMENTREQUEST_0_DESC0' => urlencode($paypal_description),
					'L_PAYMENTREQUEST_0_AMT0' => urlencode($paypal_price),
					'L_PAYMENTREQUEST_0_QTY0' => urlencode('1'),
					//'ITEMAMT' => urlencode('1'),
					'PAYMENTREQUEST_0_DESC' => urlencode($paypal_description),
					'PAYMENTREQUEST_0_CURRENCYCODE' => urlencode('USD'),
					'PAYMENTREQUEST_0_SHIPPINGAMT' => urlencode('0.00'),
					'PAYMENTREQUEST_0_TAXAMT' => urlencode('0.00'),
					'CANCELURL' => urlencode('http://www.iaccusa.org/upgrade'),
					'RETURNURL' => urlencode('http://www.iaccusa.org/confirm')					
					);

				$fields_string = '';

				foreach ($paypal_fields as $key => $value)
				{
					$fields_string .= $key.'='.$value.'&';
				}

				
				rtrim($fields_string,'&');
				
				if ($paypal_price > 0)
				{

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
			?>


			<form action="<?php echo the_permalink(); ?>" method="POST">

				<select name="upgrade_type">

					<optgroup label="Donor Membership">
						<option value="7">Silver ($<?php echo $donor_silver_price; ?>)</option>
						<option value="8">Gold ($<?php echo $donor_gold_price; ?>)</option>
						<option value="9">Platinum ($<?php echo $donor_platinum_price; ?>)</option>
					</optgroup>
					<optgroup label="Corporate Membership">
						<option value="3">1-25 Employees ($<?php echo $corporate_member_25_price; ?>)</option>	
						<option value="4">26-50 Employees ($<?php echo $corporate_member_50_price; ?>)</option>
						<option value="5">51-75 Employees ($<?php echo $corporate_member_75_price; ?>)</option>
						<option value="6">76-100 Employees ($<?php echo $corporate_member_100_price; ?>)</option>
					</optgroup>
					<optgroup label="Individual Membership">
						<option value="2">Individual Member ($<?php echo $individual_member_price; ?>)</option>	
					</optgroup>
					
				</select><br />

				<br />
				<input type="submit" name="submit" value="Upgrade Membership" id="upgrade-button" class="button style-5">

			</form>



		</section><!--/ #content -->

		

	</section><!--/ .container -->

<?php get_footer(); ?>