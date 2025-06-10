<?php

	function vmb_ContactInfo() {
		$thisToolbox = new Toolbox();
		$booking_id = $thisToolbox->GetBookingID();
		$thisBooking = new Booking($booking_id);

		$tt_username = get_field('traveltek_username', 'option');
		$tt_password = get_field('traveltek_password', 'option');
		$tt_sitename = get_field('traveltek_site', 'option');

		$email = sanitize_text_field($_POST['email']);
		$telephone = sanitize_text_field($_POST['telephone']);
		$address_1 = sanitize_text_field($_POST['address_1']);
		$address_2 = sanitize_text_field($_POST['address_2']);
		$address_3 = sanitize_text_field($_POST['address_3']);
		$address_4 = sanitize_text_field($_POST['address_4']);
		$postcode = sanitize_text_field($_POST['postcode']);
		$country = sanitize_text_field($_POST['country']);

		$xml = [];

		$xml[] = 'xml=<?xml version="1.0"?>';
		$xml[] = '<request xmlns="http://fusionapi.traveltek.net/1.0/xsds">';
			$xml[] = '  <auth password="'.$tt_password.'" username="'.$tt_username.'"/>';
			$xml[] = '  <method action="updatecontact" sitename="'.$tt_sitename.'" bookingid="'.$booking_id.'">';
				$xml[] = '    <contact title="'.$thisBooking->Title().'" firstname="'.$thisBooking->FirstName().'" middlename="'.$thisBooking->MiddleName().'" lastname="'.$thisBooking->LastName().'" telephone="'.$telephone.'" mobilephone="'.$thisBooking->MobilePhone().'" workphone="" altphone="" faxnumber="" email="'.$email.'" altemail="" individualtaxnumber="" dob="'.$thisBooking->DateOfBirth().'">';
					$xml[] = '      <billingaddress postcode="'.$postcode.'" address1="'.$address_1.'" address2="'.$address_2.'" address3="'.$address_3.'" address4="'.$address_4.'" country="'.$country.'" />';
					$xml[] = '      <shippingaddress postcode="'.$postcode.'" address1="'.$address_1.'" address2="'.$address_2.'" address3="'.$address_3.'" address4="'.$address_4.'" country="'.$country.'" />';
				$xml[] = '    </contact>';
			$xml[] = '  </method>';
		$xml[] = '</request>';

		$headers = ['Content-Type: application/x-www-form-urlencoded; charset=utf-8'];

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, 'https://fusionapi.traveltek.net/1.0/backoffice.pl/updatecontact');
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_TIMEOUT, 10);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, implode("\n", $xml));
		$response = curl_exec($curl);
		curl_close($curl);

		file_put_contents(SCRIPTSPATH.'/xml/contact-info-call-'.$booking_id.'.xml', implode("\n", $xml));
		file_put_contents(SCRIPTSPATH.'/xml/contact-info-response-'.$booking_id.'.xml', $response);

		//echo '<textarea style="width:100%;height:200px;border:0;">'. print_r(implode("\n", $xml), true) .'</textarea>';

	    shell_exec($thisToolbox->Script('get-portfolio-details.sh').' '.$booking_id);

	    setcookie('vmb_updated', true, 0, '/');

		nocache_headers();
		wp_safe_redirect('/manage-my-booking/contact-information/');
		exit;
	}

	add_action('admin_post_ContactInfo', 'vmb_ContactInfo');
	add_action('admin_post_nopriv_ContactInfo', 'vmb_ContactInfo');