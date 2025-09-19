<?php

	function shortcode_make_payment() {
		global $wpdb, $booking_id;

		$currency_code = get_field('site_currency', 'option');

		//if (isset($_GET['payment']) && $_GET['payment'] === 'adyen') {

			$html = [];

			$html[] = '<form action="" method="post" id="create-payment">';

				$html[] = '<div class="row mt-4">';
					$html[] = '<div class="col-lg-3">';

						$html[] = '<div class="input-group">';
							$html[] = '<label for="amount" class="input-group-text">'.Currency($currency_code).'</label>';
							$html[] = '<input type="number" step="0.01" min="0.00" name="amount" id="amount" class="form-control" value="" placeholder="000.00" required>';
						$html[] = '</div>';

					$html[] = '</div>';

					$html[] = '<div class="col-lg-3">';
						$html[] = '<button type="submit" class="btn btn-primary">Begin Payment</button>';
					$html[] = '</div>';
				$html[] = '</div>';

			$html[] = '</form>';

			$html[] = '<div id="traveltek-iframe"><iframe src="" width="100%" height=""></iframe></div>';

			return implode("\n", $html);

		/*} else {

			$html = [];

			$thisPayment = new Payment();

			return $thisPayment->Iframe();


		}*/
	}

	add_shortcode('make-payment', 'shortcode_make_payment');