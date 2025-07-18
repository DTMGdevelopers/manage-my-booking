<?php
	if (!function_exists('is_user_logged_in')) {
		$pieces = explode("/public/", dirname(__FILE__).'/');
		$public = $pieces[0].'/public';
		include($public."/wp-load.php");
	}

	//wp_mail('craig@iprogress.co.uk,peter@iprogress.co.uk', 'VMB Payment Response 1', json_encode($_POST));

	if (isset($_POST) && !empty($_POST['xml'])) {

		$xml = stripcslashes($_POST['xml']);

		$array = simplexml_load_string($xml);

		$session_key = sanitize_text_field((string)$array->sessionkey);
		$status = sanitize_text_field((string)$array->status);
		//$card_type = sanitize_text_field((string)$array->card_type);
		//$amount = sanitize_text_field((string)$array->amount);

		//wp_mail('craig@iprogress.co.uk,peter@iprogress.co.uk', 'VMB Payment Response 2', json_encode($array));

		file_put_contents(SCRIPTSPATH.'/xml/book-'.wp_date('Y-m-d-h-i-s').'.xml', $xml);

		if (!empty($session_key) && !empty($status) && $status === 'success') {

			$thisPayment = new Payment($session_key);
			$thisPayment->SaveXML($xml);
			$thisPayment->Complete();

			//wp_mail('craig@iprogress.co.uk,peter@iprogress.co.uk', 'VMB Payment Response 3', 'Session: '.$session_key.', Status: '.$status);
		}

	}

	exit;