<?php
	class Booking extends Toolbox {
		var $booking_id, $booking, $passengers;

	    public function __construct($booking_id) {
	        global $wpdb;

	        $this->booking_id = $booking_id;

	        # Booking


		    #    $sql = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}portfolio_details AS details INNER JOIN {$wpdb->prefix}portfolios AS portfolio ON
		    # portfolio.bookingreference = details.bookingreference WHERE portfolio.id = %d", $this->booking_id);

	        $sql = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}portfolio_details AS details INNER JOIN {$wpdb->prefix}portfolios AS portfolio ON
		    portfolio.id = details.id WHERE portfolio.id = %d", $this->booking_id);

		        $this->booking = $wpdb->get_row($sql);

		        //echo '<pre>'. print_r($this->booking, true) .'</pre>';
	    }

	    function BookingReference() {
	    	return (!empty($this->booking->bookingreference) ? $this->booking->bookingreference : false);
	    }

	    function TotalCost() {
	    	return (!empty($this->booking->totalcost) ? $this->booking->totalcost : 0);
	    }

	    function TotalDue() {
	    	return (!empty($this->booking->outstanding) ? $this->booking->outstanding : 0);
	    }

	    function AmountPaid() {
	    	return ($this->TotalCost() - $this->TotalDue());
	    }

	    function Title() {
	    	return (!empty($this->booking->title) ? $this->booking->title : false);
	    }

	    function FirstName() {
	    	return (!empty($this->booking->firstname) ? $this->booking->firstname : false);
	    }

	    function MiddleName() {
	    	return (!empty($this->booking->middlename) ? $this->booking->middlename : false);
	    }

	    function LastName() {
	    	return (!empty($this->booking->lastname) ? $this->booking->lastname : false);
	    }

	    function Address($line) {
	    	if (!empty($this->booking->billingaddress1) && $line === 1) { return $this->booking->billingaddress1; }
	    	if (!empty($this->booking->billingaddress2) && $line === 2) { return $this->booking->billingaddress2; }
	    	if (!empty($this->booking->billingaddress3) && $line === 3) { return $this->booking->billingaddress3; }
	    	if (!empty($this->booking->billingaddress4) && $line === 4) { return $this->booking->billingaddress4; }
	    	if (!empty($this->booking->billingaddress5) && $line === 5) { return $this->booking->billingaddress5; }

	    	return false;
	    }

	    function PostCode() {
	    	return (!empty($this->booking->billingpostcode) ? $this->booking->billingpostcode : false);
	    }

	    function Country() {
	    	if (!empty($this->booking->billingcountryisocode) && !empty($this->booking->billingcountry)) {
	    		return [$this->booking->billingcountryisocode, $this->booking->billingcountry];
	    	}

	    	return false;
	    }

	    function DateOfBirth() {
	    	return (!empty($this->booking->dob) ? $this->booking->dob : false);
	    }

	    function Telephone() {
	    	return (!empty($this->booking->telephone) ? $this->booking->telephone : false);
	    }

	    function MobilePhone() {
	    	return (!empty($this->booking->mobilephone) ? $this->booking->mobilephone : false);
	    }

	    function EmailAddress() {
	    	return (!empty($this->booking->email) ? $this->booking->email : false);
	    }

	    function Passengers() {
	    	global $wpdb;

	    	$columns = [
				'title',
				'firstname',
				'middlename',
				'lastname',
				'dob',
				'nationalityisocode',
				'nationality',
				'placeofbirth',
				'passportauthority',
				'passport',
				'passportissuedate',
				'passportexpirydate',
				'passportissuecountryisocode',
				'passportissuecountry',
				'emergencyname',
				'emergencyphone',
				'emergencyemail',
				'insurancecompany',
				'insurancepolicynumber',
				'insurancetelnumber'
	    	];

	        $sql = $wpdb->prepare("SELECT ".implode(", ", $columns)." FROM {$wpdb->prefix}portfolio_passenger WHERE bookingid = %d", $this->booking_id);
	        return $wpdb->get_results($sql);
	    }

	    function Passenger($index) {
	    	global $wpdb;

	    	$columns = [
				'id',
				'title',
				'firstname',
				'middlename',
				'lastname',
				'gender',
				'dob',
				'postcode',
				'address1',
				'address2',
				'address3',
				'address4',
				'country',
				'telephone',
				'mobile',
				'email'
	    	];

	        $sql = $wpdb->prepare("SELECT ".implode(", ", $columns)." FROM {$wpdb->prefix}portfolio_passenger WHERE bookingid = %d", $this->booking_id);
	        $results = $wpdb->get_results($sql);

	        return $results[$index];
	    }

	    function Documents() {
	    	global $wpdb;

	    	shell_exec($this->Script('get-portfolio-documents.sh').' '.$this->booking_id);

	    	$columns = [
				'name',
				'link',
				'created'
	    	];

	        $sql = $wpdb->prepare("SELECT ".implode(", ", $columns)." FROM {$wpdb->prefix}portfolio_document WHERE bookingid = %d ORDER BY created DESC", $this->booking_id);
	        return $wpdb->get_results($sql);
	    }

	    function Transfers() {
	    	global $wpdb;

	    	$array = [];

	    	$columns = [
				'id',
				'pickupdetail',
				'dropoffdetail',
				'pickupdate'
	    	];

	        $sql = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}portfolio_transfer WHERE bookingid = %d AND status = 'booked'", $this->booking_id);
	        $results = $wpdb->get_results($sql);

	        if (!empty($results)) {

	        	foreach ($results as $result) {
	        		$array[] = [
	        			'date' => $result->pickupdate,
	        			'type' => 'transfer',
	        			'data' => $result
	        		];
	        	}
	        }

	        return $array;
	    }

	    function Hotels() {
	    	global $wpdb;

	    	$array = [];

	    	$columns = [
				'id',
				'hotelname',
				'rating',
				'checkin',
				'numbernights'
	    	];

	        $sql = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}portfolio_accom WHERE bookingid = %d AND status = 'booked'", $this->booking_id);
	        $results = $wpdb->get_results($sql);

	        if (!empty($results)) {

	        	foreach ($results as $result) {
	        		$array[] = [
	        			'date' => $result->checkin,
	        			'type' => 'hotel',
	        			'data' => $result
	        		];
	        	}
	        }

	        return $array;
	    }

	    function Flights() {
	    	global $wpdb;

	    	$array = [];

	    	$columns = [];

	        $sql = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}portfolio_flight WHERE bookingid = %d AND status = 'booked'", $this->booking_id);
	        $results = $wpdb->get_results($sql);

	        if (!empty($results)) {

	        	foreach ($results as $result) {

					$sql = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}portfolio_segments WHERE bookingid = %d AND bookingreference = %s", $this->booking_id, $result->bookingreference);
					$segments = $wpdb->get_results($sql);

	        		$result->segments = $segments;

	        		$array[] = [
	        			'date' => $result->outdepartdate,
	        			'type' => 'flight',
	        			'data' => $result
	        		];
	        	}
	        }

	        return $array;
	    }

	    function Cruises() {
	    	global $wpdb;

	    	$array = [];

	    	$columns = [];

	        $sql = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}portfolio_cruise WHERE bookingid = %d AND status = 'booked'", $this->booking_id);
	        $results = $wpdb->get_results($sql);

	        if (!empty($results)) {

	        	foreach ($results as $result) {

			        $sql = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}portfolio_cabins WHERE bookingid = %d AND codetocruiseid = %d", $this->booking_id, $result->codetocruiseid);
			        $result->cabin = $wpdb->get_row($sql);

	        		$array[] = [
	        			'date' => $result->depdate,
	        			'type' => 'cruise',
	        			'data' => $result
	        		];

	        	}
	        }

	        return $array;
	    }

	    function Attractions() {
	    	global $wpdb;

	    	$array = [];

	    	$columns = [];

	        $sql = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}portfolio_attraction WHERE bookingid = %d AND status = 'booked'", $this->booking_id);
	        $results = $wpdb->get_results($sql);

	        if (!empty($results)) {

	        	foreach ($results as $result) {
	        		$array[] = [
	        			'date' => str_replace(" 00:00", " 23:59", $result->depdate), # Attractions that have no time end up being shown too early on the page.
	        			'type' => 'attraction',
	        			'data' => $result
	        		];
	        	}
	        }

	        return $array;
	    }

	    function Tickets() {
	    	global $wpdb;

	    	$array = [];

	    	$columns = [];

	        $sql = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}portfolio_ticket WHERE bookingid = %d AND status = 'booked' AND deleted = 'N'", $this->booking_id);
	        $results = $wpdb->get_results($sql);

	        if (!empty($results)) {

	        	foreach ($results as $result) {
	        		$array[] = [
	        			'date' => $result->depdate,
	        			'type' => 'ticket',
	        			'data' => $result
	        		];
	        	}
	        }

	        return $array;
	    }

	    function Insurance() {
	    	global $wpdb;

	    	$array = [];

	    	$columns = [];

	        $sql = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}portfolio_insurance WHERE bookingid = %d AND deleted = 'N'", $this->booking_id);
	        $results = $wpdb->get_results($sql);

	        if (!empty($results)) {

	        	foreach ($results as $result) {
	        		$array[] = [
	        			'date' => $result->depdate,
	        			'type' => 'insurance',
	        			'data' => $result
	        		];
	        	}
	        }

	        return $array;
	    }

	    function SortDates($types) {
	    	$array = [];

	    	foreach ($types as $items) {
		    	foreach ($items as $item) {
		    		$array[] = $item;
		    	}
	    	}

			usort($array, function($a, $b) {
				return $a['date'] <=> $b['date'];
			});

	    	return $array;
	    }

	}