<?php

	function vmb_EssentialInfo() {
		$thisToolbox = new Toolbox();
		$booking_id = $thisToolbox->GetBookingID();
		$thisBooking = new Booking($booking_id);

		$tt_username = get_field('traveltek_username', 'option');
		$tt_password = get_field('traveltek_password', 'option');
		$tt_sitename = get_field('traveltek_site', 'option');

		$index = (int)sanitize_text_field($_POST['index']);
		$nationality = sanitize_text_field($_POST['passenger']['nationality']);
		//$birthplace = sanitize_text_field($_POST['passenger']['birthplace']);
		//$passport_authority = sanitize_text_field($_POST['passenger']['passport_authority']);
		$passport_number = sanitize_text_field($_POST['passenger']['passport_number']);
		$issue_day = sanitize_text_field($_POST['passenger']['issue_day']);
		$issue_month = sanitize_text_field($_POST['passenger']['issue_month']);
		$issue_year = sanitize_text_field($_POST['passenger']['issue_year']);
		$issue_date = $issue_year.'-'.$issue_month.'-'.$issue_day;
		$expiry_day = sanitize_text_field($_POST['passenger']['expiry_day']);
		$expiry_month = sanitize_text_field($_POST['passenger']['expiry_month']);
		$expiry_year = sanitize_text_field($_POST['passenger']['expiry_year']);
		$expiry_date = $expiry_year.'-'.$expiry_month.'-'.$expiry_day;
		$issuingcountry = sanitize_text_field($_POST['passenger']['issuingcountry']);
		/*$emergency_name = sanitize_text_field($_POST['passenger']['emergency_name']);
		$emergency_phone = sanitize_text_field($_POST['passenger']['emergency_phone']);
		$emergency_email = sanitize_text_field($_POST['passenger']['emergency_email']);
		$insurance_name = sanitize_text_field($_POST['passenger']['insurance_name']);
		$insurance_number = sanitize_text_field($_POST['passenger']['insurance_number']);
		$insurance_phone = sanitize_text_field($_POST['passenger']['insurance_phone']);*/

		$thisPassenger = $thisBooking->Passenger($index);

		$xml = [];

		$xml[] = 'xml=<?xml version="1.0"?>';
		$xml[] = '<request xmlns="http://fusionapi.traveltek.net/1.0/xsds">';
			$xml[] = '  <auth password="'.$tt_password.'" username="'.$tt_username.'"/>';
			$xml[] = '  <method action="updatepassenger" sitename="'.$tt_sitename.'" bookingid="'.$booking_id.'" passengerid="'.$thisPassenger->id.'">';
				$xml[] = '    <passenger titleid="'.$thisPassenger->title.'" type="adult" firstname="'.$thisPassenger->firstname.'" middlename="'.$thisPassenger->middlename.'" lastname="'.$thisPassenger->lastname.'" gender="'.$thisPassenger->gender.'" dob="'.$thisPassenger->dob.'" leadpassenger="'.($index === 0 ? 'Y' : 'N').'">';
					//$xml[] = '      <address postcode="'.$thisPassenger->postcode.'" address1="'.$thisPassenger->address1.'" address2="'.$thisPassenger->address2.'" address3="'.$thisPassenger->address3.'" address4="'.$thisPassenger->address4.'" country="'.$thisPassenger->country.'"/>';
					$xml[] = '      <contact telephone="'.$thisPassenger->telephone.'" mobile="'.$thisPassenger->mobile.'" email="'.$thisPassenger->email.'" />';
					$xml[] = '      <passport nationality="'.$nationality.'" passportissuecountry="'.$issuingcountry.'" passportissuedate="'.$issue_date.'" passportexpirydate="'.$expiry_date.'" passportnumber="'.$passport_number.'"/>';
				$xml[] = '    </passenger>';
			$xml[] = '  </method>';
		$xml[] = '</request>';

		// echo '<textarea style="width:100%;height:200px;border:0;">'. print_r(implode("\n", $xml), true) .'</textarea>';exit;

		$headers = ['Content-Type: application/x-www-form-urlencoded; charset=utf-8'];

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, 'https://fusionapi.traveltek.net/1.0/backoffice.pl/updatepassenger');
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_TIMEOUT, 10);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, implode("\n", $xml));
		$response = curl_exec($curl);
		curl_close($curl);

		//file_put_contents(SCRIPTSPATH.'/xml/update-passenger-call-'.$booking_id.'-'.$thisPassenger->id.'.xml', implode("\n", $xml));
		//file_put_contents(SCRIPTSPATH.'/xml/update-passenger-response-'.$booking_id.'-'.$thisPassenger->id.'.xml', $response);

	    shell_exec($thisToolbox->Script('get-portfolio-details.sh').' '.$booking_id);

	    setcookie('vmb_updated', true, 0, '/');

		nocache_headers();
		wp_safe_redirect('/manage-my-booking/essential-information/');
		exit;
	}

	add_action('admin_post_EssentialInfo', 'vmb_EssentialInfo');
	add_action('admin_post_nopriv_EssentialInfo', 'vmb_EssentialInfo');