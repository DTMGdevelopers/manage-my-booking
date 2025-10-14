<?php

	function shortcode_booking_details() {
		global $wpdb, $booking_id;

		$currency_code = get_field('site_currency', 'option');

		$html = [];

		if ($booking_id) {

			$thisToolbox = new Toolbox();

	    	shell_exec($thisToolbox->Script('get-portfolio-details.sh').' '.$booking_id);

			$thisBooking = new Booking($booking_id);
			$thisPassengers = $thisBooking->Passengers();
			$thisTransfers = $thisBooking->Transfers();
			$thisHotels = $thisBooking->Hotels();
			$thisFlights = $thisBooking->Flights();
			$thisCruises = $thisBooking->Cruises();
			$thisAttractions = $thisBooking->Attractions();
			$thisTickets = $thisBooking->Tickets();
			$thisInsurance = $thisBooking->Insurance();
			$thisCarHire= $thisBooking->CarHire();

			$thisDetails = $thisBooking->SortDates([$thisTransfers, $thisHotels, $thisFlights, $thisCruises, $thisAttractions, $thisTickets, $thisInsurance, $thisCarHire]);

			// if (is_user_logged_in() === true) {
			// 	echo '<pre>'.print_r($thisFlights, true).'</pre>';
			// }

			// if ($thisBooking->BookingReference() === 'MYC-76883') {
			// 	echo '<pre>'. print_r($thisDetails, true) .'</pre>';
			// }

			$html[] = '<div id="booking-details">';

				$html[] = '<h3 class="border-bottom mb-5 pb-3">Reference Number: '.$thisBooking->BookingReference().'</h3>';

				$html[] = '<h3>Contact Information</h3>';

				$html[] = '<div class="container">';
					$html[] = '<div class="row border-bottom border-secondary">';
						$html[] = '<div class="col-lg">';
							$html[] = '<strong>Name</strong>';
						$html[] = '</div>';

						$html[] = '<div class="col-lg pl-0 pr-0 pt-1 pb-1">';
							$html[] = $thisBooking->Title().' '.$thisBooking->FirstName().' '.$thisBooking->LastName();
						$html[] = '</div>';
					$html[] = '</div>';

					$html[] = '<div class="row border-bottom border-secondary">';
						$html[] = '<div class="col-lg pl-0 pr-0 pt-1 pb-1">';
							$html[] = '<strong>Address</strong>';
						$html[] = '</div>';

						$html[] = '<div class="col-lg pl-0 pr-0 pt-1 pb-1">';
							$html[] = $thisBooking->Address(1).'<br>';
							$html[] = $thisBooking->Address(2).'<br>';
							$html[] = $thisBooking->Address(3).'<br>';
							$html[] = $thisBooking->Address(4);
						$html[] = '</div>';
					$html[] = '</div>';

					$html[] = '<div class="row border-bottom border-secondary">';
						$html[] = '<div class="col-lg pl-0 pr-0 pt-1 pb-1">';
							$html[] = '<strong>Telephone</strong>';
						$html[] = '</div>';

						$html[] = '<div class="col-lg pl-0 pr-0 pt-1 pb-1">';
							$html[] = $thisBooking->Telephone();
						$html[] = '</div>';
					$html[] = '</div>';

					$html[] = '<div class="row border-bottom border-secondary">';
						$html[] = '<div class="col-lg pl-0 pr-0 pt-1 pb-1">';
							$html[] = '<strong>Email Address</strong>';
						$html[] = '</div>';

						$html[] = '<div class="col-lg pl-0 pr-0 pt-1 pb-1">';
							$html[] = $thisBooking->EmailAddress();
						$html[] = '</div>';
					$html[] = '</div>';
				$html[] = '</div>';

				if (!empty($thisPassengers)) {
					$html[] = '<table class="table mt-4">';
						$html[] = '<thead>';
							$html[] = '<tr>';
								$html[] = '<th>Passenger</th>';
								$html[] = '<th>Title</th>';
								$html[] = '<th>First Name</th>';
								$html[] = '<th>Middle Name</th>';
								$html[] = '<th>Surname</th>';
								$html[] = '<th>Date of birth</th>';
							$html[] = '</tr>';
						$html[] = '</thead>';

						$html[] = '<tbody>';

							foreach ($thisPassengers as $index => $passenger) {
								$html[] = '<tr>';
									$html[] = '<td>'.($index + 1).'</td>';
									$html[] = '<td>'.(!empty($passenger->title) ? $passenger->title : '').'</td>';
									$html[] = '<td>'.(!empty($passenger->firstname) ? $passenger->firstname : '').'</td>';
									$html[] = '<td>'.(!empty($passenger->middlename) ? $passenger->middlename : '').'</td>';
									$html[] = '<td>'.(!empty($passenger->lastname) ? $passenger->lastname : '').'</td>';
									$html[] = '<td>'.(!empty($passenger->dob) ? wp_date(get_option('date_format'), strtotime($passenger->dob)) : '').'</td>';
								$html[] = '</tr>';
							}

						$html[] = '</tbody>';

					$html[] = '</table>';
				}

				if (!empty($thisDetails)) {
					foreach ($thisDetails as $thisDetail) {

						$html[] = '<div class="border-bottom pb-4 mt-5">';

							## MARK: Transfer

							if ($thisDetail['type'] === 'transfer') {
								$html[] = '<div>';
									$html[] = '<h3 class="mb-4">Transfer Reservation ID: '.$thisDetail['data']->bookingreference.'</h3>';

									$html[] = '<div class="container">';
										$html[] = '<div class="row align-items-center">';
											$html[] = '<div class="col-lg-1">';
												$html[] = '<p class="mb-0 transfer-icon d-flex align-items-center justify-content-center"><i class="fas fa-bus-alt"></i></p>';
											$html[] = '</div>';

											$html[] = '<div class="col-lg-5">';
												$html[] = '<p class="m-0"><strong>'.$thisDetail['data']->pickupdetail.' to '.$thisDetail['data']->dropoffdetail.'</strong></p>';
											$html[] = '</div>';

											$html[] = '<div class="col-lg-6">';
												$html[] = '<p>Pick up: '.wp_date('l jS F Y', strtotime($thisDetail['data']->pickupdate)).(wp_date('H:i', strtotime($thisDetail['data']->pickupdate)) !== '00:00' ? wp_date('H:i', strtotime($thisDetail['data']->pickupdate)) : '').'</p>';
											$html[] = '</div>';
										$html[] = '</div>';
									$html[] = '</div>';
								$html[] = '</div>';
							}

							## MARK: Hotel

							if ($thisDetail['type'] === 'hotel') {
								$html[] = '<div>';
									$html[] = '<h3 class="mb-4">Hotel Reservation ID: '.$thisDetail['data']->bookingreference.'</h3>';

									$html[] = '<div class="container">';
										$html[] = '<div class="row align-items-center">';
											$html[] = '<div class="col-lg-1">';
												$html[] = '<p class="mb-0 transfer-icon d-flex align-items-center justify-content-center"><i class="fas fa-hotel"></i></p>';
											$html[] = '</div>';

											$html[] = '<div class="col-lg-5">';
												$html[] = '<p class="m-0"><strong>'.$thisDetail['data']->hotelname.'</strong> <span class="d-inline-block ms-2 rating">'.$thisToolbox->DisplayStars($thisDetail['data']->rating).'</span></p>';
											$html[] = '</div>';

											$html[] = '<div class="col-lg-6">';
												$html[] = '<p>Check-in: '.wp_date('l jS F Y', strtotime($thisDetail['data']->checkin)).' for '.$thisDetail['data']->numbernights.' night'.((int)$thisDetail['data']->numbernights === 1 ? '' : 's').'</p>';
											$html[] = '</div>';
										$html[] = '</div>';
									$html[] = '</div>';
								$html[] = '</div>';
							}

							## MARK: Flight

							if ($thisDetail['type'] === 'flight') {
								$html[] = '<div>';
									$html[] = '<h3 class="mb-4">Flights Reservation ID: '.$thisDetail['data']->vendorreference.'</h3>';

									if (!empty($thisDetail['data']->segments)) {
										foreach ($thisDetail['data']->segments as $index => $segment) {

											$html[] = '<div class="container '.(($index + 1) === count($thisDetail['data']->segments) ? '' : 'border-bottom mb-3 pb-3').' segment-'.$segment->journey.'">';
												$html[] = '<div class="row align-items-center">';
													$html[] = '<div class="col-lg-1">';
														$html[] = '<p class="mb-0 transfer-icon d-flex align-items-center justify-content-center"><i class="fas fa-plane fa-rotate-45"></i></p>';
													$html[] = '</div>';

													$html[] = '<div class="col-lg-5">';
														$html[] = '<p class="m-0"><strong>'.$segment->depname.' ('.$segment->depaircode.') to '.$segment->destname.' ('.$segment->destaircode.')</strong></p>';
														// if ($segment->journey === 'out') {
															$html[] = '<p class="m-0">Depart '.wp_date(get_option('date_format'), strtotime($segment->depdate)).', '.wp_date(get_option('time_format'), strtotime($segment->departuretime)).'</p>';
															$html[] = '<p class="m-0">Arrive '.wp_date(get_option('date_format'), strtotime($segment->arrivaldate)).', '.wp_date(get_option('time_format'), strtotime($segment->arrivaltime)).'</p>';
														// } else {
														// 	$html[] = '<p class="m-0">Depart '.wp_date(get_option('date_format'), strtotime($thisDetail['data']->indepartdate)).', '.wp_date(get_option('time_format'), strtotime($thisDetail['data']->indepartdate)).'</p>';
														// 	$html[] = '<p class="m-0">Arrive '.wp_date(get_option('date_format'), strtotime($thisDetail['data']->inarrivedate)).', '.wp_date(get_option('time_format'), strtotime($thisDetail['data']->inarrivedate)).'</p>';
														// }
													$html[] = '</div>';

													$html[] = '<div class="col-lg-6">';
														//$html[] = '<p class="m-0">Journey Time:</p>';
														$html[] = '<p class="m-0">Flight Number: '.$segment->flightnumber.'</p>';
														$html[] = '<p class="m-0">'.$segment->carrier.'</p>';
													$html[] = '</div>';
												$html[] = '</div>';
											$html[] = '</div>';

										}
									} else {

										$html[] = '<div class="container">';
											$html[] = '<div class="row align-items-center">';
												$html[] = '<div class="col-lg-1">';
													$html[] = '<p class="mb-0 transfer-icon d-flex align-items-center justify-content-center"><i class="fas fa-plane fa-rotate-45"></i></p>';
												$html[] = '</div>';

												$html[] = '<div class="col-lg-5">';
													$html[] = '<p class="m-0"><strong>('.$thisDetail['data']->outdepartcode.') to '.$thisDetail['data']->outarrivename.' ('.$thisDetail['data']->outarrivecode.')</strong></p>';
													$html[] = '<p class="m-0">Depart '.wp_date(get_option('date_format'), strtotime($thisDetail['data']->outdepartdate)).'</p>';
													$html[] = '<p class="m-0">Arrive '.wp_date(get_option('date_format'), strtotime($thisDetail['data']->outarrivedate)).'</p>';
												$html[] = '</div>';

												$html[] = '<div class="col-lg-6">';
													$html[] = '<p class="m-0">Journey Time:</p>';
													$html[] = '<p class="m-0">Flight Number:</p>';
													$html[] = '<p class="m-0">'.$thisDetail['data']->carriers.'</p>';
												$html[] = '</div>';
											$html[] = '</div>';
										$html[] = '</div>';

									}

								$html[] = '</div>';
							}

							## MARK: Cruise

							if ($thisDetail['type'] === 'cruise') {
								$html[] = '<div>';
									$html[] = '<h3 class="mb-4">Cruiseline Reference: '.$thisDetail['data']->bookingreference.'</h3>';

									$html[] = '<div class="container">';
										$html[] = '<div class="row">';
											$html[] = '<div class="col-lg-1">';
												$html[] = '<p class="mb-0 transfer-icon d-flex align-items-center justify-content-center"><i class="fas fa-ship"></i></p>';
											$html[] = '</div>';

											$html[] = '<div class="col-lg-5">';
												$html[] = '<p class="m-0"><strong>'.$thisDetail['data']->cruisename.'</strong></p>';
												$html[] = '<p class="m-0">Cruiseline: '.$thisDetail['data']->linename.'</p>';
												$html[] = '<p class="m-0">Ship: '.$thisDetail['data']->shipname.'</p>';
												$html[] = '<p class="m-0">Embark date: '.wp_date('l jS F Y', strtotime($thisDetail['data']->startdate)).'</p>';
												$html[] = '<p class="m-0">Disembark date: '.wp_date('l jS F Y', strtotime($thisDetail['data']->enddate)).'</p>';
											$html[] = '</div>';

											$html[] = '<div class="col-lg-6">';
												if (!empty($thisDetail['data']->cabin->number)) {
													$html[] = '<p class="m-0">Cabin: '.($thisDetail['data']->cabin->number === 'GUAR' ? 'To be assigned' : $thisDetail['data']->cabin->number).'</p>';
												}

												// $html[] = '<p class="m-0">Smoking: '.($thisDetail['data']->diningsmoking === 'Y' ? 'Yes' : 'No').'</p>';
												// $html[] = '<p class="m-0">Dining Seating: '.$thisDetail['data']->diningseating.'</p>';
												// $html[] = '<p class="m-0">Table Size: '.$thisDetail['data']->tablesize.'</p>';
											$html[] = '</div>';
										$html[] = '</div>';
									$html[] = '</div>';
								$html[] = '</div>';
							}

							## MARK: Attraction

							if ($thisDetail['type'] === 'attraction') {
								$html[] = '<div>';
									$html[] = '<h3 class="mb-4">Attraction: '.$thisDetail['data']->bookingreference.'</h3>';

									$html[] = '<div class="container">';
										$html[] = '<div class="row align-items-center">';
											$html[] = '<div class="col-lg-1">';
												$html[] = '<p class="mb-0 transfer-icon d-flex align-items-center justify-content-center"><i class="fas fa-store"></i></p>';
											$html[] = '</div>';

											$html[] = '<div class="col-lg-5">';
												$html[] = '<p class="m-0"><strong>'.$thisDetail['data']->name.'</strong></p>';
											$html[] = '</div>';

											$html[] = '<div class="col-lg-6">';
												$html[] = '<p class="m-0">'.wp_date('l jS F Y', strtotime($thisDetail['data']->startdate)).'</p>';
											$html[] = '</div>';
										$html[] = '</div>';
									$html[] = '</div>';

									if (!empty($thisDetail['data']->description)) {
										$html[] = '<div class="overflow-auto mt-5 p-2" style="max-height: 200px;">';
											$html[] = $thisDetail['data']->description;
										$html[] = '</div>';
									}

								$html[] = '</div>';
							}

							## MARK: Ticket

							if ($thisDetail['type'] === 'ticket') {
								$html[] = '<div>';
									$html[] = '<h3 class="mb-4">Miscellaneous: '.$thisDetail['data']->bookingreference.'</h3>';

									$html[] = '<div class="container">';
										$html[] = '<div class="row align-items-center">';
											$html[] = '<div class="col-lg-1">';
												$html[] = '<p class="mb-0 transfer-icon d-flex align-items-center justify-content-center"><i class="fas fa-ticket-alt"></i></p>';
											$html[] = '</div>';

											$html[] = '<div class="col-lg-5">';
												//$html[] = '<p class="m-0"><strong>'.$thisDetail['data']->suppliername.'</strong><br>'.$thisDetail['data']->ticketdescription.'</p>';
												$html[] = '<p class="m-0">'.$thisDetail['data']->ticketdescription.'</p>';
											$html[] = '</div>';

											$html[] = '<div class="col-lg-6">';
												$html[] = '<p class="m-0">'.wp_date('l jS F Y', strtotime($thisDetail['data']->depdate)).'</p>';
											$html[] = '</div>';
										$html[] = '</div>';
									$html[] = '</div>';

									if (!empty($thisDetail['data']->freetext)) {
										$html[] = '<div class="overflow-auto mt-5 p-2" style="max-height: 200px;">';
											$html[] = $thisDetail['data']->freetext;
										$html[] = '</div>';
									}

								$html[] = '</div>';
							}

							## MARK: Insurance

							if ($thisDetail['type'] === 'insurance') {
								$html[] = '<div>';
									$html[] = '<h3 class="mb-4">Insurance: '.$thisDetail['data']->bookingreference.'</h3>';

									$html[] = '<div class="container">';
										$html[] = '<div class="row align-items-center">';
											$html[] = '<div class="col-lg-1">';
												$html[] = '<p class="mb-0 transfer-icon d-flex align-items-center justify-content-center"><i class="fa-solid fa-dollar-sign"></i></p>';
											$html[] = '</div>';

											$html[] = '<div class="col-lg-5">';
												$html[] = '<p class="m-0"><strong>'.$thisDetail['data']->suppliername.'</strong><br>'.$thisDetail['data']->freetext.'</p>';
											$html[] = '</div>';

											$html[] = '<div class="col-lg-6">';
												$html[] = '<p class="m-0">'.wp_date('l jS F Y', strtotime($thisDetail['data']->policystartdate)).' - '.wp_date('l jS F Y', strtotime($thisDetail['data']->policyenddate)).'</p>';
											$html[] = '</div>';
										$html[] = '</div>';
									$html[] = '</div>';

								$html[] = '</div>';
							}

							## MARK: Car Hire

							if ($thisDetail['type'] === 'carhire') {
								$html[] = '<div>';
									$html[] = '<h3 class="mb-4">Car Hire: '.$thisDetail['data']->bookingreference.'</h3>';

									$html[] = '<div class="container">';
										$html[] = '<div class="row align-items-center">';
											$html[] = '<div class="col-lg-1">';
												$html[] = '<p class="mb-0 transfer-icon d-flex align-items-center justify-content-center"><i class="fa-solid fa-car-side"></i></p>';
											$html[] = '</div>';

											$html[] = '<div class="col-lg-5">';
												$html[] = '<p class="m-0"><strong>'.$thisDetail['data']->suppliername.'</strong><br>'.$thisDetail['data']->freetext.'</p>';
											$html[] = '</div>';

											$html[] = '<div class="col-lg-6">';
												$html[] = '<p class="m-0">Pick-up: '.wp_date(get_option('date_format').', '.get_option('time_format'), strtotime($thisDetail['data']->pickupdate)).' - '.$thisDetail['data']->pickupdetail.'</p>';
												$html[] = '<p class="m-0">Drop-off: '.wp_date(get_option('date_format').', '.get_option('time_format'), strtotime($thisDetail['data']->dropoffdate)).' - '.$thisDetail['data']->dropoffdetail.'</p>';
											$html[] = '</div>';
										$html[] = '</div>';
									$html[] = '</div>';

								$html[] = '</div>';
							}

						$html[] = '</div>';

					}
				}

				$html[] = '<div id="booking-cost" class="mt-5 mb-3">';
					$html[] = '<div class="row">';
						$html[] = '<div class="col-lg-4">';
							$html[] = '<p class="m-0"><strong>Total price: '.Currency($currency_code).$thisToolbox->FormatNumber($thisBooking->TotalCost()).'</strong></p>';
						$html[] = '</div>';
						$html[] = '<div class="col-lg-4">';
							$html[] = '<p class="m-0"><strong>Amount paid: '.Currency($currency_code).$thisToolbox->FormatNumber($thisBooking->AmountPaid()).'</strong></p>';
						$html[] = '</div>';
						$html[] = '<div class="col-lg-4">';
							if ($thisBooking->TotalDue() >= 0) {
								$html[] = '<p class="m-0"><strong>Balance due: '.Currency($currency_code).$thisToolbox->FormatNumber($thisBooking->TotalDue()).'</strong></p>';
							}
						$html[] = '</div>';
					$html[] = '</div>';
				$html[] = '</div>';

			$html[] = '</div>';
		}

		return implode("\n", $html);
	}

	add_shortcode('booking-details', 'shortcode_booking_details');