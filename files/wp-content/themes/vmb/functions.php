<?php
	# Generated: 2021-12-13 3:10:34
	# https://www.iprogress.co.uk

	session_start();

	ini_set('display_errors', 0);
    // error_reporting(E_ALL);

    # Composer
    	include("vendor/autoload.php");

    # Set up
    	define('VMB_COOKIE', 'vmb_booking');
    	define('VMB_SESSION', 'vmb_booking_verify');

	# Classes
		include("classes/toolbox.php");
		include("classes/brand.php");
		include("classes/booking.php");
		include("classes/payment.php");

	# Actions
		include("actions/validate-session.php");
		include("actions/find-booking.php");
		include("actions/contact-info.php");
		include("actions/essential-info.php");
		include("actions/create-payment.php");
		include("actions/sign-out.php");

	# Templates
		include("templates/find-booking.php");
		include("templates/booking-details.php");
		include("templates/view-documents.php");
		include("templates/contact-info.php");
		include("templates/essential-info.php");
		include("templates/make-payment.php");

	function vmb_setup() {
		add_editor_style();
		add_theme_support('post-thumbnails');
		add_theme_support('automatic-feed-links');
		register_nav_menus(array(
			'primary' => __('Navigation','vmb'),
		));
	}

	add_action('after_setup_theme','vmb_setup');

	function vmb_widgets_init() {
		register_sidebar(array(
			'name' => __('Primary Widget Area','vmb'),
			'id' => 'primary-widget-area',
			'description' => __('The primary widget area','vmb'),
			'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
			'after_widget' => '</li>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
		));
	}

	add_action('widgets_init','vmb_widgets_init');

	# Add Custom Login Logo
		function cust_login_head() {
			echo "
				<style>
					.login h1 {
						height: 46px;
						width: 100%;
						margin: 0px 0px 20px;
					}
					.login h1 a {
						background: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAL8AAAAuCAYAAAB50MjgAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoV2luZG93cykiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6NDAzQzU2MzM1ODlEMTFFQUI0MkI4OTk0NDM2NDQ1MkYiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6NDAzQzU2MzQ1ODlEMTFFQUI0MkI4OTk0NDM2NDQ1MkYiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDo0MDNDNTYzMTU4OUQxMUVBQjQyQjg5OTQ0MzY0NDUyRiIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDo0MDNDNTYzMjU4OUQxMUVBQjQyQjg5OTQ0MzY0NDUyRiIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PofQU4wAABLoSURBVHja7J0HeFVF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') no-repeat scroll center top transparent !important;
						height: 46px;
						width: 191px;
					}
				</style>
			";
		}

		add_action("login_head", "cust_login_head");

		function login_headertext() {
			return 'CruiseAppy';
		}

		add_filter('login_headertext', 'login_headertext');

		function login_headerurl() {
			return 'https://www.cruiseappy.com';
		}

		add_filter('login_headerurl', 'login_headerurl');

		remove_action('wp_head','feed_links_extra', 3);
		remove_action('wp_head','feed_links', 2);
		remove_action('wp_head','rsd_link');
		remove_action('wp_head','wlwmanifest_link');
		remove_action('wp_head','index_rel_link');
		remove_action('wp_head','parent_post_rel_link', 10, 0);
		remove_action('wp_head','start_post_rel_link', 10, 0);
		remove_action('wp_head','adjacent_posts_rel_link', 10, 0);
		remove_action('wp_head','wp_generator');

	# Pre-populate Username
		function populateUsernames($hook) {
			if ('user-new.php' != $hook) { return; }
			wp_enqueue_script('usernames', get_bloginfo('template_url').'/js/usernames.js', [], filemtime(THEMEPATH.'js/usernames.js'), true);
		}

		if (get_option('force_usernames') !== 'yes') { add_action('admin_enqueue_scripts', 'populateUsernames'); }

		function force_usernames_fields_html() {
			$value = get_option('force_usernames');
			$checked = ($value=='yes') ? 'checked="checked"' : '';
			echo '<label for="force_usernames"><input id="force_usernames" type="checkbox" '.$checked.' name="force_usernames" value="yes"> Disable randomly generated usernames.</label>';
		}

		function force_usernames_register_fields() {
			register_setting('general','force_usernames','esc_attr');
			add_settings_field('mk_force_usernames','<label for="force_usernames">Disable Random Usernames</label>','force_usernames_fields_html','general');
		}

		add_filter('admin_init','force_usernames_register_fields');

	# Show PHP Error Log
		function error_log_menu() { add_options_page('Error Log', 'Error Log', 'manage_options', 'error_log', 'error_log_page'); }
		function error_log_page() { include(ABSPATH."wp-content/error-log.php"); }

		add_action('admin_menu', 'error_log_menu');

	# Theme Settings
		if (function_exists('acf_add_options_page')) {
			acf_add_options_page(array(
				'page_title' 	=> 'Theme Settings',
				'menu_title'	=> 'Theme Settings',
				'menu_slug' 	=> 'theme-settings',
				'capability'	=> 'edit_posts',
				'redirect'		=> false,
				'position'		=> 3
			));
		}

	# Register Public CSS / JS
		function public_assets() {

			# CSS
				//wp_enqueue_style('fontawesome', 'https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.1.1/css/all.min.css', [], '5.15.3');
				wp_enqueue_style('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css', [], '5.1.3');
				wp_enqueue_style('opensans', 'https://cdn.jsdelivr.net/npm/@fontsource/open-sans@4.5.2/index.min.css', [], '5.1.3');
				//wp_enqueue_style('fancybox', 'https://cdn.jsdelivr.net/npm/@fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css', [], '3.5.7');
				//wp_enqueue_style('slick', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.css', [], '1.8.1');
				wp_enqueue_style('style', get_bloginfo('template_url').'/css/style.css', [], filemtime(THEMEPATH.'css/style.css'));
				wp_enqueue_style('greycliffe-font','https://use.typekit.net/jqe7zxb.css',[],'');
				wp_enqueue_style('ignite-icons', get_bloginfo('template_url').'/fonts/ignite-icons/ignite-icons.css', [], filemtime(THEMEPATH.'fonts/ignite-icons/ignite-icons.css'));




			# JS

				wp_enqueue_script('jquery', 'https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js', [], '3.6.0', true);
				// wp_enqueue_script('popper', 'https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js', [] , '1.12.9', true);
				wp_enqueue_script('fontawesome-6', 'https://kit.fontawesome.com/48a79cd4c8.js');


				//wp_enqueue_script('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js', ['jquery'], '5.1.3', true);

				wp_enqueue_script('bootstrap', 'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.2.1/js/bootstrap.bundle.min.js', ['jquery'], '5.1.3', true);
				//wp_enqueue_script('fancybox', 'https://cdn.jsdelivr.net/npm/@fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js', ['jquery'], '3.5.7', true);
				//wp_enqueue_script('slick', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js', ['jquery'], '1.8.1', true);
				wp_enqueue_script('cookie', 'https://cdn.jsdelivr.net/npm/cookiejs@2.0.2/dist/cookie.min.js', ['jquery'], '2.0.2', true);

				wp_enqueue_script('custom', get_bloginfo('template_url').'/js/custom.js', ['jquery'], filemtime(THEMEPATH.'js/custom.js'), true);

				wp_localize_script('custom', 'admin', [
					'ajax' => admin_url('admin-ajax.php'),
				]);
		}

		add_action('wp_enqueue_scripts', 'public_assets');

	# TinyMCE Font Size Support
		function tinymce_buttons($buttons) {
			array_unshift($buttons, 'fontsizeselect');
			return $buttons;
		}

		add_filter('mce_buttons_2', 'tinymce_buttons');

		function tinymce_fontsizes($initArray){
			$initArray['fontsize_formats'] = "9px 10px 12px 13px 14px 16px 18px 21px 24px 28px 32px 36px";
			return $initArray;
		}

		add_filter('tiny_mce_before_init', 'tinymce_fontsizes');

	# Shortcodes
		function shortcode_button($args, $content) {
			return '<a href="'.$args['link'].'" class="btn btn-'.(!empty($args['colour']) ? $args['colour'] : '').' '.(!empty($args['class']) ? $args['class'] : '').'" target="'.(!empty($args['target']) ? $args['target'] : '_self').'">'.$content.'</a>';
		}

		add_shortcode('button', 'shortcode_button');

		function shortcode_sign_out( $atts, $item, $args, $depth ) {
			if (strpos($atts['href'], '[sign-out]') !== false) {
				$atts['href'] = esc_url(admin_url('admin-post.php')).'?action=SignOut';
			}

			return $atts;
		}

		add_filter('nav_menu_link_attributes', 'shortcode_sign_out', 10, 4);

		function shortcode_row($atts, $content) {
			return '<div class="row">'.do_shortcode($content).'</div>';
		}

		add_shortcode('row', 'shortcode_row');

		function shortcode_col($atts, $content) {
			$style = '';

			if (isset($atts['width'])) {
				$style .= 'width:'.$atts['width'].'%;flex:unset;';
			}

			if (isset($atts['background'])) {
				$style .= 'background-color:'.$atts['background'].';padding:20px;';
			}

			return '<div class="col-lg '.(!empty($atts['class']) ? $atts['class'] : '').'" '.($style !== '' ? 'style="'.$style.'"' : '').'>'.do_shortcode($content).'</div>';
		}

		add_shortcode('column', 'shortcode_col');

		function shortcode_booking_reference() {
			$thisToolbox = new Toolbox();

			if ($thisToolbox->GetBookingID()) {
				$thisBooking = new Booking($thisToolbox->GetBookingID());
				return $thisBooking->BookingReference();
			}

			return false;
		}

		add_shortcode('booking-reference', 'shortcode_booking_reference');

		function shortcode_balance_due() {
			$thisToolbox = new Toolbox();

			if ($thisToolbox->GetBookingID()) {
				$thisBooking = new Booking($thisToolbox->GetBookingID());
				return $thisToolbox->FormatNumber($thisBooking->TotalDue());
			}

			return false;
		}

		add_shortcode('balance-due', 'shortcode_balance_due');

		function shortcode_if_balance_due($args, $content) {
			$thisToolbox = new Toolbox();

			if ($thisToolbox->GetBookingID()) {
				$thisBooking = new Booking($thisToolbox->GetBookingID());

				if ($thisBooking->TotalDue() > 0) {
					return do_shortcode($content);
				}
			}

			return false;
		}

		add_shortcode('if-balance-due', 'shortcode_if_balance_due');