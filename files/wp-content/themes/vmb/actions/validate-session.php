<?php

	function vmb_ValidateSession() {
		global $post, $booking_id;

		if (isset($post)) {
			$secure = get_field('secure', $post->ID);

			if ($secure === true && !empty($_SESSION[VMB_SESSION]) && !empty($_COOKIE[VMB_COOKIE])) {
				if (wp_check_password(SECURE_AUTH_KEY.$_SESSION[VMB_SESSION].SECURE_AUTH_SALT, $_COOKIE[VMB_COOKIE]) === true) {
					# Validated

					$booking_id = $_SESSION[VMB_SESSION];

				} else {
					# Failed

					setcookie(VMB_COOKIE, null, -1, '/');
					$_SESSION[VMB_SESSION] = null;

					nocache_headers();
					wp_safe_redirect(home_url());
					exit;
				}
			}
		}
	}

	add_action('wp', 'vmb_ValidateSession');