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
			$member_price = '39.00';
			$corporate_member_price = '100.00';

			// Pack data into form for paypal
			if (isset($_POST['submit']) && $_POST['submit'] == 'Upgrade Membership')
			{

				// gather _POST data
				$user_id = $_POST['user_id'];
				$membership_type = $_POST['upgrade_type'];
				if ($_POST['upgrade_type'] == 'member')
				{
					$paypal_description = 'IACC Membership';
				}
				elseif ($_POST['upgrade_type'] == 'corporate_member')
				{
					$paypal_description = 'IACC Corporate Membership';
				}
				if ($membership_type == 'member') {$paypal_price = $member_price;}
				elseif ($membership_type) {$paypal_price = $corporate_member_price;}
				else {$paypal_price = null;}

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
					'PAYMENTREQUEST_0_AMT' => urlencode($paypal_price),
					'PAYMENTREQUEST_0_AMT0' => urlencode($paypal_price),
					'PAYMENTREQUEST_0_ITEMAMT' => urlencode($paypal_price),
					'L_PAYMENTREQUEST_0_NAME0' => urlencode($paypal_description),
					'L_PAYMENTREQUEST_0_DESC0' => urlencode($membership_type),
					'L_PAYMENTREQUEST_0_AMT0' => urlencode($paypal_price),
					'L_PAYMENTREQUEST_0_QTY0' => urlencode('1'),
					'ITEMAMT' => urlencode('1'),
					'PAYMENTREQUEST_0_DESC' => urlencode($membership_type),
					'PAYMENTREQUEST_0_CURRENCYCODE' => urlencode('USD'),
					'PAYMENTREQUEST_0_SHIPPINGAMT' => urlencode('0.00'),
					'PAYMENTREQUEST_0_TAXAMT' => urlencode('0.00'),
					'CANCELURL' => urlencode('http://iacc.thelodgemediagroup.com/upgrade'),
					'RETURNURL' => urlencode('http://iacc.thelodgemediagroup.com/confirm')					
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
			?>


			<form action="<?php echo the_permalink(); ?>" method="POST">

				<select name="upgrade_type">

					<option value="member">Member ($<?php echo $member_price; ?>)</option>
					<option value="corporate_member">Corporate Member ($<?php echo $corporate_member_price; ?>)</option>

				</select><br />

				<input type="hidden" name="user_id" value="<?php echo get_current_user_id(); ?>">

				<input type="submit" name="submit" value="Upgrade Membership">

			</form>



		</section><!--/ #content -->

		

	</section><!--/ .container -->

<?php get_footer(); ?>

