<?php

	function shortcode_find_booking() {
		$months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

		$html = [];

		$html[] = '<form action="'.esc_url(admin_url('admin-post.php')).'" method="post">';
			$html[] = '<input type="hidden" name="action" value="FindBooking">';

			if (isset($_GET['feedback']) && $_GET['feedback'] === 'error') {
				$html[] = '<div class="alert alert-danger" role="alert">';
					$html[] = 'We couldn&rsquo;t find your details.';
				$html[] = '</div>';
			}

			$html[] = '<div class="row mt-5 mb-4">';
				$html[] = '<div class="col-lg-3">';
					$html[] = '<label class="mb-2" for="booking_reference">Booking Reference</label>';
					$html[] = '<input type="text" name="booking_reference" id="booking_reference" class="form-control" value="" placeholder="Eg: IGZ-1234" required>';
				$html[] = '</div>';
				$html[] = '<div class="col-lg-3">';
					$html[] = '<label class="mb-2" for="passenger_surname">Primary Passenger Surname</label>';
					$html[] = '<input type="text" name="passenger_surname" id="passenger_surname" class="form-control" value="" placeholder="" required>';
				$html[] = '</div>';
				$html[] = '<div class="col-lg-6">';
					$html[] = '<label class="mb-2" for="dob_day">Date of Birth</label>';

					$html[] = '<div class="row">';
						$html[] = '<div class="col-lg-4">';
							$html[] = '<select name="dob_day" id="dob_day" class="form-select" required>';
								$html[] = '<option value="" selected>Day</option>';
								for ($x = 1; $x < 32; $x++) {
									$html[] = '<option value="'.($x < 10 ? '0'.$x : $x).'">'.($x < 10 ? '0'.$x : $x).'</option>';
								}
							$html[] = '</select>';
						$html[] = '</div>';

						$html[] = '<div class="col-lg-4">';
							$html[] = '<select name="dob_month" id="dob_month" class="form-select" required>';
								$html[] = '<option value="" selected>Month</option>';
								for ($x = 1; $x < 13; $x++) {
									$html[] = '<option value="'.($x < 10 ? '0'.$x : $x).'">'.$months[($x-1)].'</option>';
								}
							$html[] = '</select>';
						$html[] = '</div>';

						$html[] = '<div class="col-lg-4">';
							$html[] = '<select name="dob_year" id="dob_year" class="form-select" required>';
								$html[] = '<option value="" selected>Year</option>';
								for ($x = (date("Y") - 16); $x >= (date("Y") - 100); $x--) {
									$html[] = '<option value="'.$x.'">'.$x.'</option>';
								}
							$html[] = '</select>';
						$html[] = '</div>';
					$html[] = '</div>';
				$html[] = '</div>';
			$html[] = '</div>';

			$html[] = '<p><button type="submit" class="btn btn-primary">Find Booking</button>';
		$html[] = '</form>';

		return implode("\n", $html);
	}

	add_shortcode('find-booking', 'shortcode_find_booking');