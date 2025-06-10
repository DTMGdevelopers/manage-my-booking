<?php
	class Payment extends Toolbox {
		var $Session;

	    public function __construct($session_key = null) {

	    	$this->Session = $session_key;

	    }

	    function AdyenIframe($amount) {
	    	$domain = get_field('traveltek_domain', 'option');
	    	$site = get_field('traveltek_site', 'option');
	    	$customer_id = get_field('traveltek_customer_id', 'option');

	    	return $domain.'/fusion/cardpayment.pl?customerid='.$customer_id.'&systemid='.$this->GetBookingID().'&site='.$site.'&front-end-transaction=1&sessionkey='.$this->SessionKey($amount).'&confirmurl='.get_bloginfo('template_url').'/actions/receive-payment.php&redirurl='.get_bloginfo('template_url').'/actions/complete-payment.php&ibos=1';

	    }

	    function Iframe() {
	    	$site_code = get_field('traveltek_site_code', 'option');

	    	$url = 'https://secure.traveltek.net/'.$site_code.'/fusion/cardpayment.pl?sessionkey='.$this->SessionKey().'&transactiontype=full&confirmurl='.get_bloginfo('template_url').'/actions/receive-payment.php&redirurl='.get_bloginfo('template_url').'/actions/complete-payment.php';

	    	return '<div id="traveltek-iframe"><iframe src="'.$url.'" width="100%" height="800px"></iframe></div>';
	    }

	    function SessionKey($amount = 0) {
	    	return trim(shell_exec($this->Script('get-session.sh').' '.$this->GetBookingID().($amount > 0 ? ' '.$amount : '')));
	    }

	    function SaveXML($xml) {
	    	global $wpdb;

    		$sql = $wpdb->prepare("SELECT bookingid AS id FROM {$wpdb->prefix}portfolio_session WHERE sessionkey = %s", $this->Session);
    		$booking = $wpdb->get_row($sql, 'ARRAY_A');

    		if (!empty($booking['id'])) {
	    		file_put_contents(SCRIPTSPATH.'/xml/book-'.$booking['id'].'.xml', $xml);
	    	}
	    }

	    function Complete() {
	    	global $wpdb;

    		$sql = $wpdb->prepare("SELECT bookingid AS id FROM {$wpdb->prefix}portfolio_session WHERE sessionkey = %s", $this->Session);
    		$booking = $wpdb->get_row($sql, 'ARRAY_A');

    		if (!empty($booking['id'])) {
	    		shell_exec($this->Script('add-receipt.sh').' '.$booking['id']);
	    	}
	    }

	    function PaymentMethod($card_type) {

			$tt_username = get_field('traveltek_username', 'option');
			$tt_password = get_field('traveltek_password', 'option');
			$tt_sitename = get_field('traveltek_site', 'option');

			$xml = [];
			$xml[] = 'xml=<?xml version="1.0"?>';
			$xml[] = '<request xmlns="http://fusionapi.traveltek.net/1.0/xsds">';
				$xml[] = '  <auth password="'.$tt_password.'" username="'.$tt_username.'"/>';
				$xml[] = '<method action="listpaymentmethods" sitename="'.$tt_sitename.'" />';
			$xml[] = '</request>';

			$headers = ['Content-Type: application/x-www-form-urlencoded; charset=utf-8'];

			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, 'https://fusionapi.traveltek.net/1.0/backoffice.pl/listpaymentmethods');
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($curl, CURLOPT_TIMEOUT, 10);
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, implode("\n", $xml));
			$response = stripcslashes(curl_exec($curl));
			curl_close($curl);

			$array = simplexml_load_string($response);

			$methods = get_object_vars($array->paymentmethods);
			$allMethods = [];

			foreach ($methods['paymentmethod'] as $index => $method) {
				$thisMethod = get_object_vars($method);

				$allMethods[] = $thisMethod['@attributes'];
			}

			$position = $this->MultiArraySearch($card_type, 'creditcardcode', $allMethods);

			if (!empty($allMethods[$position])) {
				return [
					'id' => $allMethods[$position]['id'],
					'type' => $allMethods[$position]['chargetype'],
					'charge' => $allMethods[$position]['chargevalue']
				];
			}

			return false;
	    }
	}