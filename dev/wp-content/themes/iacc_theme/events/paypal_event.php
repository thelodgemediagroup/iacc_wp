<?php

if (isset($_POST['submit']) && $_POST['submit'] == 'Purchase Tickets')
			{

				// gather _POST data
				$user_id = $_POST['user_id'];
				$ticket_quantity = $_POST['ticket_quantity'];
				$event_id = $_POST['event_id'];
				$event_title = $_POST['event_title'];
				$event_venue = $_POST['event_venue'];
				$event_cost = $_POST['event_cost'];
				$event_date = $_POST['event_date'];

				$paypal_name = 'Tickets for '.$event_title.' '.$event_date;
				$paypal_total_cost = $event_cost * $ticket_quantity * 1.00;
				$paypal_desc = 'Event #'.$event_id;

				// set paypal constants
				$paypal_user = 'iaccbizmerchant_api1.iacctest.com';
				$paypal_pwd = '8GZ7NVHFRLD2P2TC';
				$paypal_signature = 'A2vrgv8RnN71M0W0b47Zo.s0QqLnA9YVeS0HX.PkUR2NJuqxPE3EtS8N';
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
					'PAYMENTREQUEST_0_ITEMAMT' => urlencode($event_cost),
					'L_PAYMENTREQUEST_0_NAME0' => urlencode($paypal_name),
					'L_PAYMENTREQUEST_0_DESC0' => urlencode($paypal_desc),
					'L_PAYMENTREQUEST_0_AMT0' => urlencode($event_cost),
					'L_PAYMENTREQUEST_0_QTY0' => urlencode($ticket_quantity),
					'ITEMAMT' => urlencode($ticket_quantity),
					'PAYMENTREQUEST_0_DESC' => urlencode($paypal_desc),
					'PAYMENTREQUEST_0_CURRENCYCODE' => urlencode('USD'),
					'PAYMENTREQUEST_0_SHIPPINGAMT' => urlencode('0.00'),
					'PAYMENTREQUEST_0_TAXAMT' => urlencode('0.00'),
					'CANCELURL' => urlencode('http://localhost:80/events/category/iacc-events/'),
					'RETURNURL' => urlencode('http://localhost:80/event-confirm/')					
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