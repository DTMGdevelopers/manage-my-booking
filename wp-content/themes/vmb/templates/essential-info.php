<?php

	function shortcode_essential_info() {
		global $wpdb, $booking_id;

		$html = [];

		$positions = ['Main', 'Second', 'Third', 'Fourth'];
		$months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

		if ($booking_id) {
			$thisBooking = new Booking($booking_id);
			$thisPassengers = $thisBooking->Passengers();
			$thisCountries = $thisBooking->Countries();

				if (!empty($thisPassengers)) {
					foreach ($thisPassengers as $index => $passenger) {

						$html[] = '<form action="'.esc_url(admin_url('admin-post.php')).'" method="post">';
							$html[] = '<input type="hidden" name="action" value="EssentialInfo">';
							$html[] = '<input type="hidden" name="index" value="'.$index.'">';

							$html[] = '<div class="mt-5">';
								$html[] = '<h3>'.$positions[$index].' Passenger: '.$passenger->firstname.' '.$passenger->lastname.'</h3>';

								$html[] = '<div class="row">';
									$html[] = '<div class="col-lg-6">';
										$html[] = '<p>';
											$html[] = '<label class="mb-2" for="nationality_'.$index.'">Nationality</label>';
											$html[] = '<select name="passenger[nationality]" id="nationality_'.$index.'" class="form-select" required>';
												$html[] = '<option value=""></option>';

												if (!empty($thisCountries)) {
													foreach ($thisCountries as $code => $country) {
														$selected = ($code === $passenger->nationality ? 'selected="selected"' : '');

														$html[] = '<option value="'.$code.'" '.$selected.'>'.wptexturize($country).'</option>';
													}
												}

											$html[] = '</select>';
										$html[] = '</p>';
									$html[] = '</div>';

									/*$html[] = '<div class="col-lg-6">';
										$html[] = '<p>';
											$html[] = '<label class="mb-2" for="birthplace_'.$index.'">Place of birth</label>';
											$html[] = '<input type="text" name="passenger[birthplace]" id="birthplace_'.$index.'" class="form-control" value="'.$passenger->placeofbirth.'" placeholder="" required>';
										$html[] = '</p>';
									$html[] = '</div>';*/
								$html[] = '</div>';

								$html[] = '<div class="row">';
									$html[] = '<div class="col-lg-6">';
										$html[] = '<p>';
											// $html[] = '<label class="mb-2" for="passport_authority_'.$index.'">Passort Issuing Authority</label>';
											// $html[] = '<input type="text" name="passenger[passport_authority]" id="passport_authority_'.$index.'" class="form-control" value="'.$passenger->passportissuecountry.'" placeholder="" required>';
											$html[] = '<label class="mb-2" for="issuingcountry_'.$index.'">Passport Issuing Authority</label>';
											$html[] = '<select name="passenger[issuingcountry]" id="issuingcountry_'.$index.'" class="form-select" required>';
												$html[] = '<option value=""></option>';

												if (!empty($thisCountries)) {
													foreach ($thisCountries as $code => $country) {
														$selected = ($code.';'.$country === $passenger->passportissuecountryisocode.';'.$passenger->passportissuecountry ? 'selected="selected"' : '');

														$html[] = '<option value="'.$code.'" '.$selected.'>'.wptexturize($country).'</option>';
													}
												}

											$html[] = '</select>';
										$html[] = '</p>';
									$html[] = '</div>';

									$html[] = '<div class="col-lg-6">';
										$html[] = '<p>';
											$html[] = '<label class="mb-2" for="passport_number_'.$index.'">Passport Number</label>';
											$html[] = '<input type="text" name="passenger[passport_number]" id="passport_number_'.$index.'" class="form-control" value="'.$passenger->passport.'" placeholder="" required>';
										$html[] = '</p>';
									$html[] = '</div>';
								$html[] = '</div>';

								$html[] = '<div class="row mb-3">';
									$html[] = '<div class="col-lg-6">';
										$html[] = '<label class="mb-2" for="issue_day_'.$index.'">Issue Date</label>';

										$issue_day = (!empty($passenger->passportissuedate) ? wp_date('d', strtotime($passenger->passportissuedate)) : '');
										$issue_month = (!empty($passenger->passportissuedate) ? wp_date('m', strtotime($passenger->passportissuedate)) : '');
										$issue_year = (!empty($passenger->passportissuedate) ? wp_date('Y', strtotime($passenger->passportissuedate)) : '');

										$html[] = '<div class="row">';
											$html[] = '<div class="col-lg-4">';
												$html[] = '<select name="passenger[issue_day]" id="issue_day_'.$index.'" class="form-select" required>';
													$html[] = '<option value="" selected>Day</option>';
													for ($x = 1; $x < 32; $x++) {
														$selected = '';

														if (($x < 10 ? '0'.$x : $x) == $issue_day) {
															$selected = 'selected="selected"';
														}

														$html[] = '<option value="'.($x < 10 ? '0'.$x : $x).'" '.$selected.'>'.($x < 10 ? '0'.$x : $x).'</option>';
													}
												$html[] = '</select>';
											$html[] = '</div>';

											$html[] = '<div class="col-lg-4">';
												$html[] = '<select name="passenger[issue_month]" id="issue_month_'.$index.'" class="form-select" required>';
													$html[] = '<option value="" selected>Month</option>';
													for ($x = 1; $x < 13; $x++) {
														$selected = '';

														if (($x < 10 ? '0'.$x : $x) == $issue_month) {
															$selected = 'selected="selected"';
														}

														$html[] = '<option value="'.($x < 10 ? '0'.$x : $x).'" '.$selected.'>'.$months[($x-1)].'</option>';
													}
												$html[] = '</select>';
											$html[] = '</div>';

											$html[] = '<div class="col-lg-4">';
												$html[] = '<select name="passenger[issue_year]" id="issue_year_'.$index.'" class="form-select" required>';
													$html[] = '<option value="" selected>Year</option>';
													for ($x = date("Y"); $x >= (date("Y") - 100); $x--) {
														$selected = '';

														if ($x == $issue_year) {
															$selected = 'selected="selected"';
														}

														$html[] = '<option value="'.$x.'" '.$selected.'>'.$x.'</option>';
													}
												$html[] = '</select>';
											$html[] = '</div>';
										$html[] = '</div>';
									$html[] = '</div>';

									$html[] = '<div class="col-lg-6">';
										$html[] = '<label class="mb-2" for="expiry_day_'.$index.'">Expiry Date</label>';

										$expiry_day = (!empty($passenger->passportexpirydate) && $passenger->passportexpirydate !== '0000-00-00' ? wp_date('d', strtotime($passenger->passportexpirydate)) : '');
										$expiry_month = (!empty($passenger->passportexpirydate) && $passenger->passportexpirydate !== '0000-00-00' ? wp_date('m', strtotime($passenger->passportexpirydate)) : '');
										$expiry_year = (!empty($passenger->passportexpirydate) && $passenger->passportexpirydate !== '0000-00-00' ? wp_date('Y', strtotime($passenger->passportexpirydate)) : '');

										$html[] = '<div class="row">';
											$html[] = '<div class="col-lg-4">';
												$html[] = '<select name="passenger[expiry_day]" id="expiry_day_'.$index.'" class="form-select" required>';
													$html[] = '<option value="" selected>Day</option>';
													for ($x = 1; $x < 32; $x++) {
														$selected = '';

														if (($x < 10 ? '0'.$x : $x) == $expiry_day) {
															$selected = 'selected="selected"';
														}

														$html[] = '<option value="'.($x < 10 ? '0'.$x : $x).'" '.$selected.'>'.($x < 10 ? '0'.$x : $x).'</option>';
													}
												$html[] = '</select>';
											$html[] = '</div>';

											$html[] = '<div class="col-lg-4">';
												$html[] = '<select name="passenger[expiry_month]" id="expiry_month_'.$index.'" class="form-select" required>';
													$html[] = '<option value="" selected>Month</option>';
													for ($x = 1; $x < 13; $x++) {
														$selected = '';

														if (($x < 10 ? '0'.$x : $x) == $expiry_month) {
															$selected = 'selected="selected"';
														}

														$html[] = '<option value="'.($x < 10 ? '0'.$x : $x).'" '.$selected.'>'.$months[($x-1)].'</option>';
													}
												$html[] = '</select>';
											$html[] = '</div>';

											$html[] = '<div class="col-lg-4">';
												$html[] = '<select name="passenger[expiry_year]" id="expiry_year_'.$index.'" class="form-select" required>';
													$html[] = '<option value="" selected>Year</option>';
													for ($x = (date("Y") + 20); $x >= (date("Y") - 100); $x--) {
														$selected = '';

														if ($x == $expiry_year) {
															$selected = 'selected="selected"';
														}

														$html[] = '<option value="'.$x.'" '.$selected.'>'.$x.'</option>';
													}
												$html[] = '</select>';
											$html[] = '</div>';
										$html[] = '</div>';
									$html[] = '</div>';
								$html[] = '</div>';

								$html[] = '<div class="row">';
									// $html[] = '<div class="col-lg-6">';
									// 	$html[] = '<p>';
									// 		$html[] = '<label class="mb-2" for="issuingcountry_'.$index.'">Issuing Country</label>';
									// 		$html[] = '<select name="passenger[issuingcountry]" id="issuingcountry_'.$index.'" class="form-select" required>';
									// 			$html[] = '<option value=""></option>';

									// 			if (!empty($thisCountries)) {
									// 				foreach ($thisCountries as $code => $country) {
									// 					$selected = ($code.';'.$country === $passenger->passportissuecountryisocode.';'.$passenger->passportissuecountry ? 'selected="selected"' : '');

									// 					$html[] = '<option value="'.$code.'" '.$selected.'>'.wptexturize($country).'</option>';
									// 				}
									// 			}

									// 		$html[] = '</select>';
									// 	$html[] = '</p>';
									// $html[] = '</div>';

									$html[] = '<div class="col-lg-6">';
										$html[] = '<p class="text-muted">Ensure your passport has the correct validity for your travel destination</p>';
									$html[] = '</div>';
								$html[] = '</div>';

								/*$html[] = '<div class="row">';
									$html[] = '<div class="col-lg-6">';
										$html[] = '<p>';
											$html[] = '<label class="mb-2" for="emergency_name_'.$index.'">Emergency Contact name</label>';
											$html[] = '<input type="text" name="passenger[emergency_name]" id="emergency_name_'.$index.'" class="form-control" value="'.$passenger->emergencyname.'" placeholder="" required>';
										$html[] = '</p>';
									$html[] = '</div>';

									$html[] = '<div class="col-lg-6">';
										$html[] = '<p>';
											$html[] = '<label class="mb-2" for="emergency_phone_'.$index.'">Emergency Contact Telephone number</label>';
											$html[] = '<input type="text" name="passenger[emergency_phone]" id="emergency_phone_'.$index.'" class="form-control" value="'.$passenger->emergencyphone.'" placeholder="" required>';
										$html[] = '</p>';
									$html[] = '</div>';
								$html[] = '</div>';

								$html[] = '<div class="row">';
									$html[] = '<div class="col-lg-6">';
										$html[] = '<p>';
											$html[] = '<label class="mb-2" for="emergency_email_'.$index.'">Emergency Contact email address</label>';
											$html[] = '<input type="text" name="passenger[emergency_email]" id="emergency_email_'.$index.'" class="form-control" value="'.$passenger->emergencyemail.'" placeholder="" required>';
										$html[] = '</p>';
									$html[] = '</div>';

									$html[] = '<div class="col-lg-6"></div>';
								$html[] = '</div>';*/

								/*$html[] = '<div class="row">';
									$html[] = '<div class="col-lg-6">';
										$html[] = '<p>';
											$html[] = '<label class="mb-2" for="insurance_name_'.$index.'">Insurance Company name</label>';
											$html[] = '<input type="text" name="passenger[insurance_name]" id="insurance_name_'.$index.'" class="form-control" value="'.$passenger->insurancecompany.'" placeholder="" required>';
										$html[] = '</p>';
									$html[] = '</div>';

									$html[] = '<div class="col-lg-6">';
										$html[] = '<p>';
											$html[] = '<label class="mb-2" for="insurance_number_'.$index.'">Insurance Policy number</label>';
											$html[] = '<input type="text" name="passenger[insurance_number]" id="insurance_number_'.$index.'" class="form-control" value="'.$passenger->insurancepolicynumber.'" placeholder="" required>';
										$html[] = '</p>';
									$html[] = '</div>';
								$html[] = '</div>';

								$html[] = '<div class="row">';
									$html[] = '<div class="col-lg-6">';
										$html[] = '<p>';
											$html[] = '<label class="mb-2" for="insurance_phone_'.$index.'">Insurance 24hr Emergency Contact Telephone number</label>';
											$html[] = '<input type="text" name="passenger[insurance_phone]" id="insurance_phone_'.$index.'" class="form-control" value="'.$passenger->insurancetelnumber.'" placeholder="" required>';
										$html[] = '</p>';
									$html[] = '</div>';

									$html[] = '<div class="col-lg-6"></div>';
								$html[] = '</div>';*/

							$html[] = '</div>';

							$html[] = '<p class="mt-4"><button type="submit" class="btn btn-primary">Update Details</button>';

						$html[] = '</form>';
					}
				}
		}

		return implode("\n", $html);
	}

	add_shortcode('essential-info', 'shortcode_essential_info');