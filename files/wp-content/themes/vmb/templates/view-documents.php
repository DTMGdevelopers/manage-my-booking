<?php

	function shortcode_view_documents() {
		global $wpdb, $booking_id;

		$html = [];

		if ($booking_id) {
			$thisBooking = new Booking($booking_id);
			$thisDocuments = $thisBooking->Documents();

			$html[] = '';

			if (!empty($thisDocuments)) {
				$html[] = '<ul id="view-documents" class="fa-ul">';
					foreach ($thisDocuments as $document) {
						$html[] = '<li>';
							$html[] = '<span class="fa-li"><i class="far fa-file-alt"></i></span>';
							$html[] = '<a href="'.$document->link.'" target="_blank">';
								$html[] = wptexturize($document->name);
							$html[] = '</a>';
							$html[] = wp_date("d/m/Y", strtotime($document->created));
						$html[] = '</li>';
					}
				$html[] = '</ul>';
			} else {
				$html[] = '<p><em>You currently have no documents available.</em></p>';
			}
		}

		return implode("\n", $html);
	}

	add_shortcode('view-documents', 'shortcode_view_documents');