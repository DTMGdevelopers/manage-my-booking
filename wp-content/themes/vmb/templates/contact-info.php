<?php

	function shortcode_contact_info() {
		global $wpdb, $booking_id;

		$html = [];

		if ($booking_id) {

			$thisBooking = new Booking($booking_id);
			$thisCountries = $thisBooking->Countries();

			$html[] = '<div class="row">';
				$html[] = '<div class="col-6">';
					$html[] = '<form action="'.esc_url(admin_url('admin-post.php')).'" method="post">';
						$html[] = '<input type="hidden" name="action" value="ContactInfo">';

						$html[] = '<p>';
							$html[] = '<label class="mb-2" for="email">Email address</label>';
							$html[] = '<input type="email" name="email" id="email" class="form-control" value="'.$thisBooking->EmailAddress().'" placeholder="" required>';
						$html[] = '</p>';

						$html[] = '<p>';
							$html[] = '<label class="mb-2" for="telephone">Telephone number</label>';
							$html[] = '<input type="tel" name="telephone" id="telephone" class="form-control" value="'.$thisBooking->Telephone().'" placeholder="" required>';
						$html[] = '</p>';

						$html[] = '<p>';
							$html[] = '<label class="mb-2" for="address_1">Address</label>';
							$html[] = '<input type="text" name="address_1" id="address_1" class="mb-1 form-control" value="'.$thisBooking->Address(1).'" placeholder="" required>';
							$html[] = '<input type="text" name="address_2" id="address_2" class="mb-1 form-control" value="'.$thisBooking->Address(2).'" placeholder="">';
							$html[] = '<input type="text" name="address_3" id="address_3" class="mb-1 form-control" value="'.$thisBooking->Address(3).'" placeholder="">';
							$html[] = '<input type="text" name="address_4" id="address_4" class="form-control" value="'.$thisBooking->Address(4).'" placeholder="">';
						$html[] = '</p>';

						$html[] = '<p>';
							$html[] = '<label class="mb-2" for="postcode">Post Code</label>';
							$html[] = '<input type="text" name="postcode" id="postcode" class="form-control" value="'.$thisBooking->PostCode().'" placeholder="" required>';
						$html[] = '</p>';

						$html[] = '<p>';
							$html[] = '<label class="mb-2" for="country">Country</label>';
							$html[] = '<select name="country" class="form-select" required>';
								$html[] = '<option value=""></option>';

								if (!empty($thisCountries)) {
									foreach ($thisCountries as $code => $country) {
										$selected = (!empty($thisBooking->Country()[0]) && !empty($thisBooking->Country()[1]) && $code.';'.$country === $thisBooking->Country()[0].';'.$thisBooking->Country()[1] ? 'selected="selected"' : '');

										$html[] = '<option value="'.$code.'" '.$selected.'>'.wptexturize($country).'</option>';
									}
								}

							$html[] = '</select>';
						$html[] = '</p>';

						$html[] = '<p class="mt-4"><button type="submit" class="btn btn-primary">Save your details</button>';

					$html[] = '</form>';
				$html[] = '</div>';
			$html[] = '</div>';
		}

		return implode("\n", $html);
	}

	add_shortcode('contact-info', 'shortcode_contact_info');