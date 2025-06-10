<?php
	class Brand extends Toolbox {
	    public function __construct() {
	        // Nothing
	    }

		function TelephoneNumber() {
			return $this->GetACF('telephone_number', 'option');
		}

		function EmailAddress() {
			return $this->GetACF('email_address', 'option');
		}

		function OfficeAddress() {
			return $this->GetACF('office_address', 'option');
		}

		function Logo($type = 'main_logo', $size = 'full') {
			$images = $this->GetACF($type, 'option');

			if ($size === 'full') {
				$image = $images['url'];
			} else {
				$image = $images['sizes'][$size];
			}

			return $image;
		}

		function Favicons() {
			$favicons = $this->GetACF('favicons', 'option');

			$html = [];

	        if (!empty($favicons)) {
	            if (!empty($favicons['desktop'])) { $html[] = '<link rel="icon" type="image/png" href="'.$favicons['desktop'].'">'; }
	            if (!empty($favicons['phone'])) { $html[] = '<link rel="apple-touch-icon" href="'.$favicons['phone'].'">'; }
	            if (!empty($favicons['tablet'])) { $html[] = '<link rel="apple-touch-icon" sizes="76x76" href="'.$favicons['tablet'].'">'; }
	            if (!empty($favicons['phone_retina'])) { $html[] = '<link rel="apple-touch-icon" sizes="120x120" href="'.$favicons['phone_retina'].'">'; }
	            if (!empty($favicons['tablet_retina'])) { $html[] = '<link rel="apple-touch-icon" sizes="152x152" href="'.$favicons['tablet_retina'].'">'; }
			}

			return implode("\n", $html);
		}

		function SocialMedia() {
			return $this->GetACF('social_media', 'option');
		}

		function Colours() {
			return $this->GetACF('colours', 'option');
		}
	}