<?php

	function vmb_SignOut() {
		setcookie(VMB_COOKIE, null, -1, '/');
		$_SESSION[VMB_SESSION] = null;

		nocache_headers();
		wp_safe_redirect(home_url());
		exit;
	}

	add_action('admin_post_SignOut', 'vmb_SignOut');
	add_action('admin_post_nopriv_SignOut', 'vmb_SignOut');