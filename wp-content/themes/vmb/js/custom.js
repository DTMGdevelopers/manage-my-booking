(function($){



/*####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### #######



 ######  ######## ######## ##     ## ########
##    ## ##          ##    ##     ## ##     ##
##       ##          ##    ##     ## ##     ##
 ######  ######      ##    ##     ## ########
      ## ##          ##    ##     ## ##
##    ## ##          ##    ##     ## ##
 ######  ########    ##     #######  ##



####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### #######*/



const $baseURL = "https://"+window.location.hostname;
const $pathURL = $baseURL+"/wp-content/themes/vmb";
const $currentURL = window.location;

const $background = $("#alert-background");
const $container = $("#alert-container");
const $alert = $("#alert-content");



/*####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### #######



########     ###     ######   ########    ##        #######     ###    ########
##     ##   ## ##   ##    ##  ##          ##       ##     ##   ## ##   ##     ##
##     ##  ##   ##  ##        ##          ##       ##     ##  ##   ##  ##     ##
########  ##     ## ##   #### ######      ##       ##     ## ##     ## ##     ##
##        ######### ##    ##  ##          ##       ##     ## ######### ##     ##
##        ##     ## ##    ##  ##          ##       ##     ## ##     ## ##     ##
##        ##     ##  ######   ########    ########  #######  ##     ## ########



####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### #######*/



if (window.location !== window.top.location) {
	window.top.location = window.location;
}



/*####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### #######



########  #######   #######  ##       ######## #### ########   ######
   ##    ##     ## ##     ## ##          ##     ##  ##     ## ##    ##
   ##    ##     ## ##     ## ##          ##     ##  ##     ## ##
   ##    ##     ## ##     ## ##          ##     ##  ########   ######
   ##    ##     ## ##     ## ##          ##     ##  ##              ##
   ##    ##     ## ##     ## ##          ##     ##  ##        ##    ##
   ##     #######   #######  ########    ##    #### ##         ######



####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### #######*/



$(document).ready(function(){
	$('.footer-info-item p').tooltip({
		html: true
	});
});



/*####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### #######



########     ###    ##    ## ##     ## ######## ##    ## ########
##     ##   ## ##    ##  ##  ###   ### ##       ###   ##    ##
##     ##  ##   ##    ####   #### #### ##       ####  ##    ##
########  ##     ##    ##    ## ### ## ######   ## ## ##    ##
##        #########    ##    ##     ## ##       ##  ####    ##
##        ##     ##    ##    ##     ## ##       ##   ###    ##
##        ##     ##    ##    ##     ## ######## ##    ##    ##



####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### #######*/



$(document).on("submit", "#create-payment", function(e){
	e.preventDefault();

	let $form = $(this);
	let $amount = Number($form.find("input[name='amount']").val().replace(",", ""));
	let $button = $form.find("button");
	let $button_html = $button.html();
	let $button_width = $button.outerWidth();

	$button.attr("disabled", "disabled").css("width", $button_width).html(Spinner());

	$.ajax({
		url: admin.ajax,
		data: {
			action: 'CreatePayment',
			amount: $amount
		},
		type: 'POST',
		dataType: 'json',
		timeout: 30000,

		success: function($response) {

			if ($response.error === false && $response.payment_url) {

				$form.find("input[name='amount']").attr("disabled", "disabled");
				$button.remove();
				$("iframe").attr("src", $response.payment_url).css("height", 800);

			} else {

				showAlert({
					'title' : 'Error',
					'icon' : '<i class="fas fa-times"></i>',
					'icon_colour' : 'var(--bs-red)',
					'content': 'Something went wrong, please refresh the page and try again.',
					'buttons' : {
						'continue' : 'Ok',
						//'continue_link' : $currentURL
					}
				});

				$button.html($button_html).removeAttr("disabled");

			}

		},

		error: function($error) {

			showAlert({
				'title' : 'Error',
				'icon' : '<i class="fas fa-times"></i>',
				'icon_colour' : 'var(--bs-red)',
				'content': 'Something went wrong, please refresh the page and try again.',
				'buttons' : {
					'continue' : 'Ok',
					//'continue_link' : $currentURL
				}
			});

			$button.html($button_html).removeAttr("disabled");

		}

	});

});


/*####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### #######



##    ##  #######  ######## #### ######## ####  ######     ###    ######## ####  #######  ##    ##  ######
###   ## ##     ##    ##     ##  ##        ##  ##    ##   ## ##      ##     ##  ##     ## ###   ## ##    ##
####  ## ##     ##    ##     ##  ##        ##  ##        ##   ##     ##     ##  ##     ## ####  ## ##
## ## ## ##     ##    ##     ##  ######    ##  ##       ##     ##    ##     ##  ##     ## ## ## ##  ######
##  #### ##     ##    ##     ##  ##        ##  ##       #########    ##     ##  ##     ## ##  ####       ##
##   ### ##     ##    ##     ##  ##        ##  ##    ## ##     ##    ##     ##  ##     ## ##   ### ##    ##
##    ##  #######     ##    #### ##       ####  ######  ##     ##    ##    ####  #######  ##    ##  ######



####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### #######*/



if (cookie.get('vmb_updated') == 1) {
	showAlert({
		'title' : 'Update Successful',
		'icon' : '<i class="fas fa-check"></i>',
		'icon_colour' : 'var(--bs-green)',
		'content': 'Your details have been updated',
		'buttons' : {
			'continue' : 'Ok',
		}
	});

	cookie.remove('vmb_updated');
}

if (cookie.get('vmb_payment') == 1) {
	showAlert({
		'title' : 'Thank You',
		'icon' : '<i class="fas fa-check"></i>',
		'icon_colour' : 'var(--bs-green)',
		'content': 'Your payment was successful',
		'buttons' : {
			'continue' : 'Ok',
		}
	});

	cookie.remove('vmb_payment');
}



/*####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### #######



 ######  ####  ######   ##    ##     #######  ##     ## ########
##    ##  ##  ##    ##  ###   ##    ##     ## ##     ##    ##
##        ##  ##        ####  ##    ##     ## ##     ##    ##
 ######   ##  ##   #### ## ## ##    ##     ## ##     ##    ##
      ##  ##  ##    ##  ##  ####    ##     ## ##     ##    ##
##    ##  ##  ##    ##  ##   ###    ##     ## ##     ##    ##
 ######  ####  ######   ##    ##     #######   #######     ##



####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### #######*/



$(".sign-out a").on("click", function(e){
	e.preventDefault();

	let $this = $(this);
	let $link = $this.attr("href");

	showAlert({
		'title' : 'Sign Out',
		'icon' : '<i class="fas fa-sign-out-alt"></i>',
		'icon_colour' : 'var(--primary)',
		'content': 'Are you sure you want to sign out?',
		'buttons' : {
			'cancel' : 'Cancel',
			'continue' : 'Continue',
			'continue_link' : $link
		}
	});
});



/*####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### #######



 ######   #######   ######  ####    ###    ##          ##     ## ######## ########  ####    ###
##    ## ##     ## ##    ##  ##    ## ##   ##          ###   ### ##       ##     ##  ##    ## ##
##       ##     ## ##        ##   ##   ##  ##          #### #### ##       ##     ##  ##   ##   ##
 ######  ##     ## ##        ##  ##     ## ##          ## ### ## ######   ##     ##  ##  ##     ##
      ## ##     ## ##        ##  ######### ##          ##     ## ##       ##     ##  ##  #########
##    ## ##     ## ##    ##  ##  ##     ## ##          ##     ## ##       ##     ##  ##  ##     ##
 ######   #######   ######  #### ##     ## ########    ##     ## ######## ########  #### ##     ##



####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### #######*/



$(".social-media a").on("mouseover", function(){
	let $this = $(this);
	let $color = $this.data("color");

	$this.css("background", $color);
}).on("mouseout", function(){
	let $this = $(this);

	$this.removeAttr("style");
});



/*####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### #######



########    ###    ##    ##  ######  ##    ## ########   #######  ##     ##
##         ## ##   ###   ## ##    ##  ##  ##  ##     ## ##     ##  ##   ##
##        ##   ##  ####  ## ##         ####   ##     ## ##     ##   ## ##
######   ##     ## ## ## ## ##          ##    ########  ##     ##    ###
##       ######### ##  #### ##          ##    ##     ## ##     ##   ## ##
##       ##     ## ##   ### ##    ##    ##    ##     ## ##     ##  ##   ##
##       ##     ## ##    ##  ######     ##    ########   #######  ##     ##



####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### #######*/



//$('a[href$=".gif"], a[href$=".jpg"], a[href$=".jpeg"], a[href$=".png"], a[href$=".bmp"]').attr("data-fancybox","gallery").fancybox();



/*####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### #######



##     ##  #######  ########  #### ##       ########    ##    ##    ###    ##     ##
###   ### ##     ## ##     ##  ##  ##       ##          ###   ##   ## ##   ##     ##
#### #### ##     ## ##     ##  ##  ##       ##          ####  ##  ##   ##  ##     ##
## ### ## ##     ## ########   ##  ##       ######      ## ## ## ##     ## ##     ##
##     ## ##     ## ##     ##  ##  ##       ##          ##  #### #########  ##   ##
##     ## ##     ## ##     ##  ##  ##       ##          ##   ### ##     ##   ## ##
##     ##  #######  ########  #### ######## ########    ##    ## ##     ##    ###



####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### #######*/



$("#mobile-navigation-control").on("click",function(e){
	e.stopPropagation();
	$("html, body, #mobile-navigation, #mobile-background").toggleClass("mobile-nav-open");
});

$(document).on("click",function(){
	$("html.mobile-nav-open, body.mobile-nav-open, #mobile-navigation.mobile-nav-open, #mobile-background.mobile-nav-open").toggleClass("mobile-nav-open");
});



/*####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### #######



##     ## ########  ######      ###       ##     ## ######## ##    ## ##     ##
###   ### ##       ##    ##    ## ##      ###   ### ##       ###   ## ##     ##
#### #### ##       ##         ##   ##     #### #### ##       ####  ## ##     ##
## ### ## ######   ##   #### ##     ##    ## ### ## ######   ## ## ## ##     ##
##     ## ##       ##    ##  #########    ##     ## ##       ##  #### ##     ##
##     ## ##       ##    ##  ##     ##    ##     ## ##       ##   ### ##     ##
##     ## ########  ######   ##     ##    ##     ## ######## ##    ##  #######



####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### #######*/



$('#mega-menu-control').on('click',function(){
	$('html, body').toggleClass('open');
	if ($('#mega-menu-control i').hasClass('fa-bars')){
		$('#mega-menu-control i').removeClass('fa-bars');
		$('#mega-menu-control i').addClass('fa-x');
	} else if ($('#mega-menu-control i').hasClass('fa-x')){
		$('#mega-menu-control i').removeClass('fa-x');
		$('#mega-menu-control i').addClass('fa-bars');
	}
});


$('.column-title').on('click',function(e){
	e.preventDefault();
	$this = $(this);
	$theColumn = $(this).parents('.sub-column');
	if ($theColumn.hasClass('open')){
		$theColumn.removeClass('open');
		$theColumn.children('ul').slideUp();
	} else {
		$theColumn.addClass('open');
		$theColumn.children('ul').slideDown();
	}
	console.log($theColumn);
	
});



/*####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### #######



   ###    ##       ######## ########  ########
  ## ##   ##       ##       ##     ##    ##
 ##   ##  ##       ##       ##     ##    ##
##     ## ##       ######   ########     ##
######### ##       ##       ##   ##      ##
##     ## ##       ##       ##    ##     ##
##     ## ######## ######## ##     ##    ##



####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### #######*/



$alert.find(".cancel").on("click", function(){
	$background.removeClass("open");
	$container.removeClass("open");
});

$alert.find(".continue:not(a)").on("click", function(){
	$background.removeClass("open");
	$container.removeClass("open");
});



/*####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### #######



######## ##     ## ##    ##  ######  ######## ####  #######  ##    ##  ######
##       ##     ## ###   ## ##    ##    ##     ##  ##     ## ###   ## ##    ##
##       ##     ## ####  ## ##          ##     ##  ##     ## ####  ## ##
######   ##     ## ## ## ## ##          ##     ##  ##     ## ## ## ##  ######
##       ##     ## ##  #### ##          ##     ##  ##     ## ##  ####       ##
##       ##     ## ##   ### ##    ##    ##     ##  ##     ## ##   ### ##    ##
##        #######  ##    ##  ######     ##    ####  #######  ##    ##  ######



####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### #######*/



function showAlert($options) {
	$alert.find("h3").html($options.title);
	$alert.find("p.content").html($options.content);
	$alert.find(".icon").html($options.icon);

	if ($options.icon_colour) {
		$alert.find(".icon").css("background", $options.icon_colour);
	}

	if ($options.buttons.cancel) {
		$alert.find(".cancel").html($options.buttons.cancel).show();
	} else {
		$alert.find(".cancel").hide();
	}

	if ($options.buttons.continue) {
		$alert.find(".continue").html($options.buttons.continue);
	} else {
		$alert.find(".continue").hide();
	}

	if ($options.buttons.continue_link) {
		$alert.find("button.continue").hide();
		$alert.find("a.continue").attr("href", $options.buttons.continue_link).show();
	} else {
		$alert.find("a.continue").hide();
		$alert.find("button.continue").show();
	}

	$background.addClass("open");
	$container.addClass("open");
}

function Spinner($string) {
    return '<i class="fas fa-spinner fa-spin"></i>'+($string ? ' '+$string : '');
}

})(this.jQuery);