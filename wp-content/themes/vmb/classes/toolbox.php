<?php
	use OzdemirBurak\Iris\Color\Hex;

	class Toolbox {
	    public function __construct($post_id = null) {
	    	// Nothing
	    	if (!empty($post_id)) {
	    		$this->ID = $post_id;
	    	}
		}

		function Validated() {
			if (!empty($_SESSION[VMB_SESSION]) && !empty($_COOKIE[VMB_COOKIE])) {
				return wp_check_password(SECURE_AUTH_KEY.$_SESSION[VMB_SESSION].SECURE_AUTH_SALT, $_COOKIE[VMB_COOKIE]);
			}

			return false;
		}

		function GetBookingID() {
			if (!empty($_SESSION[VMB_SESSION]) && !empty($_COOKIE[VMB_COOKIE])) {
				if (wp_check_password(SECURE_AUTH_KEY.$_SESSION[VMB_SESSION].SECURE_AUTH_SALT, $_COOKIE[VMB_COOKIE])) {
					return $_SESSION[VMB_SESSION];
				}
			}

			return false;
		}

		function DisplayStars($stars, $total = 5) {
			$html = '';

			if (!empty($stars) && $stars > 0) {
				for ($i = 0; $i < $stars; $i++) {
					$total--;
					$html .= '<i class="fas fa-star"></i>';
				}

				if ($total > 0) {
					for ($i = 0; $i < $total; $i++) {
						$html .= '<i class="far fa-star"></i>';
					}
				}
			}

			return $html;
		}

		function Script($script) {
			return SCRIPTSPATH.$script;
		}

		function ID() {
			return (int)$this->ID;
		}

		function Darken($hex, $percent) {
			$hex = new Hex($hex);
			echo $hex->darken($percent);
		}

		function Lighten($hex, $percent) {
			$hex = new Hex($hex);
			echo $hex->lighten($percent);
		}

		function DaysBetween($start, $end) {
			$start = new DateTime($start);
			$end = new DateTime($end);
			return (int)$start->diff($end)->format('%a');
		}

		function FormatDate($format = 'Y-m-d H:i:s', $date) {
			return wp_date($format, strtotime($date));
		}

		function FormatDateUTC($format = 'Y-m-d H:i:s', $date) {
			return date($format, strtotime($date));
		}

		function FormatNumber($number, $decimals = 2, $separator = ',') {
			return number_format($number, $decimals, '.', $separator);
		}

		function Spinner($string = null) {
			return '<i class="fas fa-spinner fa-spin"></i>'.(!empty($string) ? ' '.wptexturize($string) : '');
		}

		function RandomString($length = 8) {
			return bin2hex(openssl_random_pseudo_bytes($length));
		}

		function FeaturedImage($size = null, $fallback = false) {
			if (!empty($this->ID)) {
				$image_id = get_post_thumbnail_id($this->ID);

				if ($fallback === true && ((int)$image_id === 0 || !$image_id)) {
					$image_id = get_field('fallback_image', 'option');
				}

				$images = $this->Image($image_id, null, false);

				if (!empty($images)) {
					if (empty($size)) {
						return $images;
					} else {
						return [
							'url' => $images['sizes'][$size],
							'meta' => [
								'alt' => wptexturize(get_post_meta($image_id, '_wp_attachment_image_alt', true)),
								'title' => wptexturize(get_the_title($image_id)),
								'caption' => wptexturize(get_the_excerpt($image_id)),
							]
						];
					}
				}
			}

			return false;
		}

		function Image($image_id = 0, $thumbnail = null, $fallback = true) {
			if ($fallback === true && ((int)$image_id === 0 || !$image_id)) {
				$image_id = get_field('fallback_image', 'option');
			}

			if ($image_id > 0) {
				$sizes = get_intermediate_image_sizes();

				if (!$image_id) {
					$image_id = get_post_thumbnail_id();
				}

				$images = [];

				foreach ($sizes as $size) {
					if (!empty($size)) {
						$images['sizes'][$size] = wp_get_attachment_image_src($image_id, $size)[0];
					}

					$images['sizes']['full'] = wp_get_attachment_image_src($image_id, 'full')[0];
				}

				$images['meta'] = [
					'alt' => wptexturize(get_post_meta($image_id, '_wp_attachment_image_alt', true)),
					'title' => wptexturize(get_the_title($image_id)),
					'caption' => wptexturize(get_the_excerpt($image_id)),
				];

				$images['id'] = $image_id;

				if (empty($thumbnail)) {
					return $images;
				} else {
					return [
						'url' => $images['sizes'][$thumbnail],
						'meta' => [
							'alt' => wptexturize(get_post_meta($image_id, '_wp_attachment_image_alt', true)),
							'title' => wptexturize(get_the_title($image_id)),
							'caption' => wptexturize(get_the_excerpt($image_id)),
						],
						'id' => $image_id
					];
				}
			} else {
				return false;
			}
		}

		function MultiArraySearch($value, $search, $array, $limit = 1) {
			$return = [];

			if (!empty($array)) {
				foreach ($array as $key => $val) {
					if (isset($val[$search]) && $val[$search] == $value) {
						if ($limit === 1) {
							return $key;
						} else if ($limit <= count($return)) {
							$return[] = $key;
						}
					}
				}
			}

			return (!empty($return) ? $return : -1);
		}

		function ArrayPluck($value, $search, $array) {
			if (!empty($array)) {
				foreach ($array as $key => $val) {
					if ($val[$search] == $value) {
						return $array[$key];
					}
				}
			}

			return false;
		}

		function ArrayDump($array) {
			return '<div class="array-dump"><pre>'.print_r($array, true).'</pre>';
		}

		function ResetArrayKeys($array) {
			$reset = [];

			foreach ($array as $item) {
				if (!empty($item)) {
					$reset[] = $item;
				}
			}

			return $reset;
		}

		function SanitizeArrayText($array) {
		    foreach ($array as $index => &$value) {
		        if (is_array($value)) {
		            $value = sanitize_array($value);
		        } else {
		            $value = sanitize_text_field($value);
		        }
		    }

		    return $array;
		}

		function SanitizeArrayTitle($array) {
		    foreach ($array as $index => &$value) {
		        if (is_array($value)) {
		            $value = sanitize_array($value);
		        } else {
		            $value = sanitize_title($value);
		        }
		    }

		    return $array;
		}

		function SanitizeTextarea($content) {
			return wp_kses($content, [
			    'a' => [
			        'href' => [],
			        'title' => [],
			        'target' => []
			    ],
			    'br' => [],
			    'em' => [],
			    'strong' => [],
			    'ol' => [],
			    'ul' => [],
			    'li' => [],
			    'span' => [
			    	'style' => []
			    ],
			    'p' => [
			    	'style' => []
			    ],
			    'blockquote' => [],
			    'img' => []
			]);
		}

		function TrimSpace($string) {
			return preg_replace('/\s/', '', $string);
		}

		function StripTags($string) {
			return str_replace("\n", "", strip_tags($string));
		}

		function GetACF($key, $post_id) {
			/*global $thisCache;

			$thisACF = $thisCache->getItem($key.$post_id);

			if ($thisACF->isHit()) {
				$value = $thisACF->get();
			} else {*/
				$value = get_field($key, $post_id);

				/*$thisACF->set($value)->expiresAfter(3600)->AddTag('cache_'.$post_id);
				$thisCache->save($thisACF);
			}*/

			return $value;
		}

		function FetchAll($sql) {
	        global $wpdb;

	        return $wpdb->get_results($wpdb->prepare($sql), 'ARRAY_A');
		}

		function Countries() {
			return [ "AU" => "Australia", "AF" => "Afghanistan", "AL" => "Albania", "DZ" => "Algeria", "AS" => "American Samoa", "AD" => "Andorra", "AO" => "Angola", "AI" => "Anguilla", "AQ" => "Antarctica", "AG" => "Antigua and Barbuda", "AR" => "Argentina", "AM" => "Armenia", "AW" => "Aruba", "AU" => "Australia", "AT" => "Austria", "AZ" => "Azerbaijan", "BS" => "Bahamas", "BH" => "Bahrain", "BD" => "Bangladesh", "BB" => "Barbados", "BY" => "Belarus", "BE" => "Belgium", "BZ" => "Belize", "BJ" => "Benin", "BM" => "Bermuda", "BT" => "Bhutan", "BO" => "Bolivia", "BA" => "Bosnia and Herzegovina", "BW" => "Botswana", "BV" => "Bouvet Island", "BR" => "Brazil", "BQ" => "British Antarctic Territory", "IO" => "British Indian Ocean Territory", "VG" => "British Virgin Islands", "BN" => "Brunei", "BG" => "Bulgaria", "BF" => "Burkina Faso", "BI" => "Burundi", "KH" => "Cambodia", "CM" => "Cameroon", "CA" => "Canada", "CT" => "Canton and Enderbury Islands", "CV" => "Cape Verde", "KY" => "Cayman Islands", "CF" => "Central African Republic", "TD" => "Chad", "CL" => "Chile", "CN" => "China", "CX" => "Christmas Island", "CC" => "Cocos [Keeling] Islands", "CO" => "Colombia", "KM" => "Comoros", "CG" => "Congo - Brazzaville", "CD" => "Congo - Kinshasa", "CK" => "Cook Islands", "CR" => "Costa Rica", "HR" => "Croatia", "CU" => "Cuba", "CY" => "Cyprus", "CZ" => "Czech Republic", "CI" => "Côte d’Ivoire", "DK" => "Denmark", "DJ" => "Djibouti", "DM" => "Dominica", "DO" => "Dominican Republic", "NQ" => "Dronning Maud Land", "DD" => "East Germany", "EC" => "Ecuador", "EG" => "Egypt", "SV" => "El Salvador", "GQ" => "Equatorial Guinea", "ER" => "Eritrea", "EE" => "Estonia", "ET" => "Ethiopia", "FK" => "Falkland Islands", "FO" => "Faroe Islands", "FJ" => "Fiji", "FI" => "Finland", "FR" => "France", "GF" => "French Guiana", "PF" => "French Polynesia", "TF" => "French Southern Territories", "FQ" => "French Southern and Antarctic Territories", "GA" => "Gabon", "GM" => "Gambia", "GE" => "Georgia", "DE" => "Germany", "GH" => "Ghana", "GI" => "Gibraltar", "GR" => "Greece", "GL" => "Greenland", "GD" => "Grenada", "GP" => "Guadeloupe", "GU" => "Guam", "GT" => "Guatemala", "GG" => "Guernsey", "GN" => "Guinea", "GW" => "Guinea-Bissau", "GY" => "Guyana", "HT" => "Haiti", "HM" => "Heard Island and McDonald Islands", "HN" => "Honduras", "HK" => "Hong Kong SAR China", "HU" => "Hungary", "IS" => "Iceland", "IN" => "India", "ID" => "Indonesia", "IR" => "Iran", "IQ" => "Iraq", "IE" => "Ireland", "IM" => "Isle of Man", "IL" => "Israel", "IT" => "Italy", "JM" => "Jamaica", "JP" => "Japan", "JE" => "Jersey", "JT" => "Johnston Island", "JO" => "Jordan", "KZ" => "Kazakhstan", "KE" => "Kenya", "KI" => "Kiribati", "KW" => "Kuwait", "KG" => "Kyrgyzstan", "LA" => "Laos", "LV" => "Latvia", "LB" => "Lebanon", "LS" => "Lesotho", "LR" => "Liberia", "LY" => "Libya", "LI" => "Liechtenstein", "LT" => "Lithuania", "LU" => "Luxembourg", "MO" => "Macau SAR China", "MK" => "Macedonia", "MG" => "Madagascar", "MW" => "Malawi", "MY" => "Malaysia", "MV" => "Maldives", "ML" => "Mali", "MT" => "Malta", "MH" => "Marshall Islands", "MQ" => "Martinique", "MR" => "Mauritania", "MU" => "Mauritius", "YT" => "Mayotte", "FX" => "Metropolitan France", "MX" => "Mexico", "FM" => "Micronesia", "MI" => "Midway Islands", "MD" => "Moldova", "MC" => "Monaco", "MN" => "Mongolia", "ME" => "Montenegro", "MS" => "Montserrat", "MA" => "Morocco", "MZ" => "Mozambique", "MM" => "Myanmar [Burma]", "NA" => "Namibia", "NR" => "Nauru", "NP" => "Nepal", "NL" => "Netherlands", "AN" => "Netherlands Antilles", "NT" => "Neutral Zone", "NC" => "New Caledonia", "NZ" => "New Zealand", "NI" => "Nicaragua", "NE" => "Niger", "NG" => "Nigeria", "NU" => "Niue", "NF" => "Norfolk Island", "KP" => "North Korea", "VD" => "North Vietnam", "MP" => "Northern Mariana Islands", "NO" => "Norway", "OM" => "Oman", "PC" => "Pacific Islands Trust Territory", "PK" => "Pakistan", "PW" => "Palau", "PS" => "Palestinian Territories", "PA" => "Panama", "PZ" => "Panama Canal Zone", "PG" => "Papua New Guinea", "PY" => "Paraguay", "YD" => "People's Democratic Republic of Yemen", "PE" => "Peru", "PH" => "Philippines", "PN" => "Pitcairn Islands", "PL" => "Poland", "PT" => "Portugal", "PR" => "Puerto Rico", "QA" => "Qatar", "RO" => "Romania", "RU" => "Russia", "RW" => "Rwanda", "RE" => "Réunion", "BL" => "Saint Barthélemy", "SH" => "Saint Helena", "KN" => "Saint Kitts and Nevis", "LC" => "Saint Lucia", "MF" => "Saint Martin", "PM" => "Saint Pierre and Miquelon", "VC" => "Saint Vincent and the Grenadines", "WS" => "Samoa", "SM" => "San Marino", "SA" => "Saudi Arabia", "SN" => "Senegal", "RS" => "Serbia", "CS" => "Serbia and Montenegro", "SC" => "Seychelles", "SL" => "Sierra Leone", "SG" => "Singapore", "SK" => "Slovakia", "SI" => "Slovenia", "SB" => "Solomon Islands", "SO" => "Somalia", "ZA" => "South Africa", "GS" => "South Georgia and the South Sandwich Islands", "KR" => "South Korea", "ES" => "Spain", "LK" => "Sri Lanka", "SD" => "Sudan", "SR" => "Suriname", "SJ" => "Svalbard and Jan Mayen", "SZ" => "Swaziland", "SE" => "Sweden", "CH" => "Switzerland", "SY" => "Syria", "ST" => "São Tomé and Príncipe", "TW" => "Taiwan", "TJ" => "Tajikistan", "TZ" => "Tanzania", "TH" => "Thailand", "TL" => "Timor-Leste", "TG" => "Togo", "TK" => "Tokelau", "TO" => "Tonga", "TT" => "Trinidad and Tobago", "TN" => "Tunisia", "TR" => "Turkey", "TM" => "Turkmenistan", "TC" => "Turks and Caicos Islands", "TV" => "Tuvalu", "UM" => "U.S. Minor Outlying Islands", "PU" => "U.S. Miscellaneous Pacific Islands", "VI" => "U.S. Virgin Islands", "UG" => "Uganda", "UA" => "Ukraine", "SU" => "Union of Soviet Socialist Republics", "AE" => "United Arab Emirates", "GB" => "United Kingdom", "US" => "United States", "ZZ" => "Unknown or Invalid Region", "UY" => "Uruguay", "UZ" => "Uzbekistan", "VU" => "Vanuatu", "VA" => "Vatican City", "VE" => "Venezuela", "VN" => "Vietnam", "WK" => "Wake Island", "WF" => "Wallis and Futuna", "EH" => "Western Sahara", "YE" => "Yemen", "ZM" => "Zambia", "ZW" => "Zimbabwe", "AX" => "Åland Islands" ];
		}

		function Show404() {
			header('HTTP/1.0 404 Not Found', true, 404);
			echo '<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
					<html>
						<head>
							<title>404 Not Found</title>
							<style>
							    body { margin:20px;font-family:sans-serif;max-width:800px; }
							    .error { color:#e00; }
							    pre { font-size:16px; }
							    h1 { font-size:28px; }
							</style>
						</head>
						<body>
							<h1>Not Found</h1>
							<p>The requested URL was not found on this server.</p>
						</body>
					</html>';
			die();
		}

		function IsLight($color) {
			# https://gist.github.com/apapirovski/2580052

			$color = str_replace("#", "", $color);

			if (!$color) {
				return true;
			}

	        if (strlen($color) === 6) {
                $hex = [
                	$color[0].$color[1],
                	$color[2].$color[3],
                	$color[4].$color[5]
                ];
	        } else if (strlen($color) === 3 ) {
                $hex = [
                	$color[0].$color[0],
                	$color[1].$color[1],
                	$color[2].$color[2]
                ];
	        }

	        $brightness = number_format((intval($hex[0], 16) * 299 + intval($hex[1], 16) * 587 + intval($hex[2], 16) * 114) / 1000, 0);

	        if ($brightness > 200) {
	        	return true;
	        } else {
	        	return false;
	        }
		}

		function WordsToNumber($data) {
		    # https://gist.github.com/bainternet/5756049
		    $data = strtr(
		        $data,
		        array(
		            'zero'      => '0',
		            'a'         => '1',
		            'one'       => '1',
		            'two'       => '2',
		            'three'     => '3',
		            'four'      => '4',
		            'five'      => '5',
		            'six'       => '6',
		            'seven'     => '7',
		            'eight'     => '8',
		            'nine'      => '9',
		            'ten'       => '10',
		            'eleven'    => '11',
		            'twelve'    => '12',
		            'thirteen'  => '13',
		            'fourteen'  => '14',
		            'fifteen'   => '15',
		            'sixteen'   => '16',
		            'seventeen' => '17',
		            'eighteen'  => '18',
		            'nineteen'  => '19',
		            'twenty'    => '20',
		            'thirty'    => '30',
		            'forty'     => '40',
		            'fourty'    => '40', // common misspelling
		            'fifty'     => '50',
		            'sixty'     => '60',
		            'seventy'   => '70',
		            'eighty'    => '80',
		            'ninety'    => '90',
		            'hundred'   => '100',
		            'thousand'  => '1000',
		            'million'   => '1000000',
		            'billion'   => '1000000000',
		            'and'       => '',
		        )
		    );

		    // Coerce all tokens to numbers
		    $parts = array_map(
		        function ($val) {
		            return floatval($val);
		        },
		        preg_split('/[\s-]+/', $data)
		    );

		    $stack = new SplStack; // Current work stack
		    $sum   = 0; // Running total
		    $last  = null;

		    foreach ($parts as $part) {
		        if (!$stack->isEmpty()) {
		            // We're part way through a phrase
		            if ($stack->top() > $part) {
		                // Decreasing step, e.g. from hundreds to ones
		                if ($last >= 1000) {
		                    // If we drop from more than 1000 then we've finished the phrase
		                    $sum += $stack->pop();
		                    // This is the first element of a new phrase
		                    $stack->push($part);
		                } else {
		                    // Drop down from less than 1000, just addition
		                    // e.g. "seventy one" -> "70 1" -> "70 + 1"
		                    $stack->push($stack->pop() + $part);
		                }
		            } else {
		                // Increasing step, e.g ones to hundreds
		                $stack->push($stack->pop() * $part);
		            }
		        } else {
		            // This is the first element of a new phrase
		            $stack->push($part);
		        }

		        // Store the last processed part
		        $last = $part;
		    }

		    return $sum + $stack->pop();
		}
	}
