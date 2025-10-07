<?php
	if (!function_exists('is_user_logged_in')) {
		if (isset($_SERVER['HOME']) && file_exists($_SERVER['HOME']."/files/wp-load.php")) {
			include($_SERVER['HOME']."/files/wp-load.php");
		} else if (file_exists("/srv/users/vmb/apps/vmb/public/wp-load.php")) {
			include("/srv/users/vmb/apps/vmb/public/wp-load.php");
		} else {
			exit;
		}
	}

	wp_mail('craig@iprogress.co.uk,peter@iprogress.co.uk', 'VMB Payment Received (1/3)', [$_POST, $_SERVER]);

	if (isset($_POST) && !empty($_POST['xml'])) {

		$xml = stripcslashes($_POST['xml']);

		$array = simplexml_load_string($xml);

		$session_key = sanitize_text_field((string)$array->sessionkey);
		$status = sanitize_text_field((string)$array->status);
		//$card_type = sanitize_text_field((string)$array->card_type);
		//$amount = sanitize_text_field((string)$array->amount);

		wp_mail('craig@iprogress.co.uk,peter@iprogress.co.uk', 'VMB Payment Processed (2/3)', json_encode($array));

		file_put_contents(SCRIPTSPATH.'xml/book-'.wp_date('Y-m-d-h-i-s').'.xml', $xml);

		if (!empty($session_key) && !empty($status) && $status === 'success') {

			$thisPayment = new Payment($session_key);
			$thisPayment->SaveXML($xml);
			$thisPayment->Complete();

			wp_mail('craig@iprogress.co.uk,peter@iprogress.co.uk', 'VMB Payment Completed (3/3)', 'Session: '.$session_key.', Status: '.$status);
		}

	}

	exit;