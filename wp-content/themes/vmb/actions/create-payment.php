<?php

	function vmb_CreatePayment() {

		$response = ['error' => true];

		$amount = (float)sanitize_text_field($_POST['amount']);

		if ($amount > 0) {

			$thisPayment = new Payment();

			$payment_url = $thisPayment->AdyenIframe(($amount * 100));

			if (!empty($payment_url)) {

				$response = [
					'error' => false,
					'payment_url' => $payment_url
				];

			}
		}

		echo json_encode($response);
		exit;

	}

	add_action('wp_ajax_CreatePayment', 'vmb_CreatePayment');
	add_action('wp_ajax_nopriv_CreatePayment', 'vmb_CreatePayment');