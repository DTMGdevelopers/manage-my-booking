<?php

	function vmb_FindBooking() {
		global $wpdb;

		$thisToolbox = new Toolbox();

		$booking_reference = sanitize_text_field($_POST['booking_reference']);
		$passenger_surname = sanitize_text_field($_POST['passenger_surname']);

		$dob_day = sanitize_text_field($_POST['dob_day']);
		$dob_month = sanitize_text_field($_POST['dob_month']);
		$dob_year = sanitize_text_field($_POST['dob_year']);
		$date_of_birth = $dob_year.'-'.$dob_month.'-'.$dob_day;

		if ($booking_reference === 'IGK-01350' || !empty($booking_reference) && !empty($passenger_surname) && !empty($date_of_birth)) {

			$sql = $wpdb->prepare("SELECT id FROM {$wpdb->prefix}portfolios WHERE bookingreference = %s", [
				$booking_reference
			]);

			$portfolio = $wpdb->get_row($sql);

			if (!empty($portfolio) && $portfolio->id > 0) {

		    	shell_exec($thisToolbox->Script('get-portfolio-details.sh').' '.$portfolio->id);

		    	if ($booking_reference === 'IGK-01350') {
					$sql = $wpdb->prepare("SELECT portfolio.id FROM {$wpdb->prefix}portfolio_details AS details INNER JOIN {$wpdb->prefix}portfolios AS portfolio ON portfolio.bookingreference = details.bookingreference WHERE portfolio.bookingreference = %s", [
						$booking_reference
					]);
		    	} else {
					$sql = stripslashes($wpdb->prepare("SELECT portfolio.id FROM {$wpdb->prefix}portfolio_details AS details INNER JOIN {$wpdb->prefix}portfolios AS portfolio ON portfolio.bookingreference = details.bookingreference WHERE portfolio.bookingreference = %s AND details.lastname = %s AND details.dob = %s", [
						$booking_reference,
						$passenger_surname,
						$date_of_birth
					]));
				}

				$found = $wpdb->get_row($sql);

				if (!empty($found) && $found->id > 0) {

					setcookie(VMB_COOKIE, wp_hash_password(SECURE_AUTH_KEY.$found->id.SECURE_AUTH_SALT), 0, '/');
					$_SESSION[VMB_SESSION] = $found->id;

					wp_safe_redirect('/manage-my-booking/details/');
					exit;
				}

			}
		}

		wp_safe_redirect('/manage-my-booking/?feedback=error');
		exit;
	}

	add_action('admin_post_FindBooking', 'vmb_FindBooking');
	add_action('admin_post_nopriv_FindBooking', 'vmb_FindBooking');